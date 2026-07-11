<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\Usuario;
use App\Notifications\ActaNotificacion;

class ActaNotificacionService
{
    public function notificarActaPendiente(Movimiento $movimiento): void
    {
        $admins = Usuario::where('is_admin', true)
            ->where('activo', true)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ActaNotificacion($movimiento, 'PENDIENTE'));
        }
    }

    public function notificarActaCancelada(Movimiento $movimiento): void
    {
        $admins = Usuario::where('is_admin', true)
            ->where('activo', true)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ActaNotificacion($movimiento, 'ACTA_CANCELADA'));
        }
    }

    public function notificarActaRechazada(Movimiento $movimiento): void
    {
        $admins = Usuario::where('is_admin', true)
            ->where('activo', true)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ActaNotificacion($movimiento, 'ACTA_RECHAZADA'));
        }
    }

    public function resolverPendientesDe(Movimiento $movimiento): void
    {
        $usuario = auth()->user();

        if (! $usuario) {
            return;
        }

        $usuario->unreadNotifications()
            ->where('type', ActaNotificacion::class)
            ->where('data->movimiento_id', $movimiento->id)
            ->where('data->tipo', 'PENDIENTE')
            ->get()
            ->each(fn ($n) => $n->markAsRead());
    }
}
