<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movimiento;
use App\Models\HistorialMovimiento;
use Illuminate\Support\Facades\Auth;

class ModelObserver
{
    /**
     * Resuelve el ID del usuario autenticado de forma segura.
     */
    private function resolveAuthenticatedUserId(): ?int
    {
        try {
            $user = Auth::user();
            if ($user && isset($user->id)) {
                return (int) $user->id;
            }

            $identifier = Auth::id();
            if (is_numeric($identifier)) {
                return (int) $identifier;
            }

            if (is_string($identifier) && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $u = \App\Models\Usuario::where('correo', $identifier)->first();
                return $u?->id;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    /**
     * Registra movimiento de creación.
     */
    public function created(Model $model): void
    {
        $this->registrarMovimiento($model, 'Registro', sprintf(
            'Registro de %s (id=%s)', class_basename($model), $model->id
        ));
    }

    /**
     * Registra movimiento de actualización con detalle de cambios.
     */
    public function updated(Model $model): void
    {
        $original = $model->getOriginal();
        $changes = collect($model->getChanges())->except(['updated_at']);

        $detalle = $changes->map(function ($nuevo, $campo) use ($original) {
            $viejo = $original[$campo] ?? 'N/A';
            return "$campo: $viejo -> $nuevo";
        })->implode('; ');

        $this->registrarMovimiento($model, 'Actualización', $detalle ?: 'Actualización');
    }

    /**
     * Registra movimiento de eliminación.
     */
    public function deleting(Model $model): void
    {
        $this->registrarMovimiento($model, 'Eliminación', sprintf(
            'Eliminación de %s (id=%s)', class_basename($model), $model->id
        ));
    }

    /**
     * Método centralizado para registrar movimientos y su historial.
     */
    private function registrarMovimiento(Model $model, string $tipo, string $observaciones): void
    {
        try {
            $mov = Movimiento::create([
                'subject_type' => get_class($model),
                'subject_id' => $model->id,
                'tipo' => $tipo,
                'fecha' => now(),
                'observaciones' => $observaciones,
                'usuario_id' => $this->resolveAuthenticatedUserId(),
            ]);

            if ($mov && $tipo === 'Actualización') {
                HistorialMovimiento::create([
                    'movimiento_id' => $mov->id,
                    'fecha' => now(),
                    'detalle' => $observaciones,
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

