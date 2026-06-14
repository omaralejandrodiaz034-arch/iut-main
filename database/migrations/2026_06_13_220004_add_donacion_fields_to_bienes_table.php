<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->boolean('es_donacion')->default(false)->after('precio');
            $table->string('tipo_donante')->nullable()->after('es_donacion');
            $table->string('donante_nombre')->nullable()->after('tipo_donante');
            $table->string('donante_documento')->nullable()->after('donante_nombre');
            $table->string('donante_direccion')->nullable()->after('donante_documento');
            $table->string('acta_donacion')->nullable()->after('donante_direccion');
        });
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropColumn([
                'es_donacion',
                'tipo_donante',
                'donante_nombre',
                'donante_documento',
                'donante_direccion',
                'acta_donacion',
            ]);
        });
    }
};
