<?php

namespace Database\Seeders;

use App\Enums\EstadoBien;
use App\Enums\TipoBien as TB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Insertando datos de demo...');

        // 1. Organismos
        $organismos = [
            ['codigo' => 'MPPEU-001', 'nombre' => 'Ministerio del Poder Popular para la Educación Universitaria'],
            ['codigo' => 'MPPEU-002', 'nombre' => 'SENIAT'],
            ['codigo' => 'MPPET-001', 'nombre' => 'Ministerio del Poder Popular para la Educación'],
        ];

        foreach ($organismos as $Org) {
            DB::table('organismos')->updateOrInsert(['codigo' => $Org['codigo']], $Org);
        }
        $this->command->info('✓ Organismos creados');

        // 2. Unidades Administradoras
        $organismoIds = DB::table('organismos')->pluck('id', 'codigo');

        $unidades = [
            ['organismo_id' => $organismoIds['MPPEU-001'], 'codigo' => '1430', 'nombre' => 'UPTOS "Clodosbaldo Russian"'],
            ['organismo_id' => $organismoIds['MPPEU-001'], 'codigo' => 'MPPEU-001-U01', 'nombre' => 'Dirección de Planificación'],
            ['organismo_id' => $organismoIds['MPPEU-001'], 'codigo' => 'MPPEU-001-U02', 'nombre' => 'Dirección de RRHH'],
            ['organismo_id' => $organismoIds['MPPEU-001'], 'codigo' => 'MPPEU-001-U03', 'nombre' => 'Dirección de Servicios'],
            ['organismo_id' => $organismoIds['MPPEU-002'], 'codigo' => 'SENIAT-001', 'nombre' => 'Gerencia de Bienes'],
            ['organismo_id' => $organismoIds['MPPET-001'], 'codigo' => 'MPPET-001-U01', 'nombre' => 'Coordinación de Admisión'],
        ];

        foreach ($unidades as $u) {
            DB::table('unidades_administradoras')->updateOrInsert(['codigo' => $u['codigo']], $u);
        }
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

        // 4. Dependencias (sin tipo_responsable_id en la tabla)
        $unidadIds = DB::table('unidades_administradoras')->pluck('id', 'codigo');

        $dependencias = [
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-001', 'nombre' => 'Decanato'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-002', 'nombre' => 'Secretaría'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-003', 'nombre' => 'Informática'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-004', 'nombre' => 'Contabilidad'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-005', 'nombre' => 'Biblioteca'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-006', 'nombre' => 'Lab. Física'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-007', 'nombre' => 'Lab. Química'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-008', 'nombre' => 'Aula Magna'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-009', 'nombre' => 'Personal'],
            ['unidad_administradora_id' => $unidadIds['1430'], 'codigo' => 'D-010', 'nombre' => 'Mantenimiento'],
            ['unidad_administradora_id' => $unidadIds['MPPEU-001-U01'], 'codigo' => 'D-011', 'nombre' => 'Planificación'],
            ['unidad_administradora_id' => $unidadIds['MPPEU-001-U02'], 'codigo' => 'D-012', 'nombre' => 'Nómina'],
        ];

        foreach ($dependencias as $d) {
            DB::table('dependencias')->updateOrInsert(['codigo' => $d['codigo']], $d);
        }
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
        $dependenciaIds = DB::table('dependencias')->pluck('id', 'codigo');

        // 6. Usuarios del sistema
        $roles = DB::table('roles')->pluck('id', 'nombre');

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

        // 7. Bienes
        $usuarioIds = DB::table('usuarios')->pluck('id', 'cedula');

        $bienes = [
            // Electrónicos
            ['codigo' => 'ELE-001', 'descripcion' => 'Computadora Desktop Dell OptiPlex', 'precio' => 850.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'ELE-002', 'descripcion' => 'Laptop HP ProBook 450', 'precio' => 920.00, 'ubicacion' => 'D-002', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-002']],
            ['codigo' => 'ELE-003', 'descripcion' => 'Monitor Samsung 24"', 'precio' => 180.00, 'ubicacion' => 'D-003', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-003']],
            ['codigo' => 'ELE-004', 'descripcion' => 'Impresora Laser HP', 'precio' => 350.00, 'ubicacion' => 'D-004', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-004']],
            ['codigo' => 'ELE-005', 'descripcion' => 'Proyector Epson', 'precio' => 650.00, 'ubicacion' => 'D-008', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-008']],
            ['codigo' => 'ELE-006', 'descripcion' => 'Servidor Dell PowerEdge', 'precio' => 8500.00, 'ubicacion' => 'D-003', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-003']],
            ['codigo' => 'ELE-007', 'descripcion' => 'Router Cisco', 'precio' => 1200.00, 'ubicacion' => 'D-003', 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-003']],
            ['codigo' => 'ELE-008', 'descripcion' => 'Tablet Samsung', 'precio' => 250.00, 'ubicacion' => 'D-005', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-005']],
            ['codigo' => 'ELE-009', 'descripcion' => 'Cámara IP Hikvision', 'precio' => 180.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'ELE-010', 'descripcion' => 'Escáner Fujitsu', 'precio' => 450.00, 'ubicacion' => 'D-004', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::ELECTRONICO, 'dependencia_id' => $dependenciaIds['D-004']],

            // Mobiliarios
            ['codigo' => 'MOB-001', 'descripcion' => 'Escritorio Ejecutivo', 'precio' => 350.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'MOB-002', 'descripcion' => 'Silla Ejecutiva', 'precio' => 180.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'MOB-003', 'descripcion' => 'Estantería Metálica', 'precio' => 220.00, 'ubicacion' => 'D-005', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-005']],
            ['codigo' => 'MOB-004', 'descripcion' => 'Mesa de Reuniones', 'precio' => 450.00, 'ubicacion' => 'D-002', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-002']],
            ['codigo' => 'MOB-005', 'descripcion' => 'Archivador 4 gavetas', 'precio' => 280.00, 'ubicacion' => 'D-004', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-004']],
            ['codigo' => 'MOB-006', 'descripcion' => 'Pizarra Acrílica', 'precio' => 85.00, 'ubicacion' => 'D-006', 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-006']],
            ['codigo' => 'MOB-007', 'descripcion' => 'Sofá de 3 puestos', 'precio' => 550.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'MOB-008', 'descripcion' => 'Mesa de Computación', 'precio' => 195.00, 'ubicacion' => 'D-003', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::MOBILIARIO, 'dependencia_id' => $dependenciaIds['D-003']],

            // Vehículos
            ['codigo' => 'VEH-001', 'descripcion' => 'Toyota Corolla 2022', 'precio' => 25000.00, 'ubicacion' => 'Estacionamiento', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'VEH-002', 'descripcion' => 'Ford Explorer 2021', 'precio' => 35000.00, 'ubicacion' => 'Estacionamiento', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'VEH-003', 'descripcion' => 'Camioneta Chevrolet', 'precio' => 28000.00, 'ubicacion' => 'Estacionamiento', 'estado' => EstadoBien::EN_MANTENIMIENTO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['D-010']],
            ['codigo' => 'VEH-004', 'descripcion' => 'Motocicleta Yamaha', 'precio' => 4500.00, 'ubicacion' => 'Estacionamiento', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::VEHICULO, 'dependencia_id' => $dependenciaIds['D-010']],

            // Otros
            ['codigo' => 'OTR-001', 'descripcion' => 'Aire Acondicionado 24000 BTU', 'precio' => 680.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-001']],
            ['codigo' => 'OTR-002', 'descripcion' => 'Equipo de Sonido Sony', 'precio' => 320.00, 'ubicacion' => 'D-008', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-008']],
            ['codigo' => 'OTR-003', 'descripcion' => 'Generador 5000W', 'precio' => 1200.00, 'ubicacion' => 'D-010', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-010']],
            ['codigo' => 'OTR-004', 'descripcion' => 'Cortina de aluminio', 'precio' => 150.00, 'ubicacion' => 'D-002', 'estado' => EstadoBien::DANADO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-002']],
            ['codigo' => 'OTR-005', 'descripcion' => 'Reflector LED 100W', 'precio' => 45.00, 'ubicacion' => 'Estacionamiento', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-010']],
            ['codigo' => 'OTR-006', 'descripcion' => 'Bomba de Agua 2HP', 'precio' => 180.00, 'ubicacion' => 'D-010', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-010']],
            ['codigo' => 'OTR-007', 'descripcion' => 'Tanque de Agua 1000L', 'precio' => 250.00, 'ubicacion' => 'Terraza', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-010']],
            ['codigo' => 'OTR-008', 'descripcion' => 'Extinguidor 10kg', 'precio' => 85.00, 'ubicacion' => 'D-001', 'estado' => EstadoBien::ACTIVO, 'tipo_bien' => TB::OTROS, 'dependencia_id' => $dependenciaIds['D-001']],
        ];

        $bienIds = [];
        foreach ($bienes as $bien) {
            $estado = $bien['estado'];
            $tipo = $bien['tipo_bien'];
            $ubicacion = $bien['ubicacion'];
            $depId = $bien['dependencia_id'];

            unset($bien['estado'], $bien['tipo_bien'], $bien['ubicacion'], $bien['dependencia_id']);

            $bien['estado'] = $estado->value;
            $bien['tipo_bien'] = $tipo->value;
            $bien['ubicacion'] = $ubicacion;
            $bien['dependencia_id'] = $depId;
            $bien['fecha_registro'] = now()->subDays(rand(1, 365));
            $bien['caracteristicas'] = json_encode(['color' => 'Negro']);

            DB::table('bienes')->updateOrInsert(['codigo' => $bien['codigo']], $bien);
            $bienIds[] = DB::table('bienes')->where('codigo', $bien['codigo'])->first()->id;
        }
        $this->command->info('✓ Bienes creados (' . count($bienIds) . ')');

        // 8. Detalles por tipo (simplificado - las tablas tienen esquemas diferentes)
        // Se omiten detalles específicos para evitar errores de esquema
        $this->command->info('✓ BienesInsertidos (detalles omitidos)');

        // 9. Movimientos (omitido por diferencias en esquema)

        // 10. Auditoría (omitido por diferencias en esquema)

        $this->command->info('🎉 ¡Datos de demo insertados!');
        $this->command->info('📝 Credenciales: admin@inventario.com / ' . config('app.demo_password', 'password123'));
    }
}
