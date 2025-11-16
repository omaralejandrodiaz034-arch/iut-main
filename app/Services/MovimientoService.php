<?php

namespace App\Services;

use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;

class MovimientoService
{
    private static function resolveAuthenticatedUserId(): ?int
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
                if ($u) {
                    return (int) $u->id;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }

    public static function registrarMovimiento(object $subject, string $tipo = 'Trámite', ?string $observaciones = null, ?int $usuarioId = null)
    {
        try {
            $userId = $usuarioId ?? self::resolveAuthenticatedUserId();

            $subjectType = get_class($subject);
            $subjectId = method_exists($subject, 'getKey') ? $subject->getKey() : null;

            Movimiento::create([
                'bien_id' => $subjectType === \App\Models\Bien::class ? $subjectId : null,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'tipo' => $tipo,
                'fecha' => now(),
                'observaciones' => $observaciones ?? sprintf('%s sobre %s (id=%s)', $tipo, class_basename($subjectType), $subjectId),
                'usuario_id' => $userId,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
