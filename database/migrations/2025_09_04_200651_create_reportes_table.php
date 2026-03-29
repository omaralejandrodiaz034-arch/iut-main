<?php

// database/migrations/2025_09_04_000011_create_reportes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('tipo', 80);
            $table->timestamp('fecha_generado')->useCurrent();
            $table->string('archivo_pdf_path', 255)->nullable();

            $table->index('usuario_id', 'idx_reporte_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
