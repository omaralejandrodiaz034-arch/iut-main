# Fase de Elaboración (Elaboration)

## Objetivo

Establecer una arquitectura base sólida y detallar la mayoría de los requisitos.

## Entregables de Elaboración

- Documento de Arquitectura de Software (SAD).
- Modelo de Casos de Uso Detallado.
- Prototipo Arquitectónico.
- Modelo de Análisis y Diseño.
- Modelo de Datos Conceptual y Lógico.
- Lista de Riesgos Actualizada.

## Arquitectura base

El sistema utiliza:

- Laravel 12 como framework principal.
- Patrón MVC (Modelo-Vista-Controlador).
- Blade para vistas.
- Tailwind CSS para diseño visual.
- Vite para compilación de assets.
- SQLite como base de datos en desarrollo.
- Servicios especializados (`CodigoJerarquicoService`, `BienTypeService`, `MovimientoService`) para separar lógica de negocio.

## Fundamentos de diseño

- Jerarquía de entidades: `Organismo` → `Unidad Administradora` → `Dependencia` → `Bien`.
- Codificación única por jerarquía.
- Uso de tipos específicos de bienes con relaciones `hasOne`.
- Auditoría automática mediante observers y traits.
- Reportes en PDF y exportación Excel.

## Diagrama de casos de uso inicial

Ver `Casos_de_Uso_Inicial_RUP.md`.

## Modelo de datos

Ver `Modelo_Datos_RUP.md`.

## Riesgos

Actualizar según `Riesgos_Iniciales_RUP.md` y añadir riesgos mitigados con la arquitectura propuesta.

## Prototipo arquitectónico

El prototipo ejecutable es el sistema actual en el repositorio, con rutinas de creación de bienes, dependencias, organismos, reportes y auditoría.

## Decisiones clave

- Laravel 12 por su soporte MVC y ecosistema.
- SQLite en desarrollo para facilidad de ambiente y tests.
- Generación automática de códigos jerárquicos para asegurar unicidad.
- Separación de responsabilidades con servicios y controladores específicos.
