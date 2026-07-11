<?php

use App\Models\Bien;
use App\Models\BienDesincorporado;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->normalizarOrganismos();
            $this->normalizarUnidades();
            $this->normalizarDependencias();
            $this->normalizarBienes();
            $this->normalizarBienesDesincorporados();
        });
    }

    public function down(): void
    {
        // Cannot automatically restore - requires manual backup
    }

    private function normalizarOrganismos(): void
    {
        $organismos = Organismo::orderBy('id')->get();
        $pendingCodes = [];
        $sequence = 1;

        foreach ($organismos as $organismo) {
            $newCode = $this->generateOrganismCode($sequence++);

            if ($organismo->codigo !== $newCode) {
                $organismo->codigo = $this->temporaryCode('organismo', $organismo->id, 10);
                $organismo->save();
                $pendingCodes[$organismo->id] = $newCode;
            }
        }

        foreach ($organismos as $organismo) {
            if (! isset($pendingCodes[$organismo->id])) {
                continue;
            }

            $organismo->refresh();
            $organismo->codigo = $pendingCodes[$organismo->id];
            $organismo->save();
        }
    }

    private function generateOrganismCode(int $sequence): string
    {
        return str_pad((string) $sequence, 1, '0', STR_PAD_LEFT).str_repeat('0', 9);
    }

    private function temporaryCode(string $type, int $id, int $length = 50): string
    {
        if ($length === 10) {
            return '9'.str_pad((string) $id, 9, '0', STR_PAD_LEFT);
        }

        return 'TMP_'.$type.'_'.$id.'_'.md5(uniqid((string) $id, true));
    }

    private function normalizarUnidades(): void
    {
        $unidades = UnidadAdministradora::with('organismo')->orderBy('id')->get();
        $pendingCodes = [];

        foreach ($unidades as $unidad) {
            if (! $unidad->organismo) {
                continue;
            }

            $organismCode = $unidad->organismo->codigo;
            $orgSegment = substr($organismCode, 0, 1);

            $existingUnits = UnidadAdministradora::where('organismo_id', $unidad->organismo_id)
                ->where('id', '<=', $unidad->id)
                ->orderBy('id')
                ->pluck('id');

            $unitNumber = $existingUnits->search($unidad->id) + 1;
            $newCode = $orgSegment.str_pad((string) $unitNumber, 2, '0', STR_PAD_LEFT).str_repeat('0', 7);

            if ($unidad->codigo !== $newCode) {
                $unidad->codigo = $this->temporaryCode('unidad', $unidad->id, 10);
                $unidad->save();
                $pendingCodes[$unidad->id] = $newCode;
            }
        }

        foreach ($unidades as $unidad) {
            if (! isset($pendingCodes[$unidad->id])) {
                continue;
            }

            $unidad->refresh();
            $unidad->codigo = $pendingCodes[$unidad->id];
            $unidad->save();
        }
    }

    private function normalizarDependencias(): void
    {
        $dependencias = Dependencia::with('unidadAdministradora.organismo')->orderBy('id')->get();
        $pendingCodes = [];

        foreach ($dependencias as $dependencia) {
            $unidad = $dependencia->unidadAdministradora;
            if (! $unidad) {
                continue;
            }

            $prefijo = substr($unidad->codigo, 0, 3);

            $existingDeps = Dependencia::where('unidad_administradora_id', $dependencia->unidad_administradora_id)
                ->where('id', '<=', $dependencia->id)
                ->orderBy('id')
                ->pluck('id');

            $depNumber = $existingDeps->search($dependencia->id) + 1;
            $newCode = $prefijo.str_pad((string) $depNumber, 3, '0', STR_PAD_LEFT).str_repeat('0', 4);

            if ($dependencia->codigo !== $newCode) {
                $dependencia->codigo = $this->temporaryCode('dependencia', $dependencia->id, 10);
                $dependencia->save();
                $pendingCodes[$dependencia->id] = $newCode;
            }
        }

        foreach ($dependencias as $dependencia) {
            if (! isset($pendingCodes[$dependencia->id])) {
                continue;
            }

            $dependencia->refresh();
            $dependencia->codigo = $pendingCodes[$dependencia->id];
            $dependencia->save();
        }
    }

    private function normalizarBienes(): void
    {
        $bienes = Bien::with('dependencia.unidadAdministradora.organismo')->orderBy('id')->get();
        $pendingCodes = [];

        foreach ($bienes as $bien) {
            $dependencia = $bien->dependencia;
            if (! $dependencia) {
                continue;
            }

            $prefijo = substr($dependencia->codigo, 0, 6);

            $existingBienes = Bien::where('dependencia_id', $bien->dependencia_id)
                ->where('id', '<=', $bien->id)
                ->orderBy('id')
                ->pluck('id');

            $bienNumber = $existingBienes->search($bien->id) + 1;
            $newCode = $prefijo.str_pad((string) $bienNumber, 4, '0', STR_PAD_LEFT);

            if ($bien->codigo !== $newCode) {
                $bien->codigo = $this->temporaryCode('bien', $bien->id, 10);
                $bien->save();
                $pendingCodes[$bien->id] = $newCode;
            }
        }

        foreach ($bienes as $bien) {
            if (! isset($pendingCodes[$bien->id])) {
                continue;
            }

            $bien->refresh();
            $bien->codigo = $pendingCodes[$bien->id];
            $bien->save();
        }
    }

    private function normalizarBienesDesincorporados(): void
    {
        $bienesDes = BienDesincorporado::with('dependencia.unidadAdministradora.organismo')->orderBy('id')->get();

        foreach ($bienesDes as $bien) {
            if (! $bien->dependencia) {
                continue;
            }

            $dependencia = $bien->dependencia;
            $prefijo = substr($dependencia->codigo, 0, 6);

            $bienNumber = (int) substr($bien->codigo, -4);
            if (strlen($bien->codigo) !== 10) {
                $bienNumber = $bien->id;
            }

            $newCode = $prefijo.str_pad((string) $bienNumber, 4, '0', STR_PAD_LEFT);

            if ($bien->codigo !== $newCode) {
                $bien->codigo = $newCode;
                $bien->save();
            }
        }
    }
};
