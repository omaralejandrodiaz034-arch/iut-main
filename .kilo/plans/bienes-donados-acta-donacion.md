# Plan: Bienes donados y acta de donación

## Objetivo

Agregar soporte para bienes adquiridos por donación en el sistema de bienes:

- Registrar bienes donados con valor `0 Bs`.
- Mostrar un checkbox en el formulario de registro para marcar el bien como donación.
- Capturar datos del donante: tipo de donante, nombre de persona/institución, documento opcional y dirección.
- Generar y guardar automáticamente un PDF de acta de donación al registrar el bien.
- Agregar un filtro en el listado de bienes para identificar bienes donados.
- Mostrar la información y el acta de donación en el detalle del bien.

## Cambios propuestos

### 1. Base de datos

Crear nueva migración para agregar campos a `bienes`:

- `es_donacion` → `boolean`, default `false`.
- `tipo_donante` → `string`, nullable.
- `donante_nombre` → `string`, nullable.
- `donante_documento` → `string`, nullable.
- `donante_direccion` → `string`, nullable.
- `acta_donacion` → `string`, nullable.

Requisitos:

- Incluir método `down()` para revertir los campos.
- Mantener compatibilidad con registros existentes.

### 2. Modelo `Bien`

Actualizar `app/Models/Bien.php`:

- Agregar los nuevos campos al array `$fillable`.
- Agregar `es_donacion` al array `$casts` como `boolean`.

### 3. Servicio de acta de donación

Crear `app/Services/ActaDonacionService.php` siguiendo el patrón de `ActaTrasladoService`:

- Generar folio con formato `DON-YYYY-######`.
- Renderizar la vista Blade del acta con DomPDF.
- Guardar el PDF en `storage/public/actas/donacion/{folio}.pdf`.
- Retornar la ruta relativa para guardarla en el bien.

Datos del acta:

- Bien: código, descripción, tipo y precio.
- Donante: tipo, nombre, documento y dirección.
- Dependencia donde queda registrado el bien.
- Fecha y hora de generación.
- Usuario que registró el bien.

### 4. Vista del acta

Crear `resources/views/bienes/pdf/acta-donacion.blade.php`:

- Encabezado institucional.
- Título `ACTA DE DONACIÓN DE BIEN PATRIMONIAL`.
- Folio.
- Ciudad y fecha.
- Texto legal/administrativo breve.
- Datos del bien.
- Datos del donante.
- Firmas de responsable patrimonial / funcionario autorizado y donante o representante.

### 5. Formulario de registro

Actualizar `resources/views/bienes/create.blade.php`:

- Agregar checkbox `es_donacion` en la sección de valores/archivos.
- Al activar `es_donacion`, forzar visualmente precio en `0.00` y deshabilitar edición del precio.
- Mostrar campos condicionales de donación:
  - Tipo de donante: persona o institución.
  - Nombre de la persona o institución.
  - Documento opcional, por ejemplo cédula/RIF.
  - Dirección del donante.
- Agregar JavaScript para:
  - Mostrar/ocultar campos de donación.
  - Forzar `precio = 0` cuando el checkbox está activo.
  - Validar nombre y dirección cuando sea donación.

### 6. Formulario de edición

Actualizar `resources/views/bienes/edit.blade.php`:

- Permitir ver y editar el estado de donación.
- Mostrar/ocultar campos de donación según `es_donacion`.
- Si se marca como donación, forzar precio `0.00`.
- Conservar acta actual si ya fue generada.

### 7. Controlador `BienController`

Actualizar `app/Http/Controllers/BienController.php`:

#### Registro

- Agregar reglas de validación:
  - `es_donacion` → `nullable|boolean`.
  - `tipo_donante` → `required_if:es_donacion,1|string|in:PERSONA,INSTITUCION`.
  - `donante_nombre` → `required_if:es_donacion,1|string|max:255`.
  - `donante_documento` → `nullable|string|max:50`.
  - `donante_direccion` → `required_if:es_donacion,1|string|max:500`.
  - `acta_donacion` → excluido de `extractBienData`.
