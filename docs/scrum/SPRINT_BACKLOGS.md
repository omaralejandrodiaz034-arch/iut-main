# Sprint Backlogs

## Sistema de Gestión de Inventario de Bienes

| Campo | Valor |
|-------|-------|
| **Equipo de Desarrollo** | 4 desarrolladores |
| **Duración del Sprint** | 2 semanas |
| **Velocidad Promedio** | 28-35 puntos por sprint |

---

# SPRINT 1: Fundamentos y Estructura Base

## Objetivo
Establecer autenticación, roles y estructura organizacional básica

## Duración
4 Nov 2025 - 17 Nov 2025

## Story Points
28 puntos

## Estado
**✅ COMPLETADO** - Funcionalidades implementadas en el código existente

## Historias de Usuario

### HU-001: Registro de Usuarios Administradores (5 pts)

**Tareas:**
- [x] Crear migración para tabla usuarios con todos los campos requeridos
- [x] Crear modelo Usuario con validaciones
- [x] Crear formulario de registro con validación frontend
- [x] Implementar controlador de registro con validación backend
- [x] Crear vista de listado de usuarios
- [x] Implementar seeders con usuarios de prueba
- [x] Testing unitario de validaciones

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Usuario puede crear nuevos usuarios con roles y visualizar listado

---

### HU-002: Iniciar Sesión en el Sistema (5 pts)

**Tareas:**
- [x] Configurar sistema de autenticación de Laravel
- [x] Crear formulario de login con validación
- [x] Implementar lógica de autenticación con remember_token
- [x] Crear middleware para proteger rutas
- [x] Implementar redirección según rol del usuario
- [x] Crear dashboards básicos por rol
- [x] Testing de autenticación

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Usuario puede iniciar sesión y acceder a dashboard según su rol

---

### HU-003: Cerrar Sesión (3 pts)

**Tareas:**
- [x] Implementar endpoint de logout
- [x] Agregar botón de cerrar sesión en navbar
- [x] Destruir sesión y token al cerrar
- [x] Redirección a página de login
- [x] Testing de destrucción de sesión

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede cerrar sesión de forma segura

---

### HU-004: Crear Organismo (5 pts)

**Tareas:**
- [x] Crear migración para tabla organismos
- [x] Crear modelo Organismo con validaciones
- [x] Crear formulario de registro de organismo
- [x] Implementar CRUD de organismos
- [x] Crear vista de listado de organismos
- [x] Validación de código único
- [x] Testing CRUD

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Administrador puede crear y listar organismos

---

### HU-005: Crear Unidad Administradora (5 pts)

**Tareas:**
- [x] Crear migración para tabla unidades_administradoras
- [x] Crear modelo UnidadAdministradora con relaciones
- [x] Crear formulario con selector de organismo
- [x] Implementar CRUD de unidades
- [x] Crear vista de listado jerárquico
- [x] Validación de código único por organismo
- [x] Testing CRUD y relaciones

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Administrador puede crear unidades y vincularlas a organismos

---

### HU-006: Crear Dependencia (5 pts)

**Tareas:**
- [x] Crear migración para tabla dependencias
- [x] Crear modelo Dependencia con relaciones
- [x] Crear formulario con selector de unidad
- [x] Implementar CRUD de dependencias
- [x] Crear vista de jerarquía completa
- [x] Validación de código único por unidad
- [x] Testing CRUD y relaciones jerárquicas

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Administrador puede crear dependencias y visualizar jerarquía completa

---

## Definition of Done (DoD)

- [x] Código revisado por al menos un compañero (Code Review)
- [x] Tests unitarios implementados y pasando
- [x] Documentación técnica actualizada
- [x] Sin errores críticos ni warnings
- [x] Funcionalidad probada en entorno de desarrollo
- [x] Migrations ejecutadas correctamente

---

# SPRINT 2: Gestión de Inventario Base

## Objetivo
Implementar funcionalidades core de gestión de bienes

## Duración
18 Nov 2025 - 1 Dic 2025

