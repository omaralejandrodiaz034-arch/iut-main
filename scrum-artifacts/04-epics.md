# ÉPICAS - Sistema de Gestión de Inventario de Bienes

## Mapa de Dependencias

```mermaid
graph TD
    E1[E1: Estructura Organizacional<br/>✅ Completado] --> E3
    E2[E2: Usuarios y Accesos<br/>⚠️ 85% completado] --> E3
    E2 --> E4
    E2 --> E5
    E2 --> E6
    E3[E3: Gestión de Inventario<br/>⚠️ 80% completado] --> E4
    E3 --> E5
    E3 --> E6
    E4[E4: Movimientos y Trazabilidad<br/>✅ Completado] --> E5
    E4 --> E6
    E5[E5: Reportes y Auditoría<br/>⚠️ 75% completado]
    E6[E6: Optimización y UX<br/>❌ 10% completado]

    style E1 fill:#c8e6c9
    style E2 fill:#fff9c4
    style E3 fill:#fff9c4
    style E4 fill:#c8e6c9
    style E5 fill:#fff9c4
    style E6 fill:#ffcdd2
```

---

## Catálogo de Épicas

### E1: Gestión de Estructura Organizacional ✅

**Descripción:** Gestión jerárquica de organismos, unidades administrativas y dependencias.

**Usuario Objetivo:** Administrador del sistema

**Alcance:**
- CRUD de Organismos
- CRUD de Unidades Administradoras
- CRUD de Dependencias
- Visualización de jerarquía completa

**Estado:** ✅ Completado (28 pts)

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-004 | Crear Organismo | 5 | ✅ |
| HU-005 | Crear Unidad Administradora | 5 | ✅ |
| HU-006 | Crear Dependencia | 5 | ✅ |
| HU-018 | Registrar Responsable | 5 | ✅ |

---

### E2: Gestión de Usuarios y Accesos ⚠️

**Descripción:** Sistema de autenticación, roles y permisos de usuarios.

**Usuario Objetivo:** Administrador, Gerente de Bienes, Usuario Responsable

**Alcance:**
- Registro de usuarios
- Autenticación y autorización
- Roles: Administrador, Gerente, Responsable
- Gestión de perfiles
- Recuperación de contraseña

**Estado:** ⚠️ 85% completado (23/27 pts)

**Completado:**
- ✅ Login/Logout
- ✅ Gestión de usuarios
- ✅ Perfil de usuario
- ✅ Tipos de responsables

**Pendiente:**
- ❌ Recuperación de contraseña

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-001 | Registro de Usuarios | 5 | ✅ |
| HU-002 | Iniciar Sesión | 5 | ✅ |
| HU-003 | Cerrar Sesión | 3 | ✅ |
| HU-017 | Gestionar Tipos de Responsables | 5 | ✅ |
| HU-026 | Perfil de Usuario | 5 | ⚠️ |
| HU-027 | Recuperar Contraseña | 8 | ⏳ |
| HU-029 | Dashboard de Responsable | 8 | ⏳ |

---

### E3: Gestión de Inventario de Bienes ⚠️

**Descripción:** Registro, actualización, búsqueda y seguimiento de bienes patrimoniales.

**Usuario Objetivo:** Gerente de Bienes

**Alcance:**
- Registro de bienes con fotos (hasta 5)
- Edición de información
- Búsqueda avanzada
- Listados con filtros
- Estados de bienes

**Estado:** ⚠️ 80% completado (52/65 pts)

**Completado:**
- ✅ CRUD completo de bienes
- ✅ Fotos y galería
- ✅ Búsqueda y filtros
- ✅ Estados de bienes

**Pendiente:**
- ❌ Importación desde Excel

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-007 | Registrar Bien en Inventario | 8 | ✅ |
| HU-008 | Listar Bienes por Dependencia | 5 | ✅ |
| HU-009 | Ver Detalle de un Bien | 5 | ✅ |
| HU-010 | Editar Información de un Bien | 5 | ✅ |
| HU-020 | Marcar Bien como Inactivo | 5 | ✅ |
| HU-015 | Buscar Bienes Globalmente | 8 | ✅ |
| HU-022 | Importar Bienes desde Excel | 13 | ⏳ |

---

### E4: Gestión de Movimientos y Trazabilidad ✅

**Descripción:** Control de traslados, asignaciones y cambios de responsabilidad de bienes.

**Usuario Objetivo:** Gerente de Bienes

