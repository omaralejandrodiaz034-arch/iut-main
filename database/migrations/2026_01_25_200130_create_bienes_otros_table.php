<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bienes_otros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('bienes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('especificaciones')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('presentacion', 255)->nullable();
            $table->timestamps();
            $table->unique('bien_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bienes_otros');
    }
};
