# PRODUCT BACKLOG
## Sistema de Gestión de Inventario de Bienes

**Proyecto:** Sistema de Gestión de Inventario de Bienes para Instituciones Educativas  
**Product Owner:** Gerente de Administración  
**Fecha de Creación:** 04/11/2025  
**Última Actualización:** 12/05/2026  
**Estado:** 62.4% completado (123/197 pts)

---

## ÉPICAS DEL PROYECTO

### E1: Gestión de Estructura Organizacional
Gestión jerárquica de organismos, unidades administrativas y dependencias.  
**Estado:** ✅ Completado

### E2: Gestión de Usuarios y Accesos
Sistema de autenticación, roles y permisos de usuarios.  
**Estado:** ⚠️ 85% completado

### E3: Gestión de Inventario de Bienes
Registro, actualización, búsqueda y seguimiento de bienes patrimoniales.  
**Estado:** ⚠️ 80% completado

### E4: Gestión de Movimientos y Trazabilidad
Control de traslados, asignaciones y cambios de responsabilidad de bienes.  
**Estado:** ✅ Completado

### E5: Reportes y Auditoría
Generación de reportes, consultas y auditoría del sistema.  
**Estado:** ⚠️ 75% completado

### E6: Optimización y Experiencia de Usuario
Mejoras de rendimiento, usabilidad y funcionalidades avanzadas.  
**Estado:** ❌ 10% completado

---

## HISTORIAS DE USUARIO POR ESTADO

### ✅ COMPLETADAS (123 pts)

#### HU-001: Registro de Usuarios Administradores (5 pts)
**Épica:** E2 | **Sprint:** 1 | **Estado:** ✅ Completado

*Como administrador del sistema quiero registrar usuarios con diferentes roles para controlar el acceso al sistema según responsabilidades*

**Criterios de Aceptación:** ✅ Todos cumplidos
- El sistema permite crear usuarios con campos: cédula, nombre, apellido, correo, contraseña, rol
- Los roles disponibles son: Administrador, Gerente de Bienes, Usuario Responsable
- La cédula debe ser única en el sistema
- El correo debe tener formato válido
- La contraseña debe tener al menos 8 caracteres

---

#### HU-002: Iniciar Sesión en el Sistema (5 pts)
**Épica:** E2 | **Sprint:** 1 | **Estado:** ✅ Completado

---

#### HU-003: Cerrar Sesión (3 pts)
**Épica:** E2 | **Sprint:** 1 | **Estado:** ✅ Completado

---

#### HU-004: Crear Organismo (5 pts)
**Épica:** E1 | **Sprint:** 1 | **Estado:** ✅ Completado

---

#### HU-005: Crear Unidad Administradora (5 pts)
**Épica:** E1 | **Sprint:** 1 | **Estado:** ✅ Completado

---

#### HU-006: Crear Dependencia (5 pts)
**Épica:** E1 | **Sprint:** 1 | **Estado:** ✅ Completado

---

#### HU-007: Registrar Bien en Inventario (8 pts)
**Épica:** E3 | **Sprint:** 2 | **Estado:** ✅ Completado

---

#### HU-008: Listar Bienes por Dependencia (5 pts)
**Épica:** E3 | **Sprint:** 2 | **Estado:** ✅ Completado

---

#### HU-009: Ver Detalle de un Bien (5 pts)
**Épica:** E3 | **Sprint:** 2 | **Estado:** ✅ Completado

---

#### HU-010: Editar Información de un Bien (5 pts)
**Épica:** E3 | **Sprint:** 2 | **Estado:** ✅ Completado

---

#### HU-011: Registrar Movimiento de Bien (8 pts)
**Épica:** E4 | **Sprint:** 3 | **Estado:** ✅ Completado

---

#### HU-012: Cambiar Responsable de un Bien (5 pts)
**Épica:** E4 | **Sprint:** 3 | **Estado:** ✅ Completado

---

#### HU-013: Ver Historial de Movimientos de un Bien (5 pts)
**Épica:** E4 | **Sprint:** 3 | **Estado:** ✅ Completado

---

#### HU-014: Generar Reporte de Inventario por Dependencia (8 pts)
**Épica:** E5 | **Sprint:** 3 | **Estado:** ✅ Completado

---

#### HU-015: Buscar Bienes Globalmente (8 pts)
**Épica:** E3 | **Sprint:** 3 | **Estado:** ✅ Completado

---

#### HU-016: Dashboard de Administrador (8 pts)
**Épica:** E5 | **Sprint:** 4 | **Estado:** ✅ Completado

