<?php

namespace App\Enums;

enum TipoBien: string
{
    case ELECTRONICO = 'ELECTRONICO';
    case MOBILIARIO = 'MOBILIARIO';
    case VEHICULO = 'VEHICULO';
    case OTROS = 'OTROS';

    /** Etiqueta amigable en español */
    public function label(): string
    {
        return match ($this) {
            self::ELECTRONICO => 'Electrónico',
            self::MOBILIARIO => 'Mobiliario',
            self::VEHICULO => 'Vehículo',
            self::OTROS => 'Otros',
        };
    }

    /** Campos específicos que cada tipo de bien requiere */
    public function camposEspecificos(): array
    {
        return match ($this) {
            self::ELECTRONICO => [
                'procesador' => 'Procesador',
                'memoria' => 'Memoria (GB)',
                'almacenamiento' => 'Almacenamiento (GB)',
                'pantalla' => 'Tamaño de pantalla',
                'serial' => 'Número de serie',
                'garantia' => 'Garantía hasta',
            ],
            self::MOBILIARIO => [
                'material' => 'Material',
                'dimensiones' => 'Dimensiones',
                'color' => 'Color',
                'capacidad' => 'Capacidad de personas/carga',
                'cantidad_piezas' => 'Cantidad de piezas',
                'acabado' => 'Tipo de acabado',
            ],
            self::VEHICULO => [
                'marca' => 'Marca',
                'modelo' => 'Modelo',
                'anio' => 'Año',
                'placa' => 'Placa o número de registro',
                'motor' => 'Número de motor',
                'chasis' => 'Número de chasis',
                'combustible' => 'Tipo de combustible',
                'kilometraje' => 'Kilometraje actual',
            ],
            self::OTROS => [
                'especificaciones' => 'Especificaciones adicionales',
                'cantidad' => 'Cantidad de unidades',
                'presentacion' => 'Presentación/Formato',
            ],
        };
    }

    /** Arreglo de valores */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
