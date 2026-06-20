# Modelo de Análisis y Diseño RUP

## Contexto

Este modelo describe el análisis del dominio de inventario patrimonial y la estructura de diseño del sistema implementado. Se centra en las entidades y relaciones reales del código fuente.

---

## Entidades de análisis

- `Organismo`
- `UnidadAdministradora`
- `Dependencia`
- `Responsable`
- `Bien`
- `BienElectronico`
- `BienMobiliario`
- `BienVehiculo`
- `BienOtro`
- `BienDesincorporado`
- `Movimiento`
- `HistorialMovimiento`
- `Usuario`
- `Rol`
- `Reporte`
- `Auditoria`
- `TipoResponsable`

---

## Descripción general del diseño

El sistema sigue el patrón MVC de Laravel. Las entidades principales se modelan con Eloquent y se utilizan servicios y observers para lógica de negocio, auditoría y generación de códigos.

Las dependencias entre componentes son:

- Modelos Eloquent: representan las tablas y relaciones.
- Controladores: gestionan flujos HTTP CRUD y acciones específicas.
- Servicios: encapsulan reglas complejas de negocio.
- Traits/Observers: auditan operaciones y capturan cambios.
- Vistas Blade: presentan formularios, listados y reportes.

---

## Diseño de relaciones

### Organismo / UnidadAdministradora / Dependencia
- `Organismo` 1..* `UnidadAdministradora`
- `UnidadAdministradora` 1..* `Dependencia`
- `Dependencia` 1..* `Bien`

### Bien y subtipos
- `Bien` 1..1 `BienElectronico` (opcional)
- `Bien` 1..1 `BienMobiliario` (opcional)
- `Bien` 1..1 `BienVehiculo` (opcional)
- `Bien` 1..1 `BienOtro` (opcional)
- `Bien` 1..1 `BienDesincorporado` (opcional)

### Movimientos y trazabilidad
- `Bien` 1..* `Movimiento`
- `Movimiento` 1..* `HistorialMovimiento`
- `Usuario` 1..* `Movimiento`
- `Usuario` 1..* `Reporte`

### Seguridad y roles
- `Usuario` *..1 `Rol`
- `Rol` 1..* `Usuario`

### Auditoría
- `Auditoria` 1..1 `Usuario` (opcional)

---

## Componentes principales de diseño

### Modelos

Los modelos contienen relaciones Eloquent, casts y fillables. Algunos modelos importantes:

- `Bien`: encapsula el inventario patrimonial y los atributos básicos.
- `Movimiento`: almacena transacciones de cambios de estado o ubicación.
- `Usuario`: gestiona autenticación personalizada mediante `hash_password`.
- `Auditoria`: guarda eventos críticos del sistema.

### Servicios

- `CodigoJerarquicoService`: genera códigos únicos y jerárquicos para bienes.
- `BienTypeService`: administra la lógica de subtipos de bienes.
- `MovimientoService`: encapsula la lógica de creación y validación de movimientos.

### Observers / Traits

- `AuditableTrait`: registra cambios en entidades.
- `GeneratesMovimiento`: genera movimientos asociados a acciones de dominio.

---

## Reglas de negocio clave

- Los bienes deben pertenecer a una dependencia.
- Cada bien puede tener datos adicionales según su tipo.
- Los movimientos se registran con referencia opcional a un bien y sujeto polimórfico.
- El audit trail guarda datos anteriores y nuevos de operaciones relevantes.
- Las dependencias pueden tener responsables asignados.

---

## Consideraciones de diseño

- La separación por subtablas de bienes permite tipos especializados sin duplicar campos.
- El diseño actual prioriza trazabilidad y reportes sobre escalabilidad distribuida.
- El modelo es suficiente para el alcance de titulación y las funcionalidades implementadas.
