# Modelo de Pruebas - RUP

## Objetivo

Definir la estrategia de pruebas para asegurar calidad en el sistema.

## Niveles de pruebas

- Pruebas unitarias: Servicios críticos como `CodigoJerarquicoService`.
- Pruebas funcionales: Flujos principales (registro, traslado, desincorporación).
- Pruebas de integración: Interacción con reportes y exportaciones.
- Pruebas de aceptación: Validación con usuarios finales.

## Herramientas

- `phpunit` para pruebas unitarias y funcionales.
- Selenium/Playwright para pruebas end-to-end (opcional).
- Fixtures y factories para crear datos de prueba.

## Casos de prueba prioritarios

- Generación y unicidad de `codigo_jerarquico`.
- Importación de datos desde Excel con validaciones.
- Transferencia de bienes entre dependencias.
- Generación de reportes y descarga de PDFs.

## Entorno de pruebas

Uso de base de datos SQLite en memoria para pruebas unitarias y una base separada para pruebas de integración.

## Métricas de calidad

- Cobertura de pruebas para servicios críticos: > 70%.
- Tasa de fallos post-release: < 3% en la primera semana de pilotaje.

## Observaciones

- Automatizar pruebas en CI para detectar regresiones tempranas.
- Mantener un conjunto mínimo de pruebas que cubra los criterios de aceptación.
