<?php

namespace App\Services;

use FPDF;

class FpdfReportService
{
    /**
     * Crear una instancia base de FPDF en formato Vertical (P).
     */
    protected function make(string $orientation = 'P'): FPDF
    {
        $logoPath = public_path('images/logo.png');
        $instName = config('app.name', 'Institución');
        $instAddress = config('app.address', '');

        $pdf = new class($orientation, 'mm', 'Letter', $logoPath, $instName, $instAddress) extends FPDF {
            protected $logo;
            protected $instName;
            protected $instAddress;

            public function __construct($orientation, $unit, $size, $logo, $instName, $instAddress)
            {
                parent::__construct($orientation, $unit, $size);
                $this->logo = $logo;
                $this->instName = $instName;
                $this->instAddress = $instAddress;
            }

            // Header called automatically on each page
            public function Header()
            {
                // Logo left
                if ($this->logo && file_exists($this->logo)) {
                    $this->Image($this->logo, 10, 8, 24);
                }

                // Institution name centered
                $this->SetFont('Arial', 'B', 14);
                $this->Cell(0, 8, utf8_decode($this->instName), 0, 1, 'C');

                // Address / subtitle
                if (!empty($this->instAddress)) {
                    $this->SetFont('Arial', '', 9);
                    $this->Cell(0, 5, utf8_decode($this->instAddress), 0, 1, 'C');
                }

                // Divider line
                $this->Ln(2);
                $this->SetDrawColor(200, 200, 200);
                $this->SetLineWidth(0.3);
                $this->Line(10, $this->GetY(), $this->w - 10, $this->GetY());
                $this->Ln(4);
            }

            // Footer called automatically on each page
            public function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(100, 100, 100);
                $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
            }
        };