## Story Points
28 puntos

## Estado
**✅ COMPLETADO** - Funcionalidades implementadas en el código existente

## Historias de Usuario

### HU-007: Registrar Bien en Inventario (8 pts)

**Tareas:**
- [x] Crear migración para tabla bienes con todos los campos
- [x] Crear modelo Bien con relaciones y validaciones
- [x] Implementar subida de imágenes (storage)
- [x] Crear formulario de registro con selectores dinámicos
- [x] Implementar validación de código único
- [x] Crear endpoint de registro de bien
- [x] Implementar galería de fotos
- [x] Testing de registro y validaciones

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Gerente puede registrar bienes con fotos

---

### HU-008: Listar Bienes por Dependencia (5 pts)

**Tareas:**
- [x] Crear vista de tabla de bienes con datatables
- [x] Implementar filtro por dependencia
- [x] Implementar paginación
- [x] Implementar búsqueda por código/descripción
- [x] Implementar ordenamiento por columnas
- [x] Crear funcionalidad de exportar a PDF
- [x] Testing de filtros y búsqueda

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede filtrar y buscar bienes por dependencia

---

### HU-009: Ver Detalle de un Bien (5 pts)

**Tareas:**
- [x] Crear vista de detalle de bien
- [x] Implementar galería de fotos con lightbox
- [x] Mostrar información de responsable
- [x] Mostrar jerarquía organizacional
- [x] Mostrar historial resumido de movimientos
- [x] Implementar botones de acción según rol
- [x] Testing de permisos por rol

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede ver toda la información del bien

---

### HU-010: Editar Información de un Bien (5 pts)

**Tareas:**
- [x] Crear formulario de edición prellenado
- [x] Implementar actualización de datos
- [x] Implementar gestión de fotos (agregar/eliminar)
- [x] Bloquear edición de código
- [x] Implementar confirmación antes de guardar
- [x] Registrar fecha de última actualización
- [x] Testing de actualización

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Gerente puede actualizar información de bienes

---

## Tareas Técnicas del Sprint

- [x] Configurar storage para imágenes de bienes
- [x] Implementar librería para generación de PDFs
- [x] Crear componentes reutilizables de formularios
- [x] Optimizar consultas con eager loading

## Definition of Done (DoD)

- [x] Código revisado por al menos un compañero
- [x] Tests unitarios y de integración pasando
- [x] Responsive design verificado
- [x] Optimización de imágenes implementada
- [x] Documentación de API actualizada
- [x] Sin errores en consola del navegador

---

# SPRINT 3: Movimientos y Reportes

## Objetivo
Implementar trazabilidad de bienes y sistema de reportes

## Duración
2 Dic 2025 - 15 Dic 2025

## Story Points
34 puntos

## Estado
**✅ COMPLETADO** - Funcionalidades implementadas en el código existente

## Historias de Usuario

### HU-011: Registrar Movimiento de Bien (8 pts)

**Tareas:**
- [x] Crear migración para tabla movimientos
- [x] Crear modelo Movimiento con relaciones
- [x] Crear formulario de registro de movimiento
- [x] Implementar validación de dependencia origen
- [x] Implementar actualización automática de dependencia del bien
- [x] Crear registro en historial de movimientos
- [x] Implementar subida de documento de autorización
- [x] Testing de lógica de movimientos

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Gerente puede registrar traslados entre dependencias

---

### HU-012: Cambiar Responsable de un Bien (5 pts)

**Tareas:**
- [x] Crear formulario de cambio de responsable
- [x] Implementar selector de nuevo responsable
- [x] Validar motivo obligatorio
- [x] Actualizar responsable del bien
- [x] Registrar en historial de movimientos
- [x] Implementar notificación (opcional)
- [x] Testing de cambio de responsabilidad

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Gerente puede reasignar responsabilidad de bienes

---

### HU-013: Ver Historial de Movimientos de un Bien (5 pts)

