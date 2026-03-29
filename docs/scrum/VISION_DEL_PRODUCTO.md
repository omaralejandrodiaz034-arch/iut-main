# Visión del Producto

## Sistema de Gestión de Inventario de Bienes

| Campo | Valor |
|-------|-------|
| **Fecha** | 04 de Noviembre de 2025 |
| **Versión** | 1.0 |
| **Product Owner** | Gerente de Administración |

---

## 1. VISIÓN

**Ser la solución integral de gestión patrimonial para instituciones educativas venezolanas que garantice transparencia, trazabilidad y control total de los bienes públicos, facilitando auditorías y optimizando la administración de activos institucionales.**

---

## 2. DECLARACIÓN DEL PROBLEMA

### Problema

Las instituciones educativas venezolanas, especialmente los UPTOS (Universidades Politécnicas Territoriales), enfrentan serios desafíos en la gestión y control de sus activos patrimoniales:

| Problema | Descripción |
|----------|-------------|
| Falta de trazabilidad | No se conoce el paradero actual de muchos bienes |
| Pérdida de información | Registros en papel susceptibles a deterioro o extravío |
| Auditorías complejas | Procesos manuales que consumen semanas o meses |
| Responsabilidad difusa | No hay claridad sobre quién es responsable de cada bien |
| Movimientos sin registro | Traslados informales que no quedan documentados |
| Falta de evidencia | No hay respaldo fotográfico de los bienes |
| Reportes ineficientes | Generación manual de informes para auditorías |

### Impacto

- Riesgo de sanciones en auditorías gubernamentales
- Pérdida de bienes por falta de control
- Tiempo administrativo desperdiciado
- Imposibilidad de tomar decisiones informadas sobre compras
- Deterioro de la transparencia institucional

### Afectados

| Stakeholder | Rol | Necesidad |
|-------------|-----|-----------|
| Administradores | Responsables de mantener control patrimonial | Control total y visibilidad |
| Gerentes de Bienes | Encargados de supervisar inventarios | Herramientas eficientes de gestión |
| Responsables de Dependencias | Usuarios a cargo de bienes específicos | Claridad sobre responsabilidades |
| Auditores | Requieren información precisa y actualizada | Reportes detallados y verificables |
| Autoridades | Necesitan reportes oficiales confiables | Información para planificación |

---

## 3. DECLARACIÓN DE LA SOLUCIÓN

### Producto

**Sistema web de gestión de inventario de bienes patrimoniales**, desarrollado en Laravel (PHP 8.2) con base de datos relacional, que permite registrar, rastrear y generar reportes oficiales sobre todos los activos de una institución educativa.

### Para quién

- Instituciones educativas públicas venezolanas
- Específicamente UPTOS (Universidades Politécnicas Territoriales)
- Organismos adscritos al Ministerio del Poder Popular para la Educación Universitaria

### Que resuelve

| Funcionalidad | Descripción |
|---------------|-------------|
| Registro digital | Centralizado de todos los bienes institucionales |
| Trazabilidad | Completa de movimientos y cambios de responsabilidad |
| Evidencia fotográfica | De cada activo |
| Reportes oficiales | Automatizados listos para auditorías |
| Control de acceso | Por roles para diferentes niveles de responsabilidad |
| Historial | Inmutable de todas las operaciones |
| Jerarquía organizacional | Clara (Organismo > Unidad > Dependencia > Bien) |

### Diferenciadores

A diferencia de hojas de cálculo o sistemas genéricos:

- Diseñado específicamente para la realidad de instituciones educativas venezolanas
- Cumple con normativas de control patrimonial del Estado venezolano
- Interfaz intuitiva pensada para usuarios no técnicos
- Sin costos de licenciamiento (código abierto)
- Autohospedable en servidores institucionales

---

## 4. OBJETIVOS DEL PRODUCTO

### Objetivos de Negocio

| # | Objetivo | Métrica |
|---|----------|---------|
| 1 | Reducir 80% el tiempo de preparación para auditorías patrimoniales | Tiempo de preparación |
| 2 | Eliminar 100% de los registros en papel para el control de inventario | Registros digitales |
| 3 | Lograr trazabilidad completa de todos los bienes en 6 meses | Bienes con historial |
| 4 | Aprobar auditorías sin observaciones mayores en control patrimonial | Auditorías aprobadas |
| 5 | Optimizar asignación de presupuesto basado en datos de inventario real | Decisiones basadas en datos |

### Objetivos de Usuario

