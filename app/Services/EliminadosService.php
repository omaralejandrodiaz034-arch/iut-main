<?php

namespace App\Services;

use App\Models\Eliminado;
use Illuminate\Support\Facades\DB;

class EliminadosService
{
    /**
     * Archiva un modelo dado en la tabla eliminados.
     * Guarda una copia JSON del modelo y el usuario que borró.
     */
    /**
     * @param mixed $deletedBy id del usuario que elimina (int|string|null)
     */
    public static function archiveModel($model, $deletedBy = null): Eliminado
    {
        $data = $model->toArray();

        // If we can resolve the user who performed the deletion, store a human-readable name inside the snapshot

        $archivedByName = null;
        $deletedById = null;

        try {
            // If a Usuario instance is passed
            if ($deletedBy instanceof \App\Models\Usuario) {
                $deletedById = (int) $deletedBy->id;
                $archivedByName = $deletedBy->nombre_completo ?? $deletedBy->correo ?? null;
            } else {
                // If numeric, treat as id
                if (is_numeric($deletedBy)) {
                    $u = \App\Models\Usuario::find((int) $deletedBy);
                    if ($u) {
                        $deletedById = (int) $u->id;
                        $archivedByName = $u->nombre_completo ?? $u->correo ?? null;
                    }
                } elseif (is_string($deletedBy) && filter_var($deletedBy, FILTER_VALIDATE_EMAIL)) {
                    // If it's an email, try to find the user by correo
                    $u = \App\Models\Usuario::where('correo', $deletedBy)->first();
                    if ($u) {
                        $deletedById = (int) $u->id;
                        $archivedByName = $u->nombre_completo ?? $u->correo ?? null;
                    } else {
                        // store the email as a human-readable archived name even if we can't resolve id
                        $archivedByName = $deletedBy;
                    }
                } elseif (is_string($deletedBy) && ! empty($deletedBy)) {
                    // If some other string was provided, keep it as a fallback name
                    $archivedByName = $deletedBy;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if ($archivedByName) {
            $data['_archived_by'] = $archivedByName;
        }

        $record = Eliminado::create([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'data' => $data,
            'deleted_by' => $deletedById,
            'deleted_at' => now(),
        ]);

        return $record;
    }

    /**
     * Restaura un registro archivado. Devuelve true/false dependiendo del éxito.
     */
    public static function restoreEliminado(Eliminado $eliminado): bool
    {
        $data = $eliminado->data ?? [];
        if (empty($data)) {
            return false;
        }

        // Obtener el nombre de la tabla del modelo
        $modelClass = $eliminado->model_type;

        if (! class_exists($modelClass)) {
            return false;
        }

        $instance = new $modelClass;
        $table = $instance->getTable();

        // Intentar reinsertar sin la clave primaria (si existe)
        $primary = $instance->getKeyName();
        if (isset($data[$primary])) {
            unset($data[$primary]);
        }

        // Filtrar sólo columnas existentes en la tabla para evitar errores de constraint
        try {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);
        } catch (\Throwable $e) {
            report($e);
            $columns = array_keys($data);
        }

        $filtered = array_intersect_key($data, array_flip($columns));

        try {
            DB::transaction(function () use ($table, $filtered, $eliminado) {
                DB::table($table)->insert($filtered);
                // Si insert fue ok, eliminar registro de eliminados
                $eliminado->delete();
            });

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
