<?php

namespace App\Services;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Dependencia;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;

class CodigoUnicoService
{
    /**
     * Obtiene el siguiente código único disponible en todo el sistema.
     * Verifica en todas las tablas: organismos, unidades_administradoras, dependencias y bienes.
     * 
     * @return string Código de 8 dígitos con ceros a la izquierda
     */
    public static function obtenerSiguienteCodigo(): string
    {
        // Obtener el código máximo de cada tabla
        $maxOrganismos = (int) Organismo::max('codigo');
        $maxUnidades = (int) UnidadAdministradora::max('codigo');
        $maxDependencias = (int) Dependencia::max('codigo');
        
        // Para bienes, solo consideramos códigos numéricos puros
        $maxBienes = (int) Bien::whereRaw('codigo REGEXP "^[0-9]+$"')
            ->orderByRaw('CAST(codigo AS UNSIGNED) DESC')
            ->value('codigo');

        // Determinar el máximo global
        $maximoGlobal = max($maxOrganismos, $maxUnidades, $maxDependencias, $maxBienes);

        // Siguiente número secuencial
        $siguienteNumero = $maximoGlobal + 1;

        // Formatear a 8 dígitos con ceros a la izquierda
        return str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica si un código ya existe en cualquiera de las tablas del sistema.
     * 
     * @param string $codigo El código a verificar
     * @param string|null $tabla Tabla a excluir de la verificación (para actualización)
     * @param int|null $excluirId ID del registro a excluir (para actualización)
     * @return bool true si el código existe, false si está disponible
     */
    public static function codigoExiste(string $codigo, ?string $tabla = null, ?int $excluirId = null): bool
    {
        $existeEnOrganismos = ($tabla !== 'organismos') 
            ? Organismo::where('codigo', $codigo)->exists() 
            : Organismo::where('codigo', $codigo)->where('id', '!=', $excluirId)->exists();

        $existeEnUnidades = ($tabla !== 'unidades') 
            ? UnidadAdministradora::where('codigo', $codigo)->exists() 
            : UnidadAdministradora::where('codigo', $codigo)->where('id', '!=', $excluirId)->exists();

        $existeEnDependencias = ($tabla !== 'dependencias') 
            ? Dependencia::where('codigo', $codigo)->exists() 
            : Dependencia::where('codigo', $codigo)->where('id', '!=', $excluirId)->exists();

        $existeEnBienes = ($tabla !== 'bienes') 
            ? Bien::where('codigo', $codigo)->exists() 
            : Bien::where('codigo', $codigo)->where('id', '!=', $excluirId)->exists();

        return $existeEnOrganismos || $existeEnUnidades || $existeEnDependencias || $existeEnBienes;
    }

    /**
     * Obtiene información de dónde ya existe un código.
     * 
     * @param string $codigo
     * @return array|null Array con 'tabla' y 'id' si existe, null si no existe
     */
    public static function obtenerUbicacionCodigo(string $codigo): ?array
    {
        $organismo = Organismo::where('codigo', $codigo)->first();
        if ($organismo) {
            return [
                'tabla' => 'Organismo',
                'nombre' => $organismo->nombre,
                'id' => $organismo->id
            ];
        }

        $unidad = UnidadAdministradora::where('codigo', $codigo)->first();
        if ($unidad) {
            return [
                'tabla' => 'Unidad Administradora',
                'nombre' => $unidad->nombre,
                'id' => $unidad->id
            ];
        }

        $dependencia = Dependencia::where('codigo', $codigo)->first();
        if ($dependencia) {
            return [
                'tabla' => 'Dependencia',
                'nombre' => $dependencia->nombre,
                'id' => $dependencia->id
            ];
        }

        $bien = Bien::where('codigo', $codigo)->first();
        if ($bien) {
            return [
                'tabla' => 'Bien',
                'nombre' => $bien->descripcion,
                'id' => $bien->id
            ];
        }

        return null;
    }
}
