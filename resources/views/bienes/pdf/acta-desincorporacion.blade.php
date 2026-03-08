<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Desincorporación de Bien Patrimonial - {{ $folio ?? 'S/N' }}</title>
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
            margin-top: 100px;          /* más espacio arriba para que no quede pegado al texto */
            display: flex;
            justify-content: space-between;
            gap: 20px;                  /* separación entre columnas */
        }
        .firma {
            flex: 1;                    /* ocupan el mismo ancho */
            text-align: center;
        }
        .linea-firma {
            border-top: 1px solid #000;
            width: 100%;
            max-width: 300px;
            margin: 60px auto 10px;     /* más espacio arriba de la línea para firma a mano */
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
    </style>
</head>
<body>

    <div class="encabezado">
        <img src="https://www.uptos.edu.ve/wp-content/uploads/2026/02/cropped-cropped-WhatsApp-Image-2026-02-11-at-10.17.38-PM-1.jpeg"
             class="logo" alt="Escudo UPTOS">
        <div class="titulo">REPÚBLICA BOLIVARIANA DE VENEZUELA</div>
        <div class="subtitulo">UNIVERSIDAD POLITÉCNICA TERRITORIAL DEL OESTE DE SUCRE</div>
        <div>"CLODOSBALDO RUSSIÁN"</div>
        <div class="subtitulo">ACTA DE DESINCORPORACIÓN DE BIEN PATRIMONIAL</div>
        <div>Folio: {{ $folio ?? 'DES-' . now()->format('Y') . '-' . str_pad($bien->id ?? 'XXXXX', 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="lugar-fecha">
        En la ciudad de {{ config('app.ciudad', 'Cumaná') }}, estado Sucre, a los {{ now()->format('d') }} días del mes de {{ now()->format('F') }} del año {{ now()->format('Y') }}.
    </div>

    <div class="cuerpo">
        <p>Comparecen por una parte el ciudadano <strong>{{ $responsable ?? '________________________________________' }}</strong>, titular de la cédula de identidad N° <strong>____________________</strong>, en su carácter de Responsable Patrimonial / Jefe de la Dependencia / Área de {{ $dependencia ?? '______________________________' }}, y por la otra el ciudadano <strong>{{ $usuario ?? '________________________________________' }}</strong>, titular de la cédula de identidad N° <strong>____________________</strong>, en su carácter de funcionario autorizado del Sistema de Gestión de Bienes Patrimoniales de esta Universidad, quienes proceden a levantar la presente <strong>ACTA DE DESINCORPORACIÓN DE BIEN PATRIMONIAL</strong>, de conformidad con lo establecido en el Decreto con Rango, Valor y Fuerza de Ley Orgánica de Bienes Públicos y la normativa técnica emanada de la Superintendencia de Bienes Públicos.</p>

        <p>Que en fecha de hoy, se ha verificado la condición del siguiente bien mueble registrado en el inventario patrimonial de esta institución:</p>

        <div class="enumeracion">
            <p><strong>1. Código del bien:</strong> {{ $bien->codigo ?? '—' }}</p>
            <p><strong>2. Descripción detallada:</strong> {{ $bien->descripcion ?? '—' }}</p>
            <p><strong>3. Tipo de bien / Clasificación:</strong> {{ $bien->tipo_bien?->label() ?? $bien->tipo ?? '—' }}</p>
            <p><strong>4. Dependencia / Área actual:</strong> {{ $dependencia ?? '—' }}</p>
            <p><strong>5. Responsable asignado:</strong> {{ $responsable ?? '—' }}</p>
        </div>

        <p>Que el bien antes descrito se encuentra en estado de <strong>{{ $motivo ?? 'inservibilidad / obsolescencia / deterioro' }}</strong>, lo cual ha sido constatado mediante inspección física realizada por el personal competente, y que por tal razón resulta procedente su desincorporación del registro patrimonial de la Universidad Politécnica Territorial del Oeste de Sucre "Clodosbaldo Russián".</p>

        <p>Que una vez desincorporado, el bien quedará bajo la responsabilidad del solicitante o de la dependencia correspondiente para su destino final (destrucción, reciclaje, donación o enajenación según corresponda), de conformidad con las normas legales vigentes y las instrucciones de la Unidad de Bienes Públicos de esta casa de estudios.</p>

        <p>Leída la presente acta en voz alta y encontrándose conforme todo su contenido, los comparecientes firman al pie en señal de autenticidad y conformidad.</p>
    </div>

    <!-- Firmas en paralelo (sin testigos) -->
    <div class="firmas">
        <div class="firma">
            <div class="linea-firma"></div>
            <strong>Responsable Patrimonial / Jefe de Dependencia</strong><br>
            {{ $responsable ?? 'Nombre y Apellido' }}<br>
            C.I. {{ $ci_responsable ?? '____________________' }}
        </div>

        <div class="firma">
            <div class="linea-firma"></div>
            <strong>Funcionario Ejecutor / Bienes Públicos</strong><br>
            {{ $usuario ?? 'Nombre y Apellido' }}<br>
            C.I. {{ $ci_usuario ?? '____________________' }}
        </div>
    </div>

    <div class="footer">
        Generado por el Sistema de Gestión de Bienes Patrimoniales UPTOS — {{ now()->format('d/m/Y H:i') }}<br>
        Documento oficial — Uso exclusivo institucional — Confidencial
    </div>

</body>
</html>
