<?php

// database/seeders/TipoResponsableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoResponsableSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Responsable Patrimonial Primario',
            'Responsable Patrimonial por Uso',
            'Obrero',
            'Docente',
            'Administrativo',
        ];

        foreach ($tipos as $nombre) {
            DB::table('tipos_responsables')->updateOrInsert(['nombre' => $nombre]);
        }
    }
}
