<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bien {{ $bien->codigo }}</title>
    <style>
        * { box-sizing: border-box; margin:0; padding:0 }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#333 }

        /* Banner institucional */
        .header-banner { width:100%; margin-bottom:16px; text-align:center }
        .header-banner img { width:100%; max-width:100%; height:auto }

        .report-title { background:#003366; color:#fff; padding:10px; text-align:center; margin:12px 0; font-size:15px; font-weight:bold; text-transform:uppercase }
        .report-subtitle { text-align:center; font-style:italic; color:#666; margin-bottom:8px; font-size:12px }
        .report-meta { text-align:right; font-size:10px; color:#888; margin-bottom:12px }

        .section { border:1px solid #d1d5db; border-radius:6px; padding:12px; margin-bottom:14px }
        .section-title { font-size:14px; font-weight:600; margin-bottom:8px; color:#111827 }
        .field { margin-bottom:8px }
        .field-label { font-size:11px; text-transform:uppercase; color:#6b7280; margin-bottom:3px }
        .field-value { font-size:13px; color:#111827 }

        table { width:100%; border-collapse:collapse; margin-top:10px; font-size:12px }
        table thead { background:#f3f4f6 }
        th, td { border:1px solid #ddd; padding:6px 8px; text-align:left }
        .small { font-size:10px; color:#666 }

        .footer { position: fixed; bottom: 0; width:100%; text-align:center; font-size:10px; color:#888; padding-top:6px; border-top:1px solid #ddd }
    </style>
</head>
<body>
    <div class="header-banner">
        <img src="{{ public_path('images/baner.jpeg') }}" alt="Banner Institucional">
    </div>

    <div class="report-title">Detalle del Bien</div>
    <div class="report-subtitle">Reporte institucional del bien</div>
    <div class="report-meta">Generado: {{ now()->format('d/m/Y H:i') }}</div>

    <div class="section">
        <div class="section-title">Datos del Bien</div>
        <div class="field">
            <div class="field-label">Código</div>
            <div class="field-value">{{ $bien->codigo }}</div>
        </div>
        <div class="field">
            <div class="field-label">Descripción</div>
            <div class="field-value">{{ $bien->descripcion }}</div>
        </div>
        <div class="field">
            <div class="field-label">Precio</div>
            <div class="field-value">{{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.</div>
        </div>
        <div class="field">
            <div class="field-label">Estado</div>
            <div class="field-value">{{ $bien->estado?->name ?? (string)$bien->estado }}</div>
        </div>
        <div class="field">
            <div class="field-label">Fecha de registro</div>
            <div class="field-value">{{ optional($bien->fecha_registro)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dependencia</div>
        <div class="field">
            <div class="field-label">Nombre</div>
            <div class="field-value">{{ $dependencia->nombre ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Responsable</div>
            <div class="field-value">{{ $dependencia->responsable->nombre_completo ?? 'Sin asignar' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Movimientos registrados</div>
        <div class="field">
            <div class="field-label">Total</div>
            <div class="field-value">{{ $movimientos->count() }}</div>
        </div>

        @if ($movimientos->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Observaciones</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $movimiento)
                        <tr>
                            <td>{{ optional($movimiento->fecha)->format('d/m/Y H:i') }}</td>
                            <td>{{ $movimiento->tipo }}</td>
                            <td>{{ $movimiento->observaciones }}</td>
                            <td>{{ $movimiento->usuario?->nombre_completo ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="small">No se registran movimientos para este bien.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Fotografía</div>
        @if ($bien->fotografia)
            <img
                src="{{ str_starts_with($bien->fotografia, 'http') ? $bien->fotografia : asset('storage/'.$bien->fotografia) }}"
                alt="Fotografía del bien {{ $bien->codigo }}"
                style="max-width: 100%; height: auto; border-radius: 8px;"
            >
        @else
            <p class="small">No se ha registrado una fotografía para este bien.</p>
        @endif
    </div>

    <div class="footer">Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name ?? 'Sistema' }}</div>
</body>
</html>







