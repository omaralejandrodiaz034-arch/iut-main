<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Usuario;

class BienesTypeCreationTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser()
    {
        // Create a minimal role and user and authenticate for protected routes
        $rol = \App\Models\Rol::create(['nombre' => 'TestRole']);
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

    public function test_create_electronico_creates_related_record()
    {
        $this->actingUser();

        // Ensure a unidad_administradora exists for the dependencia FK
        $organismo = \App\Models\Organismo::create(['codigo' => 'ORG1', 'nombre' => 'Org Test']);
        $unidad = \App\Models\UnidadAdministradora::create(['organismo_id' => $organismo->id, 'codigo' => 'U001', 'nombre' => 'Unidad Test']);
        $dependencia = Dependencia::create(['unidad_administradora_id' => $unidad->id, 'codigo' => 'D001', 'nombre' => 'Dependencia Test']);

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo' => '00000010',
            'descripcion' => 'Test Electronico',
            'precio' => 100.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'ELECTRONICO',
            'fecha_registro' => now()->format('Y-m-d'),
            'serial' => 'SN12345',
            'procesador' => 'i7',
            'memoria' => '8GB'
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '00000010', 'descripcion' => 'Test Electronico']);
        $bien = Bien::where('codigo', '00000010')->first();
        $this->assertDatabaseHas('bienes_electronicos', ['bien_id' => $bien->id, 'serial' => 'SN12345']);
    }

    public function test_create_vehiculo_creates_related_record()
    {
        $this->actingUser();
        $organismo = \App\Models\Organismo::create(['codigo' => 'ORG2', 'nombre' => 'Org Test']);
        $unidad = \App\Models\UnidadAdministradora::create(['organismo_id' => $organismo->id, 'codigo' => 'U002', 'nombre' => 'Unidad Test']);
        $dependencia = Dependencia::create(['unidad_administradora_id' => $unidad->id, 'codigo' => 'D002', 'nombre' => 'Dependencia Test']);

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo' => '00000011',
            'descripcion' => 'Test Vehiculo',
            'precio' => 5000.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'VEHICULO',
            'fecha_registro' => now()->format('Y-m-d'),
            'marca' => 'Toyota',
            'placa' => 'ABC123'
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '00000011']);
        $bien = Bien::where('codigo', '00000011')->first();
        $this->assertDatabaseHas('bienes_vehiculos', ['bien_id' => $bien->id, 'marca' => 'Toyota']);
    }

    public function test_create_mobiliario_creates_related_record()
    {
        $this->actingUser();
        $organismo = \App\Models\Organismo::create(['codigo' => 'ORG3', 'nombre' => 'Org Test']);
        $unidad = \App\Models\UnidadAdministradora::create(['organismo_id' => $organismo->id, 'codigo' => 'U003', 'nombre' => 'Unidad Test']);
        $dependencia = Dependencia::create(['unidad_administradora_id' => $unidad->id, 'codigo' => 'D003', 'nombre' => 'Dependencia Test']);

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo' => '00000012',
            'descripcion' => 'Test Mobiliario',
            'precio' => 200.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'MOBILIARIO',
            'fecha_registro' => now()->format('Y-m-d'),
            'material' => 'Madera',
            'cantidad_piezas' => 2
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '00000012']);
        $bien = Bien::where('codigo', '00000012')->first();
        $this->assertDatabaseHas('bienes_mobiliarios', ['bien_id' => $bien->id, 'material' => 'Madera']);
    }

    public function test_create_otros_creates_related_record()
    {
        $this->actingUser();
        $organismo = \App\Models\Organismo::create(['codigo' => 'ORG4', 'nombre' => 'Org Test']);
        $unidad = \App\Models\UnidadAdministradora::create(['organismo_id' => $organismo->id, 'codigo' => 'U004', 'nombre' => 'Unidad Test']);
        $dependencia = Dependencia::create(['unidad_administradora_id' => $unidad->id, 'codigo' => 'D004', 'nombre' => 'Dependencia Test']);

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo' => '00000013',
            'descripcion' => 'Test Otros',
            'precio' => 50.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'OTROS',
            'fecha_registro' => now()->format('Y-m-d'),
            'especificaciones' => 'Caja pequeña',
            'cantidad' => 5
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '00000013']);
        $bien = Bien::where('codigo', '00000013')->first();
        $this->assertDatabaseHas('bienes_otros', ['bien_id' => $bien->id, 'cantidad' => 5]);
    }
}
