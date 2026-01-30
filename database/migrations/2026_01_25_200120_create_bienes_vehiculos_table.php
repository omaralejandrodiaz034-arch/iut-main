<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bienes_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('bienes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('anio', 10)->nullable();
            $table->string('placa', 50)->nullable();
            $table->string('motor', 100)->nullable();
            $table->string('chasis', 100)->nullable();
            $table->string('combustible', 50)->nullable();
            $table->string('kilometraje', 50)->nullable();
            $table->timestamps();
            $table->unique('bien_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bienes_vehiculos');
    }
};
