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
        $bannerPath = public_path('images/baner.jpeg');
        $instName = config('app.name', 'Institución');

        $pdf = new class($orientation, 'mm', 'Letter', $bannerPath, $instName) extends FPDF
        {
            protected $banner;

            protected $instName;

            public function __construct($orientation, $unit, $size, $banner, $instName)
            {
                parent::__construct($orientation, $unit, $size);
                $this->banner = $banner;
                $this->instName = $instName;
            }

            // Header called automatically on each page
            public function Header()
            {
                // Banner institucional en la parte superior
                if ($this->banner && file_exists($this->banner)) {
                    // Calcular dimensiones para que el banner ocupe todo el ancho
                    $pageWidth = $this->GetPageWidth();
                    $bannerHeight = 20; // Altura del banner en mm

                    // Centrar el banner
                    $this->Image($this->banner, 10, 8, $pageWidth - 20, $bannerHeight);

                    // Espacio después del banner
                    $this->SetY(8 + $bannerHeight + 3);
                } else {
                    // Si no hay banner, mostrar nombre de la institución
                    $this->SetFont('Arial', 'B', 14);
                    $this->Cell(0, 8, utf8_decode($this->instName), 0, 1, 'C');
                    $this->Ln(2);
                }
            }

            // Footer called automatically on each page
            public function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(100, 100, 100);
                $this->Cell(0, 10, utf8_decode('Página '.$this->PageNo().'/{nb}'), 0, 0, 'C');
            }
        };

        $pdf->AliasNbPages();
        $pdf->SetMargins(10, 35, 10); // Margen superior aumentado para el banner
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        return $pdf;
    }

    protected function renderHeader(\FPDF $pdf, string $title, ?string $subtitle, string $generatedAt, array $data): void
    {
        // Título principal del reporte
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(0, 51, 102); // Azul institucional oscuro
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, $this->t(strtoupper($title)), 0, 1, 'C', true);
        $pdf->Ln(2);

        // Subtítulo si existe
        if ($subtitle) {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(0, 6, $this->t($subtitle), 0, 1, 'C');
            $pdf->Ln(1);
        }

        // Fecha de generación
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, $this->t('Fecha de generación: ').$generatedAt, 0, 1, 'R');
        $pdf->Ln(3);

        // Información institucional solo si hay datos
        if (! empty($data) && ! empty(array_filter($data))) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->SetTextColor(0, 0, 0);

            // Organismo
            if (! empty($data['org_nombre']) && $data['org_nombre'] !== 'N/A') {
                $pdf->Cell(0, 6, $this->t('ORGANISMO'), 0, 1, 'L', true);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 5, $this->t($data['org_nombre']), 0, 1, 'L');
                $pdf->Ln(2);
            }

            // Unidad Administradora
            if (! empty($data['uni_nombre']) && $data['uni_nombre'] !== 'N/A') {
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 6, $this->t('UNIDAD ADMINISTRADORA'), 0, 1, 'L', true);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 5, $this->t($data['uni_nombre']), 0, 1, 'L');
                $pdf->Ln(2);
            }

            // Dependencia
            if (! empty($data['dep_nombre']) && $data['dep_nombre'] !== 'N/A') {
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 6, $this->t('DEPENDENCIA'), 0, 1, 'L', true);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 5, $this->t($data['dep_nombre']), 0, 1, 'L');
                $pdf->Ln(2);
            }

            // Responsable
            if (! empty($data['res_u_nombre'])) {
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 6, $this->t('RESPONSABLE'), 0, 1, 'L', true);
                $pdf->SetFont('Arial', '', 8);
                $responsable = $data['res_u_nombre'];
                if (! empty($data['res_u_cedula'])) {
                    $responsable .= ' - C.I.: '.$data['res_u_cedula'];
                }
                $pdf->Cell(0, 5, $this->t($responsable), 0, 1, 'L');
                $pdf->Ln(2);
            }
        }

        // Línea separadora
        $pdf->SetDrawColor(0, 51, 102);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), $pdf->GetPageWidth() - 10, $pdf->GetY());
        $pdf->Ln(4);
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
        $primerBien = ! empty($bienesArray) ? reset($bienesArray) : null;

        $datosCabecera = [
            'org_codigo' => $primerBien?->dependencia?->unidadAdministradora?->organismo?->codigo ?? '0',
            'org_nombre' => $primerBien?->dependencia?->unidadAdministradora?->organismo?->nombre ?? 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA',
            'uni_codigo' => $primerBien?->dependencia?->unidadAdministradora?->codigo ?? 'N/A',
            'uni_nombre' => $primerBien?->dependencia?->unidadAdministradora?->nombre ?? 'N/A',
            'dep_codigo' => $primerBien?->dependencia?->codigo ?? 'N/A',
            'dep_nombre' => $primerBien?->dependencia?->nombre ?? 'N/A',
            // Responsables (Asegúrate de que estas relaciones existan en tu modelo)
            'res_p_cedula' => $primerBien?->dependencia?->unidadAdministradora?->responsable?->cedula ?? '3873777',
            'res_p_nombre' => $primerBien?->dependencia?->unidadAdministradora?->responsable?->nombre_completo ?? 'ENRY GOMEZ MAIZ',
            'res_u_cedula' => $primerBien?->dependencia?->responsable?->cedula ?? '',
            'res_u_nombre' => $primerBien?->dependencia?->responsable?->nombre_completo ?? '',
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

            $pdf->Cell($widths[0], 6, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');

            // Descripción con truncado para no romper la celda
            $pdf->Cell($widths[1], 6, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 50)), 1);

            // Precio
            $precio = (float) ($bien->precio ?? 0);
            $pdf->Cell($widths[2], 6, number_format($precio, 2, ',', '.'), 1, 0, 'R');
            $totalBs += $precio;

            // Dependencia (nombre corto)
            $pdf->Cell($widths[3], 6, $this->t($this->truncate($bien->dependencia?->nombre ?? '', 15)), 1, 0, 'C');

            // Fotos (SI/NO)
            $pdf->Cell($widths[4], 6, ($bien->fotografia ? 'SI' : 'NO'), 1, 0, 'C');

            // Fecha
            $pdf->Cell($widths[5], 6, $bien->created_at ? $bien->created_at->format('d/m/Y') : '', 1, 1, 'C');
        }

        // 5. Espacio vacío si no hay datos
        if (! $hasData) {
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
        $pdf = $this->make('L'); // Horizontal para mejor visualización

        // Agrupar bienes por dependencia
        $agrupados = [];
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        foreach ($bienesArray as $bien) {
            $depNombre = optional($bien->dependencia)->nombre ?? 'Sin Dependencia';
            if (! isset($agrupados[$depNombre])) {
                $agrupados[$depNombre] = [];
            }
            $agrupados[$depNombre][] = $bien;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $pdf->SetTextColor(0, 0, 0);
        $totalGeneral = 0;
        $cantidadGeneral = 0;

        foreach ($agrupados as $depNombre => $bienesGrupo) {
            // Verificar espacio para nueva página
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            // Encabezado del grupo - Estilo institucional
            $pdf->SetFillColor(0, 51, 102); // Azul institucional
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, $this->t('DEPENDENCIA: '.strtoupper($depNombre)), 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            // Encabezados de tabla
            $widths = [30, 95, 30, 35, 30, 40];
            $headers = ['Código', 'Descripción', 'Precio (Bs.)', 'Tipo', 'Estado', 'Fecha Adq.'];

            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetFont('Arial', 'B', 9);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Datos
            $pdf->SetFont('Arial', '', 8);
            $totalBs = 0;
            $alturaFila = 6;

            foreach ($bienesGrupo as $index => $bien) {
                $precio = (float) ($bien->precio ?? 0);
                $totalBs += $precio;

                // Alternar color de fondo para mejor legibilidad
                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], $alturaFila, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], $alturaFila, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 50)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], $alturaFila, number_format($precio, 2, ',', '.'), 1, 0, 'R', $fill);
                $pdf->Cell($widths[3], $alturaFila, $this->t($this->toString($bien->tipo_bien ?? '-')), 1, 0, 'C', $fill);
                $pdf->Cell($widths[4], $alturaFila, $this->t($this->toString($bien->estado ?? '-')), 1, 0, 'C', $fill);
                $pdf->Cell($widths[5], $alturaFila, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 1, 'C', $fill);
            }

            // Subtotal del grupo
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell($widths[0] + $widths[1], 7, $this->t('SUBTOTAL ('.count($bienesGrupo).' bienes)'), 1, 0, 'R', true);
            $pdf->Cell($widths[2], 7, number_format($totalBs, 2, ',', '.'), 1, 0, 'R', true);
            $pdf->Cell($widths[3] + $widths[4] + $widths[5], 7, '', 1, 1, 'C', true);
            $pdf->Ln(5);

            $totalGeneral += $totalBs;
            $cantidadGeneral += count($bienesGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('TOTAL GENERAL ('.$cantidadGeneral.' bienes)'), 1, 0, 'R', true);
        $pdf->Cell($widths[2], 8, number_format($totalGeneral, 2, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($widths[3] + $widths[4] + $widths[5], 8, '', 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
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
            if (! isset($agrupados[$uniNombre])) {
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
            $pdf->Cell(0, 8, 'UNIDAD ADMINISTRADORA: '.strtoupper($this->t($uniNombre)), 1, 1, 'L', true);
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
                $precio = (float) ($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 45)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($this->toString($bien->tipo_bien ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->toString($bien->estado ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $this->t($this->truncate($bien->dependencia?->nombre ?? '-', 15)), 1, 0, 'C');
                $pdf->Cell($widths[6], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL '.count($bienesGrupo).' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
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
            if (! isset($agrupados[$orgNombre])) {
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
            $pdf->Cell(0, 8, 'ORGANISMO: '.strtoupper($this->t($orgNombre)), 1, 1, 'L', true);
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
                $precio = (float) ($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 40)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($this->toString($bien->tipo_bien ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->toString($bien->estado ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $this->t($this->truncate($bien->dependencia?->unidadAdministradora?->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[6], 5, $this->t($this->truncate($bien->dependencia?->nombre ?? '-', 18)), 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL '.count($bienesGrupo).' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
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
            $tipo = $this->toString($bien->tipo_bien ?? 'Sin Tipo');
            if (! isset($agrupados[$tipo])) {
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
            $pdf->Cell(0, 8, 'TIPO DE BIEN: '.strtoupper($this->t($tipo)), 1, 1, 'L', true);
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
                $precio = (float) ($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 50)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($this->toString($bien->estado ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->truncate($bien->dependencia?->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL '.count($bienesGrupo).' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
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
            $estado = $this->toString($bien->estado ?? 'Sin Estado');
            if (! isset($agrupados[$estado])) {
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
            $pdf->Cell(0, 8, 'ESTADO: '.strtoupper($this->t($estado)), 1, 1, 'L', true);
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
                $precio = (float) ($bien->precio ?? 0);
                $totalBs += $precio;

                $pdf->Cell($widths[0], 5, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
                $pdf->Cell($widths[1], 5, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 50)), 1);
                $pdf->Cell($widths[2], 5, number_format($precio, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell($widths[3], 5, $this->t($this->toString($bien->tipo_bien ?? '-')), 1, 0, 'C');
                $pdf->Cell($widths[4], 5, $this->t($this->truncate($bien->dependencia?->nombre ?? '-', 20)), 1, 0, 'C');
                $pdf->Cell($widths[5], 5, $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : '-', 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell($widths[0] + $widths[1], 6, 'TOTAL '.count($bienesGrupo).' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4] + $widths[5], 6, '', 1, 0, 'C');
            $pdf->Ln(8);
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
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
            $precio = (float) ($bien->precio ?? 0);
            $totalBs += $precio;

            $pdf->Cell($widths[0], 6, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 40)), 1);
            $pdf->Cell($widths[2], 6, number_format($precio, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3], 6, $this->t($this->toString($bien->tipo_bien ?? '-')), 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $this->t($this->toString($bien->estado ?? '-')), 1, 0, 'C');
            $pdf->Ln();
        }

        if (! $hasData) {
            $pdf->Cell(0, 10, $this->t('No hay bienes registrados en el rango de fecha seleccionado'), 0, 1, 'C');
        } else {
            // Totales
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($widths[0] + $widths[1], 7, 'TOTAL: '.count($bienesArray).' BIENES', 1, 0, 'L');
            $pdf->Cell($widths[2], 7, number_format($totalBs, 2, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[3] + $widths[4], 7, '', 1, 0, 'C');
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }
}
