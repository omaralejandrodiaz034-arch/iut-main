<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // bienes_electronicos: convertir valores vacíos a NULL y permitir NULLs
        if (Schema::hasTable('bienes_electronicos')) {
            foreach (['subtipo','procesador','memoria','almacenamiento','pantalla','serial'] as $col) {
                DB::table('bienes_electronicos')->where($col, '')->update([$col => null]);
            }

            Schema::table('bienes_electronicos', function (Blueprint $table) {
                $table->string('subtipo', 20)->nullable()->change();
                $table->string('procesador', 255)->nullable()->change();
                $table->string('memoria', 255)->nullable()->change();
                $table->string('almacenamiento', 255)->nullable()->change();
                $table->string('pantalla', 255)->nullable()->change();
                $table->string('serial', 255)->nullable()->change();
                $table->date('garantia')->nullable()->change();
            });
        }

        // bienes_mobiliarios
        if (Schema::hasTable('bienes_mobiliarios')) {
            foreach (['material','dimensiones','color','capacidad','acabado'] as $col) {
                DB::table('bienes_mobiliarios')->where($col, '')->update([$col => null]);
            }
            DB::table('bienes_mobiliarios')->where('cantidad_piezas', 0)->update(['cantidad_piezas' => null]);

            Schema::table('bienes_mobiliarios', function (Blueprint $table) {
                $table->string('material', 255)->nullable()->change();
                $table->string('dimensiones', 255)->nullable()->change();
                $table->string('color', 100)->nullable()->change();
                $table->string('capacidad', 100)->nullable()->change();
                $table->integer('cantidad_piezas')->nullable()->change();
                $table->string('acabado', 100)->nullable()->change();
            });
        }

        // bienes_vehiculos
        if (Schema::hasTable('bienes_vehiculos')) {
            foreach (['marca','modelo','anio','placa','motor','chasis','combustible','kilometraje'] as $col) {
                DB::table('bienes_vehiculos')->where($col, '')->update([$col => null]);
            }

            Schema::table('bienes_vehiculos', function (Blueprint $table) {
                $table->string('marca', 100)->nullable()->change();
                $table->string('modelo', 100)->nullable()->change();
                $table->string('anio', 10)->nullable()->change();
                $table->string('placa', 50)->nullable()->change();
                $table->string('motor', 100)->nullable()->change();
                $table->string('chasis', 100)->nullable()->change();
                $table->string('combustible', 50)->nullable()->change();
                $table->string('kilometraje', 50)->nullable()->change();
            });
        }

        // bienes_otros
        if (Schema::hasTable('bienes_otros')) {
            DB::table('bienes_otros')->where('especificaciones', '')->update(['especificaciones' => null]);
            DB::table('bienes_otros')->where('presentacion', '')->update(['presentacion' => null]);
            DB::table('bienes_otros')->where('cantidad', 0)->update(['cantidad' => null]);

            Schema::table('bienes_otros', function (Blueprint $table) {
                $table->text('especificaciones')->nullable()->change();
                $table->integer('cantidad')->nullable()->change();
                $table->string('presentacion', 255)->nullable()->change();
            });
        }

        // reportes
        if (Schema::hasTable('reportes')) {
            DB::table('reportes')->where('archivo_pdf_path', '')->update(['archivo_pdf_path' => null]);

            Schema::table('reportes', function (Blueprint $table) {
                $table->string('archivo_pdf_path', 255)->nullable()->change();
            });
        }

        // bienes: caracteristicas JSON
        if (Schema::hasTable('bienes') && Schema::hasColumn('bienes', 'caracteristicas')) {
            DB::table('bienes')->where('caracteristicas', '')->orWhereNull('caracteristicas')->update(['caracteristicas' => null]);
            Schema::table('bienes', function (Blueprint $table) {
                $table->json('caracteristicas')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // No-op: la transformación de datos (cadenas vacías → NULL) es irreversible.
        // Las columnas ya eran nullable en las migraciones originales, por lo que
        // los cambios de esquema en up() no alteraron la estructura real.
    }
};
