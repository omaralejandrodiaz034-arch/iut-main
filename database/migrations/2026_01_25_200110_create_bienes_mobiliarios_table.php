<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bienes_mobiliarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('bienes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('material', 255)->nullable();
            $table->string('dimensiones', 255)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('capacidad', 100)->nullable();
            $table->integer('cantidad_piezas')->nullable();
            $table->string('acabado', 100)->nullable();
            $table->timestamps();
            $table->unique('bien_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bienes_mobiliarios');
    }
};
