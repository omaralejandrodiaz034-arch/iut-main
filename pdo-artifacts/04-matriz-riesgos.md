# MATRIZ DE RIESGOS
## Sistema de Gestión de Inventario de Bienes

| ID | Riesgo | Probabilidad | Impacto | Nivel | Estado | Mitigación |
|----|--------|--------------|---------|-------|--------|------------|
| RSK-001 | Resistencia al cambio por parte de usuarios | Alta | Alto | 🔴 Alto | Vigilando | Capacitación continua y acompañamiento |
| RSK-002 | Datos históricos inconsistentes | Media | Alto | 🟠 Medio | Identificado | Proceso de validación y limpieza de datos |
| RSK-003 | Configuración SMTP para notificaciones | Media | Medio | 🟠 Medio | Pendiente | Documentar requisitos de infraestructura |
| RSK-004 | Problemas de rendimiento con grandes volúmenes | Media | Medio | 🟠 Medio | Controlado | Índices optimizados y paginación |
| RSK-005 | Pérdida de datos | Baja | Crítico | 🟠 Medio | Controlado | Backups automáticos diarios |
| RSK-006 | Retraso en funcionalidades críticas | Media | Alto | 🔴 Alto | En seguimiento | Priorizar HU-022, HU-027 |
| RSK-007 | Configuración de QR escaneable desde móvil | Baja | Medio | 🟡 Bajo | Resuelto | Librería implementada (HU-025) |

---

## Plan de Mitigación

### Riesgos Altos (🔴)
1. **Resistencia al cambio**: Realizar talleres de capacitación antes del despliegue
2. **Retraso funcionalidades**: Asignar recursos adicionales a Fase 4

### Riesgos Medios (🟠)
1. **Datos inconsistentes**: Crear script de validación antes de migración
2. **SMTP pendiente**: Definir proveedor de correo con antelación
3. **Rendimiento**: Continuar monitoreo de consultas

### Riesgos Bajos (🟡)
1. **Pérdida de datos**: Backups automatizados en producción
2. **QR móvil**: Ya implementado en HU-025

---

## Registro de Incidentes

| Fecha | Incidente | Resolución | Estado |
|-------|-----------|------------|--------|
| 15-Dic-2025 | Error en generación de PDF | Ajuste de librería DomPDF | ✅ Resuelto |
| 20-Dic-2025 | Bug en historial de movimientos | Corrección de query Eloquent | ✅ Resuelto |
| 05-Ene-2026 | Pendiente configuración SMTP | Pendiente infraestructura | ⏳ Abierto |