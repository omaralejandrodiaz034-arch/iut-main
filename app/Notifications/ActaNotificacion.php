<?php

namespace App\Notifications;

use App\Models\Movimiento;
use Illuminate\Notifications\Notification;

class ActaNotificacion extends Notification
{
    public function __construct(
        public Movimiento $movimiento,
        public string $tipoEvento
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $bien = $this->movimiento->bien;
        $tipo = $this->movimiento->tipo;

        $titulos = [
            'PENDIENTE' => 'Acta pendiente de firma: '.$tipo,
            'ACTA_CANCELADA' => 'Acta cancelada: '.$tipo,
            'ACTA_RECHAZADA' => 'Acta rechazada: '.$tipo,
        ];

        $mensajes = [
            'PENDIENTE' => 'Se generó un acta de '.strtolower($tipo).' para el bien '.($bien?->codigo ?? 'N/A').'. Debe ser firmada y autorizada antes del '.($this->movimiento->fecha_limite_acta?->format('d/m/Y H:i') ?? 'N/A').'.',
            'ACTA_CANCELADA' => 'El acta de '.strtolower($tipo).' del bien '.($bien?->codigo ?? 'N/A').' fue cancelada. '.($this->movimiento->motivo_cancelacion_acta ?? 'Sin motivo especificado.'),
            'ACTA_RECHAZADA' => 'El acta de '.strtolower($tipo).' del bien '.($bien?->codigo ?? 'N/A').' fue rechazada. '.($this->movimiento->motivo_cancelacion_acta ?? 'Sin motivo especificado.'),
        ];

        $actionUrl = match ($this->tipoEvento) {
            'PENDIENTE' => route('actas.pendientes'),
            'ACTA_CANCELADA' => $bien ? route('bienes.show', $bien) : route('actas.pendientes'),
            'ACTA_RECHAZADA' => $bien ? route('bienes.show', $bien) : route('actas.pendientes'),
        };

        return [
            'titulo' => $titulos[$this->tipoEvento] ?? 'Notificación de acta',
            'mensaje' => $mensajes[$this->tipoEvento] ?? 'Notificación relacionada con un acta.',
            'tipo' => $this->tipoEvento,
            'bien_id' => $bien?->id,
            'movimiento_id' => $this->movimiento->id,
            'action_url' => $actionUrl,
            'vencimiento' => $this->movimiento->fecha_limite_acta?->toIso8601String(),
        ];
    }
}
