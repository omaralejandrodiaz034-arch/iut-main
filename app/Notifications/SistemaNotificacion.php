<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SistemaNotificacion extends Notification
{
    public function __construct(
        public string $titulo,
        public string $mensaje,
        public ?string $action_url = null,
        public ?string $icono = null,
        public array $meta = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'icono' => $this->icono,
            'action_url' => $this->action_url,
            'meta' => $this->meta,
        ];
    }
}
