<?php

// database/migrations/2025_09_04_000007_create_usuarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')
                ->constrained('roles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('cedula', 20)->unique();
            $table->string('nombre', 150);
            $table->string('correo', 150)->unique();
            $table->string('hash_password', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('rol_id', 'idx_usuario_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
