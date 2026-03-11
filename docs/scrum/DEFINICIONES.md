# Definition of Ready (DoR) y Definition of Done (DoD)

## Sistema de Gestión de Inventario de Bienes

---

## Definition of Ready (DoR)

Una historia de usuario está lista para ser incluída en un sprint cuando cumple los siguientes criterios:

### Criterios Obligatorios

| # | Criterio | Descripción |
|---|----------|-------------|
| 1 | **Título claro** | La historia tiene un nombre descriptivo que indica su funcionalidad |
| 2 | **Descripción completa** | Formato "Como [rol], quiero [funcionalidad], para [beneficio]" |
| 3 | **Épica identificada** | La historia está asignada a una épica del proyecto |
| 4 | **Criterios de aceptación** | Mínimo 3 criterios medibles y verificables |
| 5 | **Estimación asignada** | Puntos de historia asignados usando escala de Fibonacci |
| 6 | **Dependencias identificadas** | Cualquier dependencia externa o de otras historias está documentada |
| 7 | ** wireframes o mockups** | Para historias con componente UI significativo |
| 8 | **Datos de prueba definidos** | Escenarios de prueba identificados |

### Criterios Opcionales

| # | Criterio | Descripción |
|---|----------|-------------|
| 1 | **Prototipo funcional** | Demo preliminar para validar UX |
| 2 | **Documentación técnica** | Notas sobre implementación si es compleja |
| 3 | **Casos de prueba** | Escenarios de testing identificados |

---

## Definition of Done (DoD)

Una historia de usuario se considera completamente terminada cuando cumple todos estos criterios:

### Criterios Técnicos

| # | Criterio | Descripción |
|---|----------|-------------|
| 1 | **Código implementado** | Toda la funcionalidad según criterios de aceptación |
| 2 | **Code review aprobado** | Revisado por al menos un compañero de equipo |
| 3 | **Tests unitarios pasando** | Cobertura mínima de 70% en código modificado |
| 4 | **Tests de integración pasando** | Funcionalidad probada en entorno local |
| 5 | **Sin bugs críticos/alta** | Issues de prioridad crítica y alta resueltos |
| 6 | **Migraciones ejecutadas** | Base de datos actualizada correctamente |
| 7 | **Sin warnings/lint errors** | Código pasa validaciones de estilo |

### Criterios Funcionales

| # | Criterio | Descripción |
|---|----------|-------------|
| 8 | **Criterios de aceptación cumplidos** | Todos los criterios verificados |
| 9 | **Pruebas en desarrollo** | Funcionalidad probada en ambiente de desarrollo |
| 10 | **Documentación actualizada** | README, API docs, o comentarios actualizados |

### Criterios de Calidad

| # | Criterio | Descripción |
|---|----------|-------------|
| 11 | **Accesibilidad básica** | UI usable sin assistive technology bloqueante |
| 12 | **Diseño responsive** | Funcional en dispositivos objetivo |
| 13 | **Rendimiento aceptable** | Tiempos de respuesta < 2 segundos |

### Criterios de Proceso

| # | Criterio | Descripción |
|---|----------|-------------|
| 14 | **Aprobación del Product Owner** | Funcionalidad validada por PO |
| 15 | **Código mergeado** | Merge a rama principal completado |
| 16 | **Deployment preparado** | Cambios listos para producción |

---

## Checklist de Cierre de Sprint

```markdown
## Sprint Review Checklist

- [ ] Todas las historias completadas según DoD
- [ ] Demo preparada para Product Owner
- [ ] Métricas del sprint actualizadas
- [ ] Backlog actualizado con nueva información
- [ ] Impedimentos documentados
- [ ] Próximo sprint planificado

## Sprint Retrospective Inputs

- [ ] Velocity del sprint
- [ ] Historias bloqueadas o pausadas
- [ ] Mejoras identificadas
- [ ] Acciones de mejora definidas
```

---

## Tabla de Responsabilidades

| Rol | Responsabilidad sobre DoR | Responsabilidad sobre DoD |
|-----|--------------------------|--------------------------|
| Product Owner | Definir criterios de aceptación | Validar funcionalidad |
| Scrum Master | Facilitar refinamiento | Verificar proceso |
| Desarrolladores | Estimar y clarificar | Implementar y testear |
| QA | Definir casos de prueba | Ejecutar testing |

---

## Ejemplo de DoR Aplicado

### Ejemplo: HU-022 Importar Bienes desde Excel

| Criterio | Estado | Notas |
|----------|--------|-------|
| Título claro | ✅ | "Importar Bienes desde Excel" |
| Descripción completa | ✅ | Como gerente de bienes, quiero importar múltiples bienes desde Excel... |
| Épica identificada | ✅ | E3 - Gestión de Inventario |
| Criterios de aceptación | ✅ | 8 criterios definidos |
| Estimación asignada | ✅ | 13 puntos |
| Dependencias identificadas | ✅ | Requiere instalación de Maatwebsite |
| Mockups | ✅ | Formulario de carga |
| Datos de prueba | ✅ | Template Excel de ejemplo |
