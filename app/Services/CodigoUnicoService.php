<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use InvalidArgumentException;

class CodigoUnicoService
{
    /**
     * Máximo código permitido (8 dígitos = 99,999,999)
     */
    private const MAX_CODIGO = 99999999;
    
    /**
     * Tamaño de lote para procesamiento masivo
     */
    private const BATCH_SIZE = 1000;
    
    /**
     * Mapping de tablas y modelos
     */
    private static function getModelMapping(): array
    {
        return [
            'organismos' => [Organismo::class, 'nombre', 'Organismo'],
            'unidades' => [UnidadAdministradora::class, 'nombre', 'Unidad Administradora'],
            'dependencias' => [Dependencia::class, 'nombre', 'Dependencia'],
            'bienes' => [Bien::class, 'descripcion', 'Bien'],
        ];
    }

    /**
     * Obtiene el primer código disponible en la secuencia (rellena huecos).
     * Versión optimizada que no carga todos los registros en memoria.
     * 
     * @return string Código formateado a 8 dígitos
     * @throws RuntimeException Si no hay códigos disponibles
     */
    public static function obtenerSiguienteCodigo(): string
    {
        try {
            // Para conjuntos pequeños (< 10000 registros), usar método original optimizado
            $totalRegistros = self::contarTotalRegistros();
            
            if ($totalRegistros < self::BATCH_SIZE) {
                return self::obtenerSiguienteCodigoConUnion();
            }
            
            // Para conjuntos grandes, usar algoritmo de búsqueda binaria
            return self::obtenerSiguienteCodigoBinario();
            
        } catch (\Exception $e) {
            Log::error('Error al obtener siguiente código único', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new RuntimeException('No se pudo generar un código disponible: ' . $e->getMessage());
        }
    }
    
    /**
     * Cuenta el total de registros en todas las tablas (versión optimizada)
     */
    private static function contarTotalRegistros(): int
    {
        $total = 0;
        foreach (self::getModelMapping() as $config) {
            $total += $config[0]::count();
        }
        return $total;
    }
    
    /**
     * Método original mejorado (para conjuntos pequeños/medianos)
     */
    private static function obtenerSiguienteCodigoConUnion(): string
    {
        // Obtener códigos existentes en lotes
        $existentes = [];
        foreach (self::getModelMapping() as $config) {
            $tableName = (new $config[0])->getTable();
            $codigos = DB::table($tableName)
                ->selectRaw('CAST(codigo AS UNSIGNED) as num')
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->whereRaw('LENGTH(codigo) <= 8')
                ->orderBy('num')
                ->pluck('num')
                ->toArray();
            
            $existentes = array_merge($existentes, $codigos);
        }
        
        if (empty($existentes)) {
            return self::formatearCodigo(1);
        }
        
        // Eliminar duplicados y ordenar
        $existentes = array_unique($existentes);
        sort($existentes);
        
        $siguiente = 1;
        foreach ($existentes as $codigo) {
            if ($codigo == $siguiente) {
                $siguiente++;
            } elseif ($codigo > $siguiente) {
                break;
            }
            
            // Validar límite máximo
            if ($siguiente > self::MAX_CODIGO) {
                throw new RuntimeException('Se ha alcanzado el límite máximo de códigos disponibles');
            }
        }
        
        return self::formatearCodigo($siguiente);
    }
    
    /**
     * Algoritmo de búsqueda binaria para conjuntos grandes
     */
    private static function obtenerSiguienteCodigoBinario(): string
    {
        $low = 1;
        $high = self::MAX_CODIGO;
        
        while ($low <= $high) {
            $mid = (int)(($low + $high) / 2);
            
            if (self::existeCodigoEnRango($low, $mid)) {
                // Hay códigos ocupados en la primera mitad
                if ($low == $mid) {
                    // Encontramos el primer código ocupado
                    $low++;
                    if ($low > $high) {
                        break;
                    }
                } else {
                    $high = $mid;
                }
            } else {
                // Primera mitad está libre, encontramos hueco
                $siguiente = $low;
                return self::formatearCodigo($siguiente);
            }
        }
        
        throw new RuntimeException('No se encontraron códigos disponibles. Rango completo: 1 - ' . self::MAX_CODIGO);
    }
    
    /**
     * Verifica si existe algún código en el rango especificado
     */
    private static function existeCodigoEnRango(int $min, int $max): bool
    {
        $codigoMin = self::formatearCodigo($min);
        $codigoMax = self::formatearCodigo($max);
        
        foreach (self::getModelMapping() as $config) {
            $existe = $config[0]::where('codigo', '>=', $codigoMin)
                ->where('codigo', '<=', $codigoMax)
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->exists();
            
            if ($existe) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Formatea un número a código de 8 dígitos
     */
    private static function formatearCodigo(int $numero): string
    {
        if ($numero < 1 || $numero > self::MAX_CODIGO) {
            throw new InvalidArgumentException("Número de código inválido: {$numero}. Debe estar entre 1 y " . self::MAX_CODIGO);
        }
        
        return str_pad((string) $numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Reserva un bloque de códigos para un organismo y actualiza su rango.
     *
     * @param  int  $organismoId  ID del organismo
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int, 'codigo_formato_min' => string]
     * @throws ModelNotFoundException|RuntimeException
     */
    public static function reservarCodigosParaOrganismo(int $organismoId, int $cantidad = 50): array
    {
        if ($cantidad <= 0 || $cantidad > 10000) {
            throw new InvalidArgumentException("La cantidad debe estar entre 1 y 10000");
        }
        
        return DB::transaction(function () use ($organismoId, $cantidad) {
            $organismo = Organismo::where('id', $organismoId)
                ->lockForUpdate()
                ->first();

            if (!$organismo) {
                throw new ModelNotFoundException("Organismo con ID {$organismoId} no encontrado.");
            }
            
            // Guardar rango anterior para auditoría
            $rangoAnterior = [
                'min' => $organismo->code_min,
                'max' => $organismo->code_max
            ];

            $codigoOrg = (int) $organismo->codigo;
            
            // Verificar si el rango está asignado (usando null en lugar de default)
            $rangoAsignado = !is_null($organismo->code_min) && !is_null($organismo->code_max);
            
            // Determinar el rango
            if ($rangoAsignado) {
                // Ya tiene un rango asignado: continuar desde el máximo actual
                $ultimoAsignado = $organismo->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // No tiene rango asignado: calcular basado en código del organismo
                $base = $codigoOrg * 10000;
                $min = $base + 1;
                $max = $base + $cantidad;
            }
            
            // Validar límite máximo
            if ($max > self::MAX_CODIGO) {
                throw new RuntimeException(
                    "El rango solicitado excede el límite máximo de códigos ({$max} > " . self::MAX_CODIGO . "). " .
                    "Solicite una cantidad menor o contacte al administrador del sistema."
                );
            }
            
            // Verificar disponibilidad del rango
            self::validarDisponibilidadRango($min, $max, 'organismo', $organismo->id);

            // Actualizar el organismo con el nuevo rango
            $organismo->code_min = $min;
            $organismo->code_max = $max;
            $organismo->save();
            
            // Registrar auditoría
            self::registrarAuditoria('organismo', $organismo->id, $organismo->codigo, $rangoAnterior, [
                'min' => $min,
                'max' => $max
            ], $cantidad);

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => self::formatearCodigo($min),
                'codigo_formato_max' => self::formatearCodigo($max),
                'cantidad_reservada' => $cantidad
            ];
        }, 5);
    }

    /**
     * Reserva un bloque de códigos para una unidad (dentro del rango de su organismo).
     *
     * @param  int  $unidadId  ID de la unidad
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int, 'codigo_formato_min' => string]
     * @throws ModelNotFoundException|RuntimeException
     */
    public static function reservarCodigosParaUnidad(int $unidadId, int $cantidad = 50): array
    {
        if ($cantidad <= 0 || $cantidad > 5000) {
            throw new InvalidArgumentException("La cantidad debe estar entre 1 y 5000");
        }
        
        return DB::transaction(function () use ($unidadId, $cantidad) {
            $unidad = UnidadAdministradora::where('id', $unidadId)
                ->lockForUpdate()
                ->with('organismo')
                ->first();

            if (!$unidad) {
                throw new ModelNotFoundException("Unidad Administradora con ID {$unidadId} no encontrada.");
            }

            $rangoAnterior = [
                'min' => $unidad->code_min,
                'max' => $unidad->code_max
            ];

            $codigoUnidad = (int) $unidad->codigo;
            
            // Verificar si el rango está asignado
            $rangoAsignado = !is_null($unidad->code_min) && !is_null($unidad->code_max);

            if ($rangoAsignado) {
                // Ya tiene rango asignado: continuar desde el máximo actual
                $ultimoAsignado = $unidad->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // Primera asignación: calcular basado en código de la unidad
                $base = $codigoUnidad * 100;
                $min = $base + 1;
                $max = $base + $cantidad;
            }
            
            // Validar rango del organismo padre
            self::validarRangoPadre($unidad->organismo, $max, 'Organismo', $cantidad);
            
            // Validar límite máximo
            if ($max > self::MAX_CODIGO) {
                throw new RuntimeException("El rango solicitado excede el límite máximo de códigos");
            }
            
            // Verificar disponibilidad
            self::validarDisponibilidadRango($min, $max, 'unidad', $unidad->id);

            // Actualizar la unidad con el nuevo rango
            $unidad->code_min = $min;
            $unidad->code_max = $max;
            $unidad->save();
            
            // Registrar auditoría
            self::registrarAuditoria('unidad_administradora', $unidad->id, $unidad->codigo, $rangoAnterior, [
                'min' => $min,
                'max' => $max
            ], $cantidad);

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => self::formatearCodigo($min),
                'codigo_formato_max' => self::formatearCodigo($max),
                'cantidad_reservada' => $cantidad
            ];
        }, 5);
    }

    /**
     * Reserva un bloque de códigos para una dependencia (dentro del rango de su unidad).
     *
     * @param  int  $dependenciaId  ID de la dependencia
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int, 'codigo_formato_min' => string]
     * @throws ModelNotFoundException|RuntimeException
     */
    public static function reservarCodigosParaDependencia(int $dependenciaId, int $cantidad = 50): array
    {
        if ($cantidad <= 0 || $cantidad > 1000) {
            throw new InvalidArgumentException("La cantidad debe estar entre 1 y 1000");
        }
        
        return DB::transaction(function () use ($dependenciaId, $cantidad) {
            $dependencia = Dependencia::where('id', $dependenciaId)
                ->lockForUpdate()
                ->with('unidadAdministradora.organismo')
                ->first();

            if (!$dependencia) {
                throw new ModelNotFoundException("Dependencia con ID {$dependenciaId} no encontrada.");
            }

            $rangoAnterior = [
                'min' => $dependencia->code_min,
                'max' => $dependencia->code_max
            ];

            $codigoDep = (int) $dependencia->codigo;
            
            // Verificar si el rango está asignado
            $rangoAsignado = !is_null($dependencia->code_min) && !is_null($dependencia->code_max);

            if ($rangoAsignado) {
                // Ya tiene rango asignado: continuar desde el máximo actual
                $ultimoAsignado = $dependencia->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // Primera asignación: el rango para bienes comienza justo después del código de la dependencia
                $min = $codigoDep + 1;
                $max = $codigoDep + $cantidad;
            }
            
            // Validar rango de la unidad padre
            self::validarRangoPadre($dependencia->unidadAdministradora, $max, 'Unidad Administradora', $cantidad);
            
            // Validar límite máximo
            if ($max > self::MAX_CODIGO) {
                throw new RuntimeException("El rango solicitado excede el límite máximo de códigos");
            }
            
            // Verificar disponibilidad
            self::validarDisponibilidadRango($min, $max, 'dependencia', $dependencia->id);

            // Actualizar la dependencia con el nuevo rango
            $dependencia->code_min = $min;
            $dependencia->code_max = $max;
            $dependencia->save();
            
            // Registrar auditoría
            self::registrarAuditoria('dependencia', $dependencia->id, $dependencia->codigo, $rangoAnterior, [
                'min' => $min,
                'max' => $max
            ], $cantidad);

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => self::formatearCodigo($min),
                'codigo_formato_max' => self::formatearCodigo($max),
                'cantidad_reservada' => $cantidad
            ];
        }, 5);
    }

    /**
     * Valida que el rango solicitado no exceda el rango del padre
     */
    private static function validarRangoPadre($padre, int $max, string $tipoPadre, int $cantidadSolicitada): void
    {
        if (!$padre) {
            return;
        }
        
        // Verificar si el padre tiene rango asignado
        $padreTieneRango = !is_null($padre->code_min) && !is_null($padre->code_max);
        
        if (!$padreTieneRango) {
            // El padre no tiene rango asignado, no podemos validar
            Log::warning("Validación de rango omitida: {$tipoPadre} no tiene rango asignado", [
                'padre_id' => $padre->id,
                'padre_codigo' => $padre->codigo
            ]);
            return;
        }
        
        if ($max > $padre->code_max) {
            $rangoDisponible = $padre->code_max - $padre->code_min + 1;
            $rangoUtilizado = ($padre->code_max - $padre->code_min + 1) - $rangoDisponible;
            
            throw new RuntimeException(
                "El {$tipoPadre} no tiene suficientes códigos disponibles para la reserva. " .
                "Rango del padre: {$padre->code_min} - {$padre->code_max} (Total: {$rangoDisponible} códigos). " .
                "Códigos ya utilizados: ~{$rangoUtilizado}. " .
                "Solicitados adicionales: {$cantidadSolicitada}. " .
                "Máximo permitido en esta solicitud: {$rangoDisponible}. " .
                "Consulte a su administrador para ampliar el rango del {$tipoPadre}."
            );
        }
    }
    
    /**
     * Verifica que el rango solicitado esté disponible (no se solape con otros)
     */
    private static function validarDisponibilidadRango(int $min, int $max, string $entidad, int $entidadId): void
    {
        $codigoMin = self::formatearCodigo($min);
        $codigoMax = self::formatearCodigo($max);
        
        foreach (self::getModelMapping() as $config) {
            // Excluir la propia entidad que estamos actualizando
            $query = $config[0]::where('codigo', '>=', $codigoMin)
                ->where('codigo', '<=', $codigoMax);
            
            // Si es la misma tabla que estamos actualizando, excluir el registro actual
            $tableName = (new $config[0])->getTable();
            $modeloActual = null;
            
            switch ($entidad) {
                case 'organismo':
                    $modeloActual = Organismo::class;
                    break;
                case 'unidad':
                case 'unidad_administradora':
                    $modeloActual = UnidadAdministradora::class;
                    break;
                case 'dependencia':
                    $modeloActual = Dependencia::class;
                    break;
            }
            
            if ($modeloActual && $config[0] === $modeloActual) {
                $query->where('id', '!=', $entidadId);
            }
            
            if ($query->exists()) {
                $conflicto = $query->select('codigo', 'id')->first();
                throw new RuntimeException(
                    "Conflicto de rangos: El código {$conflicto->codigo} ya está siendo utilizado " .
                    "en la tabla {$tableName} (ID: {$conflicto->id}). " .
                    "Rango solicitado: {$codigoMin} - {$codigoMax}. " .
                    "Por favor, solicite un rango diferente o amplíe el rango actual."
                );
            }
        }
    }
    
    /**
     * Registra auditoría de asignación de rangos
     */
    private static function registrarAuditoria(string $entidad, int $entidadId, string $codigoEntidad, array $rangoAnterior, array $rangoNuevo, int $cantidad): void
    {
        Log::info('Rango de códigos reservado', [
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'codigo_entidad' => $codigoEntidad,
            'rango_anterior' => $rangoAnterior,
            'rango_nuevo' => $rangoNuevo,
            'cantidad_reservada' => $cantidad,
            'usuario_id' => auth()->id() ?? 'system',
            'usuario_email' => auth()->user()->email ?? 'system',
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
            'total_rango' => $rangoNuevo['max'] - $rangoNuevo['min'] + 1
        ]);
    }

    /**
     * Obtiene el siguiente código disponible DENTRO DEL RANGO de una dependencia específica.
     * 
     * Lógica de auditoría: no rellenar huecos (gaps). Se usa MAX(code)+1 para mantener
     * la secuencia históricamente ininterrumpida. Los códigos de bienes desincorporados
     * se conservan en bienes_desincorporados, por lo que MAX(code) refleja el último
     * código asignado aunque el bien ya no esté activo.
     *
     * @param  int  $dependenciaId  ID de la dependencia
     * @return array ['codigo' => string, 'dependencia' => Dependencia, 'siguiente_numero' => int, ...]
     * @throws ModelNotFoundException|RuntimeException
     */
    public static function recomendarSiguienteCodigoParaDependencia(int $dependenciaId): array
    {
        $maxIntentos = 5;
        $intento = 0;
        
        while ($intento < $maxIntentos) {
            $intento++;
            
            try {
                return DB::transaction(function () use ($dependenciaId) {
                    // 1. Bloqueo SELECT FOR UPDATE para evitar condiciones de carrera
                    $dependencia = Dependencia::where('id', $dependenciaId)
                        ->lockForUpdate()
                        ->first();

                    if (!$dependencia) {
                        throw new ModelNotFoundException("Dependencia con ID {$dependenciaId} no encontrada.");
                    }

                    // Verificar que la dependencia tenga rango asignado
                    if (is_null($dependencia->code_min) || is_null($dependencia->code_max)) {
                        throw new RuntimeException(
                            "La dependencia '{$dependencia->nombre}' no tiene un rango de códigos asignado. " .
                            "Por favor, solicite a un administrador que asigne un rango primero."
                        );
                    }

                    $min = (int) $dependencia->code_min;
                    $max = (int) $dependencia->code_max;

                    // Validar que el rango sea válido
                    if ($min > $max) {
                        throw new RuntimeException(
                            "Configuración de rango inválida para dependencia '{$dependencia->nombre}'. " .
                            "Mínimo ({$min}) es mayor que máximo ({$max}). Contacte al administrador."
                        );
                    }
                    
                    if ($min < 1 || $max > self::MAX_CODIGO) {
                        throw new RuntimeException(
                            "Rango de códigos fuera de los límites permitidos (1 - " . self::MAX_CODIGO . "). " .
                            "Rango actual: {$min} - {$max}"
                        );
                    }

                    // 2. Calcular el próximo código: MAX(code) + 1 dentro de la dependencia
                    // Incluye TODOS los bienes de esta dependencia (activos o no)
                    $maxCode = Bien::where('dependencia_id', $dependenciaId)
                        ->whereRaw("codigo REGEXP '^[0-9]+$'")
                        ->whereRaw('LENGTH(codigo) <= 8')
                        ->max(DB::raw('CAST(codigo AS UNSIGNED)'));

                    $siguiente = $maxCode === null ? $min : (int) $maxCode + 1;

                    // 3. Validar contra el rango permitido
                    if ($siguiente < $min) {
                        Log::warning("Código siguiente menor que mínimo, ajustando", [
                            'dependencia_id' => $dependenciaId,
                            'siguiente_calculado' => $siguiente,
                            'minimo' => $min
                        ]);
                        $siguiente = $min;
                    }

                    if ($siguiente > $max) {
                        $rangoUtilizado = $maxCode ? $maxCode - $min + 1 : 0;
                        $porcentajeUso = $rangoUtilizado > 0 ? round(($rangoUtilizado / ($max - $min + 1)) * 100, 2) : 0;
                        
                        throw new RuntimeException(
                            "Rango de códigos exhausto para la dependencia '{$dependencia->nombre}'. " .
                            "Rango asignado: {$min} - {$max} (Total: " . ($max - $min + 1) . " códigos). " .
                            "Códigos utilizados: {$rangoUtilizado} ({$porcentajeUso}%). " .
                            "Último código disponible: {$max}. " .
                            "Solicite ampliación de rango al administrador."
                        );
                    }
                    
                    // 4. Verificar disponibilidad real del código (sin gaps)
                    $codigoFormateado = self::formatearCodigo($siguiente);
                    
                    // Verificar si el código ya existe (colisión)
                    $intentosColision = 0;
                    $maxIntentosColision = 100;
                    
                    while (Bien::where('dependencia_id', $dependenciaId)
                        ->where('codigo', $codigoFormateado)
                        ->exists() && $intentosColision < $maxIntentosColision) {
                        
                        $intentosColision++;
                        $siguiente++;
                        
                        if ($siguiente > $max) {
                            throw new RuntimeException(
                                "Rango de códigos exhausto después de detectar códigos ocupados. " .
                                "Último intento: {$max}. Contacte al administrador."
                            );
                        }
                        
                        $codigoFormateado = self::formatearCodigo($siguiente);
                    }
                    
                    if ($intentosColision >= $maxIntentosColision) {
                        throw new RuntimeException(
                            "Demasiadas colisiones al generar código único. Intente nuevamente."
                        );
                    }
                    
                    // 5. Log de la asignación (para trazabilidad)
                    Log::info('Código recomendado para dependencia', [
                        'dependencia_id' => $dependenciaId,
                        'dependencia_nombre' => $dependencia->nombre,
                        'codigo_generado' => $codigoFormateado,
                        'numero_secuencial' => $siguiente,
                        'rango_actual' => "{$min}-{$max}",
                        'porcentaje_uso' => round((($siguiente - $min) / ($max - $min + 1)) * 100, 2),
                        'usuario_id' => auth()->id() ?? 'system'
                    ]);

                    return [
                        'codigo' => $codigoFormateado,
                        'dependencia' => $dependencia,
                        'siguiente_numero' => $siguiente,
                        'rango_min' => $min,
                        'rango_max' => $max,
                        'disponibles_restantes' => $max - $siguiente + 1,
                        'porcentaje_uso' => round((($siguiente - $min) / ($max - $min + 1)) * 100, 2)
                    ];
                    
                }, 5); // 5 reintentos para deadlock
                
            } catch (\Illuminate\Database\QueryException $e) {
                // Deadlock o timeout detectado, reintentar
                if (str_contains($e->getMessage(), 'Deadlock') || str_contains($e->getMessage(), 'Lock wait timeout')) {
                    if ($intento >= $maxIntentos) {
                        Log::error('Error de concurrencia persistente al generar código', [
                            'dependencia_id' => $dependenciaId,
                            'intentos' => $intento,
                            'error' => $e->getMessage()
                        ]);
                        throw new RuntimeException(
                            "No se pudo generar un código único después de {$maxIntentos} intentos debido a conflictos de concurrencia. " .
                            "Por favor, intente nuevamente en unos momentos."
                        );
                    }
                    
                    $tiempoEspera = $intento * 100000; // 100ms, 200ms, 300ms...
                    Log::warning("Deadlock detectado, reintentando", [
                        'intento' => $intento,
                        'espera_ms' => $tiempoEspera / 1000
                    ]);
                    usleep($tiempoEspera);
                    continue;
                }
                
                // Otro tipo de error de BD
                throw $e;
                
            } catch (\Exception $e) {
                // Errores no relacionados con concurrencia
                Log::error('Error inesperado al recomendar código', [
                    'dependencia_id' => $dependenciaId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
        
        throw new RuntimeException(
            "No se pudo generar un código único después de {$maxIntentos} intentos."
        );
    }

    /**
     * Verifica disponibilidad de un código (Útil para validar el código que ingresa el usuario)
     * 
     * @param string $codigo Código a verificar
     * @param string|null $tablaActual Tabla a excluir de la verificación
     * @param int|null $excluirId ID del registro a excluir
     * @return bool True si el código existe
     */
    public static function codigoExiste(string $codigo, ?string $tablaActual = null, ?int $excluirId = null): bool
    {
        // Validar formato del código
        if (!preg_match('/^\d{8}$/', $codigo)) {
            return false; // Código con formato inválido no puede existir
        }
        
        foreach (self::getModelMapping() as $key => $config) {
            $query = $config[0]::where('codigo', $codigo);

            if ($tablaActual === $key && $excluirId) {
                $query->where('id', '!=', $excluirId);
            }

            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Localiza un código existente
     * 
     * @param string $codigo Código a buscar
     * @return array|null Información del código o null si no existe
     */
    public static function obtenerUbicacionCodigo(string $codigo): ?array
    {
        // Validar formato
        if (!preg_match('/^\d{8}$/', $codigo)) {
            return null;
        }
        
        foreach (self::getModelMapping() as $key => $config) {
            $registro = $config[0]::where('codigo', $codigo)->first();
            if ($registro) {
                $campoNombre = $config[1];
                
                return [
                    'tabla' => $config[2],
                    'tabla_key' => $key,
                    'nombre' => $registro->$campoNombre,
                    'id' => $registro->id,
                    'codigo' => $codigo,
                    'created_at' => $registro->created_at ?? null
                ];
            }
        }

        return null;
    }
    
    /**
     * Obtiene estadísticas de uso de códigos para una dependencia
     * 
     * @param int $dependenciaId
     * @return array Estadísticas de uso
     */
    public static function obtenerEstadisticasUso(int $dependenciaId): array
    {
        $dependencia = Dependencia::find($dependenciaId);
        
        if (!$dependencia) {
            throw new ModelNotFoundException("Dependencia con ID {$dependenciaId} no encontrada");
        }
        
        if (is_null($dependencia->code_min) || is_null($dependencia->code_max)) {
            return [
                'tiene_rango' => false,
                'mensaje' => 'La dependencia no tiene un rango asignado'
            ];
        }
        
        $min = (int) $dependencia->code_min;
        $max = (int) $dependencia->code_max;
        $totalRango = $max - $min + 1;
        
        $codigosUtilizados = Bien::where('dependencia_id', $dependenciaId)
            ->whereRaw("codigo REGEXP '^[0-9]+$'")
            ->whereRaw('CAST(codigo AS UNSIGNED) BETWEEN ? AND ?', [$min, $max])
            ->count();
        
        $maxCodeUtilizado = Bien::where('dependencia_id', $dependenciaId)
            ->whereRaw("codigo REGEXP '^[0-9]+$'")
            ->max(DB::raw('CAST(codigo AS UNSIGNED)'));
        
        $siguienteDisponible = $maxCodeUtilizado ? $maxCodeUtilizado + 1 : $min;
        $disponiblesRestantes = max(0, $max - $siguienteDisponible + 1);
        
        return [
            'tiene_rango' => true,
            'rango_min' => $min,
            'rango_max' => $max,
            'total_rango' => $totalRango,
            'codigos_utilizados' => $codigosUtilizados,
            'porcentaje_uso' => round(($codigosUtilizados / $totalRango) * 100, 2),
            'max_codigo_utilizado' => $maxCodeUtilizado,
            'siguiente_disponible' => $siguienteDisponible <= $max ? $siguienteDisponible : null,
            'disponibles_restantes' => $disponiblesRestantes
        ];
    }
}