<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\BienElectronico;
use App\Models\BienVehiculo;
use App\Models\BienMobiliario;
use App\Models\BienOtro;

class BienTypeService
{
    /**
     * Sync type-specific records for a `Bien`.
     * Converts empty strings to null for optional fields.
     */
    public function sync(Bien $bien, string $tipo, array $data): void
    {
        $tipo = strtoupper($tipo);

        $normalize = function ($arr) {
            $out = [];
            foreach ($arr as $k => $v) {
                if (is_string($v) && trim($v) === '') {
                    $out[$k] = null;
                } else {
                    $out[$k] = $v;
                }
            }
            return $out;
        };

        if ($tipo === 'ELECTRONICO') {
            $fields = ['subtipo','procesador','memoria','almacenamiento','pantalla','serial','garantia'];
            BienElectronico::updateOrCreate(
                ['bien_id' => $bien->id],
                $normalize(array_intersect_key($data, array_flip($fields)))
            );
            return;
        }

        if ($tipo === 'VEHICULO') {
            $fields = ['marca','modelo','anio','placa','motor','chasis','combustible','kilometraje'];
            BienVehiculo::updateOrCreate(
                ['bien_id' => $bien->id],
                $normalize(array_intersect_key($data, array_flip($fields)))
            );
            return;
        }

        if ($tipo === 'MOBILIARIO') {
            $fields = ['material','dimensiones','color','capacidad','cantidad_piezas','acabado'];
            BienMobiliario::updateOrCreate(
                ['bien_id' => $bien->id],
                $normalize(array_intersect_key($data, array_flip($fields)))
            );
            return;
        }

        if ($tipo === 'OTROS') {
            $fields = ['especificaciones','cantidad','presentacion'];
            BienOtro::updateOrCreate(
                ['bien_id' => $bien->id],
                $normalize(array_intersect_key($data, array_flip($fields)))
            );
            return;
        }
    }
}