| # | Objetivo | Métrica |
|---|----------|---------|
| 1 | Registrar un bien nuevo en menos de 5 minutos | Tiempo de registro |
| 2 | Generar reporte oficial en menos de 2 minutos | Tiempo de generación |
| 3 | Localizar cualquier bien en menos de 30 segundos | Tiempo de búsqueda |
| 4 | Registrar movimientos de forma sencilla y rápida | UX del proceso |
| 5 | Consultar historial completo de cualquier activo | Accesibilidad |

### Objetivos Técnicos

| # | Objetivo | Métrica |
|---|----------|---------|
| 1 | Disponibilidad del sistema 99.5% del tiempo | Uptime |
| 2 | Tiempo de respuesta menor a 2 segundos en operaciones comunes | Latencia |
| 3 | Escalabilidad para manejar hasta 50,000 bienes | Capacidad |
| 4 | Seguridad con autenticación robusta y auditoría completa | Nivel de seguridad |
| 5 | Respaldos automáticos diarios de toda la información | Frecuencia de backup |

---

## 5. STAKEHOLDERS

### Stakeholders Primarios

| Stakeholder | Rol | Interés | Poder | Necesidades |
|-------------|-----|---------|-------|-------------|
| Product Owner | Gerente de Administración | Sistema que facilite control administrativo | Alto - Decisión final | Priorización de funcionalidades |
| Usuarios Administradores | Gestión completa del sistema | Control total y visibilidad | Alto | Dashboards, gestión de usuarios, acceso total |
| Gerentes de Bienes | Supervisión de inventarios | Herramientas eficientes | Medio | Registro rápido, reportes, movimientos |
| Usuarios Responsables | Cuidado de bienes asignados | Claridad sobre responsabilidades | Medio | Ver bienes, notificaciones, constancias |

### Stakeholders Secundarios

| Stakeholder | Rol | Interés | Necesidades |
|-------------|-----|---------|-------------|
| Auditores Gubernamentales | Verificación de control patrimonial | Información confiable | Reportes detallados, historial, evidencia |
| Autoridades Institucionales | Toma de decisiones estratégicas | Información para planificación | Reportes ejecutivos, estadísticas |
| Equipo de Desarrollo | Construcción y mantenimiento | Código mantenible | Arquitectura clara, documentación |

---

## 6. ALCANCE DEL PRODUCTO

### En Alcance (Versión 1.0)

#### Gestión de Estructura Organizacional
- ✅ CRUD de Organismos
- ✅ CRUD de Unidades Administradoras
- ✅ CRUD de Dependencias
- ✅ Visualización de jerarquía completa

#### Gestión de Usuarios y Seguridad
- ✅ Registro de usuarios con roles
- ✅ Autenticación y autorización
- ✅ Roles: Administrador, Gerente, Responsable
- ✅ Gestión de perfiles
- ❌ Recuperación de contraseña

#### Gestión de Inventario
- ✅ Registro de bienes con fotos (hasta 5)
- ✅ Edición de información de bienes
- ✅ Búsqueda avanzada de bienes
- ✅ Listados con filtros y paginación
- ✅ Estados de bienes (Activo, Inactivo, Mantenimiento, Dado de Baja, Extraviado)
- ❌ Importación masiva desde Excel

#### Gestión de Responsables
- ✅ Registro de responsables
- ✅ Tipos de responsables (Primario, Por Uso)
- ✅ Asignación de responsabilidad

#### Movimientos y Trazabilidad
- ✅ Registro de traslados entre dependencias
- ✅ Cambios de responsabilidad
- ✅ Historial completo de movimientos
- ✅ Documentos de autorización

#### Reportes y Auditoría
- ✅ Reporte de inventario por dependencia (PDF)
- ✅ Reporte por responsable (PDF)
- ❌ Exportación a Excel
- ✅ Auditoría automática de acciones
- ✅ Consulta de logs de auditoría

#### Funcionalidades Avanzadas
- ❌ Generación de códigos QR
- ❌ Escaneo de QR desde móvil
- ❌ Notificaciones por correo
- ✅ Dashboards por rol

### Fuera de Alcance (Versión 1.0)

| Funcionalidad | Razón |
|---------------|-------|
| Integración con sistema de compras | Versión futura |
| Recordatorios automáticos de mantenimiento | Versión futura |
| App móvil nativa | Versión futura |
| Firma digital electrónica | Versión futura |
| Geolocalización con IoT | Versión futura |
| API pública para integraciones | Versión futura |
| Módulo de garantías y seguros | Versión futura |
| Gestión de depreciación | Versión futura |
| Valorización automática | Versión futura |

---

## 7. SUPUESTOS Y RESTRICCIONES

