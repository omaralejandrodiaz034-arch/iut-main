<?php

namespace App\Console\Commands;

use App\Models\Dependencia;
use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarDependenciasSinResponsable extends Command
{
    protected $signature = 'dependencias:verificar-responsable';
    protected $description = 'Verifica dependencias sin responsable y notifica a los administradores.';

    public function handle(NotificacionService $notificacionService): int
    {
        $dependencias = Dependencia::whereNull('responsable_id')->get();

        if ($dependencias->isEmpty()) {
            $this->info('No hay dependencias sin responsable.');

            return self::SUCCESS;
        }

        foreach ($dependencias as $dependencia) {
            $notificacionService->notificarDependenciaSinResponsable(
                dependenciaNombre: $dependencia->nombre,
                codigo: $dependencia->codigo,
            );
        }

        Log::info('Verificación de dependencias sin responsable', [
            'cantidad' => $dependencias->count(),
        ]);

        $this->info("Se notificaron {$dependencias->count()} dependencia(s) sin responsable.");

        return self::SUCCESS;
    }
}
