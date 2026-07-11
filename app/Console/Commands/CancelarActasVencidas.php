<?php

namespace App\Console\Commands;

use App\Services\ActaRegressionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelarActasVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actas:cancelar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela automáticamente las operaciones (traslado, donación, desincorporación) cuyas actas no fueron firmadas y selladas dentro del plazo de 2 días.';

    /**
     * Execute the console command.
     */
    public function handle(ActaRegressionService $service): int
    {
        $cancelados = $service->procesarActasVencidas();

        if ($cancelados > 0) {
            Log::info('Regresión de actas: se cancelaron actas vencidas.', ['cantidad' => $cancelados]);
            $this->info("Se cancelaron {$cancelados} acta(s) vencida(s) y se revirtieron sus operaciones.");
        } else {
            $this->info('No hay actas vencidas pendientes de cancelar.');
        }

        return self::SUCCESS;
    }
}
