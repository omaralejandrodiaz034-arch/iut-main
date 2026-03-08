<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('observaciones');
            $table->string('acta_path', 500)->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'acta_path']);
        });
    }
};