**Tareas:**
- [x] Crear vista de historial cronológico
- [x] Implementar listado de movimientos con detalles
- [x] Implementar filtros por fecha
- [x] Crear funcionalidad de exportar a PDF
- [x] Mostrar documentos adjuntos si existen
- [x] Testing de consultas de historial

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede consultar historial completo de movimientos

---

### HU-014: Generar Reporte de Inventario por Dependencia (8 pts)

**Tareas:**
- [x] Crear migración para tabla reportes
- [x] Diseñar template de reporte PDF oficial
- [x] Implementar generación de reporte con encabezado institucional
- [x] Implementar opción de incluir fotos
- [x] Calcular valor total de inventario
- [x] Incluir logo institucional
- [x] Implementar numeración y firma digital
- [x] Testing de generación de PDF

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Gerente puede generar reportes oficiales en PDF

---

### HU-015: Buscar Bienes Globalmente (8 pts)

**Tareas:**
- [x] Crear formulario de búsqueda avanzada
- [x] Implementar búsqueda por múltiples criterios
- [x] Implementar filtros combinables
- [x] Crear vista de resultados con paginación
- [x] Implementar funcionalidad de guardar búsquedas
- [x] Implementar exportación a Excel
- [x] Optimizar consultas de búsqueda
- [x] Testing de búsqueda avanzada

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Usuario puede buscar bienes usando múltiples criterios

---

## Definition of Done (DoD)

- [x] Código revisado y aprobado
- [x] Tests de integración pasando
- [x] Reportes PDF con formato profesional
- [x] Optimización de consultas complejas
- [x] Documentación de endpoints
- [x] Pruebas de carga de búsquedas

---

# SPRINT 4: Auditoría y Responsables

## Objetivo
Implementar sistema de auditoría y gestión de responsables

## Duración
16 Dic 2025 - 29 Dic 2025

## Story Points
31 puntos

## Estado
**✅ COMPLETADO** - Funcionalidades implementadas en el código existente

## Historias de Usuario

### HU-016: Dashboard de Administrador (8 pts)

**Tareas:**
- [x] Diseñar layout de dashboard
- [x] Implementar cálculo de métricas
- [x] Crear gráficos de distribución (Chart.js)
- [x] Implementar widget de últimas actividades
- [x] Implementar filtros de fecha para métricas
- [x] Optimizar consultas de dashboard
- [x] Testing de cálculos

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Administrador ve métricas clave del sistema

---

### HU-017: Gestionar Tipos de Responsables (5 pts)

**Tareas:**
- [x] Crear migración para tabla tipos_responsables
- [x] Crear modelo TipoResponsable
- [x] Implementar CRUD de tipos
- [x] Crear seeder con tipos por defecto
- [x] Implementar validación de eliminación
- [x] Testing CRUD

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Administrador puede gestionar tipos de responsables

---

### HU-018: Registrar Responsable (5 pts)

**Tareas:**
- [x] Crear migración para tabla responsables
- [x] Crear modelo Responsable con relaciones
- [x] Crear formulario de registro
- [x] Implementar CRUD de responsables
- [x] Vincular con usuarios (opcional)
- [x] Implementar estado activo/inactivo
- [x] Testing CRUD

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Administrador puede registrar responsables completos

---

### HU-019: Registro de Auditoría del Sistema (8 pts)

**Tareas:**
- [x] Crear migración para tabla auditoria
- [x] Crear modelo Auditoria
- [x] Implementar observers para modelos críticos
- [x] Registrar automáticamente acciones CRUD
- [x] Capturar IP y user agent
- [x] Crear vista de consulta de auditoría
- [x] Implementar filtros de auditoría
- [x] Testing de registro automático

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Sistema registra automáticamente todas las acciones críticas

---

### HU-020: Marcar Bien como Inactivo/Dado de Baja (5 pts)

**Tareas:**
- [x] Agregar campo estado a tabla bienes
- [x] Crear enum de estados
- [x] Crear formulario de cambio de estado
- [x] Implementar validación de motivo
- [x] Registrar en historial
- [x] Implementar subida de documento de autorización
- [x] Filtrar bienes dados de baja en reportes activos
- [x] Testing de cambio de estado

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Gerente puede cambiar estado de bienes con justificación

