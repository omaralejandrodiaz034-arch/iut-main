# Documento de Arquitectura de Software (SAD) - RUP

## Resumen

Este documento describe la arquitectura del sistema de gestión de inventario, definiendo componentes, servicios y decisiones arquitectónicas.

## Motor tecnológico

- Framework: Laravel 12
- Lenguaje: PHP 8.2
- Plantillas: Blade
- Estilo: Tailwind CSS
- Compilación de assets: Vite
- Base de datos: SQLite (desarrollo), MySQL/Postgres (producción opcional)

## Visión de alto nivel

- Aplicación web monolítica con estructura MVC.
- Servicios de dominio desacoplados para lógica compleja (ej. `CodigoJerarquicoService`).
- Observers para auditoría y trazabilidad.
- Repositorios/Services para encapsular acceso y reglas de negocio.

## Diagramas y componentes

- Controladores: CRUD para Organismos, Unidades, Dependencias, Bienes, Movimientos y Usuarios.
- Servicios: `CodigoJerarquicoService`, `BienService`, `MovimientoService`.
- Models: Eloquent models para `Organismo`, `UnidadAdministradora`, `Dependencia`, `Bien`, `Usuario`.
- Vistas: Plantillas Blade con componentes reutilizables.

## Patrones y decisiones

- Uso de servicios para reglas de negocio reutilizables.
- Validaciones en controladores y Requests.
- Paginación y eager loading para rendimiento.
- Uso de traits y observers para auditoría.

## Calidad y pruebas

- Pruebas unitarias para servicios críticos.
- Pruebas funcionales para flujos de uso principales.
- Integración continua recomendada con `phpunit`.

## Despliegue

- Contenerización opcional o despliegue en servidor LAMP/LEMP.
- Uso de `artisan migrate` para migraciones y `npm run build` para assets.

## Supuestos y limitaciones

- La arquitectura asume un único reparto de responsabilidades dentro del monolito.
- Escalabilidad horizontal no es objetivo inmediato; si necesario, extraer servicios críticos.
