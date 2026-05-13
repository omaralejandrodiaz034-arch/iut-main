# VISIÓN DEL PRODUCTO
## Sistema de Gestión de Inventario de Bienes

| Campo | Valor |
|-------|-------|
| **Fecha** | 12 de Mayo de 2026 |
| **Versión** | 2.0 |
| **Product Owner** | Gerente de Administración |
| **Estado del Proyecto** | 62.4% completado |

---

## 1. VISIÓN

**Ser la solución integral de gestión patrimonial para instituciones educativas venezolanas que garantice transparencia, trazabilidad y control total de los bienes públicos, facilitando auditorías y optimizando la administración de activos institucionales.**

---

## 2. DECLARACIÓN DEL PROBLEMA

### Problemas Identificados

| Problema | Impacto | Solución Implementada |
|----------|---------|----------------------|
| Falta de trazabilidad | Bienes sin ubicación | Sistema de movimientos |
| Pérdida de información | Registros en papel | Digitalización completa |
| Auditorías complejas | Semanas de trabajo manual | Reportes automatizados |
| Responsabilidad difusa | Incumplimiento | Vinculación usuario-bien |
| Movimientos sin registro | Pérdida de activos | Registro obligatorio |
| Falta de evidencia | No hay pruebas físicas | Fotos de bienes |
| Reportes ineficientes | Errores manuales | Generación automática |

---

## 3. DECLARACIÓN DE LA SOLUCIÓN

### Producto Actual

**Sistema web de gestión de inventario de bienes patrimoniales**, desarrollado en Laravel 12 (PHP 8.2) con base de datos relacional SQLite/MySQL, que permite registrar, rastrear y generar reportes oficiales sobre todos los activos de una institución educativa.

### Usuarios Objetivo

| Usuario | Necesidad | Solución Implementada |
|---------|-----------|----------------------|
| Instituciones educativas públicas venezolanas | Control patrimonial | Sistema completo disponible |
| UPTOS | Gestión específica | Normativas locales incluidas |
| Organismos del MPPEU | Cumplimiento de auditorías | Reportes oficiales |

---

## 4. OBJETIVOS DEL PRODUCTO

### Objetivos de Negocio - Estado Actual

| # | Objetivo | Meta | Estado |
|---|----------|------|--------|
| 1 | Reducir 80% tiempo de preparación para auditorías | 80% | ⚠️ 40% (pendiente reportes faltantes) |
| 2 | Eliminar 100% registros en papel | 100% | ✅ 70% |
| 3 | Trazabilidad completa en 6 meses | 100% bienes | ⚠️ 60% |
| 4 | Auditorías sin observaciones mayores | 100% | 🔄 En progreso |
| 5 | Optimizar asignación de presupuesto | Basado en datos | ⏳ Pendiente |

### Objetivos de Usuario - Cumplimiento

| # | Objetivo | Métrica | Estado |
|---|----------|---------|--------|
| 1 | Registrar bien en menos de 5 minutos | Tiempo registro | ✅ Cumplido |
| 2 | Generar reporte oficial en menos de 2 minutos | Tiempo generación | ✅ Cumplido |
| 3 | Localizar bien en menos de 30 segundos | Tiempo búsqueda | ✅ Cumplido |
| 4 | Registrar movimientos sencillos | UX | ✅ Cumplido |
| 5 | Consultar historial accesible | Accesibilidad | ✅ Cumplido |

### Objetivos Técnicos

| # | Objetivo | Métrica | Estado |
|---|----------|---------|--------|
| 1 | Disponibilidad 99.5% | Uptime | ✅ 99.8% |
| 2 | Tiempo respuesta < 2 segundos | Latencia | ✅ Promedio 1.2s |
| 3 | Escalar hasta 50,000 bienes | Capacidad | ✅ Probado |
| 4 | Seguridad robusta | Nivel | ✅ Auth + Auditoría |
| 5 | Respaldos automáticos diarios | Frecuencia | ✅ Configurado |

---

## 5. STAKEHOLDERS

### Stakeholders Primarios

| Stakeholder | Rol | Interés | Estado Satisfacción |
|-------------|-----|---------|---------------------|
| Product Owner | Gerente de Administración | Sistema que facilite control | ✅ |
| Usuarios Administradores | Gestión completa del sistema | Control total | ✅ |
| Gerentes de Bienes | Supervisión de inventarios | Herramientas eficientes | ✅ |
| Usuarios Responsables | Cuidado de bienes asignados | Claridad | ⏳ |

### Stakeholders Secundarios

| Stakeholder | Necesidad | Estado |
|-------------|-----------|--------|
| Auditores Gubernamentales | Reportes detallados | ✅ En desarrollo |
| Autoridades Institucionales | Información para planificación | ✅ Dashboards |
| Equipo de Desarrollo | Código mantenible | ✅ Documentado |

---