---

#### HU-017: Gestionar Tipos de Responsables (5 pts)
**Épica:** E2 | **Sprint:** 4 | **Estado:** ✅ Completado

---

#### HU-018: Registrar Responsable (5 pts)
**Épica:** E1 | **Sprint:** 4 | **Estado:** ✅ Completado

---

#### HU-019: Registro de Auditoría del Sistema (8 pts)
**Épica:** E5 | **Sprint:** 4 | **Estado:** ✅ Completado

---

#### HU-020: Marcar Bien como Inactivo/Dado de Baja (5 pts)
**Épica:** E3 | **Sprint:** 4 | **Estado:** ✅ Completado

---

### ⏳ PENDIENTES (74 pts)

#### HU-021: Notificaciones por Correo (8 pts)
**Épica:** E6 | **Sprint:** 5 | **Prioridad:** Media | **Estado:** ⏳ Pendiente

*Como usuario responsable quiero recibir notificaciones por correo de cambios en mis bienes para estar informado de movimientos o reasignaciones*

**Criterios de Aceptación:**
- [ ] Envío de correo al asignar un bien nuevo
- [ ] Envío de correo al remover responsabilidad
- [ ] Envío de correo al registrar movimiento de bien bajo su cargo
- [ ] Template HTML profesional
- [ ] Opción de desactivar notificaciones en perfil de usuario

---

#### HU-022: Importar Bienes desde Excel (13 pts)
**Épica:** E3 | **Sprint:** 5 | **Prioridad:** Alta | **Estado:** ⏳ Pendiente

---

#### HU-023: Exportar Inventario a Excel (5 pts)
**Épica:** E5 | **Sprint:** 5 | **Prioridad:** Alta | **Estado:** ⏳ Pendiente

---

#### HU-024: Generar Código de Barra para Bienes (8 pts)
**Épica:** E6 | **Sprint:** 5 | **Prioridad:** Alta | **Estado:** ⏳ Pendiente

---

#### HU-025: Escanear Código de Barra desde Móvil (8 pts)
**Épica:** E6 | **Sprint:** 5 | **Prioridad:** Media | **Estado:** ⏳ Pendiente

---

#### HU-026: Perfil de Usuario (5 pts)
**Épica:** E2 | **Sprint:** 5 | **Prioridad:** Baja | **Estado:** ⚠️ 60% completado

*Como usuario del sistema quiero ver y editar mi información de perfil para mantener mis datos actualizados*

**Completado:** Vista, edición de datos, cambio de contraseña
**Pendiente:** Foto de perfil, historial de actividad

---

#### HU-027: Recuperar Contraseña (8 pts)
**Épica:** E2 | **Sprint:** 6 | **Prioridad:** Media | **Estado:** ⏳ Pendiente

---

#### HU-028: Reporte de Bienes por Responsable (5 pts)
**Épica:** E5 | **Sprint:** 6 | **Prioridad:** Media | **Estado:** ⏳ Pendiente

---

#### HU-029: Dashboard de Responsable (8 pts)
**Épica:** E2 | **Sprint:** 6 | **Prioridad:** Media | **Estado:** ⏳ Pendiente

---

#### HU-030: Filtros Avanzados en Listados (8 pts)
**Épica:** E6 | **Sprint:** 6 | **Prioridad:** Baja | **Estado:** ⏳ Pendiente

---

### BLOQUEADAS (0 pts)

Ninguna historia bloqueada actualmente.

---

## RESUMEN DE ESTIMACIÓN

| Sprint | Planeado | Completado | Pendiente | % Avance |
|--------|----------|------------|-----------|----------|
| Sprint 1 | 28 pts | 28 pts | 0 pts | 100% |
| Sprint 2 | 28 pts | 28 pts | 0 pts | 100% |
| Sprint 3 | 34 pts | 22 pts | 12 pts | 65% |
| Sprint 4 | 31 pts | 31 pts | 0 pts | 100% |
| Sprint 5 | 47 pts | 0 pts | 47 pts | 0% |
| Sprint 6 | 29 pts | 0 pts | 29 pts | 0% |
| **TOTAL** | **197 pts** | **123 pts** | **74 pts** | **62.4%** |

---

## DEFINICIÓN DE PRIORIDADES

- **Crítica:** Funcionalidad esencial sin la cual el sistema no puede operar
- **Alta:** Funcionalidad muy importante para el valor del producto
- **Media:** Funcionalidad que agrega valor significativo pero no es crítica
- **Baja:** Mejoras futuras, características avanzadas o nice-to-have