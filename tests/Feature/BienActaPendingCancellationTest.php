<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BienActaPendingCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): Usuario
    {
        $rol = \App\Models\Rol::create(['nombre' => 'TestRole']);

        return Usuario::create([
            'rol_id' => $rol->id,
            'cedula' => 'V-12345679',
            'nombre' => 'Test',
            'apellido' => 'Admin',
            'correo' => 'test.admin@example.com',
            'hash_password' => bcrypt('secret'),
            'activo' => true,
            'is_admin' => true,
        ]);
    }

    private function createDependencia(): Dependencia
    {
        $organismo = Organismo::create(['codigo' => '1000000001', 'nombre' => 'Org Test']);
        $unidad = UnidadAdministradora::create([
            'organismo_id' => $organismo->id,
            'codigo' => '1001000001',
            'nombre' => 'Unidad Test',
        ]);

        return Dependencia::create([
            'unidad_administradora_id' => $unidad->id,
            'codigo' => '1001010001',
            'nombre' => 'Dependencia Test',
        ]);
    }

    public function test_expired_pending_acta_cancels_desincorporation_and_restores_bien(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user);
        $dependencia = $this->createDependencia();

        $bien = Bien::create([
            'dependencia_id' => $dependencia->id,
            'codigo' => '1001010002',
            'descripcion' => 'Bien de prueba',
            'precio' => 150.00,
            'estado' => 'ACTIVO',
            'fecha_registro' => now()->toDateString(),
        ]);

        $this->post(route('bienes.desincorporar', $bien), [
            'motivo' => 'Motivo de desincorporación suficientemente detallado para la prueba.',
        ]);

        $movimiento = $bien->fresh()->movimientos()->latest('fecha')->first();
        $this->assertNotNull($movimiento);

        $movimiento->update([
            'acta_estado' => 'PENDIENTE_FIRMA',
            'fecha_limite_acta' => now()->subDay(),
        ]);

        $response = $this->get(route('bienes.show', $bien));

        $response->assertOk();
        $bien->refresh();

        $this->assertSame('ACTIVO', $bien->estado->value);
        $this->assertNull($bien->fresh()->desincorporado()->first());
        $this->assertDatabaseHas('movimientos', [
            'bien_id' => $bien->id,
            'tipo' => 'CANCELACION_ACTA',
        ]);
        $this->assertDatabaseHas('movimientos', [
            'bien_id' => $bien->id,
            'acta_estado' => 'CANCELADA',
        ]);
    }
}
