<?php

namespace App\Services;

use App\Models\Usuario;
use App\Notifications\SistemaNotificacion;
use Illuminate\Support\Facades\Notification;

class NotificacionService
{
    public function notificarAdmins(string $titulo, string $mensaje, ?string $action_url = null, ?string $icono = null, array $meta = []): void
    {
        $admins = Usuario::where('is_admin', true)
            ->where('activo', true)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new SistemaNotificacion($titulo, $mensaje, $action_url, $icono, $meta));
        }
    }

    public function notificarCreacionBien(string $codigo, string $descripcion, ?int $usuarioId = null): void
    {
        $this->notificarAdmins(
            titulo: 'Nuevo bien registrado',
            mensaje: "Se registró el bien {$codigo}: {$descripcion}.",
            action_url: route('bienes.show', ['bien' => $codigo]),
            icono: '📦',
            meta: ['tipo' => 'bien_creado', 'codigo' => $codigo, 'usuario_id' => $usuarioId],
        );
    }

    public function notificarTraslado(string $codigo, string $dependenciaNombre, ?int $usuarioId = null): void
    {
        $this->notificarAdmins(
            titulo: 'Traslado de bien',
            mensaje: "El bien {$codigo} fue trasladado a {$dependenciaNombre}.",
            action_url: route('bienes.index'),
            icono: '🚚',
            meta: ['tipo' => 'traslado', 'codigo' => $codigo, 'usuario_id' => $usuarioId],
        );
    }

    public function notificarSubidaActaFirmada(string $codigo, string $tipo, ?int $usuarioId = null): void
    {
        $this->notificarAdmins(
            titulo: 'Acta firmada cargada',
            mensaje: "Se adjuntó el acta firmada de {$tipo} para el bien {$codigo}.",
            action_url: route('bienes.show', ['bien' => $codigo]),
            icono: '📄',
            meta: ['tipo' => 'acta_firmada', 'codigo' => $codigo, 'usuario_id' => $usuarioId],
        );
    }

    public function notificarDependenciaSinResponsable(string $dependenciaNombre, string $codigo): void
    {
        $this->notificarAdmins(
            titulo: 'Dependencia sin responsable',
            mensaje: "La dependencia {$dependenciaNombre} ({$codigo}) no tiene responsable asignado.",
            action_url: route('dependencias.index'),
            icono: '⚠️',
            meta: ['tipo' => 'dependencia_sin_responsable', 'codigo' => $codigo],
        );
    }

    public function notificarReporteGenerado(string $tipoReporte, ?int $usuarioId = null): void
    {
        $this->notificarAdmins(
            titulo: 'Reporte generado',
            mensaje: "Se generó un reporte de {$tipoReporte}.",
            action_url: route('reportes.index'),
            icono: '📊',
            meta: ['tipo' => 'reporte_generado', 'usuario_id' => $usuarioId],
        );
    }

    public function notificarAccionUsuario(string $accion, string $detalle, ?int $usuarioId = null): void
    {
        $this->notificarAdmins(
            titulo: 'Acción de usuario',
            mensaje: "{$accion}: {$detalle}",
            action_url: route('dashboard'),
            icono: '🔔',
            meta: ['tipo' => 'accion_usuario', 'usuario_id' => $usuarioId],
        );
    }
}
