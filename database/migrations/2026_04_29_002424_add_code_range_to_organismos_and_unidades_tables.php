<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organismos', function (Blueprint $table) {
            $table->integer('code_min')->default(1)->after('codigo');
            $table->integer('code_max')->default(50)->after('code_min');
        });

        Schema::table('unidades_administradoras', function (Blueprint $table) {
            $table->integer('code_min')->default(1)->after('codigo');
            $table->integer('code_max')->default(50)->after('code_min');
        });

        // Inicializar rangos para datos existentes
        $this->inicializarRangosOrganismos();
        $this->inicializarRangosUnidades();
    }

    private function inicializarRangosOrganismos(): void
    {
        DB::table('organismos')->orderBy('id')->chunk(100, function ($organismos) {
            foreach ($organismos as $organismo) {
                $codigoNum = (int) $organismo->codigo;
                $base = $codigoNum * 10000;
                $min = $base + 1;
                $max = $base + 50;

                DB::table('organismos')
                    ->where('id', $organismo->id)
                    ->update(['code_min' => $min, 'code_max' => $max]);
            }
        });
    }

    private function inicializarRangosUnidades(): void
    {
        DB::table('unidades_administradoras')->orderBy('id')->chunk(100, function ($unidades) {
            foreach ($unidades as $unidad) {
                $codigoNum = (int) $unidad->codigo;
                $base = $codigoNum * 100;
                $min = $base + 1;
                $max = $base + 50;

                DB::table('unidades_administradoras')
                    ->where('id', $unidad->id)
                    ->update(['code_min' => $min, 'code_max' => $max]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidades_administradoras', function (Blueprint $table) {
            $table->dropColumn(['code_min', 'code_max']);
        });

        Schema::table('organismos', function (Blueprint $table) {
            $table->dropColumn(['code_min', 'code_max']);
        });
    }
};
