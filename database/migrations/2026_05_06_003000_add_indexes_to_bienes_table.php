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
            if (! Schema::hasIndex('bienes', 'idx_bien_estado')) {
                $table->index('estado', 'idx_bien_estado');
            }
            if (! Schema::hasIndex('bienes', 'idx_bien_tipo_bien')) {
                $table->index('tipo_bien', 'idx_bien_tipo_bien');
            }
            if (! Schema::hasIndex('bienes', 'idx_bien_fecha_registro')) {
                $table->index('fecha_registro', 'idx_bien_fecha_registro');
            }
            if (! Schema::hasIndex('bienes', 'idx_bien_dep_estado')) {
                $table->index(['dependencia_id', 'estado'], 'idx_bien_dep_estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (Schema::hasIndex('bienes', 'idx_bien_dep_estado')) {
                $table->dropIndex('idx_bien_dep_estado');
            }
            if (Schema::hasIndex('bienes', 'idx_bien_fecha_registro')) {
                $table->dropIndex('idx_bien_fecha_registro');
            }
            if (Schema::hasIndex('bienes', 'idx_bien_tipo_bien')) {
                $table->dropIndex('idx_bien_tipo_bien');
            }
            if (Schema::hasIndex('bienes', 'idx_bien_estado')) {
                $table->dropIndex('idx_bien_estado');
            }
        });
    }
};
