<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bienes')) {
            Schema::create('bienes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dependencia_id');
            $table->string('codigo', 8)->unique();
            $table->string('descripcion', 255);
            $table->decimal('precio', 15, 2)->default(0);
            $table->string('fotografia', 255)->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->string('estado', 50)->default('ACTIVO');
            $table->date('fecha_registro')->nullable();
            $table->timestamps();

            $table->foreign('dependencia_id')->references('id')->on('dependencias')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bienes')) {
            Schema::dropIfExists('bienes');
        }
    }
};
