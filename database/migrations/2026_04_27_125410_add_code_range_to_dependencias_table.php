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
        Schema::table('dependencias', function (Blueprint $table) {
            // Rango de códigos permitidos para esta dependencia (para bienes)
            $table->integer('code_min')->default(1)->after('responsable_id');
            $table->integer('code_max')->default(99999999)->after('code_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dependencias', function (Blueprint $table) {
            $table->dropColumn(['code_min', 'code_max']);
        });
    }
};
