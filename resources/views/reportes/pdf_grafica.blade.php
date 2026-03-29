<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Datos de gráfica' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #222; font-size:12px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .filters table { width:100%; border-collapse:collapse; font-size:11px; margin-top:6px; }
        .report-table { width:100%; border-collapse:collapse; font-size:12px; }
        .report-table thead tr { background:#f7fafc; }
        .report-table th, .report-table td { padding:8px 10px; border-bottom:1px solid #e6edf3; }
        .report-table tbody tr:nth-child(even) { background:#fbfdff; }
        .total-row { background:#f1f5f9; font-weight:700; }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Carbon;
    $generatedAt = Carbon::now()->format('d/m/Y H:i');
    $isCurrency = isset($title) && (stripos($title, 'valor') !== false || stripos($title, 'bs') !== false || stripos($title, 'boliva') !== false);
    $numericValues = array_filter($data, fn($v) => is_numeric($v));
    $totalNumeric = array_sum($numericValues);
    $format = function($v) use ($isCurrency) {
        if (!is_numeric($v)) return $v;
        if ($isCurrency) return number_format($v, 2, ',', '.') . ' Bs.';
        return (intval($v) == $v) ? number_format($v, 0, ',', '.') : number_format($v, 2, ',', '.');
    };
@endphp

<div>
    <div class="header">
        <div>
            <div style="font-size:16px; font-weight:700;">Sistema IUT - Reportes</div>
            <div style="font-size:11px; color:#555;">Generado: {{ $generatedAt }}</div>
        </div>
        <div style="text-align:right; font-size:12px; color:#333;">{{ $title }}</div>
    </div>

    @if(!empty($filters))
        <div class="filters" style="margin-bottom:10px;">
            <strong>Filtros aplicados:</strong>
            <table>
                @foreach($filters as $k => $v)
                    <tr>
                        <td style="width:30%; padding:4px 6px; vertical-align:top; color:#444;"><strong>{{ $k }}</strong></td>
                        <td style="padding:4px 6px; color:#333;">@if(is_array($v)) {{ implode(', ', $v) }} @else {{ $v }} @endif</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div style="border:1px solid #e2e8f0; border-radius:6px; overflow:hidden;">
        <table class="report-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Etiqueta</th>
                    <th style="text-align:right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $label => $value)
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="text-align:right;">{{ $format($value) }}</td>
                    </tr>
                @endforeach
            </tbody>
            @if(count($numericValues) > 0)
            <tfoot>
                <tr class="total-row">
                    <td>Total</td>
                    <td style="text-align:right;">{{ $format($totalNumeric) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div style="margin-top:12px; font-size:11px; color:#666;">Fuente: Sistema de Inventario IUT</div>
</div>
</body>
</html>
