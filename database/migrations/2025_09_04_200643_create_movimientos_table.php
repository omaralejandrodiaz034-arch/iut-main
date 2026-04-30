<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            // Relación opcional con bienes
            $table->foreignId('bien_id')->nullable()
                ->constrained('bienes')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Polimorfismo sujeto
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('tipo', 80);
            $table->timestamp('fecha')->useCurrent();
            $table->text('observaciones')->nullable();

            // Relación con usuarios
            $table->foreignId('usuario_id')->nullable()
                ->constrained('usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Índices
            $table->index('bien_id', 'idx_mov_bien');
            $table->index('fecha', 'idx_mov_fecha');
            $table->index(['subject_type', 'subject_id'], 'idx_mov_subject');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
