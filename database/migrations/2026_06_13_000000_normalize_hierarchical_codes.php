<?php

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Dependencia;
use App\Models\Bien;
use App\Models\BienDesincorporado;
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
        
        $usedCodes = [];
        foreach ($organismos as $organismo) {
            $newCode = $this->generateOrganismCode($organismo->id, $usedCodes);
            if ($organismo->codigo !== $newCode) {
                $organismo->codigo = $newCode;
                $organismo->save();
            }
        }
    }

    private function generateOrganismCode(int $id, array &$usedCodes): string
    {
        $code = str_pad((string) $id, 1, '0', STR_PAD_LEFT);
        $fullCode = $code . str_repeat('0', 9);
        
        if (!isset($usedCodes['organismos'])) {
            $usedCodes['organismos'] = Organismo::pluck('codigo')->toArray();
        }
        
        while (in_array($fullCode, $usedCodes['organismos'])) {
            $id++;
            $code = str_pad((string) $id, 1, '0', STR_PAD_LEFT);
            $fullCode = $code . str_repeat('0', 9);
        }
        
        return $fullCode;
    }

    private function normalizarUnidades(): void
    {
        $unidades = UnidadAdministradora::with('organismo')->orderBy('id')->get();
        
        foreach ($unidades as $unidad) {
            $organismCode = $unidad->organismo->codigo;
            $orgSegment = substr($organismCode, 0, 1);
            
            $existingUnits = UnidadAdministradora::where('organismo_id', $unidad->organismo_id)
                ->where('id', '<=', $unidad->id)
                ->orderBy('id')
                ->pluck('id');
            
            $unitNumber = $existingUnits->search($unidad->id) + 1;
            
            $newCode = $orgSegment . str_pad((string) $unitNumber, 2, '0', STR_PAD_LEFT) . str_repeat('0', 7);
            
            if ($unidad->codigo !== $newCode) {
                $unidad->codigo = $newCode;
                $unidad->save();
            }
        }
    }

    private function normalizarDependencias(): void
    {
        $dependencias = Dependencia::with('unidadAdministradora.organismo')->orderBy('id')->get();
        
        foreach ($dependencias as $dependencia) {
            $unidad = $dependencia->unidadAdministradora;
            $prefijo = substr($unidad->codigo, 0, 3);
            
            $existingDeps = Dependencia::where('unidad_administradora_id', $dependencia->unidad_administradora_id)
                ->where('id', '<=', $dependencia->id)
                ->orderBy('id')
                ->pluck('id');
            
            $depNumber = $existingDeps->search($dependencia->id) + 1;
            
            $newCode = $prefijo . str_pad((string) $depNumber, 3, '0', STR_PAD_LEFT) . str_repeat('0', 4);
            
            if ($dependencia->codigo !== $newCode) {
                $dependencia->codigo = $newCode;
                $dependencia->save();
            }
        }
    }

    private function normalizarBienes(): void
    {
        $bienes = Bien::with('dependencia.unidadAdministradora.organismo')->orderBy('id')->get();
        
        foreach ($bienes as $bien) {
            $dependencia = $bien->dependencia;
            $prefijo = substr($dependencia->codigo, 0, 6);
            
            $existingBienes = Bien::where('dependencia_id', $bien->dependencia_id)
                ->where('id', '<=', $bien->id)
                ->orderBy('id')
                ->pluck('id');
            
            $bienNumber = $existingBienes->search($bien->id) + 1;
            
            $newCode = $prefijo . str_pad((string) $bienNumber, 4, '0', STR_PAD_LEFT);
            
            if ($bien->codigo !== $newCode) {
                $bien->codigo = $newCode;
                $bien->save();
            }
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

            $newCode = $prefijo . str_pad((string) $bienNumber, 4, '0', STR_PAD_LEFT);

            if ($bien->codigo !== $newCode) {
                $bien->codigo = $newCode;
                $bien->save();
            }
        }
    }
};