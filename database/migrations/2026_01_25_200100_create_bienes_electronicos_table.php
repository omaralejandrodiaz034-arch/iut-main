<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bienes_electronicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('bienes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('subtipo', 20)->nullable(); // CPU, PANTALLA, ROUTER
            $table->string('procesador', 255)->nullable();
            $table->string('memoria', 255)->nullable();
            $table->string('almacenamiento', 255)->nullable();
            $table->string('pantalla', 255)->nullable();
            $table->string('serial', 255)->nullable();
            $table->date('garantia')->nullable();
            $table->timestamps();
            $table->unique('bien_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bienes_electronicos');
    }
};
