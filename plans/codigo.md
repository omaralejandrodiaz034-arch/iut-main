# Plan: Asignación de Códigos por Jerarquía

## Objetivo
Al crear un bien, el sistema debe recomendar el **siguiente código disponible dentro del rango reservado de la dependencia seleccionada** (jerarquía: Organismo → Unidad → Dependencia → Bien), en lugar de usar el método global que busca huecos en todas las tablas.

---

## Estado Actual Detectado

### Servicio `CodigoUnicoService.php`
- ✅ `obtenerSiguienteCodigo()` → Busca huecos globalmente en todas las tablas (organismos, unidades, dependencias, bienes).
- ✅ `recomendarSiguienteCodigoParaDependencia($dependenciaId)` → Devuelve el siguiente código **dentro del rango `[code_min, code_max]`** de la dependencia, usando `MAX(codigo)+1` (sin rellenar huecos).
- ✅ Métodos de reserva jerárquica: `reservarCodigosParaOrganismo()`, `reservarCodigosParaUnidad()`, `reservarCodigosParaDependencia()`.

### Controlador `BienController.php`
- Línea 148: `create()` asigna `$codigoSugerido = CodigoUnicoService::obtenerSiguienteCodigo();` (método global).
- Línea 161-200: método `recomendarCodigo(Dependencia $dependencia)` ya existe y devuelve JSON con el código recomendado por dependencia.

### Vista `bienes/create.blade.php`
- Línea 84: input código tiene `value="{{ old('codigo', $codigoSugerido ?? '') }}"` (pre-llena con sugerencia global).
- No hay JavaScript que consulte a `recomendarCodigo` al cambiar la dependencia.
- Existe un botón de sugerencia (`#btn-sugerencia`) pero apunta a `$codigoSugerido` global.

### Rutas
- Línea 97-98 en `routes/web.php`: `GET /bienes/{dependencia}/recomendar-codigo` → `BienController@recomendarCodigo` (ya existe).

---

## Cambios Requeridos

### 1. BienController.php – Método `create()`
**Archivo**: `app/Http/Controllers/BienController.php` (línea 146-156)

**Cambio**: Eliminar la sugerencia global.

```php
// ANTES
public function create()
{
    $codigoSugerido = CodigoUnicoService::obtenerSiguienteCodigo();
    $dependencias = Dependencia::with('responsable')->get();
    // ...
    return view('bienes.create', compact('dependencias', 'tiposBien', 'codigoSugerido'));
}

// DESPUÉS
public function create()
{
    $dependencias = Dependencia::with('responsable')->get();
    $tiposBien = collect(TipoBien::cases())->mapWithKeys(
        fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
    );
    return view('bienes.create', compact('dependencias', 'tiposBien'));
}
```

**Justificación**: La sugerencia debe obtenerse dinámicamente según la dependencia seleccionada, no una global al cargar el formulario.

---

### 2. bienes/create.blade.php – Campo de código y JavaScript

#### a) Campo de código (línea 84)
**Cambio**: Eliminar el valor pre-llenado global.

```html
<!-- ANTES -->
<input type="text" name="codigo" id="codigo" value="{{ old('codigo', $codigoSugerido ?? '') }}" ...>

<!-- DESPUÉS -->
<input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}" ...>
```

#### b) JavaScript – Nueva lógica de sugerencia
**Reemplazar** el bloque de sec  cción 2 (líneas 196-236 actual) por:

