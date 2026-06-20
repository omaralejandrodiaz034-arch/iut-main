# Fase de Elaboración
## Modelo de análisis (dominio)
### Núcleo jerárquico
- Organismo
- UnidadAdministradora
- Dependencia
- Bien
### Entidades de apoyo
- Responsable
- TipoResponsable
- Usuario
- Rol
- Movimiento
- HistorialMovimiento
- Reporte
- Auditoria
- Eliminado
- BienDesincorporado
- BienElectronico
- BienMobiliario
- BienVehiculo
- BienOtro
## Reglas de negocio relevantes
- La jerarquía operativa sigue: Organismo > Unidad > Dependencia > Bien.
- El responsable se asocia a dependencia.
- Los bienes registran estado y tipo.
- Las operaciones críticas generan trazabilidad (movimientos y/o auditoría).
- Solo administradores pueden ejecutar operaciones restringidas.
