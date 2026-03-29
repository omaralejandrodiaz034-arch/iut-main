<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // ⚠️ Quitar este bloque porque ya existe en create_dependencias_table
    /*
    Schema::table('dependencias', function (Blueprint $table) {
        $table->foreignId('responsable_id')->nullable()->after('nombre')
            ->constrained('responsables')->nullOnDelete()->cascadeOnUpdate();
        $table->index('responsable_id', 'idx_dep_responsable');
    });
    */

    // 2) Migrar datos desde bienes
    $dependencias = DB::table('dependencias')->select('id')->get();
    foreach ($dependencias as $dep) {
        $resp = DB::table('bienes')
            ->where('dependencia_id', $dep->id)
            ->whereNotNull('responsable_id')
            ->value('responsable_id');

        if ($resp) {
            DB::table('dependencias')->where('id', $dep->id)->update(['responsable_id' => $resp]);
        }
    }

    // 3) Eliminar columna responsable_id de bienes
    Schema::table('bienes', function (Blueprint $table) {
        if (Schema::hasColumn('bienes', 'responsable_id')) {
            $table->dropForeign(['responsable_id']);
            $table->dropIndex('idx_bien_responsable');
            $table->dropColumn('responsable_id');
        }
    });
}


    public function down(): void
    {
        // Volver a crear responsable_id en bienes (nullable)
        Schema::table('bienes', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()
                ->constrained('responsables')->nullOnDelete()->cascadeOnUpdate();
            $table->index('responsable_id', 'idx_bien_responsable');
        });

        // Repropagar los responsables desde dependencias a bienes
        $bienes = DB::table('bienes')->select('id', 'dependencia_id')->get();
        foreach ($bienes as $b) {
            $resp = DB::table('dependencias')->where('id', $b->dependencia_id)->value('responsable_id');
            if ($resp) {
                DB::table('bienes')->where('id', $b->id)->update(['responsable_id' => $resp]);
            }
        }

        // NOTA: NO se elimina responsable_id de dependencias porque fue creado
        // en create_dependencias_table, no en esta migración.
    }
};
