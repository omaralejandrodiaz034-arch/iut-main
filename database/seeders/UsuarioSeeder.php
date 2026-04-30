<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $adminRol = DB::table('roles')->where('nombre', 'Administrador')->first();
        $userRol = DB::table('roles')->where('nombre', 'Usuario Normal')->first();

        // Administrador
        DB::table('usuarios')->updateOrInsert(
            ['cedula' => '3873777'],
            [
                'rol_id' => $adminRol->id,
                'nombre' => 'ENRY',
                'apellido' => 'GÓMEZ MAIZ',
                'correo' => 'admin@inventario.com',
                'hash_password' => Hash::make('password'),
                'activo' => true,
                'is_admin' => true,
            ]
        );

        // Usuario normal
        DB::table('usuarios')->updateOrInsert(
            ['cedula' => '20000001'],
            [
                'rol_id' => $userRol->id,
                'nombre' => 'Usuario',
                'apellido' => 'Responsable',
                'correo' => 'usuario@inventario.com',
                'hash_password' => Hash::make('password'),
                'activo' => true,
                'is_admin' => false,
            ]
        );

        // Más usuarios de ejemplo
        $usuariosEjemplo = [
            ['cedula' => '30000001', 'nombre' => 'Juan', 'apellido' => 'Pérez', 'correo' => 'juan@example.com', 'is_admin' => false],
            ['cedula' => '30000002', 'nombre' => 'Ana', 'apellido' => 'García', 'correo' => 'ana@example.com', 'is_admin' => false],
            ['cedula' => '30000003', 'nombre' => 'Carlos', 'apellido' => 'López', 'correo' => 'carlos@example.com', 'is_admin' => true],
        ];

        foreach ($usuariosEjemplo as $user) {
            DB::table('usuarios')->updateOrInsert(
                ['cedula' => $user['cedula']],
                [
                    'rol_id' => $user['is_admin'] ? $adminRol->id : $userRol->id,
                    'nombre' => $user['nombre'],
                    'apellido' => $user['apellido'],
                    'correo' => $user['correo'],
                    'hash_password' => Hash::make('password'),
                    'activo' => true,
                    'is_admin' => $user['is_admin'],
                ]
            );
        }
    }
}
