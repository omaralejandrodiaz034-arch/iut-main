<?php

// database/migrations/2025_09_04_200700_create_auditoria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()
                ->constrained('usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Tabla que fue modificada
            $table->string('tabla', 100);

            // ID del registro que fue modificado
            $table->unsignedBigInteger('registro_id');

            // Tipo de operación: CREATE, UPDATE, DELETE
            $table->enum('operacion', ['CREATE', 'UPDATE', 'DELETE']);

            // Datos anteriores (para UPDATE y DELETE)
            $table->json('valores_anteriores')->nullable();

            // Datos nuevos (para CREATE y UPDATE)
            $table->json('valores_nuevos')->nullable();

            // Descripción de la acción
            $table->text('descripcion')->nullable();

            // IP del usuario que realizó la acción
            $table->string('ip_address', 45)->nullable();

            // User Agent del navegador
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Índices para búsquedas frecuentes
            $table->index(['tabla', 'registro_id'], 'idx_auditoria_tabla_registro');
            $table->index(['usuario_id', 'created_at'], 'idx_auditoria_usuario_fecha');
            $table->index(['tabla', 'operacion'], 'idx_auditoria_tabla_operacion');
            $table->index('created_at', 'idx_auditoria_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
