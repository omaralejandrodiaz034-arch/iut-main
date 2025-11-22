<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bien {{ $bien->codigo }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 24px;
            color: #1f2937;
            font-size: 14px;
            line-height: 1.5;
        }
        h1, h2 { margin: 0 0 12px 0; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 {
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #111827;
        }
        .field { margin-bottom: 10px; }
        .field-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .field-value {
            font-size: 14px;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 13px;
        }
        table thead { background: #f3f4f6; }
        table th,
        table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        .small {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema de Gestión de Inventario de Bienes</h1>
        <p class="small">Reporte institucional del bien</p>
    </div>

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
            <div class="field-label">Ubicación</div>
            <div class="field-value">{{ $bien->ubicacion ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Fecha de registro</div>
            <div class="field-value">{{ optional($bien->fecha_registro)->format('d/m/Y') }}</div>
        </div>
        <div class="field">
            <div class="field-label">Creado en el sistema</div>
            <div class="field-value">{{ optional($bien->created_at)->format('d/m/Y H:i') }}</div>
        </div>
        <div class="field">
            <div class="field-label">Última actualización</div>
            <div class="field-value">{{ optional($bien->updated_at)->format('d/m/Y H:i') }}</div>
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

    <p class="small">Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name ?? 'Sistema' }}</p>
</body>
</html>







