<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Importar el Facade Hash

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nos aseguramos de que exista el rol 'Administrador' y usamos su id dinámicamente
        $rolAdmin = \App\Models\Rol::firstOrCreate([
            'nombre' => 'Administrador',
        ], [
            'permisos' => [],
        ]);

        $rolAdminId = $rolAdmin->id;

        // Usamos updateOrCreate. Si el correo 'admin@example.com' ya existe, actualiza su hash_password y otros datos.
        Usuario::updateOrCreate(
            ['correo' => 'admin@example.com'], // Condiciones de búsqueda (únicas)
            [
                'rol_id' => $rolAdminId,
                'cedula' => 'V-12.345.678', // Formato de cédula compatible
                'nombre' => 'Admin',
                'apellido' => 'Sistema', // Asegura que este campo tenga un valor
                'hash_password' => Hash::make('admin123'),
                'activo' => true,
                'is_admin' => true, // Establecer explícitamente como administrador
            ]
        );
    }
}
