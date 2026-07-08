<?php

namespace Database\Seeders;

use App\Enums\EstadoBien;
use App\Enums\TipoBien as TB;
use App\Services\ActaDonacionService;
use App\Services\CodigoJerarquicoService;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Insertando datos de demo...');

        $faker = Faker::create('es_VE');

        // 1. Organismos
        $organismoNombres = [
            'Ministerio del Poder Popular para la Educación Universitaria',
            'SENIAT',
            'Ministerio del Poder Popular para la Educación',
        ];

        $organismoIds = [];
        $organismosExistentes = DB::table('organismos')->get();
        if ($organismosExistentes->isNotEmpty()) {
            foreach ($organismosExistentes as $organismo) {
                $organismoIds[$organismo->codigo] = $organismo->id;
            }
            $this->command->info('✓ Organismos existentes detectados, se usarán los registros actuales');
        } else {
            foreach ($organismoNombres as $nombre) {
                $codigo = CodigoJerarquicoService::generarCodigoOrganismo();
                DB::table('organismos')->updateOrInsert(['codigo' => $codigo], ['codigo' => $codigo, 'nombre' => $nombre]);
                $organismo = DB::table('organismos')->where('codigo', $codigo)->first();
                $organismoIds[$codigo] = $organismo->id;
            }
            $this->command->info('✓ Organismos creados');
        }

        // 2. Unidades administrativas
        $unidadNames = [
            'UPTOS "Clodosbaldo Russian"',
            'Dirección de Planificación',
            'Dirección de RRHH',
            'Dirección de Servicios',
        ];

        $unidadIds = [];
        $unidadesExistentes = DB::table('unidades_administradoras')->get();
        if ($unidadesExistentes->isNotEmpty()) {
            foreach ($unidadesExistentes as $unidad) {
                $unidadIds[$unidad->codigo] = $unidad->id;
            }
            $this->command->info('✓ Unidades administrativas existentes detectadas, se usarán los registros actuales');
        } else {
            foreach ($organismoIds as $organismoId) {
                foreach ($unidadNames as $name) {
                    $codigoUnidad = CodigoJerarquicoService::generarCodigoUnidad($organismoId);
                    DB::table('unidades_administradoras')->updateOrInsert(
                        ['codigo' => $codigoUnidad],
                        ['organismo_id' => $organismoId, 'codigo' => $codigoUnidad, 'nombre' => $name]
                    );
                    $unidad = DB::table('unidades_administradoras')->where('codigo', $codigoUnidad)->first();
                    $unidadIds[$codigoUnidad] = $unidad->id;
                }
            }
            $this->command->info('✓ Unidades administrativas creadas');
        }

        // 3. Tipos de responsable
        $tiposResponsable = [
            ['nombre' => 'Responsable Principal'],
            ['nombre' => 'Responsable Suplente'],
            ['nombre' => 'Custodio'],
        ];

        foreach ($tiposResponsable as $tr) {
            DB::table('tipos_responsables')->updateOrInsert(['nombre' => $tr['nombre']], $tr);
        }
        $this->command->info('✓ Tipos de responsable creados');

        // 4. Dependencias
        $dependenciaNames = [
            'Decanato',
            'Secretaría',
            'Informática',
            'Contabilidad',
            'Biblioteca',
            'Lab. Física',
            'Lab. Química',
            'Aula Magna',
            'Personal',
            'Mantenimiento',
        ];

        $dependenciaIds = [];
        $dependenciasExistentes = DB::table('dependencias')->get();
        if ($dependenciasExistentes->isNotEmpty()) {
            foreach ($dependenciasExistentes as $dependencia) {
                $dependenciaIds[$dependencia->codigo] = $dependencia->id;
            }
            $this->command->info('✓ Dependencias existentes detectadas, se usarán los registros actuales');
        } else {
            $primerUnidadId = $unidadIds[array_key_first($unidadIds)] ?? null;
            if ($primerUnidadId) {
                foreach ($dependenciaNames as $nombre) {
                    $codigoDependencia = CodigoJerarquicoService::generarCodigoDependencia($primerUnidadId);
                    DB::table('dependencias')->updateOrInsert(
                        ['codigo' => $codigoDependencia],
                        ['unidad_administradora_id' => $primerUnidadId, 'codigo' => $codigoDependencia, 'nombre' => $nombre]
                    );
                    $dependencia = DB::table('dependencias')->where('codigo', $codigoDependencia)->first();
                    $dependenciaIds[$codigoDependencia] = $dependencia->id;
                }
            }
            $this->command->info('✓ Dependencias creadas');
        }

        // 5. Responsables
        $tipoRespIds = DB::table('tipos_responsables')->pluck('id', 'nombre');
        $responsables = [
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-12345678', 'nombre' => 'Pedro Rodríguez', 'telefono' => '0414-1234567', 'correo' => 'pedro@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-23456789', 'nombre' => 'María Fernández', 'telefono' => '0414-2345678', 'correo' => 'maria@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-34567890', 'nombre' => 'José García', 'telefono' => '0414-3456789', 'correo' => 'jose@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-45678901', 'nombre' => 'Ana López', 'telefono' => '0414-4567890', 'correo' => 'ana@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-56789012', 'nombre' => 'Luis Martínez', 'telefono' => '0414-5678901', 'correo' => 'luis@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'] ?? null, 'cedula' => 'V-67890123', 'nombre' => 'Carmen Sánchez', 'telefono' => '0414-6789012', 'correo' => 'carmen@uptos.edu.ve'],
        ];

        foreach ($responsables as $responsable) {
            DB::table('responsables')->updateOrInsert(['cedula' => $responsable['cedula']], $responsable);
        }
        $this->command->info('✓ Responsables creados');

        // 6. Usuarios del sistema
        $roles = DB::table('roles')->pluck('id', 'nombre')->toArray();
        $usuarios = [
            ['cedula' => 'V-00000001', 'nombre' => 'Administrador', 'apellido' => 'Sistema', 'correo' => 'admin@inventario.com', 'is_admin' => true],
            ['cedula' => 'V-00000002', 'nombre' => 'Usuario', 'apellido' => 'Prueba', 'correo' => 'prueba@inventario.com', 'is_admin' => false],
            ['cedula' => 'V-10000001', 'nombre' => 'Juan', 'apellido' => 'Pérez', 'correo' => 'juan.perez@uptos.edu.ve', 'is_admin' => false],
            ['cedula' => 'V-10000002', 'nombre' => 'María', 'apellido' => 'González', 'correo' => 'maria.gonzalez@uptos.edu.ve', 'is_admin' => false],
            ['cedula' => 'V-10000003', 'nombre' => 'Carlos', 'apellido' => 'Rodríguez', 'correo' => 'carlos.rodriguez@uptos.edu.ve', 'is_admin' => false],
            ['cedula' => 'V-10000004', 'nombre' => 'Ana', 'apellido' => 'Martínez', 'correo' => 'ana.martinez@uptos.edu.ve', 'is_admin' => false],
            ['cedula' => 'V-10000005', 'nombre' => 'Pedro', 'apellido' => 'Sánchez', 'correo' => 'pedro.sanchez@uptos.edu.ve', 'is_admin' => true],
        ];

        foreach ($usuarios as $u) {
            DB::table('usuarios')->updateOrInsert(
                ['cedula' => $u['cedula']],
                array_merge($u, [
                    'rol_id' => $u['is_admin'] ? ($roles['Administrador'] ?? null) : ($roles['Usuario Normal'] ?? $roles['Usuario'] ?? null),
                    'hash_password' => Hash::make(config('app.demo_password', 'password123')),
                    'activo' => true,
                ])
            );
        }
        $this->command->info('✓ Usuarios del sistema creados');

        // 7. Bienes
        $adminUser = DB::table('usuarios')->where('is_admin', true)->first();
        $dependenciaList = array_values(DB::table('dependencias')->pluck('id')->toArray());

        $bienes = [
            ['descripcion' => 'Computadora Desktop Dell OptiPlex', 'precio' => 850.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Laptop HP ProBook 450', 'precio' => 920.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Monitor Samsung 24"', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Impresora Laser HP', 'precio' => 350.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Proyector Epson', 'precio' => 650.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Servidor Dell PowerEdge', 'precio' => 8500.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Router Cisco', 'precio' => 1200.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Tablet Samsung', 'precio' => 250.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Cámara IP Hikvision', 'precio' => 180.00, 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Escáner Fujitsu', 'precio' => 450.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null],
            ['descripcion' => 'Escritorio Ejecutivo', 'precio' => 350.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Silla Ejecutiva', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Estantería Metálica', 'precio' => 220.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Mesa de Reuniones', 'precio' => 450.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Archivador 4 gavetas', 'precio' => 280.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Pizarra Acrílica', 'precio' => 85.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Sofá de 3 puestos', 'precio' => 550.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Mesa de Computación', 'precio' => 195.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null],
            ['descripcion' => 'Toyota Corolla 2022', 'precio' => 25000.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => null],
            ['descripcion' => 'Ford Explorer 2021', 'precio' => 35000.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => null],
            ['descripcion' => 'Camioneta Chevrolet', 'precio' => 28000.00, 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => null],
            ['descripcion' => 'Motocicleta Yamaha', 'precio' => 4500.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => null],
            ['descripcion' => 'Aire Acondicionado 24000 BTU', 'precio' => 680.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Equipo de Sonido Sony', 'precio' => 320.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Generador 5000W', 'precio' => 1200.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Cortina de aluminio', 'precio' => 150.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Reflector LED 100W', 'precio' => 45.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Bomba de Agua 2HP', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Tanque de Agua 1000L', 'precio' => 250.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Extinguidor 10kg', 'precio' => 85.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => null],
            ['descripcion' => 'Computadora HP All-in-One', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null, 'es_donacion' => true, 'tipo_donante' => 'INSTITUCION', 'donante_nombre' => 'Fundación Digitel', 'donante_documento' => 'J-123456789', 'donante_direccion' => 'Av. Bolívar, Cumana, Estado Sucre'],
            ['descripcion' => 'Silla Ergonómica', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null, 'es_donacion' => true, 'tipo_donante' => 'PERSONA', 'donante_nombre' => 'Luis Alberto Marcano', 'donante_documento' => 'V-87654321', 'donante_direccion' => 'Calle Bolívar, Casa 45, Cumaná'],
            ['descripcion' => 'Impresora 3D Creality', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => null, 'es_donacion' => true, 'tipo_donante' => 'INSTITUCION', 'donante_nombre' => 'Instituto de Investigaciones UPTOS', 'donante_documento' => 'G-987654321', 'donante_direccion' => 'Zona Industrial, Cumaná, Sucre'],
            ['descripcion' => 'Set de 10 Sillas Plegables', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => null, 'es_donacion' => true, 'tipo_donante' => 'PERSONA', 'donante_nombre' => 'María Eugenia Rivas', 'donante_documento' => 'V-11223344', 'donante_direccion' => 'Urbanización Sucre, Cumaná'],
        ];

        foreach ($bienes as $bienData) {
            $estado = $bienData['estado'];
            $tipo = $bienData['tipo_bien'];
            $esDonacion = $bienData['es_donacion'] ?? false;
            $datosDonante = [
                'tipo_donante' => $bienData['tipo_donante'] ?? null,
                'donante_nombre' => $bienData['donante_nombre'] ?? null,
                'donante_documento' => $bienData['donante_documento'] ?? null,
                'donante_direccion' => $bienData['donante_direccion'] ?? null,
            ];
            $depId = $bienData['dependencia_id'] ?? null;
            if (! $depId || ! in_array($depId, $dependenciaList, true)) {
                $depId = $dependenciaList[array_rand($dependenciaList)] ?? null;
            }

            $bien = $bienData;
            unset($bien['estado'], $bien['tipo_bien'], $bien['dependencia_id'], $bien['es_donacion'], $bien['tipo_donante'], $bien['donante_nombre'], $bien['donante_documento'], $bien['donante_direccion']);

            $bien['estado'] = $estado->value;
            $bien['tipo_bien'] = $tipo->value;
            $bien['dependencia_id'] = $depId;
            $bien['fecha_registro'] = now()->subDays(rand(1, 365));
            $bien['caracteristicas'] = json_encode(['color' => 'Negro']);
            $bien['precio'] = $esDonacion ? 0 : $bien['precio'];
            $bien['es_donacion'] = $esDonacion;
            $bien['tipo_donante'] = $datosDonante['tipo_donante'];
            $bien['donante_nombre'] = $datosDonante['donante_nombre'];
            $bien['donante_documento'] = $datosDonante['donante_documento'];
            $bien['donante_direccion'] = $datosDonante['donante_direccion'];

            if (Schema::hasColumn('bienes', 'ubicacion')) {
                $bien['ubicacion'] = $faker->randomElement(['Decanato', 'Secretaría', 'Informática', 'Biblioteca', 'Mantenimiento', 'Estacionamiento']);
            }

            if ($depId) {
                $bien['codigo'] = CodigoJerarquicoService::generarCodigoBien($depId);
            } else {
                $bien['codigo'] = str_pad((string) rand(1000, 9999), 10, '0', STR_PAD_RIGHT);
            }

            DB::table('bienes')->updateOrInsert(['codigo' => $bien['codigo']], $bien);
        }
        $this->command->info('✓ Bienes creados');

        // 7.b Generar actas de donación cuando sea posible
        if (class_exists(ActaDonacionService::class) && $adminUser) {
            $donados = DB::table('bienes')->where('es_donacion', true)->get();
            $service = new ActaDonacionService();

            foreach ($donados as $bien) {
                try {
                    $actaPath = $service->generar(
                        \App\Models\Bien::find($bien->id),
                        [
                            'tipo_donante' => $bien->tipo_donante,
                            'donante_nombre' => $bien->donante_nombre,
                            'donante_documento' => $bien->donante_documento,
                            'donante_direccion' => $bien->donante_direccion,
                        ],
                        (object) $adminUser
                    );

                    DB::table('bienes')->where('id', $bien->id)->update(['acta_donacion' => $actaPath]);
                } catch (\Exception $e) {
                    $this->command->warn('⚠ No se pudo generar acta para bien '.$bien->codigo.': '.$e->getMessage());
                }
            }

            $this->command->info('✓ Actas de donación generadas: '.$donados->count());
        }

        $this->command->info('🎉 ¡Datos de demo insertados!');
        $this->command->info('📝 Credenciales: admin@inventario.com / '.config('app.demo_password', 'password123'));
    }
}