---

## Definition of Done (DoD)

- [x] Código revisado y aprobado
- [x] Tests de auditoría pasando
- [x] Dashboard responsive
- [x] Optimización de gráficos
- [x] Documentación de sistema de auditoría
- [x] Validación de permisos por rol

---

# SPRINT 5: Funcionalidades Avanzadas

## Objetivo
Implementar notificaciones, importación/exportación y códigos QR

## Duración
30 Dic 2025 - 12 Ene 2026

## Story Points
47 puntos

## Estado
**⏳ PENDIENTE** - Funcionalidades por implementar

## Historias de Usuario

### HU-021: Notificaciones por Correo (8 pts)

**Tareas:**
- [ ] Configurar servidor SMTP en Laravel
- [ ] Crear templates de email con Blade
- [ ] Implementar envío al asignar bien
- [ ] Implementar envío al remover responsabilidad
- [ ] Implementar envío al registrar movimiento
- [ ] Agregar opción de desactivar notificaciones en perfil
- [ ] Testing de envío de correos

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Usuarios reciben notificaciones por correo

---

### HU-022: Importar Bienes desde Excel (13 pts)

**Tareas:**
- [ ] Instalar librería de lectura de Excel
- [ ] Crear template de Excel descargable
- [ ] Crear formulario de carga de archivo
- [ ] Implementar validación de formato
- [ ] Implementar validación fila por fila
- [ ] Crear reporte de errores detallado
- [ ] Implementar importación en lote
- [ ] Crear barra de progreso con AJAX
- [ ] Testing de importación masiva

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Gerente puede importar hasta 500 bienes desde Excel

---

### HU-023: Exportar Inventario a Excel (5 pts)

**Tareas:**
- [ ] Instalar librería de escritura de Excel
- [ ] Implementar exportación de bienes
- [ ] Incluir hoja resumen con totales
- [ ] Generar nombre de archivo con fecha
- [ ] Testing de exportación

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Usuario puede exportar inventario a Excel

---

### HU-024: Generar Código de Barra para Bienes (8 pts)

**Tareas:**
- [ ] Instalar librería de generación de códigos de barra
- [ ] Implementar generación de código único por bien
- [ ] Crear endpoint de descarga de código individual
- [ ] Implementar generación masiva de códigos
- [ ] Diseñar template de etiquetas imprimibles
- [ ] Testing de generación

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Sistema genera códigos de barra para cada bien

---

### HU-025: Escanear Código de Barra desde Móvil (8 pts)

**Tareas:**
- [x] Implementar botón de escanear en versión móvil
- [x] Integrar librería de escaneo de códigos
- [x] Implementar acceso a cámara
- [x] Implementar redirección a detalle del bien
- [x] Optimizar para PWA
- [x] Manejo de errores de escaneo
- [x] Testing en dispositivos móviles

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede escanear código desde móvil

---

### HU-026: Perfil de Usuario (5 pts)

**Tareas:**
- [x] Crear vista de perfil de usuario
- [x] Implementar edición de datos personales
- [x] Implementar cambio de contraseña
- [ ] Implementar subida de foto de perfil
- [x] Mostrar historial de última actividad
- [x] Testing de actualización de perfil

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Usuario puede gestionar su perfil

---

## Definition of Done (DoD)

- [ ] Código revisado y aprobado
- [ ] Tests de integración pasando
- [ ] Notificaciones de correo funcionando
- [ ] Importación masiva probada con 500 registros
- [ ] QR escaneables desde múltiples dispositivos
- [ ] Documentación de configuración SMTP

---

# SPRINT 6: Finalización y Mejoras de UX

## Objetivo
Completar funcionalidades secundarias y mejorar experiencia de usuario

## Duración
13 Ene 2026 - 26 Ene 2026

## Story Points
29 puntos

## Estado
**⏳ PENDIENTE** - Funcionalidades por implementar

## Historias de Usuario

