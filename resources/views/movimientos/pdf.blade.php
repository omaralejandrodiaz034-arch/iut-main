<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimiento #{{ $movimiento->id }}</title>
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
        .small {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema de Gestión de Inventario de Bienes</h1>
        <p class="small">Reporte de Movimiento</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Movimiento</div>

        <div class="field">
            <div class="field-label">ID</div>
            <div class="field-value">{{ $movimiento->id }}</div>
        </div>
        <div class="field">
            <div class="field-label">Fecha</div>
            <div class="field-value">{{ optional($movimiento->fecha)->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Tipo</div>
            <div class="field-value">{{ $movimiento->tipo }}</div>
        </div>
        <div class="field">
            <div class="field-label">Usuario</div>
            <div class="field-value">{{ $movimiento->usuario->nombre_completo ?? $movimiento->usuario->nombre ?? '—' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Observaciones</div>
            <div class="field-value">{{ $movimiento->observaciones ?? '—' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Entidad Relacionada</div>
        @php
            $s = $movimiento->subject;
            $label = $s?->nombre_completo
                ?? $s?->nombre
                ?? $s?->descripcion
                ?? $s?->codigo
                ?? ($movimiento->bien?->codigo
                    ? $movimiento->bien->codigo.' - '.$movimiento->bien->descripcion
                    : 'ID '.$movimiento->subject_id);
        @endphp