### Supuestos

1. La institución cuenta con servidor web con PHP 8.2+
2. Usuarios tienen acceso a navegador web moderno
3. Existe conectividad a Internet para notificaciones
4. Se cuenta con capacitación básica para usuarios
5. Hay respaldo de autoridades institucionales

### Restricciones Técnicas

| Restricción | Detalle |
|-------------|---------|
| Tecnología | Laravel 12, PHP 8.2+, MySQL/SQLite |
| Hosting | Servidor institucional (no cloud público) |
| Presupuesto | $0 en licencias de software |
| Compatibilidad | Navegadores modernos (últimas 2 versiones) |
| Tiempo | 8 semanas de desarrollo (4 sprints) |

### Restricciones de Negocio

1. Cumplimiento de normativas venezolanas de control patrimonial
2. Datos sensibles no pueden salir de servidores institucionales
3. Reportes deben seguir formatos oficiales establecidos
4. Sistema debe funcionar en infraestructura limitada

### Dependencias

1. Aprobación de autoridades institucionales
2. Disponibilidad del equipo de desarrollo
3. Acceso a información de bienes existentes
4. Servidor de correo institucional configurado
5. Capacitación de usuarios finales

---

## 8. CRITERIOS DE ÉXITO

### Métricas de Adopción

| Métrica | Meta |
|---------|------|
| Usuarios activos semanalmente | 80% en el primer mes |
| Bienes críticos registrados | 100% en 3 meses |
| Satisfacción de usuarios | 90% (encuesta) |

### Métricas de Eficiencia

| Métrica | Meta |
|---------|------|
| Reducción de tiempo de generación de reportes | 75% |
| Reducción de tiempo de preparación para auditorías | 80% |
| Errores críticos en producción | 0 |

### Métricas de Calidad

| Métrica | Meta |
|---------|------|
| Movimientos con trazabilidad | 100% |
| Bienes con evidencia fotográfica | 95% |
| Observaciones mayores en auditorías | 0 |

### Métricas Técnicas

| Métrica | Meta |
|---------|------|
| Disponibilidad | >99% |
| Tiempo de respuesta | <2 segundos |
| Brechas de seguridad críticas | 0 |

---

## 9. ROADMAP DE ALTO NIVEL

### Fase 1: MVP - Fundamentos (Sprint 1)

**Duración:** 2 semanas

- Autenticación y roles
- Estructura organizacional
- Gestión básica de bienes

### Fase 2: Trazabilidad (Sprint 2)

**Duración:** 2 semanas

- Movimientos de bienes
- Cambios de responsabilidad
- Reportes oficiales
- Sistema de auditoría

### Fase 3: Optimización (Sprint 3)

**Duración:** 2 semanas

- Códigos QR
- Importación/Exportación masiva
- Notificaciones
- Mejoras de UX

### Fase 4: Consolidación (Sprint 4)

**Duración:** 2 semanas

- Despliegue en producción
- Capacitación de usuarios
- Migración de datos históricos
- Estabilización

---

## 10. RIESGOS Y MITIGACIONES

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|----------|------------|
| Resistencia al cambio de usuarios | Alta | Alto | Capacitación continua, soporte dedicado |
| Datos históricos inconsistentes | Media | Alto | Proceso de validación y limpieza antes de migración |
| Falta de conectividad estable | Media | Medio | Diseño offline-first para funciones críticas |
| Cambio de autoridades institucionales | Baja | Alto | Documentación completa, código abierto |
| Problemas de rendimiento con muchos datos | Media | Medio | Optimización de consultas, índices adecuados |
| Pérdida de datos por fallas de hardware | Baja | Crítico | Respaldos automáticos diarios, redundancia |

---

## 11. EQUIPO SCRUM

| Rol | Persona |
|-----|---------|
| Product Owner | Gerente de Administración |
| Scrum Master | Líder Técnico |
| Desarrollador Backend Senior | Por definir |
| Desarrollador Frontend Senior | Por definir |
| Desarrollador Full Stack | Por definir |
| QA Engineer | Por definir |

---

## 12. DEFINICIONES CLAVE

| Término | Definición |
|----------|------------|
| Bien | Activo institucional registrado en el sistema |
| Organismo | Entidad de más alto nivel en la jerarquía |
| Unidad Administradora | Área administrativa dentro de un organismo |
| Dependencia | Unidad dentro de una unidad administrativa |
| Responsable | Persona a cargo de uno o más bienes |
| Movimiento | Traslado o cambio de responsabilidad de un bien |
| Trazabilidad | Historial completo de movimientos de un bien |
