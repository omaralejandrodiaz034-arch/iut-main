<?php

namespace App\Services;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Dependencia;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;

class CodigoUnicoService
{
    private static function getModelMapping(): array
    {
        return [
            'organismos'   => [Organismo::class, 'nombre', 'Organismo'],
            'unidades'     => [UnidadAdministradora::class, 'nombre', 'Unidad Administradora'],
            'dependencias' => [Dependencia::class, 'nombre', 'Dependencia'],
            'bienes'       => [Bien::class, 'descripcion', 'Bien'],
        ];
    }

    /**
     * Obtiene el primer código disponible en la secuencia (rellena huecos).
     * Si existen el 1, 2, 3 y el 100, devolverá el 4.
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

        return str_pad((string)$siguiente, 8, '0', STR_PAD_LEFT);
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
                    'tabla'  => $config[2],
                    'nombre' => $registro->$campoNombre,
                    'id'     => $registro->id
                ];
            }
        }
        return null;
    }
}
