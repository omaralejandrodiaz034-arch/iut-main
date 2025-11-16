<?php

namespace Database\Seeders;

use App\Enums\EstadoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\Responsable;
use App\Models\Rol;
use App\Models\TipoResponsable;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MassiveInventorySeeder extends Seeder
{
    private int $organismosTarget = 5;

    private int $unidadesTarget = 15;

    private int $dependenciasTarget = 50;

    private int $bienesTarget = 1200;

    private int $usuariosTarget = 520;

    private int $maxAdministradores = 10;

    private int $responsablesTarget = 70;

    public function run(): void
    {
        $faker = FakerFactory::create('es_VE');

        Schema::disableForeignKeyConstraints();

        foreach ([
            'historial_movimientos',
            'movimientos',
            'bienes',
            'dependencias',
            'unidades_administradoras',
            'organismos',
            'responsables',
            'tipos_responsables',
            'reportes',
            'auditoria',
            'usuarios',
        ] as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        // Tipos de responsables de referencia.
        $tipoNombres = [
            'Responsable Patrimonial',
            'Supervisor General',
            'Coordinador de Unidad',
            'Administrador de Dependencia',
            'Auditor Interno',
        ];

        $tipoResponsables = collect();
        foreach ($tipoNombres as $nombre) {
            $tipoResponsables->push(
                TipoResponsable::create(['nombre' => $nombre])
            );
        }

        // Crear organismos.
        $organismos = collect();
        for ($i = 1; $i <= $this->organismosTarget; $i++) {
            $organismos->push(
                Organismo::create([
                    'codigo' => sprintf('ORG-%02d%s', $i, Str::upper(Str::random(2))),
                    'nombre' => Str::upper($faker->unique()->company()),
                ])
            );
        }
        $faker->unique(true);

        // Crear unidades administradoras.
        $unidades = collect();
        for ($i = 1; $i <= $this->unidadesTarget; $i++) {
            $organismo = $organismos->random();

            $unidades->push(
                UnidadAdministradora::create([
                    'organismo_id' => $organismo->id,
                    'codigo' => sprintf('UA-%04d', $i),
                    'nombre' => Str::title($faker->unique()->company()),
                ])
            );
        }
        $faker->unique(true);

        // Crear responsables.
        $cedulasResponsables = [];
        $responsables = collect();
        for ($i = 1; $i <= $this->responsablesTarget; $i++) {
            $correoResponsable = $faker->unique()->safeEmail();

            if (! $faker->boolean(70)) {
                $correoResponsable = null;
            }

            $responsables->push(
                Responsable::create([
                    'tipo_id' => $tipoResponsables->random()->id,
                    'cedula' => $this->generateCedula($faker, $cedulasResponsables),
                    'nombre' => $faker->name(),
                    'correo' => $correoResponsable,
                    'telefono' => $faker->optional(0.5)->phoneNumber(),
                ])
            );
        }
        $faker->unique(true);

        // Crear dependencias.
        $dependencias = collect();
        for ($i = 1; $i <= $this->dependenciasTarget; $i++) {
            $unidad = $unidades->random();
            $dependencias->push(
                Dependencia::create([
                    'unidad_administradora_id' => $unidad->id,
                    'codigo' => sprintf('DEP-%04d', $i),
                    'nombre' => Str::title($faker->unique()->sentence(3)),
                    'responsable_id' => $responsables->random()->id,
                ])
            );
        }
        $faker->unique(true);

        // Crear bienes.
        $estadoValues = array_map(fn (EstadoBien $estado) => $estado->value, EstadoBien::cases());
        $bienesData = [];
        for ($i = 1; $i <= $this->bienesTarget; $i++) {
            $dependencia = $dependencias->random();
            $fechaRegistro = $faker->dateTimeBetween('-3 years', 'now');

            $bienesData[] = [
                'dependencia_id' => $dependencia->id,
                'codigo' => sprintf('BIEN-%06d', $i),
                'descripcion' => Str::title($faker->unique()->words(4, true)),
                'precio' => $faker->randomFloat(2, 500, 150000),
                'fotografia' => 'https://picsum.photos/seed/bien'.$i.'/640/480',
                'ubicacion' => $faker->city().' - Área '.$faker->numberBetween(100, 999),
                'estado' => Arr::random($estadoValues),
                'fecha_registro' => $fechaRegistro,
                'created_at' => $fechaRegistro,
                'updated_at' => $fechaRegistro,
            ];

            if (count($bienesData) === 250) {
                Bien::insert($bienesData);
                $bienesData = [];
            }
        }

        if (! empty($bienesData)) {
            Bien::insert($bienesData);
        }
        $faker->unique(true);

        // Roles
        $rolAdmin = Rol::where('nombre', 'Administrador')->firstOrFail();
        $rolNormal = Rol::where('nombre', 'Usuario Normal')->firstOrFail();

        // Usuarios
        $usuariosData = [];
        $cedulasUsuarios = [];
        $correosRegistrados = collect(['admin@example.com']);

        $now = now();

        // Administrador principal conocido
        $usuariosData[] = [
            'rol_id' => $rolAdmin->id,
            'cedula' => $this->generateCedula($faker, $cedulasUsuarios, '3873777'),
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'correo' => 'admin@example.com',
            'hash_password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
            'activo' => true,
            'is_admin' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $adminsCreados = 1;
        $usuariosARestar = $this->usuariosTarget - 1;

        for ($i = 1; $i <= $usuariosARestar; $i++) {
            $esAdmin = $adminsCreados < $this->maxAdministradores && $faker->boolean(8);
            if ($esAdmin) {
                $adminsCreados++;
            }

            $nombre = $faker->firstName();
            $apellido = $faker->lastName();
            $correo = $faker->unique()->safeEmail();

            // Evitar colisión con admin@example.com
            while ($correosRegistrados->contains($correo)) {
                $correo = $faker->unique()->safeEmail();
            }
            $correosRegistrados->push($correo);

            $fechaAlta = $faker->dateTimeBetween('-2 years', 'now');

            $usuariosData[] = [
                'rol_id' => $esAdmin ? $rolAdmin->id : $rolNormal->id,
                'cedula' => $this->generateCedula($faker, $cedulasUsuarios),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'hash_password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'activo' => $faker->boolean(92),
                'is_admin' => $esAdmin,
                'created_at' => $fechaAlta,
                'updated_at' => $fechaAlta,
            ];
        }

        foreach (array_chunk($usuariosData, 200) as $chunk) {
            Usuario::insert($chunk);
        }
    }

    private function generateCedula($faker, array &$registradas, ?string $prefijada = null): string
    {
        if ($prefijada !== null) {
            $numero = (int) $prefijada;
            $registradas[] = $numero;

            $prefijo = (int) floor($numero / 1_000_000);
            $medio = (int) floor(($numero % 1_000_000) / 1_000);
            $ultimo = $numero % 1_000;

            return sprintf('V-%02d.%03d.%03d', $prefijo, $medio, $ultimo);
        }

        do {
            $numero = $faker->numberBetween(5_000_000, 45_000_000);
        } while (in_array($numero, $registradas, true));

        $registradas[] = $numero;

        $prefijo = (int) floor($numero / 1_000_000);
        $medio = (int) floor(($numero % 1_000_000) / 1_000);
        $ultimo = $numero % 1_000;

        return sprintf('V-%02d.%03d.%03d', $prefijo, $medio, $ultimo);
    }
}
