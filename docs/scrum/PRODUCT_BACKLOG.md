# Product Backlog

## Sistema de Gestión de Inventario de Bienes

| Campo | Valor |
|-------|-------|
| **Producto** | Sistema de Gestión de Inventario de Bienes para Instituciones Educativas |
| **Product Owner** | Gerente de Administración |
| **Fecha de Creación** | 04/11/2025 |
| **Última Actualización** | 04/11/2025 |
| **Versión** | 1.0 |
| **Velocity Estimado** | 35-45 puntos/sprint |
| **Duración Sprint** | 2 semanas |

---

## 1. VISIÓN DEL PRODUCTO

Ser la solución integral de gestión patrimonial para instituciones educativas venezolanas que garantice transparencia, trazabilidad y control total de los bienes públicos, facilitando auditorías y optimizando la administración de activos institucionales.

---

## 2. ÉPICAS DEL PROYECTO

### E1: Gestión de Estructura Organizacional
Gestión jerárquica de organismos, unidades administrativas y dependencias.

### E2: Gestión de Usuarios y Accesos
Sistema de autenticación, roles y permisos de usuarios.

### E3: Gestión de Inventario de Bienes
Registro, actualización, búsqueda y seguimiento de bienes patrimoniales.

### E4: Gestión de Movimientos y Trazabilidad
Control de traslados, asignaciones y cambios de responsabilidad de bienes.

### E5: Reportes y Auditoría
Generación de reportes, consultas y auditoría del sistema.

### E6: Optimización y Experiencia de Usuario
Mejoras de rendimiento, usabilidad y funcionalidades avanzadas.

---

## 3. HISTORIAS DE USUARIO PRIORIZADAS

### PRIORIDAD CRÍTICA (Must Have)

#### HU-001: Registro de Usuarios Administradores
**Épica:** E2 | **Puntos:** 5 | **Sprint:** 1

*Como administrador del sistema quiero registrar usuarios con diferentes roles para controlar el acceso al sistema según responsabilidades*

**Criterios de Aceptación:**
- El sistema permite crear usuarios con campos: cédula, nombre, apellido, correo, contraseña, rol
- Los roles disponibles son: Administrador, Gerente de Bienes, Usuario Responsable
- La cédula debe ser única en el sistema
- El correo debe tener formato válido
- La contraseña debe tener al menos 8 caracteres
- Se valida que no existan usuarios duplicados

---

#### HU-002: Iniciar Sesión en el Sistema
**Épica:** E2 | **Puntos:** 5 | **Sprint:** 1

*Como usuario registrado quiero iniciar sesión con mis credenciales para acceder a las funcionalidades según mi rol*

**Criterios de Aceptación:**
- El usuario puede iniciar sesión con cédula/correo y contraseña
- El sistema valida las credenciales contra la base de datos
- Si las credenciales son incorrectas, muestra mensaje de error
- Si las credenciales son correctas, redirige al dashboard correspondiente
- Se mantiene la sesión activa con token/remember_token
- Opción de "Recordarme" disponible

---

#### HU-003: Cerrar Sesión
**Épica:** E2 | **Puntos:** 3 | **Sprint:** 1

*Como usuario autenticado quiero cerrar mi sesión de forma segura para proteger mi cuenta cuando termino de usar el sistema*

**Criterios de Aceptación:**
- Botón de cerrar sesión visible en todas las páginas
- Al cerrar sesión, se destruye el token de autenticación
- Redirige a la página de login
- No se puede acceder a páginas protegidas después de cerrar sesión

---

#### HU-004: Crear Organismo
**Épica:** E1 | **Puntos:** 5 | **Sprint:** 1

*Como administrador quiero registrar organismos en el sistema para establecer la estructura organizacional de más alto nivel*

**Criterios de Aceptación:**
- Formulario con campos: código y nombre del organismo
- El código debe ser único
- Validación de campos obligatorios
- Mensaje de confirmación al crear exitosamente
- Lista de organismos creados visible

---

#### HU-005: Crear Unidad Administradora
**Épica:** E1 | **Puntos:** 5 | **Sprint:** 1

