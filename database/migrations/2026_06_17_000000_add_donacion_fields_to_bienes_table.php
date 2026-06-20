<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bienes')) {
            Schema::table('bienes', function (Blueprint $table) {
                if (! Schema::hasColumn('bienes', 'es_donacion')) {
                    $table->boolean('es_donacion')->default(false)->after('caracteristicas');
                }

                if (! Schema::hasColumn('bienes', 'tipo_donante')) {
                    $table->string('tipo_donante', 50)->nullable()->after('es_donacion');
                }

                if (! Schema::hasColumn('bienes', 'donante_nombre')) {
                    $table->string('donante_nombre', 150)->nullable()->after('tipo_donante');
                }

                if (! Schema::hasColumn('bienes', 'donante_documento')) {
                    $table->string('donante_documento', 50)->nullable()->after('donante_nombre');
                }

                if (! Schema::hasColumn('bienes', 'donante_direccion')) {
                    $table->string('donante_direccion', 255)->nullable()->after('donante_documento');
                }

                if (! Schema::hasColumn('bienes', 'acta_donacion')) {
                    $table->string('acta_donacion', 255)->nullable()->after('donante_direccion');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bienes')) {
            Schema::table('bienes', function (Blueprint $table) {
                if (Schema::hasColumn('bienes', 'acta_donacion')) {
                    $table->dropColumn('acta_donacion');
                }

                if (Schema::hasColumn('bienes', 'donante_direccion')) {
                    $table->dropColumn('donante_direccion');
                }

                if (Schema::hasColumn('bienes', 'donante_documento')) {
                    $table->dropColumn('donante_documento');
                }

                if (Schema::hasColumn('bienes', 'donante_nombre')) {
                    $table->dropColumn('donante_nombre');
                }

                if (Schema::hasColumn('bienes', 'tipo_donante')) {
                    $table->dropColumn('tipo_donante');
                }

                if (Schema::hasColumn('bienes', 'es_donacion')) {
                    $table->dropColumn('es_donacion');
                }
            });
        }
    }
};
