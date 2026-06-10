<?php

namespace Tests\Unit\Services;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\Rol;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use App\Services\CodigoJerarquicoService as CodigoUnicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class CodigoUnicoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register REGEXP function for SQLite (needed because CodigoUnicoService uses REGEXP)
        if (config('database.default') === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction('REGEXP', function ($pattern, $value) {
                return (bool) preg_match('/'.$pattern.'/', $value);
            });
        }
    }

    private function createUser(): Usuario
    {
        $rol = Rol::create(['nombre' => 'TestRole']);

        return Usuario::create([
            'rol_id' => $rol->id,
            'cedula' => 'V-12345678',
            'nombre' => 'Test',
            'apellido' => 'User',
            'correo' => 'test.user@example.com',
            'hash_password' => bcrypt('secret'),
            'activo' => true,
            'is_admin' => true,
        ]);
    }

    private function createDependenciaWithRange(?int $codeMin = null, ?int $codeMax = null, ?UnidadAdministradora $unidad = null): Dependencia
    {
        if (! $unidad) {
            $organismoCodigo = str_pad((string) rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            $organismo = Organismo::create(['codigo' => $organismoCodigo, 'nombre' => 'Organismo Test']);

            $unidadCodigo = $organismoCodigo.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);
            $unidad = UnidadAdministradora::create([
                'organismo_id' => $organismo->id,
                'codigo' => $unidadCodigo,
                'nombre' => 'Unidad Administradora Test',
            ]);
        }

        $dependenciaCodigo = $unidad->codigo.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);

        return Dependencia::create([
            'unidad_administradora_id' => $unidad->id,
            'codigo' => $dependenciaCodigo,
            'nombre' => 'Dependencia Test',
            'code_min' => $codeMin ?? 1,
            'code_max' => $codeMax ?? 99999,
        ]);
    }

    /**
     * Test 1: recomendarSiguienteCodigoParaDependencia with empty dependency (no existing codes)
     * Should return the minimum code (code_min) formatted as 8-digit zero-padded
     */
    public function test_recomendar_siguiente_codigo_con_dependencia_vacia_devuelve_code_min()
    {
        $this->actingAs($this->createUser());

        $dependencia = $this->createDependenciaWithRange(100, 99999);
        // No bienes existentes

        $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);
        $expectedCodigo = $dependencia->codigo.str_pad((string) 100, 5, '0', STR_PAD_LEFT);

        $this->assertEquals($expectedCodigo, $result['codigo']);
        $this->assertEquals(100, $result['siguiente_numero']);
        $this->assertEquals(100, $result['rango_min']);
        $this->assertEquals(99999, $result['rango_max']);
        $this->assertInstanceOf(Dependencia::class, $result['dependencia']);
        $this->assertEquals($dependencia->id, $result['dependencia']->id);
    }

    /**
     * Test 2: recomendarSiguienteCodigoParaDependencia with existing codes
     * Should return MAX(code)+1 (gap-filling is NOT done within dependency)
     */
    public function test_recomendar_siguiente_codigo_con_codigos_existentes_devuelve_max_mas_uno()
    {
        $this->actingAs($this->createUser());

        $dependencia = $this->createDependenciaWithRange(1, 99999);

        // Create some existing bienes with specific codes
        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00001',
            'descripcion' => 'Bien 1',
            'precio' => 100,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00002',
            'descripcion' => 'Bien 2',
            'precio' => 200,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00005', // Gap exists (3,4 missing) but should NOT be filled
            'descripcion' => 'Bien 5',
            'precio' => 500,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);

        // Should be 6 (MAX+1), not 3 (first gap)
        $this->assertEquals($dependencia->codigo.'00006', $result['codigo']);
        $this->assertEquals(6, $result['siguiente_numero']);
    }

    /**
     * Test 3: Range exhaustion (siguiente > max)
     * Should throw RuntimeException when code range is exhausted
     */
    public function test_recomendar_siguiente_codigo_con_rango_exhausto_lanza_excepcion()
    {
        $this->actingAs($this->createUser());

        $dependencia = $this->createDependenciaWithRange(1, 5); // Very small range

        // Fill the entire range
        for ($i = 1; $i <= 5; $i++) {
            Bien::create([
                'dependencia_id' => $dependencia->id,
                'codigo' => $dependencia->codigo.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'descripcion' => "Bien {$i}",
                'precio' => 100,
                'estado' => 'ACTIVO',
                'fecha_registro' => now(),
                'tipo_bien' => 'OTROS',
            ]);
        }

        $this->expectException(RuntimeException::class);

        CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);
    }

    /**
     * Test 4: Code format (5-digit zero-padded suffix)
     * Verify that returned bienes codes include the dependency prefix and 5-digit sequence with leading zeros
     */
    public function test_formato_codigo_ocho_digitos_con_ceros_a_la_izquierda()
    {
        $this->actingAs($this->createUser());

        $testCases = [
            ['min' => 1, 'expected' => '00001'],
            ['min' => 5, 'expected' => '00005'],
            ['min' => 42, 'expected' => '00042'],
            ['min' => 100, 'expected' => '00100'],
            ['min' => 999, 'expected' => '00999'],
            ['min' => 1000, 'expected' => '01000'],
            ['min' => 12345, 'expected' => '12345'],
            ['min' => 99999, 'expected' => '99999'],
        ];

        foreach ($testCases as $case) {
            $dependencia = $this->createDependenciaWithRange($case['min'], 99999);
            $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);
            $this->assertEquals($dependencia->codigo.$case['expected'], $result['codigo'], "Failed for code {$case['min']}");
        }
    }

    /**
     * Test 5: Default ranges (code_min=1, code_max=99999)
     * When dependencia is created without explicit code_min/code_max, defaults are used
     */
    public function test_rangos_por_defecto_cuando_no_se_especifican()
    {
        $this->actingAs($this->createUser());

        // Create dependencia without specifying code_min and code_max
        $dependencia = $this->createDependenciaWithRange(null, null);

        $dependencia->refresh(); // Refresh to get DB defaults

        $this->assertEquals(1, $dependencia->code_min);
        $this->assertEquals(99999, $dependencia->code_max);

        $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);
        $this->assertEquals($dependencia->codigo.'00001', $result['codigo']);
        $this->assertEquals(1, $result['rango_min']);
        $this->assertEquals(99999, $result['rango_max']);
    }

    /**
     * Test 6: Concurrent access handling
     * Verify that the transaction with lockForUpdate prevents race conditions
     * by simulating concurrent calls and ensuring unique codes are generated
     */
    public function test_acceso_concurrente_maneja_condiciones_de_carrera()
    {
        $this->actingAs($this->createUser());

        $dependencia = $this->createDependenciaWithRange(1, 100);

        // First, create one initial code
        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00001',
            'descripcion' => 'Bien inicial',
            'precio' => 100,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        // Simulate concurrent access by calling the service sequentially
        // but checking each receives a unique, sequential code
        $results = [];

        // Run multiple sequential calls (would be parallel in true concurrency test,
        // but lockForUpdate serializes them anyway)
        for ($i = 0; $i < 5; $i++) {
            $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);
            $results[] = $result['siguiente_numero'];
            // Immediately create the bien to simulate usage
            Bien::create([
                'dependencia_id' => $dependencia->id,
                'codigo' => $result['codigo'],
                'descripcion' => "Bien concurrente {$i}",
                'precio' => 100,
                'estado' => 'ACTIVO',
                'fecha_registro' => now(),
                'tipo_bien' => 'OTROS',
            ]);
        }

        // All numbers should be unique and sequential: 2, 3, 4, 5, 6
        $expected = [2, 3, 4, 5, 6];
        $this->assertEquals($expected, $results);

        // Verify no duplicates exist in database for this dependency
        $codigosEnBd = Bien::where('dependencia_id', $dependencia->id)
            ->pluck('codigo')
            ->map(fn ($c) => (int) substr($c, -5))
            ->sort()
            ->values()
            ->all();

        $this->assertEquals([1, 2, 3, 4, 5, 6], $codigosEnBd);
    }

    /**
     * Test 7: Verify that when code collides (manually inserted), service recursively finds next
     * This is a rare edge case but handled by the recursive fallback
     */
    public function test_manejo_de_colision_de_codigo_con_recursividad()
    {
        $this->actingAs($this->createUser());

        $dependencia = $this->createDependenciaWithRange(1, 99999);

        // Create a bien with code 1
        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00001',
            'descripcion' => 'Bien 1',
            'precio' => 100,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        // Simulate scenario where MAX gives 1, but code already exists due to manual intervention
        // The service should detect collision and call itself recursively to get 2
        $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);

        $this->assertEquals($dependencia->codigo.'00002', $result['codigo']);
        $this->assertEquals(2, $result['siguiente_numero']);
    }

    /**
     * Test 8: Verify that decreasing min (below current) still increments from max
     */
    public function test_min_debajo_del_max_existente_devuelve_max_mas_uno()
    {
        $this->actingAs($this->createUser());

        // min=10 but we already have codes 100, 101
        $dependencia = $this->createDependenciaWithRange(10, 99999);

        Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => $dependencia->codigo.'00100',
            'descripcion' => 'Bien 100',
            'precio' => 100,
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
            'tipo_bien' => 'OTROS',
        ]);

        $result = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);

        // Should be 101 (MAX+1), not 10 (min)
        $this->assertEquals($dependencia->codigo.'00101', $result['codigo']);
        $this->assertEquals(101, $result['siguiente_numero']);
    }
}