*Como administrador quiero registrar unidades administradoras asociadas a un organismo para organizar las instituciones por áreas administrativas*

**Criterios de Aceptación:**
- Formulario con campos: código, denominación, organismo padre
- Selector de organismo desde lista existente
- El código debe ser único dentro del organismo
- Validación de campos obligatorios
- Se muestra relación jerárquica con el organismo

---

#### HU-006: Crear Dependencia
**Épica:** E1 | **Puntos:** 5 | **Sprint:** 1

*Como administrador quiero registrar dependencias dentro de una unidad administradora para completar la estructura organizacional jerárquica*

**Criterios de Aceptación:**
- Formulario con campos: código, denominación, unidad administradora
- Selector de unidad administradora desde lista existente
- El código debe ser único dentro de la unidad
- Validación de campos obligatorias
- Se muestra la jerarquía completa: Organismo > Unidad > Dependencia

---

#### HU-007: Registrar Bien en Inventario
**Épica:** E3 | **Puntos:** 8 | **Sprint:** 2

*Como gerente de bienes quiero registrar un nuevo bien en el inventario para mantener control de los activos institucionales*

**Criterios de Aceptación:**
- Formulario con campos: código, descripción, precio, dependencia, responsable, ubicación, fecha de registro, estado
- El código del bien debe ser único
- Se pueden subir hasta 5 fotos del bien
- Estado inicial: "Activo"
- Validación de precio (número positivo)
- Selector de dependencia y responsable desde listas existentes
- Confirmación visual al registrar exitosamente

---

#### HU-008: Listar Bienes por Dependencia
**Épica:** E3 | **Puntos:** 5 | **Sprint:** 2

*Como gerente de bienes quiero ver todos los bienes asignados a una dependencia para consultar el inventario de cada área*

**Criterios de Aceptación:**
- Tabla con columnas: código, descripción, precio, responsable, ubicación, estado, fecha
- Filtro por dependencia
- Paginación (20 bienes por página)
- Búsqueda por código o descripción
- Ordenamiento por columnas
- Exportar listado a PDF

---

#### HU-009: Ver Detalle de un Bien
**Épica:** E3 | **Puntos:** 5 | **Sprint:** 2

*Como usuario del sistema quiero ver toda la información detallada de un bien para consultar sus características y estado actual*

**Criterios de Aceptación:**
- Vista con todos los campos del bien
- Galería de fotos del bien
- Información de responsable actual
- Historial resumido de movimientos
- Jerarquía organizacional (Organismo > Unidad > Dependencia)
- Botones de acción según rol del usuario

---

#### HU-010: Editar Información de un Bien
**Épica:** E3 | **Puntos:** 5 | **Sprint:** 2

*Como gerente de bienes quiero actualizar la información de un bien existente para mantener los datos actualizados en el inventario*

**Criterios de Aceptación:**
- Formulario prellenado con datos actuales
- Se pueden modificar: descripción, precio, ubicación, estado, fotos
- No se puede modificar el código del bien
- Validación de campos obligatorios
- Se registra la fecha de última actualización
- Confirmación antes de guardar cambios

---

### PRIORIDAD ALTA (Should Have)

#### HU-011: Registrar Movimiento de Bien
**Épica:** E4 | **Puntos:** 8 | **Sprint:** 3

*Como gerente de bienes quiero registrar el traslado de un bien entre dependencias para mantener trazabilidad de la ubicación de los activos*

**Criterios de Aceptación:**
- Formulario con campos: bien, dependencia origen, dependencia destino, fecha, motivo, usuario que autoriza
- Validación de que la dependencia origen coincida con la actual del bien
- Al registrar, se actualiza automáticamente la dependencia del bien
- Se crea registro en historial de movimientos
- Opción de adjuntar documento de autorización

---

#### HU-012: Cambiar Responsable de un Bien
**Épica:** E4 | **Puntos:** 5 | **Sprint:** 3

*Como gerente de bienes quiero reasignar la responsabilidad de un bien a otro usuario para actualizar quién está a cargo del activo*

