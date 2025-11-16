<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {--cedula= : Cédula del administrador}
                            {--nombre= : Nombre del administrador}
                            {--correo= : Correo del administrador}
                            {--password= : Contraseña del administrador}';

    protected $description = 'Crear un nuevo usuario administrador con permisos de gestión y eliminación';

    public function handle()
    {
        // Obtener datos del usuario administrador
        $cedula = $this->option('cedula') ?: $this->ask('Cédula del administrador');
        $nombre = $this->option('nombre') ?: $this->ask('Nombre del administrador');
        $correo = $this->option('correo') ?: $this->ask('Correo del administrador');
        $password = $this->option('password') ?: $this->secret('Contraseña del administrador');

        // Normalizar cédula a formato V-XX.XXX.XXX para consistencia
        $digits = preg_replace('/\D/', '', strtoupper($cedula));
        $digits = substr($digits, 0, 8);
        $cedula = 'V-'.substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3);

        // Validar que no exista un usuario con la misma cédula o correo
        if (Usuario::where('cedula', $cedula)->exists()) {
            $this->error("Ya existe un usuario con la cédula: {$cedula}");

            return 1;
        }

        if (Usuario::where('correo', $correo)->exists()) {
            $this->error("Ya existe un usuario con el correo: {$correo}");

            return 1;
        }

        // Obtener o crear el rol de administrador
        $rolAdmin = \App\Models\Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['permisos' => json_encode([
                'crear_usuarios' => true,
                'crear_administradores' => true,
                'eliminar_datos' => true,
                'ver_reportes' => true,
                'gestionar_roles' => true,
            ])]
        );

        // Crear el usuario administrador
        $admin = Usuario::create([
            'rol_id' => $rolAdmin->id,
            'cedula' => $cedula,
            'nombre' => $nombre,
            'correo' => $correo,
            'hash_password' => Hash::make($password),
            'activo' => true,
            'is_admin' => true,
        ]);

        $this->info("\n✅ Administrador creado exitosamente!");
        $this->line("Cédula: {$admin->cedula}");
        $this->line("Nombre: {$admin->nombre}");
        $this->line("Correo: {$admin->correo}");
        $this->line("ID: {$admin->id}");

        return 0;
    }
}
