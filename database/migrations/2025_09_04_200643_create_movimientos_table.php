<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear nueva tabla con esquema final
        Schema::create('movimientos_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bien_id')->nullable();

            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('tipo', 80);
            $table->timestamp('fecha')->useCurrent();
            $table->text('observaciones')->nullable();

            $table->foreignId('usuario_id')->nullable()
                ->constrained('usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index('bien_id', 'idx_mov_bien');
            $table->index('fecha', 'idx_mov_fecha');
            $table->index(['subject_type', 'subject_id'], 'idx_mov_subject');
        });

        // 2) Copiar datos
        $rows = DB::table('movimientos')->get();
        foreach ($rows as $row) {
            DB::table('movimientos_new')->insert([
                'id' => $row->id,
                'bien_id' => $row->bien_id ?? null,
                'subject_type' => $row->subject_type ?? null,
                'subject_id' => $row->subject_id ?? null,
                'tipo' => $row->tipo,
                'fecha' => $row->fecha,
                'observaciones' => $row->observaciones,
                'usuario_id' => $row->usuario_id ?? null,
            ]);
        }

        // 3) Eliminar antigua y renombrar
        Schema::dropIfExists('movimientos');
        Schema::rename('movimientos_new', 'movimientos');

        // 4) Agregar FK de bien_id con SET NULL
        DB::statement('ALTER TABLE movimientos ADD CONSTRAINT movimientos_bien_id_foreign FOREIGN KEY (bien_id) REFERENCES bienes(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down(): void
    {
        // Restaurar el esquema anterior (bien_id NOT NULL) si fuese necesario
        Schema::create('movimientos_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('bienes')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('tipo', 80);
            $table->timestamp('fecha')->useCurrent();
            $table->text('observaciones')->nullable();

            $table->foreignId('usuario_id')->nullable()
                ->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();

            $table->index('bien_id', 'idx_mov_bien');
            $table->index('fecha', 'idx_mov_fecha');
        });

        // Copiar solo filas con bien_id (estricto)
        $rows = DB::table('movimientos')->get();
        foreach ($rows as $row) {
            if ($row->bien_id === null) {
                continue;
            }
            DB::table('movimientos_old')->insert([
                'id' => $row->id,
                'bien_id' => $row->bien_id,
                'tipo' => $row->tipo,
                'fecha' => $row->fecha,
                'observaciones' => $row->observaciones,
                'usuario_id' => $row->usuario_id,
            ]);
        }

        Schema::dropIfExists('movimientos');
        Schema::rename('movimientos_old', 'movimientos');
    }
};

