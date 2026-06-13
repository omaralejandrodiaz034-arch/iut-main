<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ampliarColumnasCodigo();
        $this->actualizarCodigos('organismos', fn (string $codigo): string => $this->expandirCodigoBase($codigo, '00'));
        $this->actualizarCodigos('unidades_administradoras', fn (string $codigo): string => $this->expandirCodigoBase($codigo, '00'));
        $this->actualizarCodigos('dependencias', fn (string $codigo): string => $this->expandirCodigoDependencia($codigo));
        $this->actualizarCodigos('bienes', fn (string $codigo): string => $this->expandirCodigoBien($codigo));
    }

    public function down(): void
    {
        $this->actualizarCodigos('bienes', fn (string $codigo): string => $this->contraerCodigoBien($codigo));
        $this->actualizarCodigos('dependencias', fn (string $codigo): string => $this->contraerCodigoDependencia($codigo));
        $this->actualizarCodigos('unidades_administradoras', fn (string $codigo): string => strlen($codigo) === 10 && substr($codigo, 8) === '00' ? substr($codigo, 0, 8) : $codigo);
        $this->actualizarCodigos('organismos', fn (string $codigo): string => strlen($codigo) === 10 && substr($codigo, 8) === '00' ? substr($codigo, 0, 8) : $codigo);
        $this->reducirColumnasCodigo();
    }

    private function ampliarColumnasCodigo(): void
    {
        Schema::table('organismos', fn (Blueprint $table) => $table->string('codigo', 10)->change());
        Schema::table('unidades_administradoras', fn (Blueprint $table) => $table->string('codigo', 10)->change());
        Schema::table('dependencias', fn (Blueprint $table) => $table->string('codigo', 10)->change());
        Schema::table('bienes', fn (Blueprint $table) => $table->string('codigo', 10)->change());
    }

    private function reducirColumnasCodigo(): void
    {
        Schema::table('organismos', fn (Blueprint $table) => $table->string('codigo', 8)->change());
        Schema::table('unidades_administradoras', fn (Blueprint $table) => $table->string('codigo', 8)->change());
        Schema::table('dependencias', fn (Blueprint $table) => $table->string('codigo', 8)->change());
        Schema::table('bienes', fn (Blueprint $table) => $table->string('codigo', 8)->change());
    }

    private function actualizarCodigos(string $tabla, callable $normalizar): void
    {
        $filas = DB::table($tabla)->orderBy('id')->get(['id', 'codigo']);
        $actualizados = [];

        foreach ($filas as $fila) {
            $codigoOriginal = (string) $fila->codigo;
            $codigoNuevo = $normalizar($codigoOriginal);

            if ($codigoNuevo === $codigoOriginal) {
                continue;
            }

            $colision = $filas->first(fn ($otra): bool => $otra->id !== $fila->id && $otra->codigo === $codigoNuevo);

            if ($colision) {
                throw new RuntimeException("No se puede actualizar el código {$codigoOriginal} en {$tabla} porque {$codigoNuevo} ya existe.");
            }

            $actualizados[] = [
                'id' => $fila->id,
                'codigo' => $codigoNuevo,
            ];
        }

        foreach ($actualizados as $fila) {
            DB::table($tabla)
                ->where('id', $fila['id'])
                ->update(['codigo' => $fila['codigo']]);
        }
    }

    private function expandirCodigoBase(string $codigo, string $relleno): string
    {
        if (strlen($codigo) === 8 && ctype_digit($codigo)) {
            return $codigo.$relleno;
        }

        return $codigo;
    }

    private function expandirCodigoDependencia(string $codigo): string
    {
        if (strlen($codigo) === 8 && ctype_digit($codigo)) {
            return substr($codigo, 0, 6).'00'.substr($codigo, 6);
        }

        return $codigo;
    }

    private function expandirCodigoBien(string $codigo): string
    {
        if (strlen($codigo) === 8 && ctype_digit($codigo)) {
            return substr($codigo, 0, 6).'00'.substr($codigo, 6);
        }

        return $codigo;
    }

    private function contraerCodigoDependencia(string $codigo): string
    {
        if (strlen($codigo) === 10 && ctype_digit($codigo) && substr($codigo, 6, 2) === '00') {
            return substr($codigo, 0, 6).substr($codigo, 8);
        }

        return $codigo;
    }

    private function contraerCodigoBien(string $codigo): string
    {
        if (strlen($codigo) === 10 && ctype_digit($codigo) && substr($codigo, 6, 2) === '00') {
            return substr($codigo, 0, 6).substr($codigo, 8);
        }

        return $codigo;
    }
};