```javascript
/* 2. Lógica de Código con Sugerencia por Dependencia */
const codigoInput = document.getElementById('codigo');
const sugerenciaContainer = document.getElementById('sugerencia-container');
const spanSugerencia = document.getElementById('span-sugerencia');
const btnSugerencia = document.getElementById('btn-sugerencia');

const baseUrl = "{{ url('bienes') }}";
let codigoSugeridoDependencia = null;

function actualizarSugerencia(codigo) {
    codigoSugeridoDependencia = codigo;
    spanSugerencia.textContent = codigo;
    sugerenciaContainer.classList.remove('hidden');
}

function ocultarSugerencia() {
    codigoSugeridoDependencia = null;
    sugerenciaContainer.classList.add('hidden');
}

function obtenerSugerencia(dependenciaId) {
    if (!dependenciaId) {
        ocultarSugerencia();
        return;
    }

    fetch(`${baseUrl}/${dependenciaId}/recomendar-codigo`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    if (data.success === false && data.error === 'rango_exhausto') {
                        alert(data.mensaje);
                        ocultarSugerencia();
                    } else {
                        throw new Error('Error al obtener sugerencia');
                    }
                }).catch(() => { throw new Error('Error de red'); });
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                actualizarSugerencia(data.codigo);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            ocultarSugerencia();
        });
}

// Cambio de dependencia: limpiar código y cargar nueva sugerencia
depSelect.addEventListener('change', function () {
    const depId = this.value;
    codigoInput.value = '';
    ocultarSugerencia();
    if (depId) obtenerSugerencia(depId);
});

// Input: sanitización y control de sugerencia
codigoInput.addEventListener('input', function (e) {
    const cleaned = e.target.value.replace(/\D/g, '');
    e.target.value = cleaned;

    if (codigoSugeridoDependencia && cleaned !== codigoSugeridoDependencia) {
        spanSugerencia.textContent = codigoSugeridoDependencia;
        sugerenciaContainer.classList.remove('hidden');
    } else {
        sugerenciaContainer.classList.add('hidden');
    }
});

// Botón: aplicar sugerencia
btnSugerencia.addEventListener('click', function () {
    if (codigoSugeridoDependencia) {
        codigoInput.value = codigoSugeridoDependencia;
        sugerenciaContainer.classList.add('hidden');
    }
});

// Formatear a 8 dígitos al blur
codigoInput.addEventListener('blur', function () {
    if (this.value && this.value.length > 0) {
        this.value = this.value.padStart(8, '0');
    }
});
```

**Justificación**: Al seleccionar una dependencia, se consulta al endpoint `/bienes/{id}/recomendar-codigo` que devuelve el siguiente código dentro del rango reservado jerárquicamente. El botón aplica esa sugerencia.

---

## Validación en `store()` (ya existe)

El método `store()` ya valida que el código no exista en ninguna tabla (línea 216 del controlador). No requiere cambios.

---

## Ordenamiento Numérico de Códigos (Mejora adicional)

Los códigos se ordenan como strings (00000001, 00000010, 00000011...). Para orden numérico correcto, actualizar:

### BienController.php – Línea 98
```php
// ANTES
$bienes = $query->orderBy($sort, $direction)->paginate(10);

// DESPUÉS (si $sort === 'codigo')
if ($sort === 'codigo') {
    $query->orderByRaw("CAST(codigo AS UNSIGNED) $direction");
} else {
    $query->orderBy($sort, $direction);
}
$bienes = $query->paginate(10)->appends($request->query());
```

### BienExcelController.php – Línea 226
```php
// ANTES
$bienes = $query->orderBy('codigo')->get();
// DESPUÉS
$bienes = $query->orderByRaw('CAST(codigo AS UNSIGNED)')->get();
```

### ReporteController.php – Líneas 826, 842, 859, 875
Cambiar `->orderBy('codigo')` por `->orderByRaw('CAST(codigo AS UNSIGNED)')`.

---

## Resumen de Archivos a Modificar

1. `app/Http/Controllers/BienController.php` – método `create()` (línea 146-156) y `index()` (ordenamiento).
2. `resources/views/bienes/create.blade.php` – campo código (línea 84) y JavaScript (sec. ción 2).
3. `app/Http/Controllers/BienExcelController.php` – ordenamiento en `exportar()`.
4. `app/Http/Controllers/ReporteController.php` – 4 métodos de reporte.

---

## Comportamiento Esperado

1. Usuario abre formulario "Registrar Bien" → campo código vacío.
2. Selecciona una dependencia → AJAX a `/bienes/{id}/recomendar-codigo` → sugiere `00000XXX` dentro del rango de esa dependencia.
3. Si la dependencia no tiene rango asignado → error JSON → se oculta sugerencia.
4. Usuario puede hacer clic en "¿Usar código sugerido?" o ingresar manualmente.
5. Al guardar, se valida que el código no exista (validación existente en `store()`).
6. En las tablas, los códigos se ordenan numéricamente (00000001, 00000002, ...).

---

## Notas

- El servicio ya maneja transacciones, locks `FOR UPDATE` y reintentos para evitar condiciones de carrera.
- Los rangos jerárquicos se calculan:
  - Organismo: `codigo_org * 10000 + 1 .. + cantidad`
  - Unidad: `codigo_unidad * 100 + 1 .. + cantidad`
  - Dependencia: `codigo_dep + 1 .. + cantidad`
- Si un ente no tiene rango asignado (`code_min`/`code_max` nulos), se calcula automáticamente al reservar o al recomendar.
- La dependencia debe tener rango asignado para que `recomendarSiguienteCodigoParaDependencia()` funcione; de lo contrario, lanza `RuntimeException`.
