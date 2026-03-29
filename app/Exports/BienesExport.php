<?php

namespace App\Exports;

use App\Models\Bien;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BienesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Bienes a exportar
     */
    protected $bienes;

    /**
     * Constructor
     */
    public function __construct($bienes = null)
    {
        $this->bienes = $bienes ?? Bien::with(['dependencia.responsable'])->get();
    }

    /**
     * Colección de datos a exportar
     */
    public function collection()
    {
        return $this->bienes;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'Código',
            'Descripción',
            'Precio',
            'Estado',
            'Ubicación',
            'Fecha de Registro',
            'Código Dependencia',
            'Nombre Dependencia',
            'Cédula Responsable',
            'Nombre Responsable',
        ];
    }

    /**
     * Mapeo de cada fila
     */
    public function map($bien): array
    {
        return [
            $bien->codigo,
            $bien->descripcion,
            $bien->precio,
            $bien->estado instanceof \App\Enums\EstadoBien ? $bien->estado->value : $bien->estado,
            $bien->ubicacion,
            $bien->fecha_registro ? $bien->fecha_registro->format('Y-m-d') : '',
            $bien->dependencia?->codigo ?? '',
            $bien->dependencia?->nombre ?? '',
            $bien->dependencia?->responsable?->cedula ?? '',
            $bien->dependencia?->responsable?->nombre ?? '',
        ];
    }

    /**
     * Estilos de la hoja de cálculo
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }
}
