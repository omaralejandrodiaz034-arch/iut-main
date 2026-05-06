<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use App\Observers\ModelObserver;
use App\Services\FpdfReportService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FpdfReportService::class, function ($app) {
            return new FpdfReportService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usar el observer genérico para todos los modelos
        Bien::observe(ModelObserver::class);
        Organismo::observe(ModelObserver::class);
        UnidadAdministradora::observe(ModelObserver::class);
        Dependencia::observe(ModelObserver::class);
        Usuario::observe(ModelObserver::class);
    }
}
