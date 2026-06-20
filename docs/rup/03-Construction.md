# Fase de Construcción (Construction)

## Objetivo

Desarrollar el producto completo y ejecutar pruebas masivas.

## Entregables de Construction

- Sistema de software (código fuente).
- Modelo de despliegue.
- Modelo de pruebas.
- Manual de usuario y documentación de soporte.

## Desarrollo

El sistema ya cuenta con:

- Código fuente Laravel 12.
- Vistas Blade para CRUD de bienes, organismos, dependencias, unidades y usuarios.
- Servicios de código jerárquico, generación de reportes y lógica de movimientos.
- Módulos de auditoría y exportación/importación.

## Modelo de despliegue

Ver `Modelo_Despliegue_RUP.md`.

## Modelo de pruebas

Ver `Modelo_Pruebas_RUP.md`.

## Documentación de soporte

- `docs/MANUAL_USUARIO.md` — Manual de usuario.
- `INSTALLATION_GUIDE.md` — Guía de instalación y configuración.
- `DEPLOYMENT_CHECKLIST.md` — Checklist de despliegue.

## Observaciones

La fase de construcción se apoya en el prototipo arquitectónico existente y las pruebas deberán priorizar:

- Funcionalidad de registro y edición de bienes.
- Integridad de códigos jerárquicos.
- Importación y exportación de datos.
- Generación de reportes PDF.
- Auditoría y seguridad de roles.