**Criterios de Aceptación:**
- Selector de nuevo responsable desde lista de usuarios
- Campo de motivo del cambio (obligatorio)
- Fecha efectiva del cambio
- Notificación al nuevo responsable (opcional)
- Se registra en historial de movimientos
- El responsable anterior queda registrado en el historial

---

#### HU-013: Ver Historial de Movimientos de un Bien
**Épica:** E4 | **Puntos:** 5 | **Sprint:** 3

*Como gerente de bienes quiero consultar todos los movimientos históricos de un bien para auditar su trazabilidad completa*

**Criterios de Aceptación:**
- Lista cronológica de todos los movimientos
- Por cada movimiento se muestra: fecha, tipo, dependencia origen/destino, responsable anterior/nuevo, usuario que registró, motivo
- Filtros por rango de fechas
- Exportar historial a PDF
- Opción de ver documentos adjuntos si existen

---

#### HU-014: Generar Reporte de Inventario por Dependencia
**Épica:** E5 | **Puntos:** 8 | **Sprint:** 3

*Como gerente de bienes quiero generar un reporte consolidado del inventario de una dependencia para presentar información oficial ante auditorías*

**Criterios de Aceptación:**
- Selector de dependencia
- Opción de incluir fotos de los bienes
- Reporte muestra: encabezado institucional, lista de bienes con detalles, valor total, responsable de la dependencia, fecha de generación
- Exportar a PDF con formato oficial
- Incluir logo institucional
- Numeración y firma digital

---

#### HU-015: Buscar Bienes Globalmente
**Épica:** E3 | **Puntos:** 8 | **Sprint:** 3

*Como usuario del sistema quiero buscar bienes en todo el sistema usando diferentes criterios para localizar rápidamente un activo específico*

**Criterios de Aceptación:**
- Búsqueda por: código, descripción (texto parcial), dependencia, responsable, rango de precios
- Resultados muestran información resumida del bien
- Filtros combinables
- Paginación de resultados
- Opción de guardar búsquedas frecuentes
- Exportar resultados a Excel

---

#### HU-016: Dashboard de Administrador
**Épica:** E5 | **Puntos:** 8 | **Sprint:** 4

*Como administrador quiero ver un dashboard con métricas del sistema para tener una visión general del estado del inventario*

**Criterios de Aceptación:**
- Total de bienes en el sistema
- Total de bienes por estado (Activo, Inactivo, Mantenimiento)
- Valor total del inventario
- Bienes registrados este mes
- Movimientos realizados esta semana
- Gráficos de distribución por dependencia
- Últimas actividades del sistema

---

#### HU-017: Gestionar Tipos de Responsables
**Épica:** E2 | **Puntos:** 5 | **Sprint:** 4

*Como administrador quiero definir tipos de responsables (Primario, Por Uso) para clasificar los niveles de responsabilidad sobre los bienes*

**Criterios de Aceptación:**
- CRUD de tipos de responsables
- Campos: código, nombre, descripción
- Al menos 2 tipos por defecto: "Responsable Patrimonial Primario" y "Responsable Patrimonial Por Uso"
- Se asocia tipo al crear/editar responsables
- No se puede eliminar tipo si tiene responsables asociados

---

#### HU-018: Registrar Responsable
**Épica:** E1 | **Puntos:** 5 | **Sprint:** 4

*Como administrador quiero registrar responsables con sus datos completos para identificar a las personas a cargo de los bienes*

**Criterios de Aceptación:**
- Formulario con campos: cédula, nombre, apellido, cargo, tipo de responsable, dependencia asignada
- Validación de cédula única
- Relación con usuario del sistema (opcional)
- Puede ser responsable de múltiples bienes
- Estado activo/inactivo

---

#### HU-019: Registro de Auditoría del Sistema
**Épica:** E5 | **Puntos:** 8 | **Sprint:** 4

*Como administrador quiero que el sistema registre automáticamente todas las acciones críticas para mantener trazabilidad y seguridad*