**Alcance:**
- Registro de traslados
- Cambios de responsabilidad
- Historial completo
- Documentos de autorización

**Estado:** ✅ Completado (18 pts)

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-011 | Registrar Movimiento de Bien | 8 | ✅ |
| HU-012 | Cambiar Responsable de un Bien | 5 | ✅ |
| HU-013 | Ver Historial de Movimientos | 5 | ✅ |

---

### E5: Reportes y Auditoría ⚠️

**Descripción:** Generación de reportes, consultas y auditoría del sistema.

**Usuario Objetivo:** Administrador, Gerente de Bienes, Auditores

**Alcance:**
- Reporte de inventario por dependencia
- Reporte por responsable
- Exportación a Excel
- Auditoría automática

**Estado:** ⚠️ 75% completado (23/31 pts)

**Completado:**
- ✅ Reportes PDF
- ✅ Sistema de auditoría

**Pendiente:**
- ❌ Exportación a Excel
- ❌ Reporte por responsable

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-014 | Generar Reporte de Inventario | 8 | ✅ |
| HU-016 | Dashboard de Administrador | 8 | ✅ |
| HU-019 | Registro de Auditoría | 8 | ✅ |
| HU-023 | Exportar Inventario a Excel | 5 | ⏳ |
| HU-028 | Reporte de Bienes por Responsable | 5 | ⏳ |

---

### E6: Optimización y Experiencia de Usuario ❌

**Descripción:** Mejoras de rendimiento, usabilidad y funcionalidades avanzadas.

**Usuario Objetivo:** Todos los usuarios

**Alcance:**
- Códigos QR
- Escaneo desde móvil
- Notificaciones por correo
- Filtros avanzados
- Mejoras de UX

**Estado:** ❌ 25% completado (19/76 pts)

**Completado:**
- ✅ Escaneo QR desde móvil (HU-025)

**Pendiente:**
- ❌ Generación de QR
- ❌ Notificaciones
- ❌ Exportación/Importación
- ❌ Filtros avanzados

**Historias Asociadas:**
| ID | Historia | Puntos | Estado |
|----|----------|--------|--------|
| HU-015 | Buscar Bienes Globalmente | 8 | ✅ |
| HU-021 | Notificaciones por Correo | 8 | ⏳ |
| HU-024 | Generar Código QR | 8 | ⏳ |
| HU-025 | Escanear Código QR | 8 | ✅ |
| HU-022 | Importar Bienes desde Excel | 13 | ⏳ |
| HU-023 | Exportar Inventario a Excel | 5 | ⏳ |
| HU-030 | Filtros Avanzados | 8 | ⏳ |

---

## Matriz de Dependencias

| Historia | Depende de | Tipo |
|----------|------------|------|
| HU-022 Import Excel | HU-007 (Registrar Bien) | Requisito |
| HU-023 Export Excel | HU-008 (Listar Bienes) | Requisito |
| HU-024 Generar QR | HU-009 (Ver Detalle) | Requisito |
| HU-025 Escanear QR | HU-024 (Generar QR) | Secuencial |
| HU-027 Recuperar Pass | HU-002 (Login) | Requisito |
| HU-028 Reporte Responsable | HU-012 (Cambiar Responsable) | Requisito |
| HU-029 Dashboard Responsable | HU-018 (Registrar Responsable) | Requisito |
| HU-030 Filtros Avanzados | HU-008 (Listar Bienes) | Mejora |

---

## Priorización de Épicas

| Épica | Prioridad | Estado | Puntos Pendientes |
|-------|-----------|--------|-------------------|
| E3 Gestión de Inventario | Crítica | ⚠️ Parcial | 13 |
| E6 Optimización y UX | Alta | ❌ Pendiente | 57 |
| E2 Usuarios y Accesos | Crítica | ⚠️ Parcial | 11 |
| E5 Reportes y Auditoría | Alta | ⚠️ Parcial | 10 |
| E1 Estructura Organizacional | Crítica | ✅ Completado | 0 |
| E4 Movimientos | Alta | ✅ Completado | 0 |

---

## Recomendaciones de Ejecución

1. **Sprint 5** debe enfocarse en E6 (QR, Import/Export) y E2 (Recuperar Password)
2. **Sprint 6** completará E5 (reportes) y E2 (dashboard responsable)
3. **Dependencias críticas:** HU-024 antes que HU-025 para generación completa de QR
4. **Riesgo:** Configuración SMTP para notificaciones pendiente