<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (! Schema::hasColumn('bienes', 'precio')) {
                $table->decimal('precio', 12, 2)->default(0)->after('descripcion');
            }

            if (! Schema::hasColumn('bienes', 'fotografia')) {
                $table->string('fotografia', 2048)->nullable()->after('precio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            if (Schema::hasColumn('bienes', 'fotografia')) {
                $table->dropColumn('fotografia');
            }

            if (Schema::hasColumn('bienes', 'precio')) {
                $table->dropColumn('precio');
            }
        });
    }
};
