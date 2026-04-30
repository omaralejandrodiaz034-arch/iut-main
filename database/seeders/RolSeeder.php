<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['nombre' => 'Administrador'],
            ['permisos' => json_encode(['*' => true])]
        );

        DB::table('roles')->updateOrInsert(
            ['nombre' => 'Usuario Normal'],
            ['permisos' => json_encode([
                'crear_bienes' => true,
                'ver_reportes' => true,
                'crear_movimientos' => true,
            ])]
        );

    }
}
