<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            // Índices para mejorar rendimiento de filtros comunes en dashboard y reportes
            $table->index('estado');
            $table->index('tipo_bien');
            $table->index('fecha_registro');
            // Índice compuesto para consultas frecuentes de dependencia + estado
            $table->index(['dependencia_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['tipo_bien']);
            $table->dropIndex(['fecha_registro']);
            $table->dropIndex(['dependencia_id', 'estado']);
        });
    }
};
