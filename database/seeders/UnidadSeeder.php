<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadSeeder extends Seeder
{
    public function run(): void
    {
        $organismos = DB::table('organismos')->get();

        foreach ($organismos as $organismo) {
            // Unidad principal con código específico para el primero
            if ($organismo->codigo == 'MPPEU-001') {
                DB::table('unidades_administradoras')->updateOrInsert(
                    ['organismo_id' => $organismo->id, 'codigo' => '1430'],
                    ['nombre' => 'UPTOS "CLODOSBALDO RUSSIAN"']
                );
            }

            // Otras unidades
            for ($i = 1; $i <= 3; $i++) {
                $codigo = $organismo->codigo.'-U'.str_pad($i, 3, '0', STR_PAD_LEFT);
                DB::table('unidades_administradoras')->updateOrInsert(
                    ['organismo_id' => $organismo->id, 'codigo' => $codigo],
                    ['nombre' => "Unidad Administradora {$i} de {$organismo->nombre}"]
                );
            }
        }
    }
}
