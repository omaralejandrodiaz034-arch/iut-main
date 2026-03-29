<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar de la tabla bienes
        if (Schema::hasColumn('bienes', 'ubicacion')) {
            Schema::table('bienes', function (Blueprint $table) {
                $table->dropColumn('ubicacion');
            });
        }

        // Eliminar de la tabla bienes_desincorporados
        if (Schema::hasColumn('bienes_desincorporados', 'ubicacion')) {
            Schema::table('bienes_desincorporados', function (Blueprint $table) {
                $table->dropColumn('ubicacion');
            });
        }
    }

    public function down(): void
    {
        // Restaurar en bienes
        Schema::table('bienes', function (Blueprint $table) {
            $table->string('ubicacion', 255)->nullable()->after('fotografia');
        });

        // Restaurar en bienes_desincorporados
        Schema::table('bienes_desincorporados', function (Blueprint $table) {
            $table->string('ubicacion', 255)->nullable()->after('fotografia');
        });
    }
};
