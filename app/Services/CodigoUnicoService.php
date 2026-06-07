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

class CodigoJerarquicoService
{
    /**
     * Longitudes de cada nivel
     */
    private const LONG_ORGANISMO = 8;
    private const LONG_UNIDAD = 3;      // Dígitos adicionales
    private const LONG_DEPENDENCIA = 3;  // Dígitos adicionales
    private const LONG_BIEN = 5;         // Dígitos adicionales

    // Longitudes totales acumuladas
    private const TOTAL_ORGANISMO = 8;
    private const TOTAL_UNIDAD = 11;     // 8 + 3
    private const TOTAL_DEPENDENCIA = 14; // 11 + 3
    private const TOTAL_BIEN = 19;        // 14 + 5

    /**
     * Genera el siguiente código para un organismo
     */
    public static function generarCodigoOrganismo(): string
    {
        $ultimo = Organismo::max('codigo');

        if (!$ultimo) {
            return str_pad('1', self::TOTAL_ORGANISMO, '0', STR_PAD_LEFT);
        }

        $siguiente = (int)$ultimo + 1;

        if ($siguiente > pow(10, self::TOTAL_ORGANISMO) - 1) {
            throw new RuntimeException("Límite de organismos alcanzado");
        }

        return str_pad($siguiente, self::TOTAL_ORGANISMO, '0', STR_PAD_LEFT);
    }

