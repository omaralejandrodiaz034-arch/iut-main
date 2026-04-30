# Guion para Video Demo del Sistema de Gestión de Inventario de Bienes

## Información General del Video

- **Duración Estimada**: 10-15 minutos.
- **Formato**: Video screen recording con voiceover en español.
- **Herramientas**: Software de grabación de pantalla (e.g., OBS Studio), micrófono para narración.
- **Audiencia**: Los tres roles (Programador, Jefe de Proyecto, Frontier) para mostrar cómo funciona el sistema y todas sus funcionalidades.
- **Objetivo**: Demostrar el flujo completo de uso, desde login hasta reportes, enfocándose en funcionalidades operativas.

## Estructura del Video

### 1. Introducción (30 segundos)
   - **Visual**: Logo del sistema o pantalla de inicio.
   - **Narración**: "Bienvenidos a la demo del Sistema de Gestión de Inventario de Bienes. Este sistema permite gestionar activos patrimoniales en instituciones educativas venezolanas. Veremos cómo funciona desde el login hasta la generación de reportes, cubriendo todas sus funcionalidades principales."

### 2. Login al Sistema (1 minuto)
   - **Visual**: Pantalla de login, ingresar credenciales (e.g., correo y contraseña).
   - **Narración**: "Comenzamos con el login. El sistema usa autenticación segura con tabla de usuarios personalizada. Ingresamos correo y contraseña. Una vez autenticado, accedemos al dashboard principal."
   - **Funcionalidad Mostrada**: Autenticación, validación de credenciales.

### 3. Dashboard Principal (1 minuto)
   - **Visual**: Dashboard con métricas, menú lateral, navegación.
   - **Narración**: "El dashboard muestra métricas clave como total de bienes, movimientos recientes y alertas. Incluye gráficos y estadísticas. Desde aquí navegamos a todas las secciones."
   - **Funcionalidad Mostrada**: Vista general, navegación sidebar, modo oscuro opcional.

### 4. Gestión de Estructura Organizacional (2 minutos)
   - **Visual**: Menú > Organismos > Crear organismo (código, nombre). Luego Unidades Administradoras, Dependencias.
   - **Narración**: "Primero, gestionamos la estructura jerárquica. Creamos un Organismo, como el Ministerio de Educación. Luego, una Unidad Administradora dentro de él, y finalmente Dependencias. Cada nivel pertenece al superior, asegurando jerarquía."
   - **Funcionalidad Mostrada**: CRUD para Organismo, UnidadAdministradora, Dependencia. Asignación de responsables a dependencias.

### 5. Gestión de Usuarios (1.5 minutos)
   - **Visual**: Menú > Usuarios > Crear usuario (cédula, nombre, apellido, correo, rol, foto perfil).
   - **Narración**: "Ahora, gestionamos usuarios. Creamos un usuario administrador o normal, con roles definidos. Incluye subida de foto de perfil y validación de cédula normalizada."
   - **Funcionalidad Mostrada**: Registro de usuarios, roles (admin/normal), validación.

### 6. Gestión de Bienes (3 minutos)
   - **Visual**: Menú > Bienes > Crear bien (dependencia, código, descripción, precio, foto, estado, tipo, características).
   - **Narración**: "La funcionalidad central es gestionar bienes. Asignamos a una dependencia, subimos foto, seleccionamos tipo (electrónico, mobiliario, etc.) y características específicas. Estados incluyen activo, dañado, en mantenimiento. Listamos con paginación y filtros."
   - **Funcionalidad Mostrada**: CRUD bienes, subida de fotos, tipos con campos dinámicos, estados, búsqueda y filtros avanzados (organismo, unidad, dependencia, estado, tipo, fechas).

### 7. Gestión de Movimientos (2 minutos)
   - **Visual**: Seleccionar bien > Crear movimiento (tipo: traslado, asignación; destino, usuario).
   - **Narración**: "Los movimientos rastrean cambios de ubicación o estado. Creamos un movimiento para trasladar un bien a otra dependencia. Se registra automáticamente con fecha y usuario."
   - **Funcionalidad Mostrada**: Crear movimientos, historial por bien, trazabilidad completa.

### 8. Reportes y Auditoría (2 minutos)
   - **Visual**: Menú > Reportes > Generar reporte (filtros por dependencia, fechas) > Exportar a PDF o Excel.
   - **Narración**: "Generamos reportes por dependencia o global. Incluye auditoría con logs de cambios. Exportamos a PDF para impresión o Excel para análisis."
   - **Funcionalidad Mostrada**: Generación de reportes, exportación PDF/Excel, vista de auditoría.

### 9. Búsqueda y Filtros Avanzados (1 minuto)
   - **Visual**: Barra de búsqueda en lista de bienes, filtros (estado, tipo, fechas).
   - **Narración**: "El sistema incluye búsqueda global y filtros avanzados para localizar bienes rápidamente, esencial para auditorías."
   - **Funcionalidad Mostrada**: Búsqueda, filtros.

### 10. Funcionalidades Adicionales (1 minuto)
   - **Visual**: Mostrar perfil usuario, logout, o QR codes si implementado.
   - **Narración**: "Otras funcionalidades incluyen perfil de usuario con edición, códigos QR para escaneo móvil, y notificaciones por correo."
   - **Funcionalidad Mostrada**: Perfil, logout, extras como QR.

### 11. Cierre (30 segundos)
   - **Visual**: Pantalla final con logo.
   - **Narración**: "Hemos visto cómo funciona el sistema: desde la estructura organizacional hasta reportes. Todas las funcionalidades están integradas para una gestión eficiente de inventarios. Gracias por ver esta demo."

## Notas de Producción

- **Pacing**: Hablar despacio, pausar en acciones.
- **Transiciones**: Usar zooms o highlights en elementos importantes.
- **Errores**: Evitar mostrar errores; usar datos demo.
- **Idioma**: Español, claro y técnico.
- **Calidad**: Alta resolución, audio claro.

Este guion cubre todas las funcionalidades del sistema en un flujo lógico.