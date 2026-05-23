# DEFINICIONES Y CRITERIOS DE ACEPTACIÓN
## Sistema de Gestión de Inventario de Bienes

---

## Definiciones PDO

### Definición de Listo (DoR - Definition of Ready)
Un requisito/historia está listo para desarrollo cuando:
- [ ] El requisito está claramente definido con criterios de aceptación
- [ ] Los casos de uso están identificados
- [ ] Se han identificado dependencias técnicas
- [ ] Se ha estimado el esfuerzo (puntos/horas)
- [ ] El Product Owner ha aprobado los requisitos

### Definición de Hecho (DoD - Definition of Done)
Un requisito se considera completado cuando:
- [ ] Código implementado según especificaciones técnicas
- [ ] Tests unitarios implementados y pasando (>80% cobertura)
- [ ] Code review completado y aprobado
- [ ] Documentación técnica actualizada
- [ ] Sin bugs críticos ni de alta prioridad
- [ ] Funcionalidad probada en ambiente de desarrollo
- [ ] Aprobación del Product Owner

---

## Criterios de Aceptación por Épica

### E1: Gestión de Estructura Organizacional ✅
- CRUD de Organismos con validación de datos únicos
- CRUD de Unidades Administradoras vinculadas a organismos
- CRUD de Dependencias vinculadas a unidades
- Visualización jerárquica completa
- Tests de integridad referencial

### E2: Gestión de Usuarios y Accesos ⚠️
- **Completado:** Login/Logout, gestión de usuarios, perfiles, roles
- **Pendiente:** Recuperación de contraseña

### E3: Gestión de Inventario de Bienes ⚠️
- **Completado:** CRUD bienes con fotos, búsqueda, filtros, estados
- **Pendiente:** Importación masiva desde Excel (HU-022)

### E4: Movimientos y Trazabilidad ✅
- Registro de traslados con documentos adjuntos
- Cambio de responsabilidad con notificación
- Historial completo con filtros de búsqueda
- Auditoría de movimientos

### E5: Reportes y Auditoría ⚠️
- **Completado:** Reportes PDF, sistema de auditoría
- **Pendiente:** Exportación a Excel, reporte por responsable

---

## Estándares de Calidad

### Estándar de Codificación
- PSR-12 para PHP
- Convenciones Blade y Tailwind
- Documentación PHPDoc en modelos y controladores

### Estándar de Pruebas
- PHPUnit para tests unitarios
- Cobertura mínima: 70%
- Tests de integración para flujos críticos

### Estándar de Seguridad
- Validación de entrada en todos los formularios
- Sanitización de datos
- Control de acceso basado en roles
- Auditoría de acciones sensibles

---

## Matriz de Trazabilidad Requisitos-Tests

| Requisito | Test Unitario | Test Integración | Test Aceptación |
|-----------|---------------|------------------|-----------------|
| RQ-001 | TC-AUTH-01 | TC-FLOW-01 | TC-USER-ACCEPT |
| RQ-006 | TC-BIEN-01 | TC-CRUD-01 | TC-INVENTORY-ACCEPT |
| RQ-010 | TC-MOV-01 | TC-FLOW-02 | TC-MOVEMENT-ACCEPT |

---