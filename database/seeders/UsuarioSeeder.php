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
        $userRol  = DB::table('roles')->where('nombre', 'Usuario Normal')->first();

        // Administrador (Responsable Patrimonial Primario)
        DB::table('usuarios')->insert([
            'rol_id' => $adminRol->id,
            'cedula' => '3873777',
            'nombre' => 'ENRY',
            'apellido' => 'GÓMEZ MAIZ',
            'correo' => 'admin@inventario.com',
            'hash_password' => Hash::make('password'),
            'activo' => true,
            'is_admin' => true,
        ]);

        // Usuario normal (Responsable por uso)
        DB::table('usuarios')->insert([
            'rol_id' => $userRol->id,
            'cedula' => '20000001',
            'nombre' => 'Usuario',
            'apellido' => 'Responsable',
            'correo' => 'usuario@inventario.com',
            'hash_password' => Hash::make(value: 'password'),
            'activo' => true,
            'is_admin' => false,
        ]);
    }
}

