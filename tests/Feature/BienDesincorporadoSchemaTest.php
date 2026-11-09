<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BienDesincorporadoSchemaTest extends TestCase
{
    public function test_la_columna_codigo_de_bienes_desincorporados_acepta_codigos_del_sistema(): void
    {
        Schema::dropIfExists('bienes_desincorporados');

        Schema::create('bienes_desincorporados', function ($table) {
            $table->id();
            $table->string('codigo', 8);
        });

        $migration = require database_path('migrations/2026_07_08_000001_alter_codigo_length_in_bienes_desincorporados_table.php');
        $migration->up();

        $columns = collect(DB::select('PRAGMA table_info(bienes_desincorporados)'));
        $codigo = $columns->firstWhere('name', 'codigo');

        $this->assertNotNull($codigo);
        $this->assertStringContainsString('50', $codigo->type);
    }
}