- Si `es_donacion` está activo:
  - Establecer `precio = 0`.
  - Crear el bien.
  - Generar el acta con `ActaDonacionService`.
  - Guardar `acta_donacion` en el registro del bien.
- Mantener todo dentro de la transacción existente para evitar bienes sin acta si falla la generación.

#### Edición

- Permitir actualizar campos de donación.
- Si se activa `es_donacion` y no existe `acta_donacion`, generar una nueva acta.
- Si se desactiva `es_donacion`, permitir precio distinto de cero y ocultar/marcar datos de donación como no aplicables según convenga.

#### Listado y filtros

- Agregar validación de `es_donacion` como `nullable|boolean`.
- Aplicar filtro:
  - Si `es_donacion=1`, `where('es_donacion', true)`.
  - Si `es_donacion=0`, `where('es_donacion', false)`.
- Incluir `es_donacion` en `appends()` de paginación.

#### Reportes PDF

- Agregar `es_donacion` a la validación de `generarReporte`.
- Aplicar el mismo filtro en `aplicarFiltrosReporteFinal`.
- Incluir el filtro en el subtítulo del reporte.

#### Detalle

- Cargar/mostrar datos de donación en `show`.
- Agregar botón o enlace para ver/descargar el acta de donación cuando `acta_donacion` exista.

### 8. Listado de bienes

Actualizar `resources/views/bienes/index.blade.php`:

- Agregar checkbox de filtro `es_donacion` en el panel de filtros.
- Actualizar `clearAllFilters()` para limpiar ese checkbox.
- Actualizar chips de filtros activos para mostrar “Donados: Sí/No”.

Actualizar `resources/views/bienes/partials/table.blade.php`:

- Agregar columna `Donación`.
- Mostrar etiqueta:
  - `DONADO` cuando `es_donacion` sea verdadero.
  - `NO` o vacío cuando sea falso.
- Ajustar `colspan` si se agrega columna.

### 9. Detalle del bien

Actualizar `resources/views/bienes/show.blade.php`:

- Mostrar si el bien es donado.
- Mostrar donante, documento y dirección cuando existan.
- Mostrar botón para abrir el acta de donación cuando exista.

Opcionalmente actualizar `resources/views/components/show-actions.blade.php` para mostrar el acta desde el bloque común de acciones.

### 10. Exportación e importación

Evaluar si conviene incluir donación en exportación/importación:

- Si se incluye en exportación, agregar columna “Donación” y datos del donante en `BienExcelController`.
- Si se incluye en importación, agregar columnas al template y lógica de lectura.
- Si no se incluye, documentar en el plan que no forma parte del requerimiento principal.

### 11. Pruebas

Agregar o ampliar pruebas Feature:

- Crear bien donado:
  - Valida que `es_donacion` sea `true`.
  - Valida que `precio` sea `0`.
  - Valida que `acta_donacion` no esté vacío.
  - Valida que el archivo del acta exista en `storage/public`.
- Crear bien no donado:
  - Valida que `precio` pueda ser mayor que cero.
  - Valida que `es_donacion` sea `false` por defecto.
- Filtro de listado:
  - Verificar que `?es_donacion=1` solo devuelve bienes donados.
  - Verificar que `?es_donacion=0` solo devuelve bienes no donados.

Comandos de validación sugeridos:

- `vendor/bin/pint`
- `composer test`
- `npm run build`
- `php artisan migrate`

## Riesgos y decisiones

- El requerimiento no indica si el acta debe descargarse automáticamente al registrar. La implementación propuesta la genera y guarda, luego permite verla desde el detalle del bien.
- Si el usuario requiere descarga inmediata, se puede cambiar el retorno de `store()` para devolver el PDF después de guardar el bien.
- Los campos de donación se guardarán directamente en `bienes` para simplificar filtros, reportes y trazabilidad.
- Para mantener consistencia, el precio de un bien donado debe forzarse en servidor, no solo en JavaScript.
