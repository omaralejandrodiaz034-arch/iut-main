# Plan de Desarrollo Preliminar RUP

## Objetivo

Definir un plan de desarrollo detallado para el Sistema de Gestión de Inventario de Bienes, orientado al contexto real del proyecto y al entorno Laravel implementado en el repositorio.

## Supuestos

- El proyecto es un sistema de titulación para un Técnico Superior en Informática.
- La aplicación se desarrolla sobre Laravel 12 con Blade, Tailwind CSS y Vite.
- La base de datos de desarrollo es SQLite y en producción se puede usar MySQL/Postgres.
- El equipo tiene acceso al código existente y a la infraestructura de desarrollo.
- El plan se ajusta al alcance del sistema implementado: Organismos, Unidades Administradoras, Dependencias, Bienes, Movimientos, Reportes, Usuarios, Auditoría y subtipos de bienes.

## Fases de desarrollo

### Fase 1: Inception / Análisis inicial (3 semanas)

- Actividades:
  - Recopilar el alcance y validar el objetivo del sistema.
  - Identificar actores, requisitos funcionales y no funcionales.
  - Revisar el código existente y las migraciones actuales.
  - Definir riesgos clave y dependencias técnicas.
  - Establecer criterios de aceptación por módulo.
- Entregables:
  - Documento de visión actualizado.
  - Lista de requerimientos funcionales y no funcionales.
  - Glosario de términos.
  - Matriz de riesgos inicial.
  - Plan preliminar con cronograma de 7 meses.

### Fase 2: Elaboration / Diseño y arquitectura (6 semanas)

- Actividades:
  - Documentar la arquitectura del sistema Laravel.
  - Definir el modelo de datos completo y relaciones reales.
  - Generar diagramas de clases y ER basados en el código.
  - Detallar casos de uso para los flujos principales.
  - Validar integridad de datos y unicidad de códigos jerárquicos.
- Entregables:
  - Documento de Arquitectura de Software (SAD).
  - Modelo de análisis y diseño.
  - Modelo de datos y diagrama ER.
  - Diagrama de clases.
  - Casos de uso detallados.
  - Prototipo arquitectónico (estructura de módulos y rutas).

### Fase 3: Construction / Desarrollo iterativo (12 semanas)

- Actividades:
  - Implementación incremental de funcionalidades.
  - Desarrollo de CRUD para Organismos, Unidades, Dependencias, Bienes y Usuarios.
  - Implementación de movimientos, desincorporaciones, reincorporaciones y reportes.
  - Integración de importación/exportación Excel y generación de PDF.
  - Desarrollo y ajuste de vistas Blade y experiencia de usuario.
  - Creación de pruebas unitarias y funcionales.
- Entregables:
  - Código fuente completo del sistema.
  - Documentación de despliegue y pruebas.
  - Módulos ejecutables y base de datos migrada.
  - Pruebas automatizadas para servicios críticos.
  - Prototipo de interfaz con pantallas principales.

### Fase 4: Transition / Validación y despliegue (4 semanas)

- Actividades:
  - Pruebas de aceptación junto a usuarios finales.
  - Corrección de defectos y ajustes de usabilidad.
  - Documentación de usuario y guía de operación.
  - Preparación del ambiente piloto y despliegue inicial.
  - Capacitación básica a administradores y responsables.
- Entregables:
  - Release candidate listo para piloto.
  - Manual de usuario y guías de uso.
  - Plan de despliegue y respaldo.
  - Reporte de pruebas de aceptación.

### Fase 5: Cierre / Entrega final y ajustes (2 semanas)

- Actividades:
  - Ajustes finales según retroalimentación del piloto.
  - Revisión final de documentación y artefactos RUP.
  - Publicación del sistema en el entorno de entrega.
  - Retroalimentación de lecciones aprendidas.
- Entregables:
  - Versión final del sistema.
  - Documentación final de proyecto y RUP.
  - Informe de cierre y recomendaciones.

## Cronograma orientativo

- Mes 1: Inception y análisis de requisitos.
- Mes 2 y 3: Elaboración de diseño, arquitectura y modelado.
- Mes 4, 5 y 6: Construcción y pruebas del sistema.
- Mes 7: Transition, despliegue piloto y cierre.

## Hitos

1. Visión y alcance aprobados.
2. Arquitectura y modelo de datos validados.
3. Módulo de bienes y movimiento funcional.
4. Reportes y auditoría operativos.
5. Despliegue piloto y entrega final.

## Artefactos principales

- Documento de visión.
- Requisitos funcionales y no funcionales.
- Casos de uso detallados.
- Modelo de datos y diagramas ER.
- Diagrama de clases.
- Documento de arquitectura (SAD).
- Manual de usuario.
- Plan de pruebas y resultados.
- Guía de despliegue.

## Recursos

- Equipo de desarrollo: 2-3 desarrolladores Laravel.
- Líder técnico / arquitecto: 1 persona.
- Stakeholders: administradores, responsables patrimoniales y auditores.
- Infraestructura: servidor PHP/Laravel y repositorio de código.

## Dependencias

- Confirmación de estructura organizativa y datos de bienes actuales.
- Acceso a servidor con PHP 8.2, Composer, Node.js y Vite.
- Variables de entorno configuradas en `.env`.
- Datos de inventario para importación y prueba.
- Colaboración con usuarios para pruebas y aceptación.
