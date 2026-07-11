<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            if (! Schema::hasColumn('movimientos', 'acta_estado')) {
                $table->string('acta_estado')->default('SIN_REQUERIR')->after('acta_path');
            }

            if (! Schema::hasColumn('movimientos', 'fecha_limite_acta')) {
                $table->dateTime('fecha_limite_acta')->nullable()->after('acta_estado');
            }

            if (! Schema::hasColumn('movimientos', 'acta_firmada_path')) {
                $table->string('acta_firmada_path')->nullable()->after('fecha_limite_acta');
            }

            if (! Schema::hasColumn('movimientos', 'motivo_cancelacion_acta')) {
                $table->text('motivo_cancelacion_acta')->nullable()->after('acta_firmada_path');
            }

            if (! Schema::hasColumn('movimientos', 'fecha_cancelacion_acta')) {
                $table->dateTime('fecha_cancelacion_acta')->nullable()->after('motivo_cancelacion_acta');
            }

            if (! Schema::hasColumn('movimientos', 'metadata')) {
                $table->json('metadata')->nullable()->after('fecha_cancelacion_acta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn([
                'acta_estado',
                'fecha_limite_acta',
                'acta_firmada_path',
                'motivo_cancelacion_acta',
                'fecha_cancelacion_acta',
                'metadata',
            ]);
        });
    }
};