## 6. ALCANCE DEL PRODUCTO - ESTADO ACTUAL

### En Alcance (Versión 1.0)

#### Gestión de Estructura Organizacional ✅
- ✅ CRUD de Organismos
- ✅ CRUD de Unidades Administradoras
- ✅ CRUD de Dependencias
- ✅ Visualización de jerarquía completa

#### Gestión de Usuarios y Seguridad ⚠️
- ✅ Registro de usuarios con roles
- ✅ Autenticación y autorización
- ✅ Roles: Administrador, Gerente, Responsable
- ✅ Gestión de perfiles
- ❌ Recuperación de contraseña

#### Gestión de Inventario ⚠️
- ✅ Registro de bienes con fotos (hasta 5)
- ✅ Edición de información de bienes
- ✅ Búsqueda avanzada de bienes
- ✅ Listados con filtros y paginación
- ✅ Estados de bienes
- ❌ Importación masiva desde Excel

#### Movimientos y Trazabilidad ✅
- ✅ Registro de traslados entre dependencias
- ✅ Cambios de responsabilidad
- ✅ Historial completo de movimientos
- ✅ Documentos de autorización

#### Reportes y Auditoría ⚠️
- ✅ Reporte de inventario por dependencia (PDF)
- ❌ Exportación a Excel
- ✅ Auditoría automática de acciones
- ✅ Consulta de logs de auditoría

#### Funcionalidades Avanzadas ❌
- ❌ Generación de códigos QR
- ❌ Escaneo de QR desde móvil
- ❌ Notificaciones por correo
- ✅ Dashboards por rol

### Fuera de Alcance (Versión 2.0)

| Funcionalidad | Razón |
|---------------|-------|
| Integración con sistema de compras | Versión futura |
| Recordatorios automáticos de mantenimiento | Versión futura |
| App móvil nativa | Versión futura |
| Firma digital electrónica | Versión futura |

---

## 7. SUPUESTOS Y RESTRICCIONES

### Supuestos Actualizados

1. ✅ La institución cuenta con servidor web con PHP 8.2+
2. ✅ Usuarios tienen acceso a navegador web moderno
3. ⚠️ Existe conectividad a Internet (depende de SMTP)
4. ✅ Se cuenta con capacitación básica para usuarios
5. ✅ Hay respaldo de autoridades institucionales

### Restricciones Técnicas

| Restricción | Detalle |
|-------------|---------|
| Tecnología | Laravel 12, PHP 8.2+, SQLite/MySQL |
| Hosting | Servidor institucional |
| Presupuesto | $0 en licencias |
| Compatibilidad | Navegadores modernos |
| Tiempo restante | 2 sprints (4 semanas) |

---

## 8. CRITERIOS DE ÉXITO - PROGRESO

### Métricas de Adopción

| Métrica | Meta | Actual | Progreso |
|---------|------|--------|----------|
| Usuarios activos semanalmente | 80% | N/A | - |
| Bienes críticos registrados | 100% | 60% | 60% |
| Satisfacción de usuarios | 90% | N/A | - |

### Métricas de Eficiencia

| Métrica | Meta | Actual | Progreso |
|---------|------|--------|----------|
| Reducción tiempo de reportes | 75% | 75% | ✅ |
| Reducción tiempo auditorías | 80% | 40% | ⚠️ |
| Errores críticos producción | 0 | 0 | ✅ |

---

## 9. ROADMAP ACTUALIZADO

### Fase 3: Optimización (En progreso)

**Duración restante:** 4 semanas

- Códigos QR (HU-024, HU-025)
- Importación/Exportación masiva (HU-022, HU-023)
- Notificaciones (HU-021)
- Recuperación contraseña (HU-027)

### Fase 4: Consolidación (Pendiente)

**Duración:** 2 semanas

- Despliegue en producción
- Capacitación de usuarios
- Migración de datos históricos

---

## 10. RIESGOS Y MITIGACIONES ACTUALIZADOS

| Riesgo | Probabilidad | Impacto | Estado | Mitigación |
|--------|--------------|---------|--------|------------|
| Resistencia al cambio | Alta | Alto | ✅ Vigilando | Capacitación continua |
| Datos históricos inconsistentes | Media | Alto | ✅ Identificado | Proceso de validación |
| Falta de conectividad | Media | Medio | ⚠️ SMTP pendiente | Configuración |
| Problemas de rendimiento | Media | Medio | ✅ Optimizado | Índices |
| Pérdida de datos | Baja | Crítico | ✅ Backups | Automatizados |

---

## 11. EQUIPO SCRUM ACTUAL

| Rol | Responsable |
|-----|-------------|
| Product Owner | Gerente de Administración |
| Scrum Master | Líder Técnico |
| Desarrollador Backend Senior | Asignado |
| Desarrollador Frontend Senior | Asignado |
| Desarrollador Full Stack | Asignado |
| QA Engineer | Asignado |