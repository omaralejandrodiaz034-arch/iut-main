<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BienesTypeCreationTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): Usuario
    {
        $rol = \App\Models\Rol::create(['nombre' => 'TestRole']);

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

    private function createDependencia(): Dependencia
    {
        $organismo = Organismo::create(['codigo' => '1000000000', 'nombre' => 'Org Test']);
        $unidad = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => '1000100000',
            'nombre' => 'Unidad Test',
        ]);

        return Dependencia::create([
            'unidad_administradora_id' => $unidad->id,
            'codigo' => '1000100100',
            'nombre' => 'Dependencia Test',
        ]);
    }

    public function test_create_electronico_creates_related_record(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user);
        $dependencia = $this->createDependencia();

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo_secuencial' => '01',
            'codigo' => '1000100101',
            'descripcion' => 'Test Electronico',
            'precio' => 100.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'ELECTRONICO',
            'fecha_registro' => now()->format('Y-m-d'),
            'serial' => 'SN12345',
            'procesador' => 'i7',
            'memoria' => '8GB',
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '1000100101', 'descripcion' => 'Test Electronico']);
        $bien = Bien::where('codigo', '1000100101')->first();
        $this->assertDatabaseHas('bienes_electronicos', ['bien_id' => $bien->id, 'serial' => 'SN12345']);
    }

    public function test_create_vehiculo_creates_related_record(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user);
        $dependencia = $this->createDependencia();

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo_secuencial' => '02',
            'codigo' => '1000100102',
            'descripcion' => 'Test Vehiculo',
            'precio' => 5000.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'VEHICULO',
            'fecha_registro' => now()->format('Y-m-d'),
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => '2020',
            'placa' => 'ABC123',
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '1000100102']);
        $bien = Bien::where('codigo', '1000100102')->first();
        $this->assertDatabaseHas('bienes_vehiculos', ['bien_id' => $bien->id, 'marca' => 'Toyota']);
    }

    public function test_create_mobiliario_creates_related_record(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user);
        $dependencia = $this->createDependencia();

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo_secuencial' => '03',
            'codigo' => '1000100103',
            'descripcion' => 'Test Mobiliario',
            'precio' => 200.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'MOBILIARIO',
            'fecha_registro' => now()->format('Y-m-d'),
            'material' => 'Madera',
            'cantidad_piezas' => 2,
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '1000100103']);
        $bien = Bien::where('codigo', '1000100103')->first();
        $this->assertDatabaseHas('bienes_mobiliarios', ['bien_id' => $bien->id, 'material' => 'Madera']);
    }

    public function test_create_otros_creates_related_record(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user);
        $dependencia = $this->createDependencia();

        $payload = [
            'dependencia_id' => $dependencia->id,
            'codigo_secuencial' => '04',
            'codigo' => '1000100104',
            'descripcion' => 'Test Otros',
            'precio' => 50.00,
            'estado' => 'ACTIVO',
            'tipo_bien' => 'OTROS',
            'fecha_registro' => now()->format('Y-m-d'),
            'especificaciones' => 'Caja pequeña',
            'cantidad' => 5,
        ];

        $resp = $this->post(route('bienes.store'), $payload);
        $resp->assertRedirect(route('bienes.index'));

        $this->assertDatabaseHas('bienes', ['codigo' => '1000100104']);
        $bien = Bien::where('codigo', '1000100104')->first();
        $this->assertDatabaseHas('bienes_otros', ['bien_id' => $bien->id, 'cantidad' => 5]);
    }
}
