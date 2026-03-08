<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('bienes', 'dependencia_id')) {
            // Evitar consultas a information_schema en drivers que no sean MySQL (p. ej. SQLite en tests)
            $driver = DB::getPdo() ? DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) : null;
            if ($driver === 'mysql') {
                $constraint = DB::selectOne("
                    SELECT CONSTRAINT_NAME as name
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = 'bienes'
                      AND COLUMN_NAME = 'dependencia_id'
                      AND REFERENCED_TABLE_NAME = 'dependencias'
                ");

                if ($constraint && isset($constraint->name)) {
                    DB::statement("ALTER TABLE `bienes` DROP FOREIGN KEY `{$constraint->name}`");
                }
            }

            Schema::table('bienes', function (Blueprint $table) {
                $table->unsignedBigInteger('dependencia_id')->nullable()->change();
                $table->foreign('dependencia_id')
                    ->references('id')->on('dependencias')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bienes', 'dependencia_id')) {
            $driver = DB::getPdo() ? DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) : null;
            if ($driver === 'mysql') {
                $constraint = DB::selectOne("
                    SELECT CONSTRAINT_NAME as name
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = 'bienes'
                      AND COLUMN_NAME = 'dependencia_id'
                      AND REFERENCED_TABLE_NAME = 'dependencias'
                ");

                if ($constraint && isset($constraint->name)) {
                    DB::statement("ALTER TABLE `bienes` DROP FOREIGN KEY `{$constraint->name}`");
                }
            }

            Schema::table('bienes', function (Blueprint $table) {
                $table->unsignedBigInteger('dependencia_id')->nullable(false)->change();
                $table->foreign('dependencia_id')
                    ->references('id')->on('dependencias')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }
};
