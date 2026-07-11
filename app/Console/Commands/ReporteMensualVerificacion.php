<?php

namespace App\Console\Commands;

use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ReporteMensualVerificacion extends Command
{
    protected $signature = 'reportes:mensual-verificacion';
    protected $description = 'Genera un reporte mensual de verificación y notifica a los administradores.';

    public function handle(NotificacionService $notificacionService): int
    {
        $fecha = Carbon::now()->format('Y-m-d');

        $notificacionService->notificarReporteGenerado(
            tipoReporte: "Verificación mensual del sistema ({$fecha})",
            usuarioId: null,
        );

        Log::info('Reporte mensual de verificación generado', [
            'fecha' => $fecha,
        ]);

        $this->info("Reporte mensual de verificación generado: {$fecha}");

        return self::SUCCESS;
    }
}
