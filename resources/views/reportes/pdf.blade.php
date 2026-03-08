<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'Reporte' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px;
            color: #333;
        }
        
        /* Banner institucional */
        .header-banner {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .header-banner img {
            width: 100%;
            max-width: 100%;
            height: auto;
        }
        
        /* Título del reporte */
        .report-title {
            background: #003366;
            color: white;
            padding: 12px;
            text-align: center;
            margin: 15px 0 10px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .report-subtitle {
            text-align: center;
            font-style: italic;
            color: #666;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .report-meta {
            text-align: right;
            font-size: 10px;
            color: #888;
            margin-bottom: 15px;
        }
        
        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            font-size: 10px;
        }
        
        th, td { 
            border: 1px solid #ddd; 
            padding: 6px 8px;
            text-align: left;
        }
        
        th { 
            background: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        
        tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        /* Secciones agrupadas */
        .group-header {
            background: #003366;
            color: white;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .subtotal {
            background: #e6e6e6;
            font-weight: bold;
        }
        
        .total {
            background: #003366;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        
        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .small { font-size: 9px; color: #666; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #888;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Banner institucional -->
    <div class="header-banner">
        <img src="{{ public_path('images/baner.jpeg') }}" alt="Banner Institucional">
    </div>

    <!-- Título del reporte -->
    <div class="report-title">
        {{ $titulo ?? 'Reporte' }}
    </div>

    @isset($subtitulo)
        <div class="report-subtitle">
            {{ $subtitulo }}
        </div>
    @endisset

    <div class="report-meta">
        Fecha de generación: {{ ($generadoEn ?? now())->format('d/m/Y H:i') }}
    </div>

    @yield('contenido')
</body>
</html>
