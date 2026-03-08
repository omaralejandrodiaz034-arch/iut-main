<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Traslado de Bien Patrimonial - {{ $folio ?? 'S/N' }}</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 3cm 2.5cm;
            size: A4;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.6;
            text-align: justify;
        }
        .encabezado {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .titulo {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .subtitulo {
            font-size: 12pt;
            font-weight: bold;
            margin: 8px 0;
        }
        .lugar-fecha {
            text-align: right;
            font-style: italic;
            margin-bottom: 25px;
        }
        .cuerpo p {
            margin: 12px 0;
            text-indent: 40px;
        }
        .enumeracion {
            margin-left: 40px;
        }
        .firmas {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .firma {
            flex: 1;
            text-align: center;
        }
        .linea-firma {
            border-top: 1px solid #000;
            width: 100%;
            max-width: 300px;
            margin: 60px auto 10px;
        }
        .firma strong {
            display: block;
            margin-top: 8px;
            font-size: 11pt;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            font-size: 9pt;
            text-align: center;
            color: #444;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
        .destacado {
            background-color: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <div class="encabezado">
        {{-- incrustar logo como base64 (DomPDF lee sin problemas) --}}
        @php
            // buscar logo en diferentes formatos; el usuario agregó logo.gif
            $possible = ['logo-uptos.png', 'logo.gif', 'logo.png'];
            $logoPath = null;
            foreach ($possible as $name) {
                $path = public_path('images/'.$name);
                if (file_exists($path)) {
                    $logoPath = $path;
                    break;
                }
            }
            $logoData = '';
            if ($logoPath && file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        @endphp
        <img src="{{ $logoData ?: '' }}" class="logo" alt="Escudo UPTOS">
        <div class="titulo">REPÚBLICA BOLIVARIANA DE VENEZUELA</div>
        <div class="subtitulo">UNIVERSIDAD POLITÉCNICA TERRITORIAL DEL OESTE DE SUCRE</div>
        <div>"CLODOSBALDO RUSSIÁN"</div>
        <div class="subtitulo">ACTA DE TRASLADO DE BIEN PATRIMONIAL</div>
        <div>Folio: {{ $folio ?? 'TRAS-' . now()->format('Y') . '-' . str_pad($bien->id ?? 'XXXXX', 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="lugar-fecha">
        En la ciudad de {{ config('app.ciudad', 'Cumaná') }}, estado Sucre, a los {{ now()->format('d') }} días del mes de {{ now()->format('F') }} del año {{ now()->format('Y') }}.
    </div>

    <div class="cuerpo">
        <p>Comparecen por una parte el ciudadano <strong>{{ $usuario ?? '________________________________________' }}</strong>, titular de la cédula de identidad N° <strong>____________________</strong>, en su carácter de Responsable Patrimonial / Funcionario autorizado del Sistema de Gestión de Bienes Patrimoniales de la Universidad Politécnica Territorial del Oeste de Sucre "Clodosbaldo Russián", quienes proceden a levantar la presente <strong>ACTA DE TRASLADO DE BIEN PATRIMONIAL</strong>.</p>

        <p>Que en fecha de hoy, se ha realizado el traslado del siguiente bien mueble registrado en el inventario patrimonial de esta institución:</p>

        <div class="enumeracion">
            <p><strong>1. Código del bien:</strong> <span class="destacado">{{ $bien->codigo ?? '—' }}</span></p>
            <p><strong>2. Descripción detallada:</strong> {{ $bien->descripcion ?? '—' }}</p>
            <p><strong>3. Tipo de bien / Clasificación:</strong> {{ $bien->tipo_bien?->label() ?? $bien->tipo ?? '—' }}</p>
            <p><strong>4. Dependencia de Origen:</strong> {{ $dependencia_anterior ?? '—' }}</p>
            <p><strong>5. Dependencia de Destino:</strong> <span class="destacado">{{ $dependencia_nueva ?? '—' }}</span></p>
        </div>

        <p><strong>MOTIVO DEL TRASLADO:</strong></p>
        <p>{{ $motivo ?? 'No especificado' }}</p>

        <p>Que el bien antes descrito ha sido trasladado de la dependencia de origen a la dependencia de destino, quedando bajo la responsabilidad del nuevo responsable patrimonial. Este traslado queda formalizado mediante la presente acta para todos los efectos legales y administrativos correspondientes.</p>

        <p>Leída la presente acta en voz alta y encontrándose conforme todo su contenido, los comparecientes firman al pie en señal de autenticidad y conformidad.</p>
    </div>

    <div class="firmas">
        <div class="firma">
            <div class="linea-firma"></div>
            <strong>Responsable Patrimonial (Origen)</strong><br>
            Nombre y Apellido<br>
            C.I. ____________________
        </div>

        <div class="firma">
            <div class="linea-firma"></div>
            <strong>Responsable Patrimonial (Destino)</strong><br>
            Nombre y Apellido<br>
            C.I. ____________________
        </div>
    </div>

    <div class="footer">
        Generado por el Sistema de Gestión de Bienes Patrimoniales UPTOS — {{ now()->format('d/m/Y H:i') }}<br>
        Documento oficial — Uso exclusivo institucional — Confidencial
    </div>

</body>
</html>
