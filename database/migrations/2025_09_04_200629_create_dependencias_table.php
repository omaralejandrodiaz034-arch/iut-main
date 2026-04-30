<?php

// database/migrations/2025_09_04_000003_create_dependencias_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id();

            // Relación con Unidad Administradora
            $table->foreignId('unidad_administradora_id')
                ->constrained('unidades_administradoras')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Relación con Responsable (opcional)
            $table->foreignId('responsable_id')
                ->nullable()
                ->constrained('responsables')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('codigo', 50);
            $table->string('nombre', 150);
            $table->timestamps();

            // Índices y restricciones
            $table->unique(['unidad_administradora_id', 'codigo'], 'uq_dep_ua_codigo');
            $table->index('unidad_administradora_id', 'idx_dep_ua');
            $table->index('responsable_id', 'idx_dep_responsable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependencias');
    }
};
