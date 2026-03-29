<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega todas las columnas necesarias para guardar los datos completos del bien
     */
    public function up(): void
    {
        Schema::table('bienes_desincorporados', function (Blueprint $table) {
            // Datos del bien (copiados de la tabla bienes)
            $table->unsignedBigInteger('dependencia_id')->nullable()->after('bien_id');
            $table->unsignedBigInteger('responsable_id')->nullable()->after('dependencia_id');
            $table->string('codigo', 8)->after('responsable_id');
            $table->string('descripcion', 255)->after('codigo');
            $table->decimal('precio', 15, 2)->default(0)->after('descripcion');
            $table->string('fotografia', 255)->nullable()->after('precio');
            $table->string('ubicacion', 255)->nullable()->after('fotografia');
            $table->string('estado', 50)->default('ACTIVO')->after('ubicacion');
            $table->date('fecha_registro')->nullable()->after('estado');
            $table->string('tipo_bien', 50)->nullable()->after('fecha_registro');
            $table->json('caracteristicas')->nullable()->after('tipo_bien');
            
            // Información de desincorporación
            $table->string('motivo_desincorporacion', 500)->change();
            $table->string('acta_desincorporacion', 500)->nullable()->change();
            
            // Timestamps
            $table->timestamp('fecha_desincorporacion')->useCurrent()->after('acta_desincorporacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bienes_desincorporados', function (Blueprint $table) {
            $table->dropColumn([
                'dependencia_id',
                'responsable_id',
                'codigo',
                'descripcion',
                'precio',
                'fotografia',
                'ubicacion',
                'estado',
                'fecha_registro',
                'tipo_bien',
                'caracteristicas',
                'fecha_desincorporacion',
            ]);
        });
    }
};