**Criterios de Aceptación:**
- Se registra automáticamente: usuario, acción, tabla afectada, registro afectado, fecha/hora, IP
- Acciones auditadas: crear, editar, eliminar (bienes, usuarios, movimientos)
- Los registros de auditoría no pueden ser eliminados
- Vista de consulta solo para administradores
- Filtros por usuario, acción, fecha

---

#### HU-020: Marcar Bien como Inactivo/Dado de Baja
**Épica:** E3 | **Puntos:** 5 | **Sprint:** 4

*Como gerente de bienes quiero cambiar el estado de un bien a inactivo o dado de baja para reflejar que ya no está en uso o fue descartado*

**Criterios de Aceptación:**
- Estados posibles: Activo, Inactivo, En Mantenimiento, Dado de Baja, Extraviado
- Campo obligatorio: motivo del cambio de estado
- Fecha efectiva del cambio
- Se registra en historial
- Bienes dados de baja no aparecen en reportes activos por defecto
- Opción de adjuntar documento de autorización de baja

---

### PRIORIDAD MEDIA (Could Have)

#### HU-021: Notificaciones por Correo
**Épica:** E6 | **Puntos:** 8 | **Sprint:** 5

*Como usuario responsable quiero recibir notificaciones por correo de cambios en mis bienes para estar informado de movimientos o reasignaciones*

**Criterios de Aceptación:**
- Envío de correo al asignar un bien nuevo
- Envío de correo al remover responsabilidad
- Envío de correo al registrar movimiento de bien bajo su cargo
- Template HTML profesional
- Opción de desactivar notificaciones en perfil de usuario

---

#### HU-022: Importar Bienes desde Excel
**Épica:** E3 | **Puntos:** 13 | **Sprint:** 5

*Como gerente de bienes quiero importar múltiples bienes desde un archivo Excel para agilitar la carga inicial de inventario*

**Criterios de Aceptación:**
- Template de Excel descargable con formato requerido
- Validación de formato del archivo
- Validación de datos por fila
- Reporte de errores con detalle de filas con problemas
- Confirmación antes de importar
- Importación en lote (hasta 500 registros)
- Barra de progreso durante la importación

---

#### HU-023: Exportar Inventario a Excel
**Épica:** E5 | **Puntos:** 5 | **Sprint:** 5

*Como gerente de bienes quiero exportar el inventario completo a Excel para realizar análisis externos o respaldos*

**Criterios de Aceptación:**
- Exporta todos los bienes o filtrados
- Columnas: código, descripción, precio, dependencia, responsable, ubicación, estado, fecha registro
- Formato Excel (.xlsx)
- Nombre de archivo con fecha de exportación
- Incluye hoja resumen con totales

---

#### HU-024: Generar Código de Barra para Bienes
**Épica:** E6 | **Puntos:** 8 | **Sprint:** 5

*Como gerente de bienes quiero generar códigos de barra para cada bien para facilitar su identificación y consulta rápida*

**Criterios de Aceptación:**
- Código de barra único por bien
- Al escanear, redirige a página de detalle del bien
- Opción de descargar código individual en PNG
- Opción de generar códigos masivos para imprimir
- Template para imprimir etiquetas con código de barra y código del bien

---

#### HU-025: Escanear Código de Barra desde Móvil
**Épica:** E6 | **Puntos:** 8 | **Sprint:** 5

*Como usuario del sistema quiero escanear el código de barra de un bien desde mi móvil para consultar su información rápidamente*

**Criterios de Aceptación:**
- Botón de escanear en versión móvil
- Usa cámara del dispositivo
- Reconoce código de barra y redirige a detalle del bien
- Funciona sin necesidad de app nativa (PWA)
- Mensaje de error si código no es válido

---

#### HU-026: Perfil de Usuario
**Épica:** E2 | **Puntos:** 5 | **Sprint:** 5

*Como usuario del sistema quiero ver y editar mi información de perfil para mantener mis datos actualizados*

