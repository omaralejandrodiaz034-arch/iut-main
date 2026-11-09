<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bienes_desincorporados')) {
            return;
        }

        if (! Schema::hasColumn('bienes_desincorporados', 'codigo')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE bienes_desincorporados RENAME COLUMN codigo TO codigo_old');
            DB::statement("ALTER TABLE bienes_desincorporados ADD COLUMN codigo VARCHAR(50) NOT NULL DEFAULT ''");
            DB::statement('UPDATE bienes_desincorporados SET codigo = codigo_old');
            DB::statement('ALTER TABLE bienes_desincorporados DROP COLUMN codigo_old');

            return;
        }

        Schema::table('bienes_desincorporados', function (Blueprint $table) {
            $table->string('codigo', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('bienes_desincorporados')) {
            return;
        }

        if (! Schema::hasColumn('bienes_desincorporados', 'codigo')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE bienes_desincorporados RENAME COLUMN codigo TO codigo_old');
            DB::statement("ALTER TABLE bienes_desincorporados ADD COLUMN codigo VARCHAR(8) NOT NULL DEFAULT ''");
            DB::statement('UPDATE bienes_desincorporados SET codigo = codigo_old');
            DB::statement('ALTER TABLE bienes_desincorporados DROP COLUMN codigo_old');

            return;
        }

        Schema::table('bienes_desincorporados', function (Blueprint $table) {
            $table->string('codigo', 8)->change();
        });
    }
};
