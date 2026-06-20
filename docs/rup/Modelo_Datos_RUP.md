# Modelo de Datos - RUP

## Entidades principales

- `Organismo` (id, codigo, nombre, descripcion)
- `UnidadAdministradora` (id, organismo_id, codigo, nombre)
- `Dependencia` (id, unidad_administradora_id, codigo, nombre, responsable_id)
- `Bien` (id, dependencia_id, codigo_jerarquico, codigo_legible, descripcion, estado, valor, tipo)
- `Usuario` (id, cedula, nombre, apellido, correo, hash_password, rol)
- `Movimiento` (id, bien_id, tipo, fecha, origen_dependencia_id, destino_dependencia_id, usuario_id, observaciones)

## Relaciones

- `Organismo` hasMany `UnidadAdministradora`.
- `UnidadAdministradora` hasMany `Dependencia`.
- `Dependencia` hasMany `Bien`.
- `Bien` hasMany `Movimiento`.
- `Usuario` can be responsable of `Dependencia`.

## Reglas de integridad

- `codigo_jerarquico` debe ser único por `Bien`.
- Validaciones de existencia de dependencias y unidades al crear bienes.
- Valores numéricos para `valor` del bien.

## Índices y rendimiento

- Index en `codigo_jerarquico` y `dependencia_id`.
- Index en `usuario_id` en movimientos para trazabilidad.

## Consideraciones

- Normalización suficiente para evitar duplicidad de nombres entre dependencias.
- Plantillas de importación/exportación deben mapear a este modelo.
