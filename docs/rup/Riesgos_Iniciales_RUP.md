# Lista de Riesgos Iniciales RUP

## Objetivo

Identificar y priorizar los riesgos principales que pueden afectar la viabilidad técnica, de negocios o de gestión del proyecto.

| Riesgo | Probabilidad | Impacto | Mitigación | Estado |
|---|---|---|---|---|
| Datos patrimoniales inconsistentes al importar | Media | Alto | Definir plantilla clara, validaciones y proceso de limpieza antes de carga. | Abierto |
| Duplicación o conflicto de códigos jerárquicos | Baja | Alto | Implementar y validar `CodigoJerarquicoService`, pruebas unitarias y reglas de unicidad. | Abierto |
| Falta de adopción por parte de usuarios | Media | Medio | Capacitación, manual de usuario y acompañamiento operativo. | Abierto |
| Restricciones de infraestructura institucional | Media | Medio | Verificar compatibilidad PHP/Laravel, usar SQLite para pruebas y documentar requisitos. | Abierto |
| Cambios frecuentes en requisitos | Media | Medio | Mantener backlog priorizado, entregar incrementos y revisar con stakeholders. | Abierto |
| Errores en generación de reportes PDF/Excel | Media | Medio | Probar reportes con datos reales y validar formatos. | Abierto |
| Falta de pruebas automatizadas | Media | Alto | Priorizar pruebas unitarias y funcionales en el plan de construcción. | Abierto |
| Problemas de seguridad en acceso y roles | Baja | Alto | Implementar middleware de roles, validaciones y revisiones de permisos. | Abierto |
| Dependencia de archivo manual y comandos de despliegue | Baja | Medio | Documentar instalación, uso y pruebas de despliegue en `INSTALLATION_GUIDE.md`. | Abierto |

## Observaciones

- Los riesgos asociados a los datos son críticos. Se recomienda ejecutar pruebas de importación e incluir casos de datos inválidos.
- La mitigación de la generación de códigos se basa en el servicio `CodigoJerarquicoService` y sus pruebas.
- La gestión de cambios debe apoyarse en el backlog y la priorización de funcionalidades esenciales.
