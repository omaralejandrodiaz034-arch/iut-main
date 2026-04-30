<?php

// database/migrations/2025_09_04_000002_create_unidades_administradoras_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_administradoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organismo_id')
                ->constrained('organismos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('codigo', 50);
            $table->string('nombre', 150);
            $table->timestamps();

            $table->unique(['organismo_id', 'codigo'], 'uq_ua_org_codigo');
            $table->index('organismo_id', 'idx_ua_organismo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_administradoras');
    }
};