**Criterios de Aceptación:**
- Vista con datos personales: cédula, nombre, apellido, correo, rol
- Opción de cambiar contraseña
- Opción de actualizar correo
- Validación de contraseña actual al cambiar
- Foto de perfil (opcional)
- Historial de última actividad

---

#### HU-027: Recuperar Contraseña
**Épica:** E2 | **Puntos:** 8 | **Sprint:** 6

*Como usuario registrado quiero recuperar mi contraseña si la olvido para poder acceder nuevamente al sistema*

**Criterios de Aceptación:**
- Link de "Olvidé mi contraseña" en login
- Ingresa correo electrónico
- Envía email con token de recuperación
- Link válido por 1 hora
- Formulario para crear nueva contraseña
- Confirmación de contraseña actualizada

---

#### HU-028: Reporte de Bienes por Responsable
**Épica:** E5 | **Puntos:** 5 | **Sprint:** 6

*Como gerente de bienes quiero generar reporte de todos los bienes bajo responsabilidad de una persona para verificar su asignación actual*

**Criterios de Aceptación:**
- Selector de responsable
- Lista de bienes asignados con detalles
- Valor total de bienes bajo su responsabilidad
- Fecha de asignación de cada bien
- Exportar a PDF con formato oficial
- Espacio para firma del responsable

---

#### HU-029: Dashboard de Responsable
**Épica:** E2 | **Puntos:** 8 | **Sprint:** 6

*Como usuario responsable quiero ver un dashboard con los bienes bajo mi cargo para conocer mi responsabilidad patrimonial actual*

**Criterios de Aceptación:**
- Total de bienes asignados
- Valor total bajo responsabilidad
- Lista de bienes con estado
- Alertas de bienes que requieren mantenimiento
- Historial reciente de cambios en mis bienes
- Opción de imprimir constancia de responsabilidad

---

#### HU-030: Filtros Avanzados en Listados
**Épica:** E6 | **Puntos:** 8 | **Sprint:** 6

*Como usuario del sistema quiero aplicar múltiples filtros simultáneos en los listados para encontrar información específica rápidamente*

**Criterios de Aceptación:**
- Filtros por: organismo, unidad, dependencia, estado, rango de fechas, rango de precios, responsable
- Filtros se pueden combinar
- Contador de resultados
- Opción de guardar filtro favorito
- Limpiar todos los filtros con un click
- URL compartible con filtros aplicados

---

### PRIORIDAD BAJA (Won't Have - Futuras Versiones)

| ID | Historia | Puntos |
|----|----------|--------|
| HU-031 | Configuración de Recordatorios de Mantenimiento | 8 |
| HU-032 | Integración con Sistema de Compras | 13 |
| HU-033 | App Móvil Nativa | 21 |
| HU-034 | Firma Digital de Reportes | 13 |
| HU-035 | Geolocalización de Bienes | 21 |

---

## 4. RESUMEN DE ESTIMACIÓN

| Sprint | Puntos | Historias |
|--------|--------|-----------|
| Sprint 1 | 28 | HU-001 a HU-006 |
| Sprint 2 | 28 | HU-007 a HU-010 |
| Sprint 3 | 34 | HU-011 a HU-015 |
| Sprint 4 | 31 | HU-016 a HU-020 |
| Sprint 5 | 47 | HU-021 a HU-026 |
| Sprint 6 | 29 | HU-027 a HU-030 |
| **TOTAL** | **197** | **30 historias** |

---

## 5. DEFINICIÓN DE PRIORIDADES

| Prioridad | Descripción |
|----------|-------------|
| **Crítica** | Funcionalidad esencial sin la cual el sistema no puede operar |
| **Alta** | Funcionalidad muy importante para el valor del producto |
| **Media** | Funcionalidad que agrega valor significativo pero no es crítica |
| **Baja** | Mejoras futuras, características avanzadas o nice-to-have |

---

## 6. NOTAS

- Las estimaciones están en puntos de historia usando escala de Fibonacci
- Un punto equivale aproximadamente a 4-6 horas de desarrollo
- La velocidad estimada del equipo es de 28-35 puntos por sprint
- Duración de sprint: 2 semanas
