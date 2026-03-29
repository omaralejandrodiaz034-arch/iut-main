<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - Sistema de Gestión de Inventario de Bienes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.8;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fafafa;
        }
        h1 {
            color: #640B21;
            font-size: 32px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 800;
        }
        h2 {
            color: #640B21;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 20px;
            border-bottom: 3px solid #640B21;
            padding-bottom: 10px;
            font-weight: 700;
        }
        h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        h4 {
            color: #34495e;
            font-size: 16px;
            margin-top: 18px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        p {
            margin-bottom: 18px;
            text-align: justify;
            font-size: 15px;
        }
        ul, ol {
            margin-left: 30px;
            margin-bottom: 20px;
        }
        li {
            margin-bottom: 10px;
            font-size: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 14px;
            text-align: left;
        }
        th {
            background-color: #640B21;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        code {
            background-color: #f4f4f4;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #c7254e;
        }
        pre {
            background-color: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 4px solid #640B21;
            background: linear-gradient(to bottom, #fff, #fafafa);
            padding: 40px;
        }
        .header img {
            max-width: 180px;
            margin-bottom: 20px;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .version {
            color: #999;
            font-size: 13px;
            margin-top: 15px;
            font-weight: 500;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 18px;
            border-left: 5px solid #ffc107;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: #d1ecf1;
            padding: 18px;
            border-left: 5px solid #17a2b8;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            padding: 18px;
            border-left: 5px solid #28a745;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning {
            background-color: #f8d7da;
            padding: 18px;
            border-left: 5px solid #dc3545;
            margin: 20px 0;
            border-radius: 4px;
        }
        .tip {
            background-color: #e7f3ff;
            padding: 18px;
            border-left: 5px solid #2196F3;
            margin: 20px 0;
            border-radius: 4px;
        }
        hr {
            border: none;
            border-top: 2px solid #eee;
            margin: 40px 0;
        }
        .screenshot {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        .screenshot-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            border-radius: 6px;
            font-size: 14px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .screenshot-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .screenshot-title {
            font-weight: 600;
            color: #640B21;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .screenshot-desc {
            color: #666;
            font-size: 13px;
            margin-top: 10px;
            font-style: italic;
        }
        .two-col {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        .two-col > div {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-title {
            font-weight: 700;
            color: #640B21;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }
        .badge-admin { background: #640B21; color: white; }
        .badge-user { background: #3498db; color: white; }
        .badge-active { background: #27ae60; color: white; }
        .badge-inactive { background: #95a5a6; color: white; }
        
        @media print {
            body { padding: 20px; }
            h2 { page-break-after: avoid; }
            h3 { page-break-after: avoid; }
            .screenshot { break-inside: avoid; }
        }
        
        @media (max-width: 768px) {
            body { padding: 20px; }
            .two-col { display: block; }
            .two-col > div { display: block; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Sistema de Gestión de Inventario de Bienes</h1>
        <p class="subtitle">Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"</p>
        <p class="subtitle">Ministerio del Poder Popular para la Educación Universitaria</p>
        <p class="version">📖 Manual de Usuario | Versión 1.0 | Marzo 2026</p>
    </div>

    <h2>📋 Tabla de Contenidos</h2>
    <ol>
        <li><a href="#introduccion" style="color: #640B21;">1. Introducción</a></li>
        <li><a href="#acceso" style="color: #640B21;">2. Acceso al Sistema</a></li>
        <li><a href="#estructura" style="color: #640B21;">3. Estructura del Sistema</a></li>
        <li><a href="#dashboard" style="color: #640B21;">4. Panel de Control (Dashboard)</a></li>
        <li><a href="#modulos" style="color: #640B21;">5. Módulos del Sistema</a></li>
        <li><a href="#bienes" style="color: #640B21;">6. Gestión de Bienes</a></li>
        <li><a href="#movimientos" style="color: #640B21;">7. Movimientos y Trazabilidad</a></li>
        <li><a href="#reportes" style="color: #640B21;">8. Reportes y Estadísticas</a></li>
        <li><a href="#auditoria" style="color: #640B21;">9. Auditoría del Sistema</a></li>
        <li><a href="#usuarios" style="color: #640B21;">10. Gestión de Usuarios</a></li>
        <li><a href="#perfil" style="color: #640B21;">11. Perfil de Usuario</a></li>
        <li><a href="#tips" style="color: #640B21;">12. Consejos y Mejores Prácticas</a></li>
        <li><a href="#glosario" style="color: #640B21;">13. Glosario de Términos</a></li>
    </ol>

    <hr>

    <h2 id="introduccion">1. Introducción</h2>

    <h3>1.1 Propósito del Sistema</h3>
    <p>El <strong>Sistema de Gestión de Inventario de Bienes</strong> es una plataforma integral desarrollada para la <strong>Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián" (UPTOS)</strong> que permite el registro, control y trazabilidad completa de los bienes públicos institucionales.</p>
    
    <p>Este sistema cumple con las normativas de control patrimonial del Estado venezolano y constituye una herramienta fundamental para la transparencia y eficiencia en la administración de los recursos materiales de la institución.</p>

    <div class="success">
        <strong>✅ Características Principales del Sistema:</strong>
        <ul style="margin-top: 10px; margin-bottom: 0;">
            <li>Registro digital de activos con fotografías de alta calidad</li>
            <li>Códigos únicos automáticos para cada bien</li>
            <li>Trazabilidad total de movimientos entre dependencias</li>
            <li>Generación de actas oficiales en formato PDF</li>
            <li>Auditoría automática de todas las operaciones</li>
            <li>Reportes detallados por dependencia, responsable y período</li>
            <li>Valoración económica del inventario completo</li>
            <li>Importación y exportación de datos en Excel</li>
        </ul>
    </div>

    <h3>1.2 Requisitos del Sistema</h3>
    <p>Para acceder al sistema necesitas:</p>
    <ul>
        <li>Un navegador web moderno (Chrome, Firefox, Edge, Safari)</li>
        <li>Conexión a Internet estable</li>
        <li>Credenciales de usuario proporcionadas por el administrador</li>
        <li>Resolución de pantalla recomendada: 1280x720 o superior</li>
    </ul>

    <div class="tip">
        <strong>💡 Consejo:</strong> Se recomienda utilizar Google Chrome o Mozilla Firefox para una mejor experiencia de usuario y compatibilidad completa con todas las funcionalidades del sistema.
    </div>

    <h3>1.3 Compatibilidad del Sistema</h3>
    <table>
        <tr>
            <th>Navegador</th>
            <th>Versión Mínima</th>
            <th>Estado</th>
        </tr>
        <tr>
            <td>Google Chrome</td>
            <td>90.0</td>
            <td><span class="badge badge-active">✓ Recomendado</span></td>
        </tr>
        <tr>
            <td>Mozilla Firefox</td>
            <td>88.0</td>
            <td><span class="badge badge-active">✓ Compatible</span></td>
        </tr>
        <tr>
            <td>Microsoft Edge</td>
            <td>90.0</td>
            <td><span class="badge badge-active">✓ Compatible</span></td>
        </tr>
        <tr>
            <td>Safari</td>
            <td>14.0</td>
            <td><span class="badge badge-active">✓ Compatible</span></td>
        </tr>
    </table>

    <hr>

    <h2 id="acceso">2. Acceso al Sistema</h2>

    <h3>2.1 Iniciar Sesión</h3>
    <p>Para acceder al sistema, sigue estos pasos:</p>
    <ol>
        <li>Abre tu navegador web y visita la página del sistema</li>
        <li>Haz clic en el botón <strong>"Iniciar Sesión"</strong> ubicado en la página principal</li>
        <li>Ingresa tu <strong>correo electrónico institucional</strong> (ejemplo: usuario@uptos.edu.ve)</li>
        <li>Ingresa tu <strong>contraseña</strong> de acceso</li>
        <li>Haz clic en el botón <strong>"Entrar"</strong> para acceder al sistema</li>
    </ol>

    <div class="screenshot">
        <div class="screenshot-title">📷 Pantalla de Inicio de Sesión</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">🔐</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Formulario de Inicio de Sesión</div>
            <div style="font-size: 13px; opacity: 0.9;">
                • Campo para correo institucional<br>
                • Campo para contraseña<br>
                • Botón "Iniciar Sesión" en color burdeos<br>
                • Enlace para recuperar contraseña
            </div>
        </div>
        <div class="screenshot-desc">Esta es la primera pantalla que verás al acceder al sistema. Ingresa tus credenciales proporcionadas por el administrador.</div>
    </div>

    <h3>2.2 Recuperar Contraseña</h3>
    <p>Si olvidas tu contraseña, el proceso de recuperación es el siguiente:</p>
    <ol>
        <li>Contacta al <strong>administrador del sistema</strong></li>
        <li>El administrador generará una nueva contraseña temporal para tu usuario</li>
        <li>Recibirás tus nuevas credenciales por el correo institucional registrado</li>
        <li>La primera vez que accedas, el sistema te pedirá <strong>configurar tu propia contraseña</strong></li>
    </ol>

    <div class="warning">
        <strong>⚠️ Importante:</strong> Por seguridad, se recomienda cambiar la contraseña temporal inmediatamente y utilizar una combinación de letras, números y caracteres especiales.
    </div>

    <h3>2.3 Cerrar Sesión</h3>
    <p>Para cerrar sesión de manera segura:</p>
    <ol>
        <li>Haz clic en tu <strong>nombre/usuario</strong> en la barra superior derecha</li>
        <li>Selecciona la opción <strong>"Cerrar Sesión"</strong></li>
        <li>Confirma la acción si el sistema lo solicita</li>
    </ol>

    <div class="highlight">
        <strong>🔐 Seguridad:</strong> Siempre cierra sesión cuando termines de usar el sistema, especialmente si compartes computadora con otros usuarios. Esto protege tu información y evita accesos no autorizados.
    </div>

    <hr>

    <h2 id="estructura">3. Estructura del Sistema</h2>

    <h3>3.1 Jerarquía Organizacional</h3>
    <p>El sistema está organizado de forma jerárquica, reflejando la estructura organizativa de la universidad:</p>

    <div class="screenshot">
        <div class="screenshot-title">📷 Estructura Jerárquica del Sistema</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">🏛️</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Jerarquía Organizacional</div>
            <div style="font-size: 13px; opacity: 0.9; text-align: left;">
                <strong>UPTOS "Clodosbaldo Russián"</strong><br>
                ├── 📂 Dept. de Informática<br>
                │   ├── 🖥️ Lab. Computación 1<br>
                │   ├── 🌐 Lab. Redes<br>
                │   └── 📋 Oficina de Coord.<br>
                ├── 🏢 Dept. de Administración<br>
                │   ├── 💰 Contabilidad<br>
                │   └── 👥 Recursos Humanos
            </div>
        </div>
    </div>

    <table>
        <tr>
            <th>Nivel</th>
            <th>Descripción</th>
            <th>Ejemplos en UPTOS</th>
        </tr>
        <tr>
            <td><strong>Organismo</strong></td>
            <td>Institución principal rectora</td>
            <td>UPTOS "Clodosbaldo Russián"</td>
        </tr>
        <tr>
            <td><strong>Unidad Administradora</strong></td>
            <td>Departamento o área principal</td>
            <td>Informática, Administración, Ingeniería, Biblioteca</td>
        </tr>
        <tr>
            <td><strong>Dependencia</strong></td>
            <td>Salón, laboratorio u oficina específica</td>
            <td>Lab. Computación 1, Lab. Física, Salon A-101, Biblioteca Central</td>
        </tr>
        <tr>
            <td><strong>Bien</strong></td>
            <td>Activo físico registrado en el sistema</td>
            <td>Computadoras, Impresoras, Muebles, Equipos</td>
        </tr>
    </table>

    <h3>3.2 Roles de Usuario</h3>
    <p>El sistema cuenta con dos roles principales:</p>

    <table>
        <tr>
            <th>Rol</th>
            <th>Descripción</th>
            <th>Permisos</th>
        </tr>
        <tr>
            <td><span class="badge badge-admin">Administrador</span></td>
            <td>Usuario con acceso completo al sistema</td>
            <td>Gestionar usuarios, crear/editar/eliminar bienes, generar reportes, acceder a auditoría, gestionar toda la estructura organizacional</td>
        </tr>
        <tr>
            <td><span class="badge badge-user">Usuario Normal</span></td>
            <td>Usuario con acceso operativo básico</td>
            <td>Registrar bienes, registrar movimientos, generar reportes básicos, editar su propio perfil</td>
        </tr>
    </table>

    <div class="info-box">
        <strong>ℹ️ Nota:</strong> Los permisos específicos pueden variar según la configuración establecida por el administrador del sistema.
    </div>

    <hr>

    <h2 id="dashboard">4. Panel de Control (Dashboard)</h2>

    <p>El <strong>Dashboard</strong> o Panel de Control es la primera pantalla que verás después de iniciar sesión. Proporciona una visión general del estado del inventario institucional.</p>

    <h3>4.1 Indicadores Principales (KPIs)</h3>
    <p>En la parte superior del dashboard encontrarás tarjetas con los indicadores clave:</p>

    <div class="two-col">
        <div>
            <div class="card">
                <div class="card-title">📦 Total de Bienes</div>
                <p>Cantidad total de bienes registrados en el sistema, incluyendo todas las dependencias.</p>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-title">✅ Bienes Activos</div>
                <p>Bienes que se encuentran en uso institucional y en condiciones normales.</p>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div>
            <div class="card">
                <div class="card-title">🔧 En Mantenimiento</div>
                <p>Bienes que se encuentran actualmente en proceso de reparación o mantenimiento técnico.</p>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-title">⚠️ Extraviados</div>
                <p>Bienes que han sido reportados como perdidos o cuya ubicación se desconoce.</p>
            </div>
        </div>
    </div>

    <div class="screenshot">
        <div class="screenshot-title">📷 Panel de Control - KPIs Principales</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📊</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Dashboard con 4 Tarjetas de KPIs</div>
            <div style="font-size: 13px; opacity: 0.9;">
                📦 Total Bienes | ✅ Activos | 🔧 Mantenimiento | ⚠️ Extraviados<br>
                🏛️ Organismos | 🏢 Unidades | 📂 Dependencias | 👥 Usuarios<br>
                <em style="opacity: 0.8;">Cada tarjeta es clickeable para filtrar resultados</em>
            </div>
        </div>
    </div>

    <h3>4.2 Segunda Fila de Indicadores</h3>
    <p>Debajo de los indicadores principales encontrarás:</p>
    <ul>
        <li><strong>🏛️ Organismos:</strong> Cantidad de organismos registrados</li>
        <li><strong>🏢 Unidades:</strong> Cantidad de departamentos o unidades administrativas</li>
        <li><strong>📂 Dependencias:</strong> Cantidad de salones, laboratorios u oficinas</li>
        <li><strong>👥 Usuarios:</strong> Cantidad de usuarios del sistema</li>
    </ul>

    <h3>4.3 Valor Total del Inventario</h3>
    <p>Una tarjeta especial muestra el <strong>valor total del inventario activo</strong> en Bolívares:</p>

    <div class="success">
        <strong>💰 Valor Total del Inventario:</strong> Esta cantidad representa la suma de los valores económicos de todos los bienes activos en el sistema. Los bienes desincorporados no se incluyen en este cálculo.
    </div>

    <h3>4.4 Gráficos de Distribución</h3>
    <p>El dashboard incluye dos gráficos importantes:</p>

    <ul>
        <li><strong>Distribución por Estado:</strong> Muestra qué porcentaje de bienes están activos, dañados, en mantenimiento, etc.</li>
        <li><strong>Distribución por Tipo:</strong> Muestra la cantidad de bienes electrónicos, mobiliarios, vehículos, etc.</li>
    </ul>

    <div class="screenshot">
        <div class="screenshot-title">📷 Gráficos de Distribución</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📈</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Gráficos de Bienes</div>
            <div style="font-size: 13px; opacity: 0.9; text-align: left;">
                <strong>Distribución por Estado:</strong><br>
                🟢 Activo  ████████████ 75%<br>
                🟠 Dañado  ██ 5%<br>
                🟡 Mantenimiento ██ 8%<br>
                <strong>Distribución por Tipo:</strong><br>
                📱 Electrónico ████████ 45%<br>
                🪑 Mobiliario █████ 30%
            </div>
        </div>
    </div>

    <h3>4.5 Bienes Recientes y Últimos Movimientos</h3>
    <p>En la parte inferior del dashboard encontrarás:</p>
    <ul>
        <li><strong>Bienes Recientes:</strong> Lista de los últimos bienes registrados en el sistema</li>
        <li><strong>Últimos Movimientos:</strong> Registro de las transferencias y cambios más recientes</li>
    </ul>

    <hr>

    <h2 id="modulos">5. Módulos del Sistema</h2>

    <p>El sistema se divide en varios módulos accesibles desde el menú principal:</p>

    <h3>5.1 Módulo de Organismos</h3>
    <p>Permite gestionar los organismos rectores del sistema.</p>
    
    <div class="card">
        <div class="card-title">🏛️ Organismos</div>
        <p><strong>Acciones disponibles:</strong></p>
        <ul>
            <li>Ver lista de organismos</li>
            <li>Crear nuevo organismo</li>
            <li>Editar información de organismo</li>
            <li>Eliminar organismo (solo si no tiene unidades asociadas)</li>
            <li>Generar reporte PDF</li>
        </ul>
        <p><strong>Campos del formulario:</strong></p>
        <ul>
            <li>Código del organismo</li>
            <li>Nombre o denominación completa</li>
        </ul>
    </div>

    <h3>5.2 Módulo de Unidades Administradoras</h3>
    <p>Gestión de los departamentos o áreas dentro de un organismo.</p>
    
    <div class="card">
        <div class="card-title">🏢 Unidades Administradoras (Departamentos)</div>
        <p><strong>Acciones disponibles:</strong></p>
        <ul>
            <li>Ver lista de unidades</li>
            <li>Crear nueva unidad</li>
            <li>Editar información</li>
            <li>Eliminar unidad (solo si no tiene dependencias)</li>
            <li>Ver dependencias asociadas</li>
            <li>Generar reporte PDF</li>
        </ul>
        <p><strong>Campos del formulario:</strong></p>
        <ul>
            <li>Código de la unidad</li>
            <li>Denominación (nombre del departamento)</li>
            <li>Organismo al que pertenece</li>
        </ul>
    </div>

    <h3>5.3 Módulo de Dependencias</h3>
    <p>Gestión de salones, laboratorios u oficinas dentro de un departamento.</p>
    
    <div class="card">
        <div class="card-title">📂 Dependencias (Salones/Laboratorios)</div>
        <p><strong>Acciones disponibles:</strong></p>
        <ul>
            <li>Ver lista de dependencias</li>
            <li>Crear nueva dependencia</li>
            <li>Editar información</li>
            <li>Eliminar dependencia (solo si no tiene bienes asociados)</li>
            <li>Asignar responsable</li>
            <li>Ver bienes asociados</li>
            <li>Generar reporte PDF</li>
        </ul>
        <p><strong>Campos del formulario:</strong></p>
        <ul>
            <li>Código de la dependencia</li>
            <li>Denominación (nombre del salón, laboratorio u oficina)</li>
            <li>Unidad (departamento) al que pertenece</li>
            <li>Responsable asignado (opcional)</li>
        </ul>
    </div>

    <h3>5.4 Módulo de Responsables</h3>
    <p>Gestión de las personas responsables del cuidado de los bienes.</p>
    
    <div class="card">
        <div class="card-title">👤 Responsables</div>
        <p><strong>Acciones disponibles:</strong></p>
        <ul>
            <li>Ver lista de responsables</li>
            <li>Crear nuevo responsable</li>
            <li>Editar información</li>
            <li>Eliminar responsable</li>
            <li>Buscar por cédula</li>
            <li>Ver bienes asignados</li>
        </ul>
        <p><strong>Campos del formulario:</strong></p>
        <ul>
            <li>Cédula de identidad (V-xxxxxxxx)</li>
            <li>Nombre completo</li>
            <li>Correo electrónico</li>
            <li>Teléfono de contacto</li>
            <li>Tipo de responsable (Primario, Por uso)</li>
        </ul>
    </div>

    <h3>5.5 Módulo de Bienes</h3>
    <p>El módulo principal del sistema para la gestión de activos. Ver sección completa en el capítulo 6.</p>

    <h3>5.6 Módulo de Movimientos</h3>
    <p>Registro y seguimiento de todos los cambios en los bienes. Ver sección completa en el capítulo 7.</p>

    <h3>5.7 Módulo de Reportes</h3>
    <p>Generación de informes y estadísticas. Ver sección completa en el capítulo 8.</p>

    <hr>

    <h2 id="bienes">6. Gestión de Bienes</h2>

    <p>El módulo de <strong>Bienes</strong> es el corazón del sistema. Permite el registro, control y seguimiento de todos los activos de la institución.</p>

    <h3>6.1 Tipos de Bienes</h3>
    <p>El sistema maneja diferentes tipos de bienes con características específicas:</p>

    <table>
        <tr>
            <th>Tipo</th>
            <th>Ejemplos</th>
            <th>Características Específicas</th>
        </tr>
        <tr>
            <td><strong>📱 Electrónico</strong></td>
            <td>Computadoras, impresoras, equipos de red, proyectores</td>
            <td>Procesador, memoria RAM, disco duro, marca, modelo</td>
        </tr>
        <tr>
            <td><strong>🪑 Mobiliario</strong></td>
            <td>Escritorios, sillas, estantes, pupitres</td>
            <td>Material, dimensiones (alto x ancho x profundo), color</td>
        </tr>
        <tr>
            <td><strong>🚗 Vehículo</strong></td>
            <td>Automóviles, motos, camionetas, bicicletas</td>
            <td>Marca, modelo, año, placa, número de motor</td>
        </tr>
        <tr>
            <td><strong>📦 Otros</strong></td>
            <td>Herramientas, equipos agrícolas, instrumentos</td>
            <td>Descripción general, marca, modelo</td>
        </tr>
    </table>

    <h3>6.2 Estados de Bienes</h3>
    <p>Cada bien tiene un estado que indica su condición actual:</p>

    <table>
        <tr>
            <th>Estado</th>
            <th>Descripción</th>
            <th>Color en Sistema</th>
        </tr>
        <tr>
            <td><span class="badge badge-active">Activo</span></td>
            <td>En uso institucional正常运行</td>
            <td>🟢 Verde</td>
        </tr>
        <tr>
            <td><strong>Dañado</strong></td>
            <td>Requiere reparación</td>
            <td>🟠 Naranja</td>
        </tr>
        <tr>
            <td><strong>En Mantenimiento</strong></td>
            <td>En taller técnico</td>
            <td>🟡 Amarillo</td>
        </tr>
        <tr>
            <td><strong>En Camino</strong></td>
            <td>En traslado entre dependencias</td>
            <td>🔵 Azul</td>
        </tr>
        <tr>
            <td><strong>Extraviado</strong></td>
            <td>Sin localización</td>
            <td>🔴 Rojo</td>
        </tr>
        <tr>
            <td><strong>Desincorporado</strong></td>
            <td>Dado de baja oficial</td>
            <td>⚫ Gris/Negro</td>
        </tr>
    </table>

    <h3>6.3 Registrar un Nuevo Bien</h3>
    <p>Sigue estos pasos para registrar un nuevo bien en el sistema:</p>

    <ol>
        <li>Navega a <strong>Bienes</strong> en el menú principal</li>
        <li>Haz clic en el botón <strong>"+ Nuevo Bien"</strong></li>
        <li>Completa el formulario con los datos requeridos:</li>
    </ol>

    <h4>Datos Generales:</h4>
    <ul>
        <li><strong>Código único:</strong> Se genera automáticamente por el sistema</li>
        <li><strong>Descripción:</strong> Nombre o descripción del bien</li>
        <li><strong>Precio:</strong> Valor monetario en Bolívares</li>
        <li><strong>Fecha de registro:</strong> Fecha de incorporación al sistema</li>
    </ul>

    <h4>Clasificación:</h4>
    <ul>
        <li><strong>Tipo de bien:</strong> Electrónico, Mobiliario, Vehículo u Otros</li>
        <li><strong>Estado:</strong> Activo, Dañado, En Mantenimiento, etc.</li>
    </ul>

    <h4>Ubicación:</h4>
    <ul>
        <li><strong>Dependencia:</strong> Selecciona el salón, laboratorio u oficina</li>
        <li><strong>Ubicación específica:</strong> Información adicional de ubicación (opcional)</li>
    </ul>

    <h4>Características según tipo:</h4>
    <ul>
        <li><strong>Para Electrónico:</strong> Procesador, memoria RAM, disco duro, marca, modelo</li>
        <li><strong>Para Mobiliario:</strong> Material, dimensiones (alto x ancho x profundidad)</li>
        <li><strong>Para Vehículo:</strong> Marca, modelo, año, placa, número de serial</li>
        <li><strong>Para Otros:</strong> Descripción general y características</li>
    </ul>

    <h4>Fotografías:</h4>
    <ul>
        <li>Sube hasta <strong>5 fotografías</strong> del bien</li>
        <li>Formatos aceptados: <strong>JPG, PNG</strong></li>
        <li>Tamaño máximo recomendado: 2MB por imagen</li>
    </ul>

    <div class="screenshot">
        <div class="screenshot-title">📷 Formulario de Registro de Bienes</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📝</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Nuevo Bien - Formulario Completo</div>
            <div style="font-size: 13px; opacity: 0.9;">
                • Código único (auto-generado)<br>
                • Descripción del bien<br>
                • Selector de Tipo (Electrónico/Mobiliario/Vehículo/Otros)<br>
                • Selector de Dependencia<br>
                • Campo de precio (Bs.)<br>
                • Campos dinámicos según tipo<br>
                • Área de subida de fotografías (hasta 5)<br>
                • Botones Guardar / Cancelar
            </div>
        </div>
    </div>

    <h3>6.4 Buscar y Filtrar Bienes</h3>
    <p>El sistema ofrece múltiples opciones de búsqueda y filtrado:</p>

    <ul>
        <li><strong>Buscar por código:</strong> Ingresa el código único del bien</li>
        <li><strong>Buscar por descripción:</strong> Texto parcial del nombre</li>
        <li><strong>Filtrar por estado:</strong> Activo, Dañado, Mantenimiento, etc.</li>
        <li><strong>Filtrar por tipo:</strong> Electrónico, Mobiliario, Vehículo, Otros</li>
        <li><strong>Filtrar por dependencia:</strong> Selecciona una dependencia específica</li>
        <li><strong>Filtrar por unidad:</strong> Selecciona un departamento</li>
    </ul>

    <div class="tip">
        <strong>💡 Consejo:</strong> Los filtros se aplican automáticamente al seleccionarlos. Puedes combinar varios filtros para una búsqueda más precisa.
    </div>

    <h3>6.5 Ver Detalles de un Bien</h3>
    <p>Para ver toda la información de un bien:</p>
    <ol>
        <li>Haz clic en el <strong>nombre o código</strong> del bien en la lista</li>
        <li>Se abrirá la página de detalles donde verás:</li>
    </ol>

    <ul>
        <li>Datos completos del bien</li>
        <li>Galería de fotografías</li>
        <li>Historial de movimientos</li>
        <li>Responsable actual</li>
        <li>Dependencia asignada</li>
        <li>Información de características específicas</li>
    </ul>

    <div class="screenshot">
        <div class="screenshot-title">📷 Detalle de Bien - Información General</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">🔍</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Página de Detalle del Bien</div>
            <div style="font-size: 13px; opacity: 0.9;">
                • Código único del bien<br>
                • Descripción completa<br>
                • Tipo y Estado (con badges de color)<br>
                • Valor económico (Bs.)<br>
                • Fotografía principal<br>
                • Dependencia y Responsable<br>
                • Características específicas<br>
                • Botones: Editar ✏️ | Transferir ↔️ | Desincorporar 🗑️
            </div>
        </div>
    </div>

    <h3>6.6 Galería de Fotografías</h3>
    <p>Cada bien puede tener hasta 5 fotografías asociadas. Para ver todas las imágenes:</p>
    <ol>
        <li>Desde la página de detalles del bien</li>
        <li>Haz clic en <strong>"Ver Galería"</strong> o en las miniaturas</li>
        <li>Podrás navegar entre las imágenes en formato expandido</li>
    </ol>

    <h3>6.7 Editar un Bien</h3>
    <p>Para modificar los datos de un bien:</p>
    <ol>
        <li>Busca el bien en la lista</li>
        <li>Haz clic en el botón <strong>"Editar"</strong> (ícono de lápiz ✏️)</li>
        <li>Modifica los campos necesarios en el formulario</li>
        <li>Haz clic en <strong>"Actualizar"</strong> para guardar los cambios</li>
    </ol>

    <div class="warning">
        <strong>⚠️ Importante:</strong> Cada edición queda registrada en la auditoría del sistema. Incluye una descripción del cambio realizado.
    </div>

    <h3>6.8 Transferir un Bien entre Dependencias</h3>
    <p>Para mover un bien a otra dependencia:</p>
    <ol>
        <li>Selecciona el bien que deseas transferir</li>
        <li>Haz clic en el botón <strong>"Transferir"</strong></li>
        <li>Selecciona la <strong>nueva dependencia</strong> destino</li>
        <li>Agrega una <strong>observación</strong> sobre la transferencia (opcional)</li>
        <li>Confirma la transferencia</li>
    </ol>

    <div class="success">
        <strong>📄 Acta de Traslado:</strong> El sistema generará automáticamente un <strong>Acta de Traslado en PDF</strong> que documenta la transferencia del bien entre dependencias.
    </div>

    <div class="screenshot">
        <div class="screenshot-title">📷 Formulario de Transferencia</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">↔️</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Transferir Bien</div>
            <div style="font-size: 13px; opacity: 0.9;">
                • Información del bien a transferir<br>
                • Selector de dependencia destino<br>
                • Campo de observaciones (opcional)<br>
                • Resumen del movimiento<br>
                • Botón "Confirmar Transferencia"<br>
                <em style="opacity: 0.8;">Genera Acta de Traslado en PDF</em>
            </div>
        </div>
    </div>

    <h3>6.9 Desincorporar un Bien (Dar de Baja)</h3>
    <p>Para dar de baja un bien del inventario:</p>
    <ol>
        <li>Selecciona el bien a desincorporar</li>
        <li>Haz clic en el botón <strong>"Desincorporar"</strong></li>
        <li>Selecciona el <strong>motivo</strong> de la desincorporación:</li>
    </ol>

    <ul>
        <li><strong>Obsolescencia:</strong> El bien está obsoleto y no es útil</li>
        <li><strong>Daño irreparable:</strong> No puede ser reparado</li>
        <li><strong>Robo o extravío:</strong> Fue robado o perdido</li>
        <li><strong>Donación:</strong> Se donó a otra institución</li>
        <li><strong>Otra causa:</strong> Especificar el motivo</li>
    </ul>

    <ol start="4">
        <li>Agrega una <strong>descripción detallada</strong> del motivo</li>
        <li>Confirma la desincorporación</li>
    </ol>

    <div class="success">
        <strong>📄 Acta de Desincorporación:</strong> El sistema generará automáticamente un <strong>Acta de Desincorporación en PDF</strong> que constituye el documento oficial del retiro del bien.
    </div>

    <div class="screenshot">
        <div class="screenshot-title">📷 Formulario de Desincorporación</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">🗑️</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Desincorporar Bien</div>
            <div style="font-size: 13px; opacity: 0.9;">
                • Información del bien a desincorporar<br>
                • Selector de motivo:<br>
                &nbsp;&nbsp;↳ Obsolescencia | Daño irreparable<br>
                &nbsp;&nbsp;↳ Robo/Extravío | Donación | Otra causa<br>
                • Campo de descripción detallada<br>
                • Botón "Confirmar Desincorporación"<br>
                <em style="opacity: 0.8;">Genera Acta de Desincorporación en PDF</em>
            </div>
        </div>
    </div>

    <h3>6.10 Importar Bienes desde Excel</h3>
    <p>El sistema permite importar múltiples bienes desde un archivo Excel:</p>
    <ol>
        <li>Navega a <strong>Bienes > Importar</strong></li>
        <li>Descarga la <strong>plantilla</strong> de Excel proporcionada por el sistema</li>
        <li>Llena los datos siguiendo el formato requerido</li>
        <li>Sube el archivo Excel</li>
        <li>El sistema validará los datos y mostrará los resultados</li>
        <li>Confirma la importación</li>
    </ol>

    <div class="tip">
        <strong>💡 Consejo:</strong> Asegúrate de seguir exactamente el formato de la plantilla. Los errores de formato pueden causar fallos en la importación.
    </div>

    <h3>6.11 Exportar Bienes</h3>
    <p>Para exportar los bienes registrados:</p>
    <ol>
        <li>Navega a <strong>Bienes > Exportar</strong></li>
        <li>Selecciona el <strong>formato</strong> de exportación (Excel)</li>
        <li>Aplica los filtros deseados (opcional)</li>
        <li>El sistema generará un archivo con todos los bienes</li>
    </ol>

    <hr>

    <h2 id="movimientos">7. Movimientos y Trazabilidad</h2>

    <h3>7.1 ¿Qué es un Movimiento?</h3>
    <p>Un <strong>movimiento</strong> es cualquier cambio relacionado con un bien. El sistema registra automáticamente todos los movimientos, permitiendo una trazabilidad completa.</p>

    <table>
        <tr>
            <th>Tipo de Movimiento</th>
            <th>Descripción</th>
        </tr>
        <tr>
            <td><strong>Traslado</strong></td>
            <td>Cambio de ubicación entre dependencias</td>
        </tr>
        <tr>
            <td><strong>Cambio de Estado</strong></td>
            <td>Cambio en el estado del bien (daño, mantenimiento, etc.)</td>
        </tr>
        <tr>
            <td><strong>Asignación</strong></td>
            <td>Asignación a un nuevo responsable</td>
        </tr>
        <tr>
            <td><strong>Desincorporación</strong></td>
            <td>Retiro definitivo del inventario</td>
        </tr>
    </table>

    <h3>7.2 Ver Historial de Movimientos</h3>
    <p>Cada bien mantiene un historial completo de todos los cambios. Para verlo:</p>
    <ol>
        <li>Selecciona el bien</li>
        <li>Haz clic en <strong>"Ver" o "Historial"</strong></li>
        <li>Visualiza la <strong>línea de tiempo</strong> con todos los cambios</li>
    </ol>

    <div class="screenshot">
        <div class="screenshot-title">📷 Historial de Movimientos de un Bien</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📜</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Historial de Movimientos</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Línea de tiempo vertical con:</strong><br>
                📅 15/03/2026 10:30 - <strong>Traslado</strong><br>
                &nbsp;&nbsp;↳ Lab. Computación 1 → Lab. Física<br>
                &nbsp;&nbsp;↳ Usuario: Juan Pérez<br>
                <br>
                📅 10/03/2026 14:15 - <strong>Cambio de Estado</strong><br>
                &nbsp;&nbsp;↳ Activo → En Mantenimiento<br>
                &nbsp;&nbsp;↳ Usuario: María Gómez
            </div>
        </div>
    </div>

    <h3>7.3 Lista de Movimientos</h3>
    <p>Para ver todos los movimientos del sistema:</p>
    <ol>
        <li>Navega a <strong>Movimientos</strong> en el menú</li>
        <li>Verás una lista de todos los movimientos registrados</li>
        <li>Puedes filtrar por:</li>
    </ol>

    <ul>
        <li>Tipo de movimiento</li>
        <li>Fecha (rango de fechas)</li>
        <li>Usuario que lo realizó</li>
        <li>Bien específico</li>
    </ul>

    <h3>7.4 Bienes Eliminados (Papelera)</h3>
    <p>Los bienes desincorporados se mantienen temporalmente en una papelera:</p>
    <ol>
        <li>Navega a <strong>Movimientos > Eliminados</strong></li>
        <li>Verás todos los bienes dados de baja</li>
        <li>Si fue un error, puedes <strong>restaurar</strong> el bien</li>
    </ol>

    <div class="info-box">
        <strong>ℹ️ Nota:</strong> Los bienes eliminados se borran permanentemente después de <strong>30 días</strong> de haber sido desincorporados. Después de ese plazo, no será posible restaurarlos.
    </div>

    <h3>7.5 Reintegrar un Bien</h3>
    <p>Si un bien extraviado aparece o fue desincorporado por error:</p>
    <ol>
        <li>Navega a <strong>Movimientos</strong></li>
        <li>Busca la opción <strong>"Reintegrar"</strong></li>
        <li>Selecciona el bien a reintegrar</li>
        <li>Confirma la reintegración</li>
        <li>El bien vuelve al estado <strong>"Activo"</strong></li>
    </ol>

    <hr>

    <h2 id="reportes">8. Reportes y Estadísticas</h2>

    <h3>8.1 Tipos de Reportes Disponibles</h3>
    <p>El sistema permite generar diversos tipos de reportes:</p>

    <table>
        <tr>
            <th>Tipo de Reporte</th>
            <th>Descripción</th>
        </tr>
        <tr>
            <td><strong>Inventario General</strong></td>
            <td>Listado completo de todos los bienes</td>
        </tr>
        <tr>
            <td><strong>Por Dependencia</strong></td>
            <td>Bienes asignados a cada dependencia</td>
        </tr>
        <tr>
            <td><strong>Por Unidad Administradora</strong></td>
            <td>Bienes por departamento</td>
        </tr>
        <tr>
            <td><strong>Por Organismo</strong></td>
            <td>Bienes por organismo</td>
        </tr>
        <tr>
            <td><strong>Por Responsable</strong></td>
            <td>Bienes asignados a cada responsable</td>
        </tr>
        <tr>
            <td><strong>Por Estado</strong></td>
            <td>Distribución según estado</td>
        </tr>
        <tr>
            <td><strong>Por Tipo de Bien</strong></td>
            <td>Distribución según tipo</td>
        </tr>
        <tr>
            <td><strong>Histórico de Movimientos</strong></td>
            <td>Registro de todos los movimientos</td>
        </tr>
    </table>

    <h3>8.2 Formatos de Salida</h3>
    <p>Los reportes pueden generarse en los siguientes formatos:</p>
    <ul>
        <li><strong>📄 PDF:</strong> Documento oficial para impresión y archivo</li>
        <li><strong>📊 Excel:</strong> Para análisis de datos y hojas de cálculo</li>
    </ul>

    <div class="screenshot">
        <div class="screenshot-title">📷 Menú de Generación de Reportes</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📄</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Generar Reportes</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Tipos de Reporte:</strong><br>
                📋 Inventario General | 📂 Por Dependencia<br>
                🏢 Por Unidad | 🏛️ Por Organismo<br>
                👤 Por Responsable | 📊 Por Estado<br>
                📱 Por Tipo | 📜 Histórico de Movimientos<br>
                <strong>Formatos:</strong> 📄 PDF | 📊 Excel
            </div>
        </div>
    </div>

    <h3>8.3 Gráficos y Estadísticas</h3>
    <p>El sistema incluye un módulo de gráficas:</p>
    <ol>
        <li>Navega a <strong>Gráficas</strong> en el menú</li>
        <li>Visualiza los diferentes gráficos:</li>
    </ol>

    <ul>
        <li><strong>Bienes por tipo:</strong> Gráfico circular (pie chart)</li>
        <li><strong>Bienes por estado:</strong> Gráfico de barras</li>
        <li><strong>Tendencias temporales:</strong> Evolución del inventario</li>
    </ul>

    <div class="tip">
        <strong>💡 Consejo:</strong> Puedes exportar las gráficas a PDF para incluirlas en presentaciones o informes.
    </div>

    <div class="screenshot">
        <div class="screenshot-title">📷 Panel de Gráficas y Estadísticas</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">📊</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Estadísticas y Gráficos</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Gráficos Disponibles:</strong><br>
                🍩 <strong>Gráfico Circular:</strong> Bienes por Tipo<br>
                &nbsp;&nbsp;↳ Electrónico 45% | Mobiliario 30%<br>
                &nbsp;&nbsp;↳ Vehículo 15% | Otros 10%<br>
                <br>
                📊 <strong>Gráfico de Barras:</strong> Bienes por Estado<br>
                &nbsp;&nbsp;↳ Activo | Dañado | Mantenimiento<br>
                &nbsp;&nbsp;↳ En Camino | Extraviado | Desinc.<br>
                <em>Opción de exportar a PDF</em>
            </div>
        </div>
    </div>

    <hr>

    <h2 id="auditoria">9. Auditoría del Sistema</h2>

    <p>El sistema mantiene un registro automático de todas las operaciones realizadas.</p>

    <div class="info-box">
        <strong>ℹ️ Acceso:</strong> El módulo de auditoría está disponible principalmente para usuarios con rol de <strong>Administrador</strong>.
    </div>

    <h3>9.1 ¿Qué se Registra?</h3>
    <p>La auditoría registra:</p>
    <ul>
        <li><strong>Usuario:</strong> Quién realizó la acción</li>
        <li><strong>Operación:</strong> Tipo de acción (crear, editar, eliminar)</li>
        <li><strong>Módulo:</strong> Sección del sistema afectada</li>
        <li><strong>Fecha y hora:</strong> Momento exacto de la operación</li>
        <li><strong>Datos:</strong> Información antes y después del cambio</li>
    </ul>

    <h3>9.2 Ver el Registro de Auditoría</h3>
    <p>Para acceder al registro de auditoría:</p>
    <ol>
        <li>Navega a <strong>Auditoría</strong> en el menú de administración</li>
        <li>Verás un registro cronológico de todas las operaciones</li>
        <li>Puedes filtrar por:</li>
    </ol>

    <ul>
        <li>Usuario específico</li>
        <li>Tipo de operación</li>
        <li>Fecha (rango)</li>
        <li>Módulo afectado</li>
    </ul>

    <div class="screenshot">
        <div class="screenshot-title">📷 Panel de Auditoría</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">🔎</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Registro de Auditoría</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Tabla de Registro con:</strong><br>
                📅 15/03/2026 10:30 | <strong>Usuario:</strong> admin@uptos.edu.ve<br>
                &nbsp;&nbsp;↳ <strong>Acción:</strong> Crear Bien | <strong>Módulo:</strong> Bienes<br>
                &nbsp;&nbsp;↳ <strong>IP:</strong> 192.168.1.100<br>
                <br>
                📅 15/03/2026 09:15 | <strong>Usuario:</strong> juan@uptos.edu.ve<br>
                &nbsp;&nbsp;↳ <strong>Acción:</strong> Editar | <strong>Módulo:</strong> Dependencias<br>
                <br>
                <em>Filtros: Usuario, Acción, Fecha, Módulo</em>
            </div>
        </div>
    </div>

    <h3>9.3 Importancia de la Auditoría</h3>
    <div class="highlight">
        <strong>🎯 Propósito:</strong> La auditoría permite mantener la transparencia y trazabilidad de todas las operaciones, facilitando:
    </div>
    <ul>
        <li>Detectar operaciones sospechosas o no autorizadas</li>
        <li>Realizar seguimiento de cambios en bienes críticos</li>
        <li>Cumplir con normativas de control interno</li>
        <li>Investigar incidentes o errores</li>
        <li>Generar informes de cumplimiento</li>
    </ul>

    <hr>

    <h2 id="usuarios">10. Gestión de Usuarios</h2>

    <p>Este módulo permite administrar los usuarios que acceden al sistema (solo administradores).</p>

    <h3>10.1 Crear un Nuevo Usuario</h3>
    <ol>
        <li>Navega a <strong>Usuarios</strong> en el menú</li>
        <li>Haz clic en <strong>"+ Nuevo Usuario"</strong></li>
        <li>Completa el formulario:</li>
    </ol>

    <ul>
        <li>Cédula de identidad</li>
        <li>Nombre completo</li>
        <li>Apellido</li>
        <li>Correo electrónico institucional</li>
        <li>Teléfono</li>
        <li>Rol (Administrador o Usuario Normal)</li>
        <li>Contraseña temporal</li>
    </ul>

    <h3>10.2 Editar Usuario</h3>
    <ol>
        <li>Busca el usuario en la lista</li>
        <li>Haz clic en <strong>"Editar"</strong></li>
        <li>Modifica los datos necesarios</li>
        <li>Guarda los cambios</li>
    </ol>

    <h3>10.3 Eliminar Usuario</h3>
    <div class="warning">
        <strong>⚠️ Advertencia:</strong> Al eliminar un usuario, este pierde acceso al sistema. Los registros de auditoría保留nan los registros de sus acciones anteriores.
    </div>

    <h3>10.4 Importar Usuarios desde Excel</h3>
    <p>Para importar múltiples usuarios:</p>
    <ol>
        <li>Navega a <strong>Usuarios > Importar</strong></li>
        <li>Prepara un archivo Excel con los datos:</li>
    </ol>

    <ul>
        <li>Cédula</li>
        <li>Nombre</li>
        <li>Apellido</li>
        <li>Correo electrónico</li>
        <li>Teléfono (opcional)</li>
    </ul>

    <ol start="3">
        <li>Sube el archivo</li>
        <li>El sistema procesará y creará los usuarios</li>
    </ol>

    <div class="screenshot">
        <div class="screenshot-title">📷 Lista de Usuarios</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">👥</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Gestión de Usuarios</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Tabla de Usuarios:</strong><br>
                🖼️ <strong>Foto</strong> | <strong>Nombre</strong> | <strong>Correo</strong><br>
                &nbsp;&nbsp;↳ Juan Pérez | juan@uptos.edu.ve<br>
                &nbsp;&nbsp;&nbsp;&nbsp;🔴 Admin | ✅ Activo<br>
                <br>
                🖼️ María Gómez | maria@uptos.edu.ve<br>
                &nbsp;&nbsp;&nbsp;&nbsp;🔵 Usuario | ✅ Activo<br>
                <br>
                <strong>Acciones:</strong> ✏️ Editar | 🗑️ Eliminar | ➕ Nuevo Usuario
            </div>
        </div>
    </div>

    <hr>

    <h2 id="perfil">11. Perfil de Usuario</h2>

    <p>Cada usuario puede gestionar su propia información personal.</p>

    <h3>11.1 Ver tu Perfil</h3>
    <ol>
        <li>Haz clic en tu <strong>nombre</strong> en la barra superior derecha</li>
        <li>Selecciona <strong>"Mi Perfil"</strong></li>
        <li>Verás tu información personal completa</li>
    </ol>

    <h3>11.2 Editar tu Perfil</h3>
    <ol>
        <li>En tu perfil, haz clic en <strong>"Editar"</strong></li>
        <li>Modifica tu información:</li>
    </ol>

    <ul>
        <li>Nombre</li>
        <li>Apellido</li>
        <li>Teléfono</li>
        <li>Foto de perfil (opcional)</li>
    </ul>

    <ol start="3">
        <li>Guarda los cambios</li>
    </ol>

    <h3>11.3 Cambiar tu Contraseña</h3>
    <ol>
        <li>En tu perfil, busca la sección de contraseña</li>
        <li>Ingresa tu <strong>contraseña actual</strong></li>
        <li>Ingresa tu <strong>nueva contraseña</strong></li>
        <li>Confirma la <strong>nueva contraseña</strong></li>
        <li>Guarda los cambios</li>
    </ol>

    <div class="tip">
        <strong>💡 Consejo de Seguridad:</strong> Se recomienda:
        <ul>
            <li>Usar al menos 8 caracteres</li>
            <li>Combinar letras mayúsculas y minúsculas</li>
            <li>Incluir números y símbolos</li>
            <li>Cambiar la contraseña periódicamente</li>
        </ul>
    </div>

    <div class="screenshot">
        <div class="screenshot-title">📷 Perfil de Usuario</div>
        <div class="screenshot-placeholder">
            <div class="screenshot-icon">👤</div>
            <div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;">Mi Perfil</div>
            <div style="font-size: 13px; opacity: 0.9;">
                <strong>Información del Usuario:</strong><br>
                🖼️ Foto de perfil (actualizable)<br>
                👤 <strong>Nombre:</strong> Juan Pérez<br>
                📧 <strong>Correo:</strong> juan@uptos.edu.ve<br>
                🎭 <strong>Rol:</strong> Administrador<br>
                📅 <strong>Último acceso:</strong> 15/03/2026 10:30<br>
                <br>
                <strong>Acciones:</strong><br>
                ✏️ Editar Perfil | 🔐 Cambiar Contraseña
            </div>
        </div>
    </div>

    <hr>

    <h2 id="tips">12. Consejos y Mejores Prácticas</h2>

    <h3>12.1 Consejos para el Registro de Bienes</h3>
    <div class="success">
        <strong>✅ Buenas Prácticas:</strong>
        <ul style="margin-top: 10px;">
            <li><strong>Fotografías:</strong> Sube varias fotos del bien (frontal, lateral, detalle de serial)</li>
            <li><strong>Serial/Modelo:</strong> Registra el número de serie para equipos electrónicos</li>
            <li><strong>Valor:</strong> Incluye el valor real del bien para cálculos precisos</li>
            <li><strong>Descripción:</strong> Sé específico y descriptivo</li>
        </ul>
    </div>

    <h3>12.2 Consejos para Movimientos</h3>
    <div class="tip">
        <strong>💡 Recomendaciones:</strong>
        <ul style="margin-top: 10px;">
            <li>Siempre registra movimientos inmediatamente</li>
            <li>Incluye observaciones detalladas</li>
            <li>Verifica la información antes de confirmar</li>
            <li>Conserva los documentos PDF generados</li>
        </ul>
    </div>

    <h3>12.3 Consejos de Seguridad</h3>
    <div class="warning">
        <strong>🔐 Seguridad:</strong>
        <ul style="margin-top: 10px;">
            <li>No compartas tu contraseña con otros usuarios</li>
            <li>Cierra sesión al terminar de usar el sistema</li>
            <li>Reporta actividades sospechosas al administrador</li>
            <li>No accesses desde computadoras públicas</li>
        </ul>
    </div>

    <h3>12.4 Solución de Problemas Comunes</h3>
    
    <h4>No puedo iniciar sesión:</h4>
    <ul>
        <li>Verifica que tu correo y contraseña sean correctos</li>
        <li>Comprueba que el bloqueo de mayúsculas esté desactivado</li>
        <li>Contacta al administrador si olvidaste tu contraseña</li>
    </ul>

    <h4>No aparecen los datos esperados:</h4>
    <ul>
        <li>Verifica que los filtros estén correctamente aplicados</li>
        <li>Limpia la caché del navegador</li>
        <li>Intenta con otro navegador</li>
    </ul>

    <h4>Los botones no funcionan:</h4>
    <ul>
        <li>Desactiva los bloqueadores de ventanas emergentes</li>
        <li>Verifica tu conexión a internet</li>
        <li>Intenta recargar la página</li>
    </ul>

    <hr>

    <h2 id="glosario">13. Glosario de Términos</h2>

    <table>
        <tr>
            <th>Término</th>
            <th>Definición</th>
        </tr>
        <tr>
            <td><strong>Organismo</strong></td>
            <td>Universidad o institución principal (ej: UPTOS "Clodosbaldo Russián")</td>
        </tr>
        <tr>
            <td><strong>Unidad Administradora</strong></td>
            <td>Departamento dentro del organismo (ej: Departamento de Informática)</td>
        </tr>
        <tr>
            <td><strong>Dependencia</strong></td>
            <td>Salón, laboratorio u oficina (ej: Lab. Computación 1)</td>
        </tr>
        <tr>
            <td><strong>Bien</strong></td>
            <td>Activo físico registrado en el sistema (computadora, mueble, equipo)</td>
        </tr>
        <tr>
            <td><strong>Responsable</strong></td>
            <td>Persona a cargo del cuidado de bienes en una dependencia</td>
        </tr>
        <tr>
            <td><strong>Movimiento</strong></td>
            <td>Cambio en la ubicación, estado o propiedad de un bien</td>
        </tr>
        <tr>
            <td><strong>Desincorporación</strong></td>
            <td>Proceso de dar de baja un bien del inventario institucional</td>
        </tr>
        <tr>
            <td><strong>Auditoría</strong></td>
            <td>Registro automático de todas las operaciones del sistema</td>
        </tr>
        <tr>
            <td><strong>Acta</strong></td>
            <td>Documento oficial en PDF generado por el sistema</td>
        </tr>
        <tr>
            <td><strong>Trazabilidad</strong></td>
            <td>Capacidad de seguir el historial completo de un bien</td>
        </tr>
        <tr>
            <td><strong>KPI</strong></td>
            <td>Indicador clave de rendimiento (Key Performance Indicator)</td>
        </tr>
        <tr>
            <td><strong>Dashboard</strong></td>
            <td>Panel de control con visión general del sistema</td>
        </tr>
        <tr>
            <td><strong>Importar</strong></td>
            <td>Cargar datos desde un archivo externo (Excel)</td>
        </tr>
        <tr>
            <td><strong>Exportar</strong></td>
            <td>Descargar datos del sistema a un archivo</td>
        </tr>
    </table>

    <hr>

    <h2>📞 Información de Contacto y Soporte</h2>

    <div class="card">
        <div class="card-title">Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"</div>
        <p><strong>Sistema:</strong> Gestión de Inventario de Bienes</p>
        <p><strong>Versión:</strong> 1.0</p>
        <p><strong>Fecha de Actualización:</strong> Marzo 2026</p>
        <hr>
        <p><strong>Soporte Técnico:</strong></p>
        <ul>
            <li>Contacta al administrador del sistema</li>
            <li>Correo: soporte@uptos.edu.ve</li>
            <li>Teléfono: (0251) XXX-XXXX</li>
        </ul>
    </div>

    <hr>

    <div class="highlight">
        <strong>📝 Nota Final:</strong> Este manual fue creado para la versión actual del sistema (v1.0). Las funcionalidades pueden variar con futuras actualizaciones. Se recomienda revisar periódicamente si hay actualizaciones del manual.
    </div>

    <div style="text-align: center; margin-top: 40px; padding: 20px; color: #999; font-size: 12px;">
        <p>© 2026 Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"</p>
        <p>Sistema de Gestión de Inventario de Bienes - Manual de Usuario</p>
    </div>

</body>
</html>
