<?php

namespace Tests\Unit\Services;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\CodigoJerarquicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodigoJerarquicoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_codigos_jerarquicos_de_10_digitos(): void
    {
        $organismo = Organismo::create([
            'codigo' => CodigoJerarquicoService::generarCodigoOrganismo(),
            'nombre' => 'Organismo Test',
        ]);

        $unidad = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => CodigoJerarquicoService::generarCodigoUnidad($organismo->id),
            'nombre' => 'Unidad Administradora Test',
        ]);

        $dependencia = Dependencia::create([
            'unidad_administradora_id' => $unidad->id,
            'codigo' => CodigoJerarquicoService::generarCodigoDependencia($unidad->id),
            'nombre' => 'Dependencia Test',
        ]);

        $bien = Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => CodigoJerarquicoService::generarCodigoBien($dependencia->id),
            'descripcion' => 'Bien Test',
            'precio' => 100,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        $this->assertSame(10, strlen($organismo->codigo));
        $this->assertSame(10, strlen($unidad->codigo));
        $this->assertSame(10, strlen($dependencia->codigo));
        $this->assertSame(10, strlen($bien->codigo));

        $this->assertSame('1.00.000.0000', CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo));
        $this->assertSame('1.01.000.0000', CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo));
        $this->assertSame('1.01.001.0000', CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo));
        $this->assertSame('1.01.001.0001', CodigoJerarquicoService::formatearCodigoLegible($bien->codigo));

        $this->assertSame('1010010001', $bien->codigo);
    }

    public function test_decodifica_y_valida_padres_de_codigo_de_10_digitos(): void
    {
        $decodificado = CodigoJerarquicoService::decodificarCodigo('1010010001');

        $this->assertSame('bien', $decodificado['tipo']);
        $this->assertSame('1', $decodificado['organismo']);
        $this->assertSame('01', $decodificado['unidad']);
        $this->assertSame('001', $decodificado['dependencia']);
        $this->assertSame('0001', $decodificado['secuencial']);

        $this->assertSame('1010010000', CodigoJerarquicoService::obtenerCodigoPadre('1010010001'));
        $this->assertSame('1010000000', CodigoJerarquicoService::obtenerCodigoPadre('1010010000'));
        $this->assertSame('1000000000', CodigoJerarquicoService::obtenerCodigoPadre('1010000000'));

        $this->assertTrue(CodigoJerarquicoService::validarJerarquia('1010010001', '1010010000'));
        $this->assertFalse(CodigoJerarquicoService::validarJerarquia('1010010001', '1010000000'));
    }

    public function test_permite_hasta_999_dependencias_por_unidad(): void
    {
        $organismo = Organismo::create([
            'codigo' => CodigoJerarquicoService::generarCodigoOrganismo(),
            'nombre' => 'Organismo Test',
        ]);

        $unidad = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => CodigoJerarquicoService::generarCodigoUnidad($organismo->id),
            'nombre' => 'Unidad Administradora Test',
        ]);

        for ($i = 1; $i <= 999; $i++) {
            Dependencia::create([
                'unidad_administradora_id' => $unidad->id,
                'codigo' => CodigoJerarquicoService::generarCodigoDependencia($unidad->id),
                'nombre' => "Dependencia {$i}",
            ]);
        }

        $this->assertSame(999, Dependencia::where('unidad_administradora_id', $unidad->id)->count());

        $this->expectException(\RuntimeException::class);

        CodigoJerarquicoService::generarCodigoDependencia($unidad->id);
    }
}