    /**
     * Genera el siguiente código para una unidad (dentro de un organismo)
     * El código de la unidad = código_organismo + código_propio (3 dígitos)
     */
    public static function generarCodigoUnidad(int $organismoId): string
    {
        return DB::transaction(function () use ($organismoId) {
            $organismo = Organismo::lockForUpdate()->findOrFail($organismoId);

            // Obtener el prefijo del organismo (8 dígitos)
            $prefijo = $organismo->codigo;

            // Buscar el último código de unidad que tenga este prefijo
            $ultimaUnidad = UnidadAdministradora::where('codigo', 'LIKE', $prefijo . '%')
                ->orderBy('codigo', 'desc')
                ->first();

            if (!$ultimaUnidad) {
                // Primera unidad: prefijo + 001
                $siguiente = 1;
            } else {
                // Extraer los últimos 3 dígitos
                $ultimoNumero = (int)substr($ultimaUnidad->codigo, -self::LONG_UNIDAD);
                $siguiente = $ultimoNumero + 1;
            }

            $maximo = pow(10, self::LONG_UNIDAD) - 1; // 999
            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de unidades para el organismo {$organismo->codigo} alcanzado (máximo {$maximo})");
            }

            $codigoUnidad = $prefijo . str_pad($siguiente, self::LONG_UNIDAD, '0', STR_PAD_LEFT);

            // Validar que no exista
            if (self::codigoExiste($codigoUnidad)) {
                throw new RuntimeException("Conflicto de código: {$codigoUnidad} ya existe");
            }

            return $codigoUnidad;
        });
    }

    /**
     * Genera el siguiente código para una dependencia (dentro de una unidad)
     * Código = código_unidad + código_propio (3 dígitos)
     */
    public static function generarCodigoDependencia(int $unidadId): string
    {
        return DB::transaction(function () use ($unidadId) {
            $unidad = UnidadAdministradora::lockForUpdate()->findOrFail($unidadId);

            // El prefijo es el código completo de la unidad (11 dígitos)
            $prefijo = $unidad->codigo;

            // Buscar última dependencia
            $ultimaDependencia = Dependencia::where('codigo', 'LIKE', $prefijo . '%')
                ->orderBy('codigo', 'desc')
                ->first();

            if (!$ultimaDependencia) {
                $siguiente = 1;
            } else {
                // Extraer los últimos 3 dígitos
                $ultimoNumero = (int)substr($ultimaDependencia->codigo, -self::LONG_DEPENDENCIA);
                $siguiente = $ultimoNumero + 1;
            }

            $maximo = pow(10, self::LONG_DEPENDENCIA) - 1; // 999
            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de dependencias para la unidad {$unidad->codigo} alcanzado");
            }

            $codigoDependencia = $prefijo . str_pad($siguiente, self::LONG_DEPENDENCIA, '0', STR_PAD_LEFT);

            if (self::codigoExiste($codigoDependencia)) {
                throw new RuntimeException("Conflicto de código: {$codigoDependencia} ya existe");
            }

            return $codigoDependencia;
        });
    }

    /**
     * Genera el siguiente código para un bien (dentro de una dependencia)
     * Código = código_dependencia + secuencial (5 dígitos)
     */
    public static function generarCodigoBien(int $dependenciaId): string
    {
        return DB::transaction(function () use ($dependenciaId) {
            $dependencia = Dependencia::lockForUpdate()->findOrFail($dependenciaId);

            // El prefijo es el código completo de la dependencia (14 dígitos)
            $prefijo = $dependencia->codigo;

            // Buscar el último bien
            $ultimoBien = Bien::where('codigo', 'LIKE', $prefijo . '%')
                ->orderBy('codigo', 'desc')
                ->first();

            if (!$ultimoBien) {
                $siguiente = 1;
            } else {
                // Extraer los últimos 5 dígitos
                $ultimoNumero = (int)substr($ultimoBien->codigo, -self::LONG_BIEN);
                $siguiente = $ultimoNumero + 1;
            }

            $maximo = pow(10, self::LONG_BIEN) - 1; // 99999
            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de bienes para la dependencia {$dependencia->codigo} alcanzado (máximo {$maximo})");
            }

            $codigoBien = $prefijo . str_pad($siguiente, self::LONG_BIEN, '0', STR_PAD_LEFT);

            if (self::codigoExiste($codigoBien)) {
                throw new RuntimeException("Conflicto de código: {$codigoBien} ya existe");
            }

            return $codigoBien;
        });
    }

    /**
     * Decodifica un código jerárquico para entender su estructura
     */
    public static function decodificarCodigo(string $codigo): array
    {
        $longitud = strlen($codigo);

        if ($longitud === self::TOTAL_ORGANISMO) {
            // Es un organismo
            return [
                'tipo' => 'organismo',
                'codigo' => $codigo,
                'organismo' => $codigo,
            ];
        }

        if ($longitud === self::TOTAL_UNIDAD) {
            // Es una unidad: extraer organismo (8) + código unidad (3)
            return [
                'tipo' => 'unidad',
                'codigo' => $codigo,
                'organismo' => substr($codigo, 0, self::TOTAL_ORGANISMO),
                'unidad' => substr($codigo, -self::LONG_UNIDAD),
            ];
        }

        if ($longitud === self::TOTAL_DEPENDENCIA) {
            // Es una dependencia
            return [
                'tipo' => 'dependencia',
                'codigo' => $codigo,
                'organismo' => substr($codigo, 0, self::TOTAL_ORGANISMO),
                'unidad' => substr($codigo, self::TOTAL_ORGANISMO, self::LONG_UNIDAD),
                'dependencia' => substr($codigo, -self::LONG_DEPENDENCIA),
            ];
        }

        if ($longitud === self::TOTAL_BIEN) {
            // Es un bien
            return [
                'tipo' => 'bien',
                'codigo' => $codigo,
                'organismo' => substr($codigo, 0, self::TOTAL_ORGANISMO),
                'unidad' => substr($codigo, self::TOTAL_ORGANISMO, self::LONG_UNIDAD),
                'dependencia' => substr($codigo, self::TOTAL_ORGANISMO + self::LONG_UNIDAD, self::LONG_DEPENDENCIA),
                'secuencial' => substr($codigo, -self::LONG_BIEN),
            ];
        }

        throw new InvalidArgumentException("Longitud de código inválida: {$longitud}");
    }

    /**
     * Valida que un código sea consistente con su jerarquía padre
     */
    public static function validarJerarquia(string $codigoHijo, string $codigoPadreEsperado): bool
    {
        return str_starts_with($codigoHijo, $codigoPadreEsperado);
    }

    /**
     * Obtiene el padre inmediato de un código
     */
    public static function obtenerCodigoPadre(string $codigo): ?string
    {
        $longitud = strlen($codigo);

        if ($longitud === self::TOTAL_UNIDAD) {
            // El padre es el organismo (primeros 8 dígitos)
            return substr($codigo, 0, self::TOTAL_ORGANISMO);
        }

        if ($longitud === self::TOTAL_DEPENDENCIA) {
            // El padre es la unidad (primeros 11 dígitos)
            return substr($codigo, 0, self::TOTAL_UNIDAD);
        }

        if ($longitud === self::TOTAL_BIEN) {
            // El padre es la dependencia (primeros 14 dígitos)
            return substr($codigo, 0, self::TOTAL_DEPENDENCIA);
        }

        return null; // Los organismos no tienen padre
    }

    /**
     * Verifica si un código existe en cualquier tabla
     */
    public static function codigoExiste(string $codigo): bool
    {
        $longitud = strlen($codigo);

        if ($longitud === self::TOTAL_ORGANISMO) {
            return Organismo::where('codigo', $codigo)->exists();
        }

        if ($longitud === self::TOTAL_UNIDAD) {
            return UnidadAdministradora::where('codigo', $codigo)->exists();
        }

        if ($longitud === self::TOTAL_DEPENDENCIA) {
            return Dependencia::where('codigo', $codigo)->exists();
        }

        if ($longitud === self::TOTAL_BIEN) {
            return Bien::where('codigo', $codigo)->exists();
        }

        return false;
    }

    /**
     * Obtiene estadísticas de uso de códigos para un nivel específico
     */
    public static function obtenerEstadisticas(string $codigoPadre, string $tipo): array
    {
        switch ($tipo) {
            case 'unidades':
                $total = UnidadAdministradora::where('codigo', 'LIKE', $codigoPadre . '%')->count();
                $maximo = pow(10, self::LONG_UNIDAD);
                break;
            case 'dependencias':
                $total = Dependencia::where('codigo', 'LIKE', $codigoPadre . '%')->count();
                $maximo = pow(10, self::LONG_DEPENDENCIA);
                break;
            case 'bienes':
                $total = Bien::where('codigo', 'LIKE', $codigoPadre . '%')->count();
                $maximo = pow(10, self::LONG_BIEN);
                break;
            default:
                throw new InvalidArgumentException("Tipo inválido: {$tipo}");
        }

        return [
            'usados' => $total,
            'disponibles' => $maximo - $total,
            'porcentaje_uso' => round(($total / $maximo) * 100, 2),
            'siguiente' => $total + 1,
        ];
    }

    /**
     * Recomienda el siguiente código para una dependencia (útil para el frontend)
     */
    public static function recomendarSiguienteBien(int $dependenciaId): array
    {
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $codigoSugerido = self::generarCodigoBien($dependenciaId);
        $decodificado = self::decodificarCodigo($codigoSugerido);
        $stats = self::obtenerEstadisticas($dependencia->codigo, 'bienes');

        // Formatear para mostrar con guiones (más legible)
        $codigoLegible = self::formatearCodigoLegible($codigoSugerido);

        return [
            'codigo' => $codigoSugerido,
            'codigo_legible' => $codigoLegible,
            'secuencial' => $decodificado['secuencial'],
            'dependencia_id' => $dependenciaId,
            'dependencia_nombre' => $dependencia->nombre,
            'estadisticas' => $stats,
        ];
    }

    /**
     * Formatea un código para mostrar con guiones (más legible)
     * Ej: 0000000100100100001 → 00000001-001-001-00001
     */
    public static function formatearCodigoLegible(string $codigo): string
    {
        $longitud = strlen($codigo);

        if ($longitud === self::TOTAL_BIEN) {
            return implode('-', [
                substr($codigo, 0, self::TOTAL_ORGANISMO),
                substr($codigo, self::TOTAL_ORGANISMO, self::LONG_UNIDAD),
                substr($codigo, self::TOTAL_ORGANISMO + self::LONG_UNIDAD, self::LONG_DEPENDENCIA),
                substr($codigo, -self::LONG_BIEN),
            ]);
        }

        if ($longitud === self::TOTAL_DEPENDENCIA) {
            return implode('-', [
                substr($codigo, 0, self::TOTAL_ORGANISMO),
                substr($codigo, self::TOTAL_ORGANISMO, self::LONG_UNIDAD),
                substr($codigo, -self::LONG_DEPENDENCIA),
            ]);
        }

        if ($longitud === self::TOTAL_UNIDAD) {
            return implode('-', [
                substr($codigo, 0, self::TOTAL_ORGANISMO),
                substr($codigo, -self::LONG_UNIDAD),
            ]);
        }

        return $codigo;
    }
}
