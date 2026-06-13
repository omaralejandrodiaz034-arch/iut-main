<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class CodigoJerarquicoService
{
    /**
     * Longitudes de cada segmento
     */
    public const LONG_ORGANISMO = 1;

    public const LONG_UNIDAD = 2;

    public const LONG_DEPENDENCIA = 3;

    public const LONG_BIEN = 4;

    public const LONG_PREFIJO_BIEN = 6;

    /**
     * Longitud fija de todos los códigos.
     */
    public const TOTAL_ORGANISMO = 10;

    public const TOTAL_UNIDAD = 10;

    public const TOTAL_DEPENDENCIA = 10;

    public const TOTAL_BIEN = 10;

    /**
     * Genera el siguiente código para un organismo.
     */
    public static function generarCodigoOrganismo(): string
    {
        $ultimo = Organismo::max('codigo');

        if (! $ultimo) {
            return str_pad('1', self::TOTAL_ORGANISMO, '0', STR_PAD_RIGHT);
        }

        $siguiente = (int) substr($ultimo, 0, self::LONG_ORGANISMO) + 1;

        if ($siguiente > pow(10, self::LONG_ORGANISMO) - 1) {
            throw new RuntimeException('Límite de organismos alcanzado');
        }

        return str_pad((string) $siguiente, self::TOTAL_ORGANISMO, '0', STR_PAD_RIGHT);
    }

    /**
     * Genera el siguiente código de unidad administrativa dentro del organismo.
     */
    public static function generarCodigoUnidad(int $organismoId): string
    {
        return DB::transaction(function () use ($organismoId) {
            $organismo = Organismo::lockForUpdate()->findOrFail($organismoId);
            // Obtener el máximo número de unidad dentro del organismo usando la porción numérica
            $start = self::LONG_ORGANISMO + 1; // SUBSTRING index (1-based)
            $length = self::LONG_UNIDAD;
            $maxNumero = UnidadAdministradora::where('organismo_id', $organismoId)
                ->max(DB::raw("CAST(SUBSTRING(codigo, $start, $length) AS UNSIGNED)"));

            $siguiente = $maxNumero ? ((int) $maxNumero + 1) : 1;
            $maximo = pow(10, self::LONG_UNIDAD) - 1;

            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de unidades para el organismo {$organismo->codigo} alcanzado (máximo {$maximo}).");
            }

            $codigoUnidad = self::buildCodigoUnidad($organismo->codigo, $siguiente);

            if (self::codigoExisteUnidad($codigoUnidad, $organismoId)) {
                throw new RuntimeException("Conflicto de código: {$codigoUnidad} ya existe para el organismo {$organismo->codigo}.");
            }

            return $codigoUnidad;
        });
    }

    /**
     * Genera el siguiente código de dependencia dentro de una unidad.
     */
    public static function generarCodigoDependencia(int $unidadId): string
    {
        return DB::transaction(function () use ($unidadId) {
            $unidad = UnidadAdministradora::lockForUpdate()->findOrFail($unidadId);
            // Obtener máximo número de dependencia dentro de la unidad (porción numérica)
            $start = self::LONG_ORGANISMO + self::LONG_UNIDAD + 1; // SUBSTRING index (1-based)
            $length = self::LONG_DEPENDENCIA;
            $maxNumero = Dependencia::where('unidad_administradora_id', $unidadId)
                ->max(DB::raw("CAST(SUBSTRING(codigo, $start, $length) AS UNSIGNED)"));

            $siguiente = $maxNumero ? ((int) $maxNumero + 1) : 1;
            $maximo = pow(10, self::LONG_DEPENDENCIA) - 1;

            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de dependencias para la unidad {$unidad->codigo} alcanzado (máximo {$maximo}).");
            }

            $codigoDependencia = self::buildCodigoDependencia($unidad->codigo, $siguiente);

            if (self::codigoExisteDependencia($codigoDependencia, $unidadId)) {
                throw new RuntimeException("Conflicto de código: {$codigoDependencia} ya existe para la unidad {$unidad->codigo}.");
            }

            return $codigoDependencia;
        });
    }

    /**
     * Genera el siguiente código completo de bien (10 dígitos) para una dependencia.
     */
    public static function generarCodigoBien(int $dependenciaId): string
    {
        return DB::transaction(function () use ($dependenciaId) {
            $dependencia = Dependencia::lockForUpdate()->with(['unidadAdministradora.organismo'])->findOrFail($dependenciaId);
            $prefijo = self::buildPrefijoBien($dependencia);
            // Obtener el máximo secuencial numérico para bienes dentro de la dependencia
            $maxNumero = Bien::where('codigo', 'LIKE', $prefijo.'%')
                ->max(DB::raw('CAST(SUBSTR(codigo, -'.self::LONG_BIEN.') AS UNSIGNED)'));

            $siguiente = $maxNumero ? ((int) $maxNumero + 1) : 1;
            $maximo = pow(10, self::LONG_BIEN) - 1;

            if ($siguiente > $maximo) {
                throw new RuntimeException("Límite de bienes para la dependencia {$dependencia->codigo} alcanzado (máximo {$maximo}).");
            }

            $codigoBien = $prefijo
                .str_pad((string) $siguiente, self::LONG_BIEN, '0', STR_PAD_LEFT);

            if (self::codigoExiste($codigoBien)) {
                throw new RuntimeException("Conflicto de código: {$codigoBien} ya existe.");
            }

            return $codigoBien;
        });
    }

    /**
     * Decodifica un código jerárquico para entender su estructura.
     */
    public static function decodificarCodigo(string $codigo): array
    {
        if (strlen($codigo) !== self::TOTAL_BIEN) {
            throw new InvalidArgumentException('Longitud de código inválida: '.strlen($codigo));
        }

        $organismo = substr($codigo, 0, self::LONG_ORGANISMO);
        $unidad = substr($codigo, self::LONG_ORGANISMO, self::LONG_UNIDAD);
        $dependencia = substr($codigo, self::LONG_ORGANISMO + self::LONG_UNIDAD, self::LONG_DEPENDENCIA);
        $secuencial = substr($codigo, -self::LONG_BIEN);

        if ($unidad === str_repeat('0', self::LONG_UNIDAD)
            && $dependencia === str_repeat('0', self::LONG_DEPENDENCIA)
            && $secuencial === str_repeat('0', self::LONG_BIEN)) {
            return [
                'tipo' => 'organismo',
                'codigo' => $codigo,
                'organismo' => $organismo,
            ];
        }

        if ($dependencia === str_repeat('0', self::LONG_DEPENDENCIA)
            && $secuencial === str_repeat('0', self::LONG_BIEN)) {
            return [
                'tipo' => 'unidad',
                'codigo' => $codigo,
                'organismo' => $organismo,
                'unidad' => $unidad,
            ];
        }

        if ($secuencial === str_repeat('0', self::LONG_BIEN)) {
            return [
                'tipo' => 'dependencia',
                'codigo' => $codigo,
                'organismo' => $organismo,
                'unidad' => $unidad,
                'dependencia' => $dependencia,
            ];
        }

        return [
            'tipo' => 'bien',
            'codigo' => $codigo,
            'organismo' => $organismo,
            'unidad' => $unidad,
            'dependencia' => $dependencia,
            'secuencial' => $secuencial,
        ];
    }

    /**
     * Valida que un código sea consistente con su jerarquía padre.
     */
    public static function validarJerarquia(string $codigoHijo, string $codigoPadreEsperado): bool
    {
        if (strlen($codigoHijo) !== self::TOTAL_BIEN || strlen($codigoPadreEsperado) !== self::TOTAL_BIEN) {
            return false;
        }

        return self::obtenerCodigoPadre($codigoHijo) === $codigoPadreEsperado;
    }

    /**
     * Obtiene el padre inmediato de un código.
     */
    public static function obtenerCodigoPadre(string $codigo): ?string
    {
        if (strlen($codigo) !== self::TOTAL_BIEN) {
            return null;
        }

        $tipo = self::obtenerTipoPorCodigo($codigo);

        return match ($tipo) {
            'organismo' => null,
            'unidad' => substr($codigo, 0, self::LONG_ORGANISMO).str_repeat('0', self::TOTAL_BIEN - self::LONG_ORGANISMO),
            'dependencia' => substr($codigo, 0, self::LONG_ORGANISMO + self::LONG_UNIDAD).str_repeat('0', self::TOTAL_BIEN - self::LONG_ORGANISMO - self::LONG_UNIDAD),
            'bien' => substr($codigo, 0, self::LONG_ORGANISMO + self::LONG_UNIDAD + self::LONG_DEPENDENCIA).str_repeat('0', self::TOTAL_BIEN - self::LONG_ORGANISMO - self::LONG_UNIDAD - self::LONG_DEPENDENCIA),
            default => null,
        };
    }

    private static function obtenerTipoPorCodigo(string $codigo): string
    {
        if (substr($codigo, -self::LONG_BIEN) !== str_repeat('0', self::LONG_BIEN)) {
            return 'bien';
        }

        if (substr($codigo, self::LONG_ORGANISMO + self::LONG_UNIDAD, self::LONG_DEPENDENCIA) !== str_repeat('0', self::LONG_DEPENDENCIA)) {
            return 'dependencia';
        }

        if (substr($codigo, self::LONG_ORGANISMO, self::LONG_UNIDAD) !== str_repeat('0', self::LONG_UNIDAD)) {
            return 'unidad';
        }

        return 'organismo';
    }

    /**
     * Verifica si un código existe en cualquier tabla.
     */
    public static function codigoExiste(string $codigo): bool
    {
        return Organismo::where('codigo', $codigo)->exists()
            || UnidadAdministradora::where('codigo', $codigo)->exists()
            || Dependencia::where('codigo', $codigo)->exists()
            || Bien::where('codigo', $codigo)->exists();
    }

    public static function codigoExisteOrganismo(string $codigo): bool
    {
        return Organismo::where('codigo', $codigo)->exists();
    }

    public static function codigoExisteUnidad(string $codigo, int $organismoId): bool
    {
        return UnidadAdministradora::where('codigo', $codigo)
            ->where('organismo_id', $organismoId)
            ->exists();
    }

    public static function codigoExisteDependencia(string $codigo, int $unidadAdministradoraId): bool
    {
        return Dependencia::where('codigo', $codigo)
            ->where('unidad_administradora_id', $unidadAdministradoraId)
            ->exists();
    }

    public static function codigoExisteBien(string $codigo): bool
    {
        return Bien::where('codigo', $codigo)->exists();
    }

    /**
     * Obtiene estadísticas de uso de códigos para un nivel específico.
     */
    public static function obtenerEstadisticas(string|int $codigoPadre, string $tipo): array
    {
        switch ($tipo) {
            case 'unidades':
                $total = 0;
                $maximo = pow(10, self::LONG_UNIDAD);
                if (is_string($codigoPadre)) {
                    $organismo = Organismo::where('codigo', $codigoPadre)->first();
                    $total = $organismo ? $organismo->unidadesAdministradoras()->count() : 0;
                }
                break;
            case 'dependencias':
                $total = 0;
                $maximo = pow(10, self::LONG_DEPENDENCIA);
                if (is_int($codigoPadre)) {
                    $total = Dependencia::where('unidad_administradora_id', $codigoPadre)->count();
                } elseif (is_string($codigoPadre)) {
                    $unidad = UnidadAdministradora::where('codigo', $codigoPadre)->first();
                    $total = $unidad ? $unidad->dependencias()->count() : 0;
                }
                break;
            case 'bienes':
                $total = 0;
                $maximo = pow(10, self::LONG_BIEN);
                if (is_int($codigoPadre)) {
                    $dependencia = Dependencia::find($codigoPadre);
                    if ($dependencia) {
                        $prefijo = self::buildPrefijoBien($dependencia);
                        $total = Bien::where('codigo', 'LIKE', $prefijo.'%')->count();
                    }
                } elseif (is_string($codigoPadre)) {
                    $dependencia = Dependencia::where('codigo', $codigoPadre)->first();
                    if ($dependencia) {
                        $prefijo = self::buildPrefijoBien($dependencia);
                        $total = Bien::where('codigo', 'LIKE', $prefijo.'%')->count();
                    }
                }
                break;
            default:
                throw new InvalidArgumentException("Tipo inválido: {$tipo}");
        }

        return [
            'usados' => $total,
            'disponibles' => max(0, $maximo - $total),
            'porcentaje_uso' => $maximo > 0 ? round(($total / $maximo) * 100, 2) : 0,
            'siguiente' => $total + 1,
        ];
    }

    /**
     * Recomienda el siguiente código completo para un bien dentro de una dependencia.
     */
    public static function recomendarSiguienteBien(int $dependenciaId): array
    {
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $codigoSugerido = self::generarCodigoBien($dependenciaId);
        $secuencial = substr($codigoSugerido, -self::LONG_BIEN);
        $stats = self::obtenerEstadisticas($dependenciaId, 'bienes');

        return [
            'codigo' => $codigoSugerido,
            'codigo_legible' => self::formatearCodigoLegible($codigoSugerido),
            'secuencial' => $secuencial,
            'dependencia_id' => $dependenciaId,
            'dependencia_nombre' => $dependencia->nombre,
            'estadisticas' => $stats,
            'siguiente_numero' => (int) $secuencial,
            'rango_min' => 1,
            'rango_max' => pow(10, self::LONG_BIEN) - 1,
            'disponibles_restantes' => max(0, pow(10, self::LONG_BIEN) - 1 - $stats['usados']),
        ];
    }

    public static function recomendarSiguienteCodigoParaDependencia(int $dependenciaId): array
    {
        return self::recomendarSiguienteBien($dependenciaId);
    }

    /**
     * Formatea un código para mostrar con separadores legibles.
     */
    public static function formatearCodigoLegible(string $codigo): string
    {
        $longitud = strlen($codigo);

        if ($longitud === self::TOTAL_BIEN) {
            return implode('.', [
                substr($codigo, 0, self::LONG_ORGANISMO),
                substr($codigo, self::LONG_ORGANISMO, self::LONG_UNIDAD),
                substr($codigo, self::LONG_ORGANISMO + self::LONG_UNIDAD, self::LONG_DEPENDENCIA),
                substr($codigo, -self::LONG_BIEN),
            ]);
        }

        return $codigo;
    }

    private static function buildCodigoUnidad(string $codigoOrganismo, int $unidad): string
    {
        $organismoSegment = substr($codigoOrganismo, 0, self::LONG_ORGANISMO);

        return $organismoSegment
            .str_pad((string) $unidad, self::LONG_UNIDAD, '0', STR_PAD_LEFT)
            .str_repeat('0', self::LONG_DEPENDENCIA + self::LONG_BIEN);
    }

    private static function buildCodigoDependencia(string $codigoUnidad, int $dependencia): string
    {
        $prefijo = substr($codigoUnidad, 0, self::LONG_ORGANISMO + self::LONG_UNIDAD);

        return $prefijo
            .str_pad((string) $dependencia, self::LONG_DEPENDENCIA, '0', STR_PAD_LEFT)
            .str_repeat('0', self::LONG_BIEN);
    }

    private static function buildCodigoBien(string $codigoDependencia, int $secuencial): string
    {
        $prefijo = substr($codigoDependencia, 0, self::LONG_PREFIJO_BIEN);

        return $prefijo
            .str_pad((string) $secuencial, self::LONG_BIEN, '0', STR_PAD_LEFT);
    }

    private static function buildPrefijoDependencia(string $codigoDependencia): string
    {
        return substr($codigoDependencia, 0, self::LONG_ORGANISMO + self::LONG_UNIDAD + self::LONG_DEPENDENCIA);
    }

    public static function buildPrefijoBien(Dependencia $dependencia): string
    {
        return substr($dependencia->codigo, 0, self::LONG_PREFIJO_BIEN);
    }

    /**
     * Verifica si un secuencial específico está disponible en una dependencia.
     */
    public static function isSecuencialDisponibleEnDependencia(int $dependenciaId, string $secuencial): bool
    {
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $prefijo = self::buildPrefijoBien($dependencia);
        $codigo = $prefijo.str_pad($secuencial, self::LONG_BIEN, '0', STR_PAD_LEFT);

        return ! Bien::where('codigo', $codigo)->exists();
    }

    /**
     * Obtiene el siguiente secuencial disponible en una dependencia.
     */
    public static function getSiguienteSecuencialDisponible(int $dependenciaId): int
    {
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $prefijo = self::buildPrefijoBien($dependencia);

        $maxNumero = Bien::where('codigo', 'LIKE', $prefijo.'%')
            ->max(DB::raw('CAST(SUBSTR(codigo, -'.self::LONG_BIEN.') AS UNSIGNED)'));

        return $maxNumero ? ((int) $maxNumero + 1) : 1;
    }
}
