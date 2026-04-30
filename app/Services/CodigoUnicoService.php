<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CodigoUnicoService
{
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
      * Si existen el 1, 2, 3 y el 100, devolverá el 4.
      *
      * Método original - busca huecos globales en todas las entidades.
      */
     public static function obtenerSiguienteCodigo(): string
     {
         // 1. Obtenemos TODOS los códigos numéricos de todas las tablas y los unimos
         $queries = [];
         foreach (self::getModelMapping() as $config) {
             $tableName = (new $config[0])->getTable();
             $queries[] = DB::table($tableName)
                 ->selectRaw('CAST(codigo AS UNSIGNED) as num')
                 ->whereRaw("codigo REGEXP '^[0-9]+$'");
         }

         // Combinamos todos los resultados con UNION
         $todosLosCodigos = $queries[0];
         for ($i = 1; $i < count($queries); $i++) {
             $todosLosCodigos->union($queries[$i]);
         }

         // 2. Buscamos el primer número que NO esté en la lista
         // Creamos una subconsulta con los códigos existentes
         $existentes = $todosLosCodigos->pluck('num')->toArray();

         if (empty($existentes)) {
             return str_pad('1', 8, '0', STR_PAD_LEFT);
         }

         // Ordenamos para encontrar el hueco
         sort($existentes);

         $siguiente = 1;
         foreach ($existentes as $codigo) {
             if ($codigo == $siguiente) {
                 $siguiente++;
             } elseif ($codigo > $siguiente) {
                 // Encontramos un hueco (ej: tenemos 1, 2, 3 y luego 100, el siguiente es 4)
                 break;
             }
         }

         return str_pad((string) $siguiente, 8, '0', STR_PAD_LEFT);
     }

    /**
     * Reserva un bloque de códigos para un organismo y actualiza su rango.
     *
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int, 'codigo_formato_min' => string]
     */
    public static function reservarCodigosParaOrganismo(int $organismoId, int $cantidad = 50): array
    {
        return DB::transaction(function () use ($organismoId, $cantidad) {
            $organismo = Organismo::where('id', $organismoId)
                ->lockForUpdate()
                ->first();

            if (! $organismo) {
                throw new ModelNotFoundException("Organismo con ID {$organismoId} no encontrado.");
            }

            $codigoOrg = (int) $organismo->codigo;

            // Detectar si el rango actual es el default (1-50) = no asignado realmente
            $esDefault = ($organismo->code_min == 1 && $organismo->code_max == 50);

            // Determinar el rango
            if ($organismo->code_max > 0 && !$esDefault) {
                // Ya tiene un rango asignado personalizado: continuar desde el máximo actual
                $ultimoAsignado = $organismo->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // No tiene rango asignado o es default: calcular basado en código del organismo
                $base = $codigoOrg * 10000;
                $min = $base + 1;
                $max = $base + $cantidad;
            }

            // Actualizar el organismo con el nuevo rango
            $organismo->code_min = $min;
            $organismo->code_max = $max;
            $organismo->save();

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => str_pad((string) $min, 8, '0', STR_PAD_LEFT),
                'codigo_formato_max' => str_pad((string) $max, 8, '0', STR_PAD_LEFT),
            ];
        }, 3);
    }

    /**
     * Reserva un bloque de códigos para una unidad (dentro del rango de su organismo).
     *
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int]
     */
    public static function reservarCodigosParaUnidad(int $unidadId, int $cantidad = 50): array
    {
        return DB::transaction(function () use ($unidadId, $cantidad) {
            $unidad = UnidadAdministradora::where('id', $unidadId)
                ->lockForUpdate()
                ->with('organismo')
                ->first();

            if (! $unidad) {
                throw new ModelNotFoundException("Unidad Administradora con ID {$unidadId} no encontrada.");
            }

            $codigoUnidad = (int) $unidad->codigo;

            // Detectar default (1-50)
            $esDefault = ($unidad->code_min == 1 && $unidad->code_max == 50);

            if ($unidad->code_max > 0 && !$esDefault) {
                // Ya tiene rango asignado: continuar desde el máximo actual
                $ultimoAsignado = $unidad->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // No tiene rango asignado o es default: calcular basado en código de la unidad
                $base = $codigoUnidad * 100;
                $min = $base + 1;
                $max = $base + $cantidad;
            }

            // Validar que no exceda el rango del organismo padre
            $organismo = $unidad->organismo;
            if ($organismo && $organismo->code_max > 0) {
                // Si el organismo tiene default, no validamos (se corregirá luego)
                $esDefaultOrg = ($organismo->code_min == 1 && $organismo->code_max == 50);
                if (!$esDefaultOrg && $max > $organismo->code_max) {
                    throw new RuntimeException(
                        "Rango de códigos insuficiente en el organismo. ".
                        "Solicite ampliación de rango. Máximo permitido: {$organismo->code_max}"
                    );
                }
            }

            // Actualizar la unidad con el nuevo rango
            $unidad->code_min = $min;
            $unidad->code_max = $max;
            $unidad->save();

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => str_pad((string) $min, 8, '0', STR_PAD_LEFT),
                'codigo_formato_max' => str_pad((string) $max, 8, '0', STR_PAD_LEFT),
            ];
        }, 3);
    }

    /**
     * Reserva un bloque de códigos para una dependencia (dentro del rango de su unidad).
     *
     * @param  int  $dependenciaId  ID de la dependencia
     * @param  int  $cantidad  Número de códigos a reservar (por defecto 50)
     * @return array ['code_min' => int, 'code_max' => int]
     */
    public static function reservarCodigosParaDependencia(int $dependenciaId, int $cantidad = 50): array
    {
        return DB::transaction(function () use ($dependenciaId, $cantidad) {
            $dependencia = Dependencia::where('id', $dependenciaId)
                ->lockForUpdate()
                ->with('unidadAdministradora')
                ->first();

            if (! $dependencia) {
                throw new ModelNotFoundException("Dependencia con ID {$dependenciaId} no encontrada.");
            }

            $codigoDep = (int) $dependencia->codigo;

            // Detectar default (1-50)
            $esDefault = ($dependencia->code_min == 1 && $dependencia->code_max == 50);

            if ($dependencia->code_max > 0 && !$esDefault) {
                // Ya tiene rango asignado: continuar desde el máximo actual
                $ultimoAsignado = $dependencia->code_max;
                $min = $ultimoAsignado + 1;
                $max = $min + $cantidad - 1;
            } else {
                // Primera asignación: el rango para bienes comienza justo después del código de la dependencia
                // para evitar duplicados (la dependencia ya usa su propio código)
                $min = $codigoDep + 1;
                $max = $codigoDep + $cantidad;
            }

            // Validar que no exceda el rango de la unidad padre
            $unidad = $dependencia->unidadAdministradora;
            if ($unidad && $unidad->code_max > 0) {
                $esDefaultUnidad = ($unidad->code_min == 1 && $unidad->code_max == 50);
                if (!$esDefaultUnidad && $max > $unidad->code_max) {
                    throw new RuntimeException(
                        "Rango de códigos insuficiente en la unidad administradora. ".
                        "Solicite ampliación de rango. Máximo permitido: {$unidad->code_max}"
                    );
                }
            }

            // Actualizar la dependencia con el nuevo rango
            $dependencia->code_min = $min;
            $dependencia->code_max = $max;
            $dependencia->save();

            return [
                'code_min' => $min,
                'code_max' => $max,
                'codigo_formato_min' => str_pad((string) $min, 8, '0', STR_PAD_LEFT),
                'codigo_formato_max' => str_pad((string) $max, 8, '0', STR_PAD_LEFT),
            ];
        }, 3);
    }

    /**
     * Obtiene el siguiente código disponible DENTRO DEL RANGO de una dependencia específica.
      * Obtiene el siguiente código disponible DENTRO DEL RANGO de una dependencia específica.
      *
      * Lógica de auditoría: no rellenar huecos (gaps). Se usa MAX(code)+1 para mantener
      * la secuencia históricamente ininterrumpida. Los códigos de bienes desincorporados
      * se conservan en bienes_desincorporados, por lo que MAX(code) refleja el último
      * código asignado aunque el bien ya no esté activo.
      *
      * @param  int  $dependenciaId  ID de la dependencia
      * @return array ['codigo' => string, 'dependencia' => Dependencia]
      *
      * @throws RuntimeException Si el rango está agotado o hay error de concurrencia
      */
     public static function recomendarSiguienteCodigoParaDependencia(int $dependenciaId): array
     {
        return DB::transaction(function () use ($dependenciaId) {
            // 1. Bloqueo SELECT FOR UPDATE para evitar condiciones de carrera
            $dependencia = Dependencia::where('id', $dependenciaId)
                ->lockForUpdate()
                ->first();

            if (! $dependencia) {
                throw new ModelNotFoundException("Dependencia con ID {$dependenciaId} no encontrada.");
            }

            $min = (int) $dependencia->code_min;
            $max = (int) $dependencia->code_max;

            // 2. Calcular el próximo código: MAX(code) + 1 dentro de la dependencia
            // Incluye TODOS los bienes de esta dependencia (activos o no, ya que
            // los desincorporados se mueven a otra tabla pero conservan su código)
            $maxCode = Bien::where('dependencia_id', $dependenciaId)
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->max(DB::raw('CAST(codigo AS UNSIGNED)'));

            if ($maxCode === null) {
                $siguiente = $min;
            } else {
                $siguiente = (int) $maxCode + 1;
            }

            // 3. Validar contra el rango permitido
            if ($siguiente < $min) {
                $siguiente = $min;
            }

            if ($siguiente > $max) {
                throw new RuntimeException(
                    "Rango de códigos exhausto para la dependencia '{$dependencia->nombre}'. ".
                    "Último código disponible: {$max} (rango: {$min}-{$max}). ".
                    'Solicite ampliación de rango al administrador.'
                );
            }

            // 4. Formatear el código a 8 dígitos con ceros a la izquierda
            // (coincide con el formato del campo en la tabla bienes)
            $codigoFormateado = str_pad((string) $siguiente, 8, '0', STR_PAD_LEFT);

            // 5. Verificar por si acaso que no haya colisión (p.ej. código reutilizado manualmente)
            if (Bien::where('dependencia_id', $dependenciaId)
                ->where('codigo', $codigoFormateado)
                ->exists()) {
                // Si colisiona (caso muy raro debido al lock), recursividad segura
                return self::recomendarSiguienteCodigoParaDependencia($dependenciaId);
            }

            return [
                'codigo' => $codigoFormateado,
                'dependencia' => $dependencia,
                'siguiente_numero' => $siguiente,
                'rango_min' => $min,
                'rango_max' => $max,
            ];
        }, 3); // 3 reintentos en caso de deadlock
    }

    /**
     * Verifica disponibilidad (Útil para validar el "código 100" que meta el usuario)
     */
    public static function codigoExiste(string $codigo, ?string $tablaActual = null, ?int $excluirId = null): bool
    {
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
     */
    public static function obtenerUbicacionCodigo(string $codigo): ?array
    {
        foreach (self::getModelMapping() as $config) {
            $registro = $config[0]::where('codigo', $codigo)->first();
            if ($registro) {
                $campoNombre = $config[1];

                return [
                    'tabla' => $config[2],
                    'nombre' => $registro->$campoNombre,
                    'id' => $registro->id,
                ];
            }
        }

        return null;
    }
}