        $pdf->AliasNbPages();
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        return $pdf;
    }

            protected function renderHeader(\FPDF $pdf, string $title, ?string $subtitle, string $generatedAt, array $data): void
    {
        // --- FILA 1: ENCABEZADO SUPERIOR (Institución, Título y Fecha) ---
        $pdf->SetFont('Arial', 'B', 8);

        // Guardamos la posición inicial para que los tres cuadros tengan la misma altura
        $yInicio = $pdf->GetY();

        // 1.1 Cuadro Izquierdo: Datos de la Institución
        $pdf->MultiCell(70, 4.5, "UPTOS \"CLODOSBALDO RUSSIAN\"\nUNIDAD DE BIENES PÚBLICOS", 1, 'L');
        $altFila1 = $pdf->GetY() - $yInicio; // Calculamos la altura alcanzada

        // 1.2 Cuadro Centro: Título del reporte (Usamos SetXY para posicionarlo al lado)
        $pdf->SetXY(80, $yInicio);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, $altFila1, $this->t(strtoupper($title)), 1, 0, 'C');

        // 1.3 Cuadro Derecho: Fecha (Dividido en etiqueta y valor)
        $pdf->SetXY(175, $yInicio);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(31, 5, $this->t('Fecha'), 1, 1, 'L');
        $pdf->SetX(175);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(31, $altFila1 - 5, $generatedAt, 1, 1, 'L');

        // --- FILA 2: SECCIÓN ORGANISMO ---
        $pdf->SetFillColor(240, 240, 240); // Gris claro para los títulos
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, $this->t('ORGANISMO'), 1, 1, 'L', true);

        // Sub-encabezados de Organismo
        $pdf->Cell(25, 5, $this->t('Código'), 1, 0, 'L');
        $pdf->Cell(0, 5, $this->t('Denominación'), 1, 1, 'L');

        // Datos de Organismo
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(25, 6, $data['org_codigo'] ?? '0', 1, 0, 'C');
        $pdf->Cell(0, 6, $this->t($data['org_nombre'] ?? 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA'), 1, 1, 'L');

        // --- FILA 3: SECCIÓN UNIDAD Y DEPENDENCIA ---
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(98, 5, $this->t('UNIDAD ADMINISTRADORA'), 1, 0, 'L', true);
        $pdf->Cell(98, 5, $this->t('DEPENDENCIA USUARIA'), 1, 1, 'L', true);

        // Sub-encabezados de Unidad y Dependencia
        $pdf->Cell(20, 5, $this->t('Código'), 1, 0, 'L');
        $pdf->Cell(78, 5, $this->t('Denominación'), 1, 0, 'L');
        $pdf->Cell(20, 5, $this->t('Código'), 1, 0, 'L');
        $pdf->Cell(78, 5, $this->t('Denominación'), 1, 1, 'L');

        // Datos de Unidad y Dependencia
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 6, $data['uni_codigo'] ?? '', 1, 0, 'C');
        $pdf->Cell(78, 6, $this->t($data['uni_nombre'] ?? ''), 1, 0, 'L');
        $pdf->Cell(20, 6, $data['dep_codigo'] ?? '', 1, 0, 'C');
        $pdf->Cell(78, 6, $this->t($data['dep_nombre'] ?? ''), 1, 1, 'L');

        // --- FILA 4: SECCIÓN RESPONSABLES ---
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(98, 5, $this->t('RESPONSABLE PATRIMONIAL PRIMARIO'), 1, 0, 'L', true);
        $pdf->Cell(98, 5, $this->t('RESPONSABLE PATRIMONIAL POR USO'), 1, 1, 'L', true);

        // Sub-encabezados de Responsables
        $pdf->Cell(25, 5, $this->t('Cédula'), 1, 0, 'L');
        $pdf->Cell(73, 5, $this->t('Apellidos y Nombres'), 1, 0, 'L');
        $pdf->Cell(25, 5, $this->t('Cédula'), 1, 0, 'L');
        $pdf->Cell(73, 5, $this->t('Apellidos y Nombres'), 1, 1, 'L');

        // Datos de Responsables
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(25, 6, $data['res_p_cedula'] ?? '', 1, 0, 'L');
        $pdf->Cell(73, 6, $this->t($data['res_p_nombre'] ?? ''), 1, 0, 'L');
        $pdf->Cell(25, 6, $data['res_u_cedula'] ?? '', 1, 0, 'L');
        $pdf->Cell(73, 6, $this->t($data['res_u_nombre'] ?? ''), 1, 1, 'L');

        $pdf->Ln(4); // Espacio antes de empezar la tabla de bienes
    }

    /**
     * Genera el listado de bienes en formato vertical.
     */
    public function downloadBienesListado(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $bienes
    ) {
        // Convertir iterable a array para poder iterar múltiples veces
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        // 1. Extraer datos para los cuadros superiores (Organismo, Unidad, Responsables)
        // Usamos el primer bien de la colección para rellenar la cabecera institucional
        $primerBien = !empty($bienesArray) ? reset($bienesArray) : null;

        $datosCabecera = [
            'org_codigo' => $primerBien->dependencia->unidadAdministradora->organismo->codigo ?? '0',
            'org_nombre' => $primerBien->dependencia->unidadAdministradora->organismo->nombre ?? 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA',
            'uni_codigo' => $primerBien->dependencia->unidadAdministradora->codigo ?? 'N/A',
            'uni_nombre' => $primerBien->dependencia->unidadAdministradora->nombre ?? 'N/A',
            'dep_codigo' => $primerBien->dependencia->codigo ?? 'N/A',
            'dep_nombre' => $primerBien->dependencia->nombre ?? 'N/A',
            // Responsables (Asegúrate de que estas relaciones existan en tu modelo)
            'res_p_cedula' => $primerBien->dependencia->unidadAdministradora->responsable->cedula ?? '3873777',
            'res_p_nombre' => $primerBien->dependencia->unidadAdministradora->responsable->nombre_completo ?? 'ENRY GOMEZ MAIZ',
            'res_u_cedula' => $primerBien->dependencia->responsable->cedula ?? '',
            'res_u_nombre' => $primerBien->dependencia->responsable->nombre_completo ?? '',
        ];

        $pdf = $this->make('P');

        // 2. Llamamos a renderHeader pasando los datos dinámicos
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, $datosCabecera);

        // 3. Configuración de la tabla de bienes (Anchos ajustados a Letter)
        // [Código, Descripción, Precio, Dependencia, Fotos, Fecha]
        $widths = [25, 80, 25, 25, 16, 25];
        $headers = ['Código', 'Descripción', 'Precio Bs.', 'Dependencia', 'Fotos', 'Fecha'];

        // Dibujar Encabezado de la Tabla
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco como en la imagen
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C');
        }
        $pdf->Ln();

        // 4. Dibujar los Datos
        $pdf->SetFont('Arial', '', 7);
        $totalBs = 0;
        $hasData = false;

        foreach ($bienesArray as $bien) {
            $hasData = true;

            // Guardar posición inicial para controlar el alto de la fila si la descripción es larga
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Cell($widths[0], 6, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');

            // Descripción con truncado para no romper la celda
            $pdf->Cell($widths[1], 6, $this->t($this->truncate((string)($bien->descripcion ?? ''), 50)), 1);

            // Precio
            $precio = (float)($bien->precio ?? 0);
            $pdf->Cell($widths[2], 6, number_format($precio, 2, ',', '.'), 1, 0, 'R');
            $totalBs += $precio;

            // Dependencia (nombre corto)
            $pdf->Cell($widths[3], 6, $this->t($this->truncate($bien->dependencia->nombre ?? '', 15)), 1, 0, 'C');

            // Fotos (SI/NO)
            $pdf->Cell($widths[4], 6, ($bien->fotografia ? 'SI' : 'NO'), 1, 0, 'C');

            // Fecha
            $pdf->Cell($widths[5], 6, $bien->created_at ? $bien->created_at->format('d/m/Y') : '', 1, 1, 'C');
        }

        // 5. Espacio vacío si no hay datos
        if (!$hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron registros.'), 1, 1, 'C');
        }

        // 6. Fila de Total (Pie de tabla igual a la imagen)
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('Total Bs.:'), 1, 0, 'R');
        $pdf->Cell($widths[2], 8, number_format($totalBs, 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell($widths[3] + $widths[4] + $widths[5], 8, '', 1, 1);

        // 7. Retornar Respuesta
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    protected function t(string $text): string
    {
        // Convertir enums a string si es necesario
        if ($text instanceof \UnitEnum) {
            $text = $text->value;
        }
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }

    /**
     * Convierte un valor a string, manejando enums y otros tipos
     */
    protected function toString($value): string
    {
        if (is_null($value)) {
            return '';
        }
        if ($value instanceof \UnitEnum) {
            return $value->value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }
        return (string) $value;
    }

    protected function truncate(string $text, int $limit): string
    {
        return mb_strlen($text, 'UTF-8') <= $limit
            ? $text
            : mb_substr($text, 0, $limit, 'UTF-8').'...';
    }

    /**
     * Genera reporte agrupado por dependencia
     */
    public function generarPorDependencia(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('L'); // Horizontal para mejor visualización de grupos

        // Agrupar bienes por dependencia
        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $depNombre = optional($bien->dependencia)->nombre ?? 'Sin Dependencia';
            if (!isset($agrupados[$depNombre])) {
                $agrupados[$depNombre] = [];
            }
            $agrupados[$depNombre][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        foreach ($agrupados as $depNombre => $bienesGrupo) {
            // Verificar espacio para nueva página
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }

            // Encabezado del grupo
            $pdf->SetFillColor(128, 0, 32); // Vino tinto
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'DEPENDENCIA: ' . strtoupper($this->t($depNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);

            // Tabla de bienes
            $widths = [25, 90, 25, 35, 30, 25, 30];
            $headers = ['Código', 'Descripción', 'Precio', 'Tipo', 'Estado', 'U. Admin.', 'Fecha'];

            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $totalBs = 0;
            foreach ($bienesGrupo as $bien) {
                $precio = (float)($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string)($bien->descripcion ?? ''), 45)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($bien->tipo_bien ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($bien->estado ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $this->t($this->truncate($bien->dependencia->unidadAdministradora->nombre ?? '-', 15)), 1, 0, 'C');
                $pdf->Cell($widths[6], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            // Total del grupo
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL ' . count($bienesGrupo) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }

    /**
     * Genera reporte agrupado por unidad administradora
     */
    public function generarPorUnidad(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('L');

        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $uniNombre = optional(optional($bien->dependencia)->unidadAdministradora)->nombre ?? 'Sin Unidad';
            if (!isset($agrupados[$uniNombre])) {
                $agrupados[$uniNombre] = [];
            }
            $agrupados[$uniNombre][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        foreach ($agrupados as $uniNombre => $bienesGrupo) {
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(0, 82, 147); // Azul institucional
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'UNIDAD ADMINISTRADORA: ' . strtoupper($this->t($uniNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);

            $widths = [25, 90, 25, 35, 30, 25, 30];
            $headers = ['Código', 'Descripción', 'Precio', 'Tipo', 'Estado', 'Dependencia', 'Fecha'];

            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $totalBs = 0;
            foreach ($bienesGrupo as $bien) {
                $precio = (float)($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string)($bien->descripcion ?? ''), 45)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($bien->tipo_bien ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($bien->estado ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $this->t($this->truncate($bien->dependencia->nombre ?? '-', 15)), 1, 0, 'C');
                $pdf->Cell($widths[6], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL ' . count($bienesGrupo) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }

    /**
     * Genera reporte agrupado por organismo
     */
    public function generarPorOrganismo(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('L');

        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $orgNombre = optional(optional(optional($bien->dependencia)->unidadAdministradora)->organismo)->nombre ?? 'Sin Organismo';
            if (!isset($agrupados[$orgNombre])) {
                $agrupados[$orgNombre] = [];
            }
            $agrupados[$orgNombre][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        foreach ($agrupados as $orgNombre => $bienesGrupo) {
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(34, 85, 51); // Verde
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'ORGANISMO: ' . strtoupper($this->t($orgNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);

            $widths = [25, 80, 25, 30, 25, 40, 35];
            $headers = ['Código', 'Descripción', 'Precio', 'Tipo', 'Estado', 'Unidad', 'Dependencia'];

            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $totalBs = 0;
            foreach ($bienesGrupo as $bien) {
                $precio = (float)($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string)($bien->descripcion ?? ''), 40)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($bien->tipo_bien ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($bien->estado ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $this->t($this->truncate($bien->dependencia->unidadAdministradora->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[6], 5, $this->t($this->truncate($bien->dependencia->nombre ?? '-', 18)), 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL ' . count($bienesGrupo) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }

    /**
     * Genera reporte agrupado por tipo de bien
     */
    public function generarPorTipo(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('L');

        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $tipo = $bien->tipo_bien ?? 'Sin Tipo';
            if (!isset($agrupados[$tipo])) {
                $agrupados[$tipo] = [];
            }
            $agrupados[$tipo][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        foreach ($agrupados as $tipo => $bienesGrupo) {
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(128, 0, 32);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'TIPO DE BIEN: ' . strtoupper($this->t($tipo)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);

            $widths = [30, 100, 30, 35, 40, 30];
            $headers = ['Código', 'Descripción', 'Precio', 'Estado', 'Dependencia', 'Fecha'];

            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $totalBs = 0;
            foreach ($bienesGrupo as $bien) {
                $precio = (float)($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string)($bien->descripcion ?? ''), 50)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($bien->estado ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->truncate($bien->dependencia->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL ' . count($bienesGrupo) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }

    /**
     * Genera reporte agrupado por estado
     */
    public function generarPorEstado(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('L');

        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $estado = $bien->estado ?? 'Sin Estado';
            if (!isset($agrupados[$estado])) {
                $agrupados[$estado] = [];
            }
            $agrupados[$estado][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        foreach ($agrupados as $estado => $bienesGrupo) {
            if ($pdf->GetY() > 180) {
                $pdf->AddPage();
            }

            // Color según estado
            $colores = [
                'ACTIVO' => [34, 197, 94],       // Verde
                'DESINCORPORADO' => [239, 68, 68], // Rojo
                'EXTRAVIADO' => [249, 115, 22],   // Naranja
                'EN_MANTENIMIENTO' => [59, 130, 246], // Azul
            ];
            $color = $colores[strtoupper($estado)] ?? [107, 114, 128]; // Gris por defecto

            $pdf->SetFillColor($color[0], $color[1], $color[2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'ESTADO: ' . strtoupper($this->t($estado)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);

            $widths = [30, 100, 30, 35, 40, 30];
            $headers = ['Código', 'Descripción', 'Precio', 'Tipo', 'Dependencia', 'Fecha'];

            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $totalBs = 0;
            foreach ($bienesGrupo as $bien) {
                $precio = (float)($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string)($bien->descripcion ?? ''), 50)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($bien->tipo_bien ?? '-'), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->truncate($bien->dependencia->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL ' . count($bienesGrupo) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }

    /**
     * Genera reporte por rango de fecha
     */
    public function generarPorFecha(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        $pdf = $this->make('P');

        // Convertir iterable a colección para poder iterar múltiples veces
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        // Tabla de bienes
        $widths = [25, 85, 25, 25, 25];
        $headers = ['Código', 'Descripción', 'Precio', 'Tipo', 'Estado'];

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $totalBs = 0;
        $hasData = false;

        foreach ($bienesArray as $bien) {
            $hasData = true;
            $precio = (float)($bien->precio ?? 0);
            $totalBs += $precio;

            $pdf->Cell($widths[0], 6, $this->t((string)($bien->codigo ?? '')), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate((string)($bien->descripcion ?? ''), 40)), 1);
            $pdf->Cell($widths[2], 6, number_format($precio, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3], 6, $this->t($bien->tipo_bien ?? '-'), 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $this->t($bien->estado ?? '-'), 1, 0, 'C');
            $pdf->Ln();
        }

        if (!$hasData) {
            $pdf->Cell(0, 10, $this->t('No hay bienes registrados en el rango de fecha seleccionado'), 0, 1, 'C');
        } else {
            // Totales
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($widths[0] + $widths[1], 7, 'TOTAL: ' . count($bienesArray) . ' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 7, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4], 7, '', 1, 0, 'C');
        }

        $pdf->Output('I', $fileName);
        return response()->headers->set('Content-Type', 'application/pdf');
    }
}
