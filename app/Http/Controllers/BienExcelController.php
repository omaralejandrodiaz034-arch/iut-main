<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BienExcelController extends Controller
{
    /**
     * Muestra el formulario de importación
     */
    public function showImportForm()
    {
        return view('bienes.importar');
    }

    /**
     * Importa bienes desde archivo Excel
     */
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'archivo.required' => 'Debe seleccionar un archivo',
            'archivo.file' => 'El archivo no es válido',
            'archivo.mimes' => 'El archivo debe ser de tipo Excel (xlsx, xls o csv)',
            'archivo.max' => 'El archivo no puede superar 10MB',
        ]);

        try {
            // Load spreadsheet directly
            $spreadsheet = IOFactory::load($request->file('archivo'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            $registros_exitosos = 0;
            $errores = [];
            
            foreach ($rows as $index => $row) {
                $fila = $index + 2;
                
                try {
                    // Skip empty rows
                    if (empty($row[0])) {
                        continue;
                    }
                    
                    // Validate required code
                    $codigo = trim($row[0]);
                    if (empty($codigo)) {
                        $errores[] = "Fila {$fila}: Código vacío";
                        continue;
                    }
                    
                    // Check duplicate code
                    if (Bien::where('codigo', $codigo)->exists()) {
                        $errores[] = "Fila {$fila}: El código {$codigo} ya existe";
                        continue;
                    }
                    
                    // Find dependencia (column 5)
                    $dependenciaId = null;
                    if (!empty($row[5])) {
                        $dependencia = \App\Models\Dependencia::where('codigo', trim($row[5]))->first();
                        if (!$dependencia) {
                            $errores[] = "Fila {$fila}: Dependencia con código " . trim($row[5]) . " no encontrada";
                            continue;
                        }
                        $dependenciaId = $dependencia->id;
                    }
                    
                    // Find responsable (column 6)
                    $responsableId = null;
                    if (!empty($row[6])) {
                        $responsable = \App\Models\Responsable::where('cedula', trim($row[6]))->first();
                        if (!$responsable) {
                            $errores[] = "Fila {$fila}: Responsable con cédula " . trim($row[6]) . " no encontrado";
                            continue;
                        }
                        $responsableId = $responsable->id;
                    }
                    
                    // Validate estado (column 4)
                    $estadosValidos = ['Activo', 'Inactivo', 'En Mantenimiento', 'Dado de Baja', 'Extraviado'];
                    $estado = !empty($row[4]) ? trim($row[4]) : 'Activo';
                    if (!in_array($estado, $estadosValidos)) {
                        $errores[] = "Fila {$fila}: Estado '{$estado}' no válido";
                        continue;
                    }
                    
                    // Validate precio (column 2)
                    $precio = !empty($row[2]) ? floatval($row[2]) : 0;
                    if ($precio < 0) {
                        $errores[] = "Fila {$fila}: Precio debe ser un número positivo";
                        continue;
                    }
                    
                    // Create bien
                    $bien = Bien::create([
                        'codigo' => $codigo,
                        'descripcion' => !empty($row[1]) ? trim($row[1]) : '',
                        'precio' => $precio,
                        'ubicacion' => !empty($row[3]) ? trim($row[3]) : '',
                        'estado' => $estado,
                        'fecha_registro' => !empty($row[7]) ? trim($row[7]) : date('Y-m-d'),
                        'dependencia_id' => $dependenciaId,
                    ]);
                    
                    // Assign responsable
                    if ($responsableId) {
                        $bien->responsable_id = $responsableId;
                        $bien->save();
                    }
                    
                    $registros_exitosos++;
                    
                } catch (\Exception $e) {
                    $errores[] = "Fila {$fila}: Error - " . $e->getMessage();
                }
            }
            
            $mensaje = "Importación completada. {$registros_exitosos} registros importados.";
            
            if (!empty($errores)) {
                $mensaje .= " Errores: " . count($errores);
                session()->flash('errores_importacion', $errores);
            }

            session()->flash('success', $mensaje);

            return redirect()->route('bienes.index');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Descarga template de Excel para importación
     */
    public function descargarTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Encabezados
        $headers = ['codigo', 'descripcion', 'precio', 'ubicacion', 'estado', 'codigo_dependencia', 'cedula_responsable', 'fecha_registro'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Datos de ejemplo
        $exampleData = [
            ['00000001', 'Computadora Dell Inspiron 15', '1500.00', 'Oficina 101', 'Activo', '00000001', '12345678', date('Y-m-d')],
            ['00000002', 'Escritorio de madera', '250.00', 'Oficina 102', 'Activo', '00000001', '87654321', date('Y-m-d')],
        ];
        $sheet->fromArray($exampleData, null, 'A2');
        
        // Estilos para encabezados
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A1:H1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Autoajustar columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'template_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, 'template_importacion_bienes.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Exporta bienes a Excel usando PhpSpreadsheet directamente
     */
    public function exportar(Request $request)
    {
        try {
            $query = Bien::with(['dependencia.responsable']);

            // Aplicar filtros si existen
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('dependencia_id')) {
                $query->where('dependencia_id', $request->dependencia_id);
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            }

            $bienes = $query->orderBy('codigo')->get();
            
            // Create spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Encabezados
            $headers = ['Código', 'Descripción', 'Precio', 'Estado', 'Ubicación', 'Fecha de Registro', 'Código Dependencia', 'Nombre Dependencia', 'Cédula Responsable', 'Nombre Responsable'];
            $sheet->fromArray($headers, null, 'A1');
            
            // Estilos para encabezados
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:J1')->getFont()->setSize(12);
            $sheet->getStyle('A1:J1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
            $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:J1')->getAlignment()->setVertical('center');
            
            // Datos
            $rowNum = 2;
            foreach ($bienes as $bien) {
                $sheet->setCellValue('A' . $rowNum, $bien->codigo);
                $sheet->setCellValue('B' . $rowNum, $bien->descripcion);
                $sheet->setCellValue('C' . $rowNum, $bien->precio);
                $sheet->setCellValue('D' . $rowNum, $bien->estado instanceof \App\Enums\EstadoBien ? $bien->estado->value : $bien->estado);
                $sheet->setCellValue('E' . $rowNum, $bien->ubicacion);
                $sheet->setCellValue('F' . $rowNum, $bien->fecha_registro ? $bien->fecha_registro->format('Y-m-d') : '');
                $sheet->setCellValue('G' . $rowNum, $bien->dependencia?->codigo ?? '');
                $sheet->setCellValue('H' . $rowNum, $bien->dependencia?->nombre ?? '');
                $sheet->setCellValue('I' . $rowNum, $bien->dependencia?->responsable?->cedula ?? '');
                $sheet->setCellValue('J' . $rowNum, $bien->dependencia?->responsable?->nombre ?? '');
                $rowNum++;
            }
            
            // Autoajustar columnas
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'bienes_export_' . date('Y-m-d_His') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'export_');
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }
}
