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

            public function Header()
            {
                if ($this->banner && file_exists($this->banner)) {
                    $pageWidth = $this->GetPageWidth();
                    $bannerHeight = 20;
                    $this->Image($this->banner, 10, 8, $pageWidth - 20, $bannerHeight);
                    $this->SetY(8 + $bannerHeight + 3);
                } else {
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

            public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
            {
                if (is_string($txt)) {
                    $txt = utf8_decode($txt);
                }

                return parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
            }

            public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
            {
                if (is_string($txt)) {
                    $txt = utf8_decode($txt);
                }

                return parent::MultiCell($w, $h, $txt, $border, $align, $fill);
            }

            public function Write($h, $txt, $link = '')
            {
                if (is_string($txt)) {
                    $txt = utf8_decode($txt);
                }

                return parent::Write($h, $txt, $link);
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
        iterable $bienes,
        array $datosInstitucionales = []  // Nuevo parámetro
    ) {
        // Convertir iterable a array
        $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

        $pdf = $this->make('P');

        // Pasar los datos institucionales (si existen) al renderHeader
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, $datosInstitucionales);

        // Configuración de la tabla
        $widths = [25, 80, 25, 25, 16, 25];
        $headers = ['Código', 'Descripción', 'Precio Bs.', 'Dependencia', 'Fotos', 'Fecha'];

        // Encabezados de tabla
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C');
        }
        $pdf->Ln();

        // Datos
        $pdf->SetFont('Arial', '', 7);
        $totalBs = 0;
        $hasData = false;

        foreach ($bienesArray as $bien) {
            $hasData = true;

            $pdf->Cell($widths[0], 6, $this->t((string) ($bien->codigo ?? '')), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate((string) ($bien->descripcion ?? ''), 50)), 1);

            $precio = (float) ($bien->precio ?? 0);
            $pdf->Cell($widths[2], 6, number_format($precio, 2, ',', '.'), 1, 0, 'R');
            $totalBs += $precio;

            $pdf->Cell($widths[3], 6, $this->t($this->truncate($bien->dependencia?->nombre ?? '', 15)), 1, 0, 'C');
            $pdf->Cell($widths[4], 6, ($bien->fotografia ? 'SI' : 'NO'), 1, 0, 'C');
            $pdf->Cell($widths[5], 6, $bien->created_at ? $bien->created_at->format('d/m/Y') : '', 1, 1, 'C');
        }

        if (! $hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron registros.'), 1, 1, 'C');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('Total Bs.:'), 1, 0, 'R');
        $pdf->Cell($widths[2], 8, number_format($totalBs, 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell($widths[3] + $widths[4] + $widths[5], 8, '', 1, 1);

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

        return $text;
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

        $totalGeneralBs = 0;
        $totalGeneralCount = 0;

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

            $totalGeneralBs += $totalBs;
            $totalGeneralCount += count($bienesGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('TOTAL GENERAL ('.$totalGeneralCount.' bienes)'), 1, 0, 'R', true);
        $pdf->Cell($widths[2], 8, number_format($totalGeneralBs, 2, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 8, '', 1, 1, 'C', true);

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

        $totalGeneralBs = 0;
        $totalGeneralCount = 0;

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

            $totalGeneralBs += $totalBs;
            $totalGeneralCount += count($bienesGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('TOTAL GENERAL ('.$totalGeneralCount.' bienes)'), 1, 0, 'R', true);
        $pdf->Cell($widths[2], 8, number_format($totalGeneralBs, 2, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($widths[3] + $widths[4] + $widths[5] + $widths[6], 8, '', 1, 1, 'C', true);

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

        $totalGeneralBs = 0;
        $totalGeneralCount = 0;

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

            $totalGeneralBs += $totalBs;
            $totalGeneralCount += count($bienesGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('TOTAL GENERAL ('.$totalGeneralCount.' bienes)'), 1, 0, 'R', true);
        $pdf->Cell($widths[2], 8, number_format($totalGeneralBs, 2, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($widths[3] + $widths[4] + $widths[5], 8, '', 1, 1, 'C', true);

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

        $totalGeneralBs = 0;
        $totalGeneralCount = 0;

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

            $totalGeneralBs += $totalBs;
            $totalGeneralCount += count($bienesGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1], 8, $this->t('TOTAL GENERAL ('.$totalGeneralCount.' bienes)'), 1, 0, 'R', true);
        $pdf->Cell($widths[2], 8, number_format($totalGeneralBs, 2, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($widths[3] + $widths[4] + $widths[5], 8, '', 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de listado general de dependencias
     */
    public function downloadDependenciasListado(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $dependencias
    ) {
        $dependenciasArray = $dependencias instanceof \Illuminate\Support\Collection
            ? $dependencias->all()
            : iterator_to_array($dependencias);

        $pdf = $this->make('P');
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $widths = [20, 50, 40, 30, 25];
        $headers = ['Código', 'Nombre', 'Unidad', 'Responsable', 'Bienes'];

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $totalBienes = 0;
        $hasData = false;

        foreach ($dependenciasArray as $dep) {
            $hasData = true;
            $totalBienes += $dep->bienes_count ?? $dep->bienes->count() ?? 0;

            $pdf->Cell($widths[0], 6, $this->t($dep->codigo ?? ''), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate($dep->nombre ?? '', 35)), 1);
            $pdf->Cell($widths[2], 6, $this->t($this->truncate($dep->unidadAdministradora?->nombre ?? '-', 25)), 1);
            $pdf->Cell($widths[3], 6, $this->t($this->truncate($dep->responsable?->nombre ?? '-', 20)), 1);
            $pdf->Cell($widths[4], 6, ($dep->bienes_count ?? $dep->bienes->count() ?? 0), 1, 1, 'C');
        }

        if (! $hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron registros.'), 1, 1, 'C');
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($widths[0] + $widths[1] + $widths[2], 8, $this->t('TOTAL GENERAL'), 1, 0, 'R', true);
        $pdf->Cell($widths[3], 8, $this->t($dependenciasArray ? count($dependenciasArray) : 0).' dependencias', 1, 0, 'C', true);
        $pdf->Cell($widths[4], 8, $totalBienes.' bienes', 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de dependencias agrupado por unidad administradora
     */
    public function generarDependenciasPorUnidad(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $dependencias
    ) {
        $dependenciasArray = $dependencias instanceof \Illuminate\Support\Collection
            ? $dependencias->all()
            : iterator_to_array($dependencias);

        $pdf = $this->make('L');

        // Agrupar dependencias por unidad
        $agrupados = [];
        foreach ($dependenciasArray as $dep) {
            $uniNombre = $dep->unidadAdministradora?->nombre ?? 'Sin Unidad';
            if (! isset($agrupados[$uniNombre])) {
                $agrupados[$uniNombre] = [];
            }
            $agrupados[$uniNombre][] = $dep;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $totalGeneralDeps = 0;
        $totalGeneralBienes = 0;

        foreach ($agrupados as $uniNombre => $depsGrupo) {
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(0, 82, 147);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'UNIDAD ADMINISTRADORA: '.strtoupper($this->t($uniNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            $widths = [20, 55, 35, 30, 25];
            $headers = ['Código', 'Nombre', 'Responsable', 'Teléfono', 'Bienes'];

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(220, 220, 220);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 8);
            $subtotalBienes = 0;

            foreach ($depsGrupo as $index => $dep) {
                $bienesCount = $dep->bienes_count ?? $dep->bienes->count() ?? 0;
                $subtotalBienes += $bienesCount;
                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], 6, $this->t($dep->codigo ?? ''), 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $this->t($this->truncate($dep->nombre ?? '', 35)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], 6, $this->t($this->truncate($dep->responsable?->nombre ?? '-', 25)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[3], 6, $this->t($dep->responsable?->telefono ?? '-'), 1, 0, 'C', $fill);
                $pdf->Cell($widths[4], 6, $bienesCount, 1, 1, 'C', $fill);
            }

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell($widths[0] + $widths[1], 7, $this->t('SUBTOTAL ('.count($depsGrupo).' dependencias)'), 1, 0, 'R', true);
            $pdf->Cell($widths[2] + $widths[3], 7, '', 1, 0, 'C', true);
            $pdf->Cell($widths[4], 7, $subtotalBienes.' bienes', 1, 1, 'C', true);
            $pdf->Ln(5);

            $totalGeneralDeps += count($depsGrupo);
            $totalGeneralBienes += $subtotalBienes;
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($dependenciasArray).' dependencias, '.$totalGeneralBienes.' bienes'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de dependencias agrupado por responsable
     */
    public function generarDependenciasPorResponsable(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $dependencias
    ) {
        $dependenciasArray = $dependencias instanceof \Illuminate\Support\Collection
            ? $dependencias->all()
            : iterator_to_array($dependencias);

        $pdf = $this->make('L');

        // Agrupar dependencias por responsable
        $agrupados = [];
        foreach ($dependenciasArray as $dep) {
            $resNombre = $dep->responsable?->nombre ?? 'Sin Responsable';
            if (! isset($agrupados[$resNombre])) {
                $agrupados[$resNombre] = [];
            }
            $agrupados[$resNombre][] = $dep;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $totalGeneralDeps = 0;
        $totalGeneralBienes = 0;

        foreach ($agrupados as $resNombre => $depsGrupo) {
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(128, 0, 32);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'RESPONSABLE: '.strtoupper($this->t($resNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            $widths = [20, 50, 35, 30, 25, 30];
            $headers = ['Código', 'Nombre', 'Unidad', 'Teléfono', 'Email', 'Bienes'];

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(220, 220, 220);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 8);
            $subtotalBienes = 0;

            foreach ($depsGrupo as $index => $dep) {
                $bienesCount = $dep->bienes_count ?? $dep->bienes->count() ?? 0;
                $subtotalBienes += $bienesCount;
                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], 6, $this->t($dep->codigo ?? ''), 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $this->t($this->truncate($dep->nombre ?? '', 35)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], 6, $this->t($this->truncate($dep->unidadAdministradora?->nombre ?? '-', 22)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[3], 6, $this->t($dep->responsable?->telefono ?? '-'), 1, 0, 'C', $fill);
                $pdf->Cell($widths[4], 6, $this->t($this->truncate($dep->responsable?->email ?? '-', 22)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[5], 6, $bienesCount, 1, 1, 'C', $fill);
            }

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell($widths[0] + $widths[1], 7, $this->t('SUBTOTAL ('.count($depsGrupo).' dependencias)'), 1, 0, 'R', true);
            $pdf->Cell($widths[2] + $widths[3] + $widths[4], 7, '', 1, 0, 'C', true);
            $pdf->Cell($widths[5], 7, $subtotalBienes.' bienes', 1, 1, 'C', true);
            $pdf->Ln(5);

            $totalGeneralDeps += count($depsGrupo);
            $totalGeneralBienes += $subtotalBienes;
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($dependenciasArray).' dependencias, '.$totalGeneralBienes.' bienes'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de listado general de unidades administradoras
     */
    public function downloadUnidadesListado(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $unidades
    ) {
        $unidadesArray = $unidades instanceof \Illuminate\Support\Collection
            ? $unidades->all()
            : iterator_to_array($unidades);

        $pdf = $this->make('P');
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $widths = [20, 60, 40, 25, 25];
        $headers = ['Código', 'Nombre', 'Organismo', 'Dependencias', 'Bienes'];

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $totalDependencias = 0;
        $totalBienes = 0;
        $hasData = false;

        foreach ($unidadesArray as $uni) {
            $hasData = true;
            $totalDependencias += $uni->dependencias_count ?? $uni->dependencias->count() ?? 0;

            $bienesCount = 0;
            foreach ($uni->dependencias ?? [] as $dep) {
                $bienesCount += $dep->bienes_count ?? $dep->bienes->count() ?? 0;
            }
            $totalBienes += $bienesCount;

            $pdf->Cell($widths[0], 6, $this->t($uni->codigo ?? ''), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate($uni->nombre ?? '', 40)), 1);
            $pdf->Cell($widths[2], 6, $this->t($this->truncate($uni->organismo?->nombre ?? '-', 25)), 1);
            $pdf->Cell($widths[3], 6, ($uni->dependencias_count ?? $uni->dependencias->count() ?? 0), 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $bienesCount, 1, 1, 'C');
        }

        if (! $hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron registros.'), 1, 1, 'C');
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($unidadesArray).' unidades, '.$totalDependencias.' dependencias, '.$totalBienes.' bienes'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de listado general de organismos.
     */
    public function downloadOrganismosListado(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $organismos
    ) {
        $organismosArray = $organismos instanceof \Illuminate\Support\Collection
            ? $organismos->all()
            : iterator_to_array($organismos);

        $pdf = $this->make('P');
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $widths = [15, 50, 25, 25, 25];
        $headers = ['Código', 'Nombre', 'Unidades', 'Dependencias', 'Bienes'];

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $totalUnidades = 0;
        $totalDependencias = 0;
        $totalBienes = 0;
        $hasData = false;

        foreach ($organismosArray as $org) {
            $hasData = true;
            $totalUnidades += $org->unidades_count ?? $org->unidadesAdministradoras->count() ?? 0;

            $dependenciasCount = 0;
            $bienesCount = 0;
            foreach ($org->unidadesAdministradoras ?? [] as $uni) {
                $dependenciasCount += $uni->dependencias_count ?? $uni->dependencias->count() ?? 0;
                foreach ($uni->dependencias ?? [] as $dep) {
                    $bienesCount += $dep->bienes_count ?? $dep->bienes->count() ?? 0;
                }
            }
            $totalDependencias += $dependenciasCount;
            $totalBienes += $bienesCount;

            $pdf->Cell($widths[0], 6, $this->t($org->codigo ?? ''), 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $this->t($this->truncate($org->nombre ?? '', 40)), 1);
            $pdf->Cell($widths[2], 6, ($org->unidades_count ?? $org->unidadesAdministradoras->count() ?? 0), 1, 0, 'C');
            $pdf->Cell($widths[3], 6, $dependenciasCount, 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $bienesCount, 1, 1, 'C');
        }

        if (! $hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron registros.'), 1, 1, 'C');
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($organismosArray).' organismos, '.$totalUnidades.' unidades, '.$totalDependencias.' dependencias, '.$totalBienes.' bienes'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de unidades agrupado por organismo
     */
    public function generarUnidadesPorOrganismo(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $unidades
    ) {
        $unidadesArray = $unidades instanceof \Illuminate\Support\Collection
            ? $unidades->all()
            : iterator_to_array($unidades);

        $pdf = $this->make('L');

        $agrupados = [];
        foreach ($unidadesArray as $uni) {
            $orgNombre = $uni->organismo?->nombre ?? 'Sin Organismo';
            if (! isset($agrupados[$orgNombre])) {
                $agrupados[$orgNombre] = [];
            }
            $agrupados[$orgNombre][] = $uni;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $totalGeneralDeps = 0;
        $totalGeneralBienes = 0;
        $totalGeneralUnidades = 0;

        foreach ($agrupados as $orgNombre => $unisGrupo) {
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(34, 85, 51);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'ORGANISMO: '.strtoupper($this->t($orgNombre)), 1, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            $widths = [20, 55, 30, 25, 25];
            $headers = ['Código', 'Nombre', 'Dependencias', 'Responsable', 'Bienes'];

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(220, 220, 220);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 8);
            $subtotalDeps = 0;
            $subtotalBienes = 0;

            foreach ($unisGrupo as $index => $uni) {
                $depsCount = $uni->dependencias_count ?? $uni->dependencias->count() ?? 0;
                $subtotalDeps += $depsCount;

                $bienesCount = 0;
                foreach ($uni->dependencias ?? [] as $dep) {
                    $bienesCount += $dep->bienes_count ?? $dep->bienes->count() ?? 0;
                }
                $subtotalBienes += $bienesCount;

                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], 6, $this->t($uni->codigo ?? ''), 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $this->t($this->truncate($uni->nombre ?? '', 35)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], 6, $depsCount, 1, 0, 'C', $fill);
                $pdf->Cell($widths[3], 6, '-', 1, 0, 'C', $fill);
                $pdf->Cell($widths[4], 6, $bienesCount, 1, 1, 'C', $fill);
            }

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell($widths[0] + $widths[1], 7, $this->t('SUBTOTAL ('.count($unisGrupo).' unidades)'), 1, 0, 'R', true);
            $pdf->Cell($widths[2], 7, $subtotalDeps.' dependencias', 1, 0, 'C', true);
            $pdf->Cell($widths[3], 7, '-', 1, 0, 'C', true);
            $pdf->Cell($widths[4], 7, $subtotalBienes.' bienes', 1, 1, 'C', true);
            $pdf->Ln(5);

            $totalGeneralDeps += $subtotalDeps;
            $totalGeneralBienes += $subtotalBienes;
            $totalGeneralUnidades += count($unisGrupo);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($unidadesArray).' unidades, '.$totalGeneralDeps.' dependencias, '.$totalGeneralBienes.' bienes'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de listado general de movimientos.
     */
    public function downloadMovimientosListado(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $movimientos
    ) {
        $movimientosArray = $movimientos instanceof \Illuminate\Support\Collection
            ? $movimientos->all()
            : iterator_to_array($movimientos);

        $pdf = $this->make('P');
        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $widths = [28, 30, 55, 40, 40];
        $headers = ['Fecha', 'Tipo', 'Entidad', 'Usuario', 'Observaciones'];

        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $this->t($header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $hasData = false;
        $index = 0;

        foreach ($movimientosArray as $mov) {
            $hasData = true;
            $fill = ($index % 2 == 0);
            if ($fill) {
                $pdf->SetFillColor(248, 248, 248);
            }

            $pdf->Cell($widths[0], 6, $mov->fecha ? $mov->fecha->format('d/m/Y') : '-', 1, 0, 'C', $fill);
            $pdf->Cell($widths[1], 6, $this->t($this->truncate($mov->tipo ?? '-', 20)), 1, 0, 'C', $fill);
            $pdf->Cell($widths[2], 6, $this->t($this->truncate(class_basename($mov->subject_type ?? '-'), 25)), 1, 0, 'L', $fill);
            $pdf->Cell($widths[3], 6, $this->t($this->truncate($mov->usuario?->nombre_completo ?? $mov->usuario?->name ?? '-', 25)), 1, 0, 'L', $fill);
            $pdf->Cell($widths[4], 6, $this->t($this->truncate($mov->observaciones ?? '-', 28)), 1, 1, 'L', $fill);
            $index++;
        }

        if (! $hasData) {
            $pdf->Cell(array_sum($widths), 10, $this->t('No se encontraron movimientos.'), 1, 1, 'C');
        }

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL: '.count($movimientosArray).' movimientos'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de movimientos agrupados por tipo.
     */
    public function generarMovimientosPorTipo(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $movimientos
    ) {
        $movimientosArray = $movimientos instanceof \Illuminate\Support\Collection
            ? $movimientos->all()
            : iterator_to_array($movimientos);

        $pdf = $this->make('P');

        $agrupados = [];
        foreach ($movimientosArray as $mov) {
            $tipo = $mov->tipo ?? 'Sin Tipo';
            if (! isset($agrupados[$tipo])) {
                $agrupados[$tipo] = [];
            }
            $agrupados[$tipo][] = $mov;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $totalGeneral = count($movimientosArray);

        foreach ($agrupados as $tipo => $movsGrupo) {
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(0, 51, 102);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'TIPO: '.strtoupper($this->t($tipo)).' ('.count($movsGrupo).' movimientos)', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            $widths = [28, 30, 45, 35, 50];
            $headers = ['Fecha', 'Usuario', 'Entidad', 'Sujeto', 'Observaciones'];

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $index = 0;
            foreach ($movsGrupo as $mov) {
                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], 6, $mov->fecha ? $mov->fecha->format('d/m/Y') : '-', 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $this->t($this->truncate($mov->usuario?->nombre_completo ?? $mov->usuario?->name ?? '-', 20)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], 6, $this->t($this->truncate(class_basename($mov->subject_type ?? '-'), 22)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[3], 6, $this->t($this->truncate($mov->subject?->nombre_completo ?? $mov->subject?->nombre ?? $mov->subject?->descripcion ?? $mov->subject?->codigo ?? '-', 20)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[4], 6, $this->t($this->truncate($mov->observaciones ?? '-', 30)), 1, 1, 'L', $fill);
                $index++;
            }

            $pdf->Ln(4);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.$totalGeneral.' movimientos'), 1, 1, 'C', true);

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    /**
     * Genera reporte de movimientos agrupados por usuario.
     */
    public function generarMovimientosPorUsuario(
        string $fileName,
        string $title,
        ?string $subtitle,
        string $generatedAt,
        iterable $movimientos
    ) {
        $movimientosArray = $movimientos instanceof \Illuminate\Support\Collection
            ? $movimientos->all()
            : iterator_to_array($movimientos);

        $pdf = $this->make('P');

        $agrupados = [];
        foreach ($movimientosArray as $mov) {
            $usuarioNombre = $mov->usuario?->nombre_completo ?? $mov->usuario?->name ?? 'Sin Usuario';
            if (! isset($agrupados[$usuarioNombre])) {
                $agrupados[$usuarioNombre] = [];
            }
            $agrupados[$usuarioNombre][] = $mov;
        }

        $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);

        $totalGeneral = count($movimientosArray);

        foreach ($agrupados as $usuarioNombre => $movsGrupo) {
            if ($pdf->GetY() > 170) {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(34, 85, 51);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'USUARIO: '.strtoupper($this->t($usuarioNombre)).' ('.count($movsGrupo).' movimientos)', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(1);

            $widths = [28, 30, 50, 50];
            $headers = ['Fecha', 'Tipo', 'Entidad', 'Observaciones'];

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 6, $this->t($header), 1, 0, 'C', true);
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 7);
            $index = 0;
            foreach ($movsGrupo as $mov) {
                $fill = ($index % 2 == 0);
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                }

                $pdf->Cell($widths[0], 6, $mov->fecha ? $mov->fecha->format('d/m/Y') : '-', 1, 0, 'C', $fill);
                $pdf->Cell($widths[1], 6, $this->t($this->truncate($mov->tipo ?? '-', 20)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[2], 6, $this->t($this->truncate(class_basename($mov->subject_type ?? '-').' - '.($mov->subject?->nombre_completo ?? $mov->subject?->nombre ?? $mov->subject?->descripcion ?? $mov->subject?->codigo ?? '-'), 28)), 1, 0, 'L', $fill);
                $pdf->Cell($widths[3], 6, $this->t($this->truncate($mov->observaciones ?? '-', 30)), 1, 1, 'L', $fill);
                $index++;
            }

            $pdf->Ln(4);
        }

        // Total general
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 51, 102);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(array_sum($widths), 8, $this->t('TOTAL GENERAL: '.count($movimientosArray).' movimientos'), 1, 1, 'C', true);

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
