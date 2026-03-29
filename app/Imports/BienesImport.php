<?php

namespace App\Imports;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class BienesImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Almacena errores encontrados durante la importación
     */
    public $errores = [];
    
    /**
     * Contador de registros exitosos
     */
    public $registros_exitosos = 0;

    /**
     * Procesa cada fila del archivo Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $fila = $index + 2; // +2 porque empieza en 0 y hay encabezado
            
            try {
                // Validar que tenga código
                if (empty($row['codigo'])) {
                    $this->errores[] = "Fila {$fila}: Código vacío";
                    continue;
                }

                // Verificar si el código ya existe
                if (Bien::where('codigo', $row['codigo'])->exists()) {
                    $this->errores[] = "Fila {$fila}: El código {$row['codigo']} ya existe";
                    continue;
                }

                // Buscar dependencia por código
                $dependencia = null;
                if (!empty($row['codigo_dependencia'])) {
                    $dependencia = Dependencia::where('codigo', $row['codigo_dependencia'])->first();
                    if (!$dependencia) {
                        $this->errores[] = "Fila {$fila}: Dependencia con código {$row['codigo_dependencia']} no encontrada";
                        continue;
                    }
                }

                // Buscar responsable por cédula
                $responsable = null;
                if (!empty($row['cedula_responsable'])) {
                    $responsable = Responsable::where('cedula', $row['cedula_responsable'])->first();
                    if (!$responsable) {
                        $this->errores[] = "Fila {$fila}: Responsable con cédula {$row['cedula_responsable']} no encontrado";
                        continue;
                    }
                }

                // Validar estado
                $estadosValidos = ['Activo', 'Inactivo', 'En Mantenimiento', 'Dado de Baja', 'Extraviado'];
                $estado = $row['estado'] ?? 'Activo';
                if (!in_array($estado, $estadosValidos)) {
                    $this->errores[] = "Fila {$fila}: Estado '{$estado}' no válido";
                    continue;
                }

                // Validar precio
                $precio = $row['precio'] ?? 0;
                if (!is_numeric($precio) || $precio < 0) {
                    $this->errores[] = "Fila {$fila}: Precio debe ser un número positivo";
                    continue;
                }

                // Crear el bien
                $bien = Bien::create([
                    'codigo' => $row['codigo'],
                    'descripcion' => $row['descripcion'] ?? '',
                    'precio' => $precio,
                    'ubicacion' => $row['ubicacion'] ?? '',
                    'estado' => $estado,
                    'fecha_registro' => !empty($row['fecha_registro']) 
                        ? \Carbon\Carbon::parse($row['fecha_registro'])->format('Y-m-d')
                        : now()->format('Y-m-d'),
                    'dependencia_id' => $dependencia?->id,
                ]);

                // Asignar responsable si existe
                if ($responsable) {
                    $bien->responsable_id = $responsable->id;
                    $bien->save();
                }

                $this->registros_exitosos++;

            } catch (\Exception $e) {
                $this->errores[] = "Fila {$fila}: Error - " . $e->getMessage();
            }
        }
    }

    /**
     * Reglas de validación para cada fila
     */
    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'nullable|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'nullable|string|in:Activo,Inactivo,En Mantenimiento,Dado de Baja,Extraviado',
            'codigo_dependencia' => 'nullable|string|max:20',
            'cedula_responsable' => 'nullable|string|max:20',
            'fecha_registro' => 'nullable|date',
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio',
            'codigo.max' => 'El código no puede exceder 20 caracteres',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'estado.in' => 'Estado inválido. Estados válidos: Activo, Inactivo, En Mantenimiento, Dado de Baja, Extraviado',
        ];
    }
}
