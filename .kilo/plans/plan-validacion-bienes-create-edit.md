# Plan: Validación Intensiva en `create.blade.php` y `edit.blade.php`

## 1. Bloque de resumen de errores en `edit.blade.php`
- Insertar el bloque `@if($errors->any())` idéntico al de `create.blade.php` al inicio del `<form>` (después de `@method('PUT')`).

## 2. Clases de error CSS en `edit.blade.php`
Añadir `@error('...') border-red-500 @enderror` y spans de error en:
- `dependencia_id` (el select visible deshabilitado)
- `tipo_bien`
- `estado`
- `descripcion`
- `precio`
- `fecha_registro`
- Campos de donación: `tipo_donante`, `donante_nombre`, `donante_documento`, `donante_direccion`

## 3. Sincronización de campos por tipo (backend ↔ frontend)
### 3.1 Actualizar `app/Enums/TipoBien.php`
- Asegurar que `ELECTRONICO` incluya LAPTOP, TABLET.
- Garantizar que `camposEspecificos()` refleje todos los campos usados en JS.

### 3.2 Actualizar `BienController::getSpecificValidationRules`
- Añadir `LAPTOP`, `TABLET` a la validación `Rule::in` de subtipo.
- Añadir reglas condicionales:
  - `required_if:tipo_bien,ELECTRONICO` para `subtipo`.
  - `required_if:tipo_bien,VEHICULO` para `placa`, `marca`, `modelo`, `anio`.
  - `required_if:tipo_bien,VEHICULO` para `anio` con `digits:4`, `integer`, `min:1900`, `max:` + año actual.
  - `numeric`, `min:0` para `kilometraje`.
  - `in:GASOLINA,DIESEL,ELECTRICO,HIBRIDO,GNV` para `combustible`.

### 3.3 Actualizar JS `camposPorTipo` en ambas vistas
- En `create.blade.php`: añadir `anio` (regex `^\d{4}$`), `combustible` (select), `kilometraje` (numeric), `garantia` (date), `capacidad`, `cantidad_piezas`, `acabado`, `cantidad`, `presentacion` según el tipo.
- En `edit.blade.php`: alinear exactamente la misma estructura que `create.blade.php` para evitar desincronización.

## 4. Validación frontend intensiva en `create.blade.php`
### 4.1 Reforzar objeto `validacionesCampo`
- Incluir `label`, `required`, `maxlength`, `pattern`, `min`, `message` para: `serial`, `placa`, `marca`, `modelo`, `anio`, `motor`, `chasis`, `kilometraje`, `combustible`, `pantalla`, `procesador`, `memoria`, `almacenamiento`, `garantia`, `material`, `color`, `dimensiones`, `capacidad`, `cantidad_piezas`, `acabado`, `especificaciones`, `cantidad`, `presentacion`.

### 4.2 Mensajes inline en campos dinámicos
- Al generar cada input dinámico, añadir un `<p class="text-red-500 text-[10px] mt-1 hidden" data-error="...">` debajo.
- En el submit loop, mostrar/ocultar ese span con el mensaje específico.
- Limpiar bordes rojos en eventos `input`/`blur` por campo.

### 4.3 Validaciones HTML5 nativas en inputs dinámicos
- Añadir `required`, `pattern`, `min`, `max`, `type="date"`, `type="number"`, `inputmode` según corresponda.

### 4.4 Validación de archivo de foto
- Añadir listener `change` en `#fotografia` que valide tamaño (≤ 2 MB) + tipo MIME (igual backend), mostrando span inline en lugar de `alert()`.
- Aplicar la misma lógica en ambos formularios.

### 4.5 Validación de cédula/RIF donante en frontend
- Reforzar el evento `input` en `#donante_documento` con regex específico:
  - `PERSONA`: `/^[VEP]-\d{7,8}$/`
  - `INSTITUCION`: `/^[JGP]-\d{8,9}$/`
- Mostrar span de error inline en lugar de esperar al submit.

### 4.6 Bloque de errores en `create.blade.php`
- Añadir span de error debajo de `precio`, `fecha_registro`, `estado`, `tipo_bien` cuando haya errores backend (`@error`), con mensaje traducido.

## 5. Validación backend reforzada en `BienController`
### 5.1 Ampliar `getValidationMessages()`
- Añadir mensajes para cada campo dinámico (`serial`, `placa`, `modelo`, `anio`, `motor`, `chasis`, `kilometraje`, `combustible`, `garantia`, `subtipo`, etc.) en español.

### 5.2 Regex en `donante_documento`
- En `getBaseValidationRules`: `regex:/^[VEPJGP]-\d{7,9}$/` cuando `es_donacion=1`.
- En `getUpdateValidationRules`: mismo regex condicional.

### 5.3 Validación de garantía
- Asegurar en backend `after:fecha_registro` (ya existe).

### 5.4 Validación de tipo
- Asegurar `Rule::enum(TipoBien::class)` (ya existe) y ampliar subtipo ELECTRONICO a todos los valores válidos.

## 6. Refactorización a Partial Blade (opcional pero recomendado)
- Crear `resources/views/bienes/partials/campos-dinamicos.blade.php` que reciba `tipo`, `valores`, `oldInput` y renderice los grupos de campos con atributos HTML5 de validación y spans de error.
- Incluirlo en ambas vistas para eliminar duplicación de JS y HTML dinámico.

## 7. Tareas adicionales de consistencia
- Asegurar que `create.blade.php` y `edit.blade.php` tengan el mismo orden y estilo de mensajes de error.
- Revisar que los `name` de los inputs dinámicos coincidan exactamente con las reglas del backend.
