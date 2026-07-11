<?php

use App\Console\Commands\CancelarActasVencidas;
use App\Console\Commands\ReporteMensualVerificacion;
use App\Console\Commands\VerificarDependenciasSinResponsable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Regresión de actas: cancela automáticamente las operaciones (traslado,
// donación, desincorporación) cuyas actas no fueron firmadas dentro del plazo
// de 2 días, revirtiendo la operación que las emitió.
Schedule::command(CancelarActasVencidas::class)->hourly();

// Verifica dependencias sin responsable y notifica a los administradores.
Schedule::command(VerificarDependenciasSinResponsable::class)->daily();

// Genera un reporte mensual de verificación y notifica a los administradores.
Schedule::command(ReporteMensualVerificacion::class)->monthly();
