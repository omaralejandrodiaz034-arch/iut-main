<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BienSeeder extends Seeder
{
    public function run(): void
    {
        $dependencias = DB::table('dependencias')->get();

        // Datos realistas por tipo de bien
        $tiposBien = [
            'ELECTRONICO' => [
                [
                    'descripcion' => 'Computadora Portátil HP Pavilion',
                    'precio' => 85000,
                    'procesador' => 'Intel Core i7 11th Gen',
                    'memoria' => '16 GB DDR4',
                    'almacenamiento' => '512 GB SSD',
                    'pantalla' => '15.6 pulgadas FHD',
                    'serial' => 'HP-2024-001',
                    'garantia' => '2025-12-31',
                ],
                [
                    'descripcion' => 'Monitor LED Samsung 27"',
                    'precio' => 12000,
                    'pantalla' => '27 pulgadas 1440p',
                    'serial' => 'SM-2024-001',
                    'garantia' => '2025-12-31',
                ],
                [
                    'descripcion' => 'Impresora Multifuncional Canon',
                    'precio' => 8500,
                    'marca' => 'Canon',
                    'modelo' => 'PIXMA TR7640',
                    'serial' => 'CN-2024-001',
                    'garantia' => '2025-12-31',
                ],
                [
                    'descripcion' => 'Router WiFi 6 ASUS',
                    'precio' => 5200,
                    'marca' => 'ASUS',
                    'modelo' => 'RT-AX88U',
                    'serial' => 'AS-2024-001',
                ],
            ],
            'INMUEBLE' => [
                [
                    'descripcion' => 'Oficina Administrativa Piso 2',
                    'precio' => 500000,
                    'dimensiones' => '5m x 4m',
                    'material' => 'Concreto y acero',
                    'area' => '20',
                    'pisos' => '1',
                    'construccion' => '2015',
                    'direccion' => 'Avenida Principal, Edificio Central',
                ],
                [
                    'descripcion' => 'Almacén de Inventario',
                    'precio' => 450000,
                    'dimensiones' => '10m x 8m',
                    'material' => 'Bloque y acero',
                    'area' => '80',
                    'pisos' => '1',
                    'construccion' => '2018',
                    'direccion' => 'Sótano, Edificio Central',
                ],
                [
                    'descripcion' => 'Sala de Capacitación',
                    'precio' => 350000,
                    'dimensiones' => '7m x 6m',
                    'material' => 'Concreto',
                    'area' => '42',
                    'pisos' => '1',
                    'construccion' => '2016',
                    'direccion' => 'Piso 3, Edificio Central',
                ],
            ],
            'MOBILIARIO' => [
                [
                    'descripcion' => 'Escritorio Ejecutivo Madera',
                    'precio' => 18000,
                    'material' => 'Madera MDF',
                    'dimensiones' => '1.4m x 0.7m x 0.75m',
                    'color' => 'Nogal',
                    'acabado' => 'Melaminado',
                ],
                [
                    'descripcion' => 'Silla Ergonómica Negra',
                    'precio' => 9500,
                    'material' => 'Tela y acero',
                    'color' => 'Negro',
                    'capacidad' => '1 persona',
                    'acabado' => 'Tapizado',
                ],
                [
                    'descripcion' => 'Escritorio Modular 2 Puestos',
                    'precio' => 28000,
                    'material' => 'Acero y MDF',
                    'dimensiones' => '2.8m x 0.7m x 0.75m',
                    'color' => 'Blanco',
                    'cantidad_piezas' => '2',
                    'acabado' => 'Laminado',
                ],
                [
                    'descripcion' => 'Estantería Metálica Industrial',
                    'precio' => 12000,
                    'material' => 'Acero',
                    'dimensiones' => '1.8m x 0.9m x 2.0m',
                    'color' => 'Gris',
                    'capacidad' => '500 kg',
                ],
                [
                    'descripcion' => 'Mesa de Reunión Ovalada',
                    'precio' => 35000,
                    'material' => 'Madera y cristal',
                    'dimensiones' => '2.4m x 1.2m x 0.75m',
                    'color' => 'Caoba',
                    'capacidad' => '8 personas',
                ],
            ],
            'VEHICULO' => [
                [
                    'descripcion' => 'Vehículo Transporte Personal Toyota',
                    'precio' => 280000,
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'anio' => '2023',
                    'placa' => 'ABC-2023-001',
                    'motor' => 'TYT-2024-M001',
                    'chasis' => 'TYT-2024-CH001',
                    'combustible' => 'Gasolina',
                    'kilometraje' => '2500',
                ],
                [
                    'descripcion' => 'Van de Carga Hyundai H350',
                    'precio' => 450000,
                    'marca' => 'Hyundai',
                    'modelo' => 'H350',
                    'anio' => '2022',
                    'placa' => 'HYU-2022-001',
                    'motor' => 'HYU-2022-M001',
                    'chasis' => 'HYU-2022-CH001',
                    'combustible' => 'Diésel',
                    'kilometraje' => '5800',
                ],
            ],
            'OTROS' => [
                [
                    'descripcion' => 'Proyector Multimedia Epson',
                    'precio' => 15000,
                    'especificaciones' => 'Proyector LCD, brillo 3500 lúmenes, resolución WXGA',
                    'cantidad' => '1',
                    'presentacion' => 'Equipo completo con control remoto',
                ],
            ],
        ];

        $estados = ['ACTIVO', 'EN_REPARACION', 'EN_MANTENIMIENTO'];
        $estadoIndex = 0;

        foreach ($dependencias as $dep) {
            $bienIndex = 0;
            $tiposBienArray = array_keys($tiposBien);

            // Por cada dependencia, distribuir bienes de diferentes tipos
            foreach ($tiposBienArray as $tipo) {
                foreach ($tiposBien[$tipo] as $bienData) {
                    $bienIndex++;
                    $estado = $estados[$estadoIndex % count($estados)];
                    $estadoIndex++;

                    $insertData = [
                        'dependencia_id' => $dep->id,
                        'codigo' => "B{$dep->codigo}-{$dep->id}-" . str_pad((string)$bienIndex, 3, '0', STR_PAD_LEFT),
                        'descripcion' => $bienData['descripcion'],
                        'precio' => $bienData['precio'],
                        'ubicacion' => "Oficina {$dep->nombre}",
                        'estado' => $estado,
                        'tipo_bien' => $tipo,
                        'fotografia' => null,
                        'fecha_registro' => now()->subDays(mt_rand(1, 180)),
                    ];

                    // Agregar campos específicos según el tipo
                    foreach ($bienData as $key => $value) {
                        if ($key !== 'descripcion' && $key !== 'precio') {
                            $insertData[$key] = $value;
                        }
                    }

                    DB::table('bienes')->insert($insertData);
                }
            }
        }
    }
}

