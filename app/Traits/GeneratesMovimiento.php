<?php

namespace App\Traits;

use App\Services\MovimientoService;

trait GeneratesMovimiento
{
    protected static function bootGeneratesMovimiento()
    {
        static::created(function ($model) {
            // Evitar duplicar para Bien (tiene su propio observer)
            if (get_class($model) === \App\Models\Bien::class) {
                return;
            }

            MovimientoService::registrarMovimiento($model, 'Registro');
        });

        static::updated(function ($model) {
            if (get_class($model) === \App\Models\Bien::class) {
                return;
            }

            MovimientoService::registrarMovimiento($model, 'Actualización');
        });

        static::deleted(function ($model) {
            if (get_class($model) === \App\Models\Bien::class) {
                return;
            }

            MovimientoService::registrarMovimiento($model, 'Eliminación');
        });
    }
}
