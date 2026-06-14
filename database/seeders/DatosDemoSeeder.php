<?php

namespace Database\Seeders;

use App\Enums\EstadoBien;
use App\Enums\TipoBien as TB;
use App\Services\ActaDonacionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Insertando datos de demo...');

        // 1. Organismos - Formato: X.00.000.0000 (10 dígitos)
        $organismos = [
            ['codigo' => '1000000000', 'nombre' => 'Ministerio del Poder Popular para la Educación Universitaria'],
            ['codigo' => '2000000000', 'nombre' => 'SENIAT'],
            ['codigo' => '3000000000', 'nombre' => 'Ministerio del Poder Popular para la Educación'],
        ];

        foreach ($organismos as $Org) {
            DB::table('organismos')->updateOrInsert(['codigo' => $Org['codigo']], $Org);
        }
        $organismoIds = DB::table('organismos')->pluck('id', 'codigo')->toArray();
        $this->command->info('✓ Organismos creados');

        // 2. Unidades Administradoras - Formato: X.XX.000.0000 (10 dígitos)
        $unidades = [
            ['organismo_id' => $organismoIds['1000000000'], 'codigo' => '1010000000', 'nombre' => 'UPTOS "Clodosbaldo Russian"'],
            ['organismo_id' => $organismoIds['1000000000'], 'codigo' => '1020000000', 'nombre' => 'Dirección de Planificación'],
            ['organismo_id' => $organismoIds['1000000000'], 'codigo' => '1030000000', 'nombre' => 'Dirección de RRHH'],
            ['organismo_id' => $organismoIds['1000000000'], 'codigo' => '1040000000', 'nombre' => 'Dirección de Servicios'],
            ['organismo_id' => $organismoIds['2000000000'], 'codigo' => '2010000000', 'nombre' => 'Gerencia de Bienes'],
            ['organismo_id' => $organismoIds['3000000000'], 'codigo' => '3010000000', 'nombre' => 'Coordinación de Admisión'],
        ];

        foreach ($unidades as $u) {
            DB::table('unidades_administradoras')->updateOrInsert(['codigo' => $u['codigo']], $u);
        }
        $unidadIds = DB::table('unidades_administradoras')->pluck('id', 'codigo')->toArray();
        $this->command->info('✓ Unidades administrativas creadas');

        // 3. Tipos de Responsable (solo tiene nombre)
        $tiposResponsable = [
            ['nombre' => 'Responsable Principal'],
            ['nombre' => 'Responsable Suplente'],
            ['nombre' => 'Custodio'],
        ];

        foreach ($tiposResponsable as $tr) {
            DB::table('tipos_responsables')->updateOrInsert(['nombre' => $tr['nombre']], $tr);
        }
        $this->command->info('✓ Tipos de responsable creados');

        // 4. Dependencias - Formato: X.XX.XXX.0000 (10 dígitos)
        $dependencias = [
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010010000', 'nombre' => 'Decanato'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010020000', 'nombre' => 'Secretaría'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010030000', 'nombre' => 'Informática'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010040000', 'nombre' => 'Contabilidad'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010050000', 'nombre' => 'Biblioteca'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010060000', 'nombre' => 'Lab. Física'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010070000', 'nombre' => 'Lab. Química'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010080000', 'nombre' => 'Aula Magna'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010090000', 'nombre' => 'Personal'],
            ['unidad_administradora_id' => $unidadIds['1010000000'], 'codigo' => '1010100000', 'nombre' => 'Mantenimiento'],
            ['unidad_administradora_id' => $unidadIds['1020000000'], 'codigo' => '1020010000', 'nombre' => 'Planificación'],
            ['unidad_administradora_id' => $unidadIds['1030000000'], 'codigo' => '1030010000', 'nombre' => 'Nómina'],
        ];

        foreach ($dependencias as $d) {
            DB::table('dependencias')->updateOrInsert(['codigo' => $d['codigo']], $d);
        }
        $dependenciaIds = DB::table('dependencias')->pluck('id', 'codigo')->toArray();
        $this->command->info('✓ Dependencias creadas');

        // 5. Responsables (solo tiene: id, tipo_id, cedula, nombre, correo, telefono)
        $tipoRespIds = DB::table('tipos_responsables')->pluck('id', 'nombre');

        $responsables = [
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-12345678', 'nombre' => 'Pedro Rodríguez', 'telefono' => '0414-1234567', 'correo' => 'pedro@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-23456789', 'nombre' => 'María Fernández', 'telefono' => '0414-2345678', 'correo' => 'maria@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-34567890', 'nombre' => 'José García', 'telefono' => '0414-3456789', 'correo' => 'jose@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-45678901', 'nombre' => 'Ana López', 'telefono' => '0414-4567890', 'correo' => 'ana@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-56789012', 'nombre' => 'Luis Martínez', 'telefono' => '0414-5678901', 'correo' => 'luis@uptos.edu.ve'],
            ['tipo_id' => $tipoRespIds['Responsable Principal'], 'cedula' => 'V-67890123', 'nombre' => 'Carmen Sánchez', 'telefono' => '0414-6789012', 'correo' => 'carmen@uptos.edu.ve'],
        ];

        foreach ($responsables as $r) {
            DB::table('responsables')->updateOrInsert(['cedula' => $r['cedula']], $r);
        }
        $this->command->info('✓ Responsables creados');

        // Get dependencia IDs for bienes
        $dependenciaIds = DB::table('dependencias')->pluck('id', 'codigo')->toArray();

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
                    'rol_id' => $u['is_admin'] ? $roles['Administrador'] : $roles['Usuario Normal'],
                    'hash_password' => Hash::make(config('app.demo_password', 'password123')),
                    'activo' => true,
                ])
            );
        }
        $this->command->info('✓ Usuarios del sistema creados');

        // 7. Bienes - con algunos donados para datos realistas
        $usuarioIds = DB::table('usuarios')->pluck('id', 'cedula')->toArray();
        $adminUser = DB::table('usuarios')->where('is_admin', true)->first();

        $bienes = [
            // Electrónicos
            ['descripcion' => 'Computadora Desktop Dell OptiPlex', 'precio' => 850.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Laptop HP ProBook 450', 'precio' => 920.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010020000']],
            ['descripcion' => 'Monitor Samsung 24"', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010030000']],
            ['descripcion' => 'Impresora Laser HP', 'precio' => 350.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010040000']],
            ['descripcion' => 'Proyector Epson', 'precio' => 650.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010080000']],
            ['descripcion' => 'Servidor Dell PowerEdge', 'precio' => 8500.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010030000']],
            ['descripcion' => 'Router Cisco', 'precio' => 1200.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010030000']],
            ['descripcion' => 'Tablet Samsung', 'precio' => 250.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010050000']],
            ['descripcion' => 'Cámara IP Hikvision', 'precio' => 180.00, 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Escáner Fujitsu', 'precio' => 450.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010040000']],

            // Mobiliarios
            ['descripcion' => 'Escritorio Ejecutivo', 'precio' => 350.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Silla Ejecutiva', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Estantería Metálica', 'precio' => 220.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010050000']],
            ['descripcion' => 'Mesa de Reuniones', 'precio' => 450.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010020000']],
            ['descripcion' => 'Archivador 4 gavetas', 'precio' => 280.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010040000']],
            ['descripcion' => 'Pizarra Acrílica', 'precio' => 85.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010060000']],
            ['descripcion' => 'Sofá de 3 puestos', 'precio' => 550.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Mesa de Computación', 'precio' => 195.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010030000']],

            // Vehículos
            ['descripcion' => 'Toyota Corolla 2022', 'precio' => 25000.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Ford Explorer 2021', 'precio' => 35000.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Camioneta Chevrolet', 'precio' => 28000.00, 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['1010100000']],
            ['descripcion' => 'Motocicleta Yamaha', 'precio' => 4500.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['1010100000']],

            // Otros
            ['descripcion' => 'Aire Acondicionado 24000 BTU', 'precio' => 680.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010010000']],
            ['descripcion' => 'Equipo de Sonido Sony', 'precio' => 320.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010080000']],
            ['descripcion' => 'Generador 5000W', 'precio' => 1200.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010100000']],
            ['descripcion' => 'Cortina de aluminio', 'precio' => 150.00, 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010020000']],
            ['descripcion' => 'Reflector LED 100W', 'precio' => 45.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010100000']],
            ['descripcion' => 'Bomba de Agua 2HP', 'precio' => 180.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010100000']],
            ['descripcion' => 'Tanque de Agua 1000L', 'precio' => 250.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010100000']],
            ['descripcion' => 'Extinguidor 10kg', 'precio' => 85.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['1010010000']],

            // Donados - nuevos registros realistas
            ['descripcion' => 'Computadora HP All-in-One', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010030000'], 'es_donacion' => true, 'tipo_donante' => 'INSTITUCION', 'donante_nombre' => 'Fundación Digitel', 'donante_documento' => 'J-123456789', 'donante_direccion' => 'Av. Bolívar, Cumana, Estado Sucre'],
            ['descripcion' => 'Silla Ergonómica', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010010000'], 'es_donacion' => true, 'tipo_donante' => 'PERSONA', 'donante_nombre' => 'Luis Alberto Marcano', 'donante_documento' => 'V-87654321', 'donante_direccion' => 'Calle Bolívar, Casa 45, Cumaná'],
            ['descripcion' => 'Impresora 3D Creality', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['1010060000'], 'es_donacion' => true, 'tipo_donante' => 'INSTITUCION', 'donante_nombre' => 'Instituto de Investigaciones UPTOS', 'donante_documento' => 'G-987654321', 'donante_direccion' => 'Zona Industrial, Cumaná, Sucre'],
            ['descripcion' => 'Set de 10 Sillas Plegables', 'precio' => 0.00, 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['1010080000'], 'es_donacion' => true, 'tipo_donante' => 'PERSONA', 'donante_nombre' => 'María Eugenia Rivas', 'donante_documento' => 'V-11223344', 'donante_direccion' => 'Urbanización Sucre, Cumaná'],
        ];

        $bienIds = [];
        foreach ($bienes as $bien) {
            $estado = $bien['estado'];
            $tipo = $bien['tipo_bien'];
            $esDonacion = $bien['es_donacion'] ?? false;
            $datosDonante = [
                'tipo_donante' => $bien['tipo_donante'] ?? null,
                'donante_nombre' => $bien['donante_nombre'] ?? null,
                'donante_documento' => $bien['donante_documento'] ?? null,
                'donante_direccion' => $bien['donante_direccion'] ?? null,
            ];
            $depId = $bien['dependencia_id'];

            unset($bien['estado'], $bien['tipo_bien'], $bien['dependencia_id'], $bien['es_donacion'], $bien['tipo_donante'], $bien['donante_nombre'], $bien['donante_documento'], $bien['donante_direccion']);

            $bien['estado'] = $estado->value;
            $bien['tipo_bien'] = $tipo->value;
            $bien['dependencia_id'] = $depId;
            $bien['fecha_registro'] = now()->subDays(rand(1, 365));
            $bien['caracteristicas'] = json_encode(['color' => 'Negro']);
            $bien['es_donacion'] = $esDonacion;
            $bien['tipo_donante'] = $datosDonante['tipo_donante'];
            $bien['donante_nombre'] = $datosDonante['donante_nombre'];
            $bien['donante_documento'] = $datosDonante['donante_documento'];
            $bien['donante_direccion'] = $datosDonante['donante_direccion'];
            $bien['precio'] = $esDonacion ? 0 : $bien['precio'];

            $dep = DB::table('dependencias')->where('id', $depId)->first();
            $prefijo = substr($dep->codigo, 0, 6);
            $existingCount = DB::table('bienes')->where('dependencia_id', $depId)->where('codigo', 'like', $prefijo.'%')->count() + 1;
            $bien['codigo'] = $prefijo . str_pad((string) $existingCount, 4, '0', STR_PAD_LEFT);

            DB::table('bienes')->updateOrInsert(['codigo' => $bien['codigo']], $bien);
            $bienIds[] = DB::table('bienes')->where('codigo', $bien['codigo'])->first()->id;
        }
        $this->command->info('✓ Bienes creados ('.count($bienIds).')');

        // Generar actas de donación para bienes donados
        if (class_exists(ActaDonacionService::class)) {
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
                        $adminUser
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