### HU-027: Recuperar Contraseña (8 pts)

**Tareas:**
- [ ] Crear tabla de tokens de recuperación
- [ ] Implementar formulario de solicitud
- [ ] Implementar envío de email con token
- [ ] Crear formulario de reset de contraseña
- [ ] Implementar validación de token (1 hora)
- [ ] Implementar actualización de contraseña
- [ ] Testing de flujo completo

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Usuario puede recuperar contraseña olvidada

---

### HU-028: Reporte de Bienes por Responsable (5 pts)

**Tareas:**
- [ ] Crear endpoint de reporte por responsable
- [ ] Diseñar template PDF de reporte
- [ ] Calcular valor total de bienes
- [ ] Incluir espacio para firma
- [ ] Testing de generación

**Responsable:** Desarrollador Backend
**Criterio de Completitud:** Gerente puede generar reporte por responsable

---

### HU-029: Dashboard de Responsable (8 pts)

**Tareas:**
- [ ] Diseñar dashboard de responsable
- [ ] Implementar métricas de bienes asignados
- [ ] Implementar alertas de mantenimiento
- [ ] Mostrar historial reciente
- [ ] Implementar impresión de constancia
- [ ] Testing de cálculos

**Responsable:** Desarrollador Full Stack
**Criterio de Completitud:** Responsable ve dashboard de sus bienes

---

### HU-030: Filtros Avanzados en Listados (8 pts)

**Tareas:**
- [ ] Implementar filtros combinables
- [ ] Implementar contador de resultados
- [ ] Crear funcionalidad de guardar filtros favoritos
- [ ] Implementar botón de limpiar filtros
- [ ] Implementar URLs compartibles con filtros
- [ ] Optimizar consultas con filtros múltiples
- [ ] Testing de filtros combinados

**Responsable:** Desarrollador Frontend
**Criterio de Completitud:** Usuario puede aplicar filtros avanzados

---

## Tareas Finales del Sprint

- [ ] Revisión completa de UI/UX
- [ ] Optimización de rendimiento
- [ ] Corrección de bugs menores
- [ ] Actualización de documentación completa
- [ ] Preparación para despliegue

## Definition of Done (DoD)

- [ ] Código revisado y aprobado
- [ ] Tests de regresión pasando
- [ ] Documentación completa actualizada
- [ ] Manual de usuario creado
- [ ] Sistema listo para producción
- [ ] Plan de despliegue documentado

---

# MÉTRICAS DE SEGUIMIENTO

## Burndown Chart (Por Sprint)

- Actualización diaria de story points completados vs restantes
- Identificación temprana de desviaciones

## Velocity Chart

- Seguimiento de puntos completados por sprint
- Ajuste de capacidad para sprints futuros

## Cumulative Flow Diagram

- Visualización de trabajo en progreso
- Identificación de cuellos de botella

---

# CEREMONIAS SCRUM

| Ceremonia | Frecuencia | Duración |
|-----------|------------|----------|
| Daily Standup | Diario | 15 min |
| Sprint Planning | Inicio de sprint | 4 horas |
| Sprint Review | Fin de sprint | 2 horas |
| Sprint Retrospective | Fin de sprint | 1.5 horas |

---

# ROLES DEL EQUIPO

| Rol | Persona |
|-----|---------|
| Product Owner | Gerente de Administración |
| Scrum Master | Líder Técnico |
| Desarrollador Backend Senior | - |
| Desarrollador Frontend Senior | - |
| Desarrollador Full Stack | - |
| QA Engineer | - |

---

# DEFINICIÓN DE "HECHO" GLOBAL

Una historia se considera terminada cuando:

1. Código implementado según criterios de aceptación ✅
2. Tests unitarios y de integración pasando ✅
3. Code review completado y aprobado ✅
4. Documentación técnica actualizada ✅
5. Sin bugs críticos ni de alta prioridad ✅
6. Funcionalidad probada en ambiente de desarrollo ✅
7. Aprobación del Product Owner ✅
8. Código mergeado a rama principal ✅
