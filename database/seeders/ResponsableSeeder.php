<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponsableSeeder extends Seeder
{
    public function run(): void
    {
        $primario = DB::table('tipos_responsables')->where('nombre', 'Responsable Patrimonial Primario')->first();
        $uso = DB::table('tipos_responsables')->where('nombre', 'Responsable Patrimonial por Uso')->first();

        if (! $primario || ! $uso) {
            return; // Evitar errores si no existen
        }

        DB::table('responsables')->updateOrInsert(
            ['cedula' => '3873777'],
            [
                'tipo_id' => $primario->id,
                'nombre' => 'ENRY GÓMEZ MAIZ',
                'correo' => 'enry.gomez@pai.gob.ve',
                'telefono' => '0412-1234567',
            ]
        );

        DB::table('responsables')->updateOrInsert(
            ['cedula' => '20000001'],
            [
                'tipo_id' => $uso->id,
                'nombre' => 'MARÍA PÉREZ',
                'correo' => 'maria.perez@pai.gob.ve',
                'telefono' => '0412-7654321',
            ]
        );

        // Agregar más responsables de ejemplo
        $tipos = DB::table('tipos_responsables')->get();
        $ejemplos = [
            ['cedula' => '30000001', 'nombre' => 'JUAN LÓPEZ', 'correo' => 'juan.lopez@example.com', 'telefono' => '0412-1111111'],
            ['cedula' => '30000002', 'nombre' => 'ANA MARTÍNEZ', 'correo' => 'ana.martinez@example.com', 'telefono' => '0412-2222222'],
            ['cedula' => '30000003', 'nombre' => 'CARLOS GONZÁLEZ', 'correo' => 'carlos.gonzalez@example.com', 'telefono' => '0412-3333333'],
        ];

        foreach ($ejemplos as $resp) {
            DB::table('responsables')->updateOrInsert(
                ['cedula' => $resp['cedula']],
                array_merge($resp, [
                    'tipo_id' => $tipos->random()->id,
                ])
            );
        }
    }
}
