<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            // Drop foreign key to modify column nullability
            if (Schema::hasColumn('bienes', 'dependencia_id')) {
                try {
                    $table->dropForeign(['dependencia_id']);
                } catch (\Throwable $e) {
                    // Some MySQL setups require constraint name; ignore if not present
                }
                $table->unsignedBigInteger('dependencia_id')->nullable()->change();
                // Re-create FK allowing nulls and set null on delete
                $table->foreign('dependencia_id')
                    ->references('id')->on('dependencias')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (Schema::hasColumn('bienes', 'dependencia_id')) {
                try {
                    $table->dropForeign(['dependencia_id']);
                } catch (\Throwable $e) {
                }
                $table->unsignedBigInteger('dependencia_id')->nullable(false)->change();
                $table->foreign('dependencia_id')
                    ->references('id')->on('dependencias')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }
};
