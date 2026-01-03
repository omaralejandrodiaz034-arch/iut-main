<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            // Agregar solo campos que no existan
            if (!Schema::hasColumn('bienes', 'tipo_bien')) {
                $table->string('tipo_bien', 50)->default('OTROS')->after('estado');
            }
            if (!Schema::hasColumn('bienes', 'memoria')) {
                $table->string('memoria')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'almacenamiento')) {
                $table->string('almacenamiento')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'pantalla')) {
                $table->string('pantalla')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'garantia')) {
                $table->string('garantia')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'marca')) {
                $table->string('marca')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'modelo')) {
                $table->string('modelo')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'anio')) {
                $table->string('anio')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'placa')) {
                $table->string('placa')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'motor')) {
                $table->string('motor')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'chasis')) {
                $table->string('chasis')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'combustible')) {
                $table->string('combustible')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'kilometraje')) {
                $table->string('kilometraje')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'capacidad')) {
                $table->string('capacidad')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'cantidad_piezas')) {
                $table->string('cantidad_piezas')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'acabado')) {
                $table->string('acabado')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'pisos')) {
                $table->string('pisos')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'construccion')) {
                $table->string('construccion')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'cantidad')) {
                $table->string('cantidad')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'presentacion')) {
                $table->string('presentacion')->nullable();
            }
            if (!Schema::hasColumn('bienes', 'especificaciones')) {
                $table->text('especificaciones')->nullable();
            }

            // Índice para búsquedas por tipo (si no existe)
            if (!Schema::hasIndex('bienes', 'idx_bien_tipo')) {
                $table->index('tipo_bien', 'idx_bien_tipo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            // Eliminar índice solo si existe
            if (Schema::hasIndex('bienes', 'idx_bien_tipo')) {
                $table->dropIndex('idx_bien_tipo');
            }

            // Eliminar solo las columnas que añadimos y que no existían antes
            $columnsToAdd = [
                'tipo_bien',
                'memoria',
                'almacenamiento',
                'pantalla',
                'garantia',
                'marca',
                'modelo',
                'anio',
                'placa',
                'motor',
                'chasis',
                'combustible',
                'kilometraje',
                'color',
                'capacidad',
                'cantidad_piezas',
                'acabado',
                'pisos',
                'construccion',
                'cantidad',
                'presentacion',
                'especificaciones',
            ];

            $existingColumns = DB::connection()->getSchemaBuilder()->getColumnListing('bienes');
            $columnsToDelete = array_filter($columnsToAdd, function ($col) use ($existingColumns) {
                return in_array($col, $existingColumns);
            });

            if (!empty($columnsToDelete)) {
                $table->dropColumn($columnsToDelete);
            }
        });
    }
};
