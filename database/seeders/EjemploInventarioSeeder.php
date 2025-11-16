<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\Responsable;
use App\Models\TipoResponsable;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EjemploInventarioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Tipo de Responsable
        $tipoResponsable = TipoResponsable::firstOrCreate(
            ['nombre' => 'Responsable Patrimonial'],
            ['nombre' => 'Responsable Patrimonial']
        );

        // 2. Crear Organismo
        $organismo = Organismo::firstOrCreate(
            ['codigo' => 'MPPEU'],
            ['nombre' => 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA']
        );

        // 3. Crear Unidad Administradora
        $unidad = UnidadAdministradora::firstOrCreate(
            ['codigo' => '1430'],
            ['organismo_id' => $organismo->id, 'nombre' => 'UPTOS "CLODOSBALDO RUSSIAN"']
        );

        // 4. Crear Dependencia
        $dependencia = Dependencia::firstOrCreate(
            ['codigo' => '0', 'unidad_administradora_id' => $unidad->id],
            ['nombre' => 'DEPENDENCIA USUARIA']
        );

        // 5. Crear Responsable Patrimonial Primario
        $responsablePrimario = Responsable::firstOrCreate(
            ['cedula' => '3873777'],
            ['tipo_id' => $tipoResponsable->id, 'nombre' => 'ENRY GÓMEZ MAIZ', 'correo' => 'enry.gomez@edu.ve']
        );

        // Asignar el responsable a la dependencia (ahora la dependencia almacena el responsable)
        $dependencia->responsable_id = $responsablePrimario->id;
        $dependencia->save();

        // 6. Crear Usuario Administrador (usamos rol dinámico)
        $rolAdmin = \App\Models\Rol::firstOrCreate([
            'nombre' => 'Administrador',
        ], [
            'permisos' => [],
        ]);

        $usuarioAdmin = Usuario::firstOrCreate(
            ['correo' => 'enry.gomez@edu.ve'],
            ['rol_id' => $rolAdmin->id, 'cedula' => '3873777', 'nombre' => 'ENRY GÓMEZ MAIZ', 'hash_password' => Hash::make('Admin123'), 'activo' => true]
        );

        // 7. Crear algunos bienes de ejemplo
        $bienes = [
            [
                'codigo' => 'BN-2024-001',
                'descripcion' => 'Computadora de Escritorio Dell',
                'precio' => 12000.00,
                'fotografia' => 'https://picsum.photos/seed/bn2024001/640/480',
                'dependencia_id' => $dependencia->id,
                'ubicacion' => 'Oficina 101',
                'estado' => 'ACTIVO',
                'fecha_registro' => '2024-01-15',
            ],
            [
                'codigo' => 'BN-2024-002',
                'descripcion' => 'Impresora Multifunción HP',
                'precio' => 4500.00,
                'fotografia' => 'https://picsum.photos/seed/bn2024002/640/480',
                'dependencia_id' => $dependencia->id,
                'ubicacion' => 'Sala de Copias',
                'estado' => 'ACTIVO',
                'fecha_registro' => '2024-01-20',
            ],
            [
                'codigo' => 'BN-2024-003',
                'descripcion' => 'Escritorio de Metal',
                'precio' => 2800.00,
                'fotografia' => 'https://picsum.photos/seed/bn2024003/640/480',
                'dependencia_id' => $dependencia->id,
                'ubicacion' => 'Oficina 102',
                'estado' => 'ACTIVO',
                'fecha_registro' => '2024-02-01',
            ],
            [
                'codigo' => 'BN-2024-004',
                'descripcion' => 'Sillas ergonómicas (5 unidades)',
                'precio' => 1500.00,
                'fotografia' => 'https://picsum.photos/seed/bn2024004/640/480',
                'dependencia_id' => $dependencia->id,
                'ubicacion' => 'Sala de Reuniones',
                'estado' => 'ACTIVO',
                'fecha_registro' => '2024-02-10',
            ],
            [
                'codigo' => 'BN-2024-005',
                'descripcion' => 'Monitor LG 27 pulgadas',
                'precio' => 2200.50,
                'fotografia' => 'https://picsum.photos/seed/bn2024005/640/480',
                'dependencia_id' => $dependencia->id,
                'ubicacion' => 'Oficina 101',
                'estado' => 'ACTIVO',
                'fecha_registro' => '2024-02-15',
            ],
        ];

        foreach ($bienes as $bien) {
            Bien::create($bien);
        }

        $this->command->info('✓ Ejemplo de inventario creado exitosamente');
        $this->command->line('');
        $this->command->info('Datos creados:');
        $this->command->line('├─ Organismo: '.$organismo->nombre);
        $this->command->line('├─ Unidad: '.$unidad->nombre);
        $this->command->line('├─ Dependencia: '.$dependencia->nombre);
        $this->command->line('├─ Responsable: '.$responsablePrimario->nombre);
        $this->command->line('├─ Usuario: '.$usuarioAdmin->nombre);
        $this->command->line('└─ Bienes: '.count($bienes).' artículos');
        $this->command->line('');
        $this->command->info('Puede iniciar sesión con:');
        $this->command->line('Email: '.$usuarioAdmin->correo);
        $this->command->line('Contraseña: Admin123');
    }
}
