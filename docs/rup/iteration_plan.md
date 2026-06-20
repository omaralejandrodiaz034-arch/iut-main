Plan de iteración y cronograma (7 meses)

Contexto: el desarrollo y análisis duraron 7 meses. A continuación se propone una reconstrucción del cronograma por iteraciones, alineada con el código existente.

Mes 1 — Inicio y análisis
- Definición de alcance, modelos conceptuales, migraciones iniciales (organismos, unidades, dependencias).

Mes 2 — Identidad y usuarios
- Implementación de `usuarios`, `roles`, autenticación y permisos básicos.

Mes 3 — Gestión de bienes
- Modelo `bienes`, CRUD básico, fotografía y atributos generales.

Mes 4 — Tipos de bienes y detalles
- Subtablas `bienes_electronicos`, `bienes_mobiliario`, `bienes_vehiculo`, `bienes_otro`.

Mes 5 — Movimientos y auditoría
- Implementación de `movimientos`, `historial_movimientos` y `auditoria` traits.

Mes 6 — Reportes y vistas
- Generación de `reportes`, pages y filtros, mejoras en UI (Blade + Vite).

Mes 7 — Pruebas, documentación y ajustes finales
- Tests unitarios/funcionales, ajustes de migraciones, documentación del sistema (este conjunto de artefactos).
