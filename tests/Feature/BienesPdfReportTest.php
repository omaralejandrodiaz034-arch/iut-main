<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\Rol;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BienesPdfReportTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): Usuario
    {
        $rol = Rol::create(['nombre' => 'TestRole']);

        $user = Usuario::create([
            'rol_id' => $rol->id,
            'cedula' => 'V-12345678',
            'nombre' => 'Test',
            'apellido' => 'User',
            'correo' => 'test.user@example.com',
            'hash_password' => bcrypt('secret'),
            'activo' => true,
            'is_admin' => true,
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_bienes_report_pdf_downloads_with_multiple_units_array_filter()
    {
        $this->actingUser();

        $organismo = Organismo::create(['codigo' => 'ORG1', 'nombre' => 'Organismo Test']);
        $unidad1 = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => 'U001',
            'nombre' => 'Unidad 1',
        ]);
        $unidad2 = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => 'U002',
            'nombre' => 'Unidad 2',
        ]);

        $dependencia1 = Dependencia::create([
            'unidad_administradora_id' => $unidad1->id,
            'codigo' => 'D001',
            'nombre' => 'Dependencia 1',
        ]);
        $dependencia2 = Dependencia::create([
            'unidad_administradora_id' => $unidad2->id,
            'codigo' => 'D002',
            'nombre' => 'Dependencia 2',
        ]);

        Bien::create([
            'dependencia_id' => $dependencia1->id,
            'codigo' => 'B001',
            'descripcion' => 'Bien unidad 1',
            'precio' => 100.00,
            'estado' => 'ACTIVO',
            'fecha_registro' => now()->format('Y-m-d'),
            'tipo_bien' => 'MOBILIARIO',
        ]);

        Bien::create([
            'dependencia_id' => $dependencia2->id,
            'codigo' => 'B002',
            'descripcion' => 'Bien unidad 2',
            'precio' => 200.00,
            'estado' => 'ACTIVO',
            'fecha_registro' => now()->format('Y-m-d'),
            'tipo_bien' => 'MOBILIARIO',
        ]);

        $response = $this->get(route('bienes.reporte', ['unidad_id' => [$unidad1->id, $unidad2->id]]));

        var_dump($response->headers->all());
        var_dump((string) $response->getContent());
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('.pdf', $response->headers->get('content-disposition'));
    }
}
