<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (! Schema::hasColumn('bienes', 'caracteristicas')) {
                $table->json('caracteristicas')->nullable()->after('tipo_bien');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (Schema::hasColumn('bienes', 'caracteristicas')) {
                $table->dropColumn('caracteristicas');
            }
        });
    }
};
