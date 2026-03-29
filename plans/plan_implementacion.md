# Plan de Implementación - Sistema IUT

## Estado de Implementación

✅ **COMPLETADO** - Fase 1: CRUD de Responsables
⏭️ **APLAZADO** - Fases 2, 3, 4 (no prioritarias)

---

## Objetivo
Completar las funcionalidades faltantes identificadas en el análisis.

---

## Fase 1: Completar CRUD de Responsables (Prioridad ALTA)

### 1.1 Actualizar ResponsableController
**Archivo**: `app/Http/Controllers/ResponsableController.php`

Agregar métodos:
```php
public function show(Responsable $responsable)
public function edit(Responsable $responsable)
public function update(Request $request, Responsable $responsable)
public function destroy(Responsable $responsable)
```

### 1.2 Agregar rutas de Responsables
**Archivo**: `routes/web.php`

Agregar después de rutas existentes de responsables:
```php
Route::resource('responsables', ResponsableController::class);
```

### 1.3 Crear vistas de Responsables

#### 1.3.1 Índice de Responsables
**Archivo**: `resources/views/responsables/index.blade.php`
- Tabla con listad de responsables
- Columns: Cédula, Nombre, Tipo, Dependencias asignadas, Acciones
- Buttons: Crear nuevo, Editar, Ver, Eliminar
- Search/filter por nombre o cédula

#### 1.3.2 Ver Detalle de Responsable
**Archivo**: `resources/views/responsables/show.blade.php`
- Información completa del responsable
- Lista de dependencias asignadas
- Lista de bienes a su cargo
- Historial de movimientos

#### 1.3.3 Editar Responsable
**Archivo**: `resources/views/responsables/edit.blade.php`
- Formulario con campos: cédula, nombre, tipo
- Validación de campos

#### 1.3.4 Actualizar navegación
**Archivo**: `resources/views/layouts/head.blade.php`

Agregar enlace a responsables:
```php
<a href="{{ route('responsables.index') }}" ...>
    <span>Responsables</span>
</a>
```

---

## Fase 2: Mejorar Historial de Movimientos (Prioridad MEDIA)

### 2.1 Crear vista detallada por Bien
**Archivo**: `resources/views/historial-movimientos/show.blade.php`

- Timeline de movimientos del bien
- Filtros por tipo de movimiento
- Información de usuario que realizó cada movimiento

### 2.2 Actualizar controlador
**Archivo**: `app/Http/Controllers/HistorialMovimientoController.php`

- Método `show(Bien $bien)` para ver historial por bien

---

## Fase 3: Agregar Interfaz para Roles (Prioridad BAJA)

### 3.1 Crear controlador con vistas
**Archivo**: `app/Http/Controllers/RolController.php` (actualizar)

Cambiar de API JSON a Blade views:
- `index()` → retornar vista con lista de roles
- `create()`, `store()`, `edit()`, `update()`, `destroy()`

### 3.2 Crear vistas para Roles
- `resources/views/roles/index.blade.php`
- `resources/views/roles/create.blade.php`
- `resources/views/roles/edit.blade.php`

### 3.3 Agregar rutas
**Archivo**: `routes/web.php`
```php
Route::resource('roles', RolController::class);
```

---

## Fase 4: Agregar Interfaz para Tipos de Responsable (Prioridad BAJA)

### 4.1 Actualizar controlador
**Archivo**: `app/Http/Controllers/TipoResponsableController.php`

Cambiar de API JSON a Blade views

### 4.2 Crear vistas
- `resources/views/tipos-responsable/index.blade.php`
- `resources/views/tipos-responsable/create.blade.php`
- `resources/views/tipos-responsable/edit.blade.php`

### 4.3 Agregar rutas
**Archivo**: `routes/web.php`
```php
Route::resource('tipos-responsable', TipoResponsableController::class);
```

---

## Orden de Implementación Sugerido

```
Semana 1-2: Fase 1 (CRUD Responsables completo)
    ↓
Semana 3: Fase 2 (Historial de Movimientos)
    ↓
Semana 4: Fase 3 (Roles UI) - Opcional
    ↓
Semana 5: Fase 4 (Tipos Responsable UI) - Opcional
```

---

## Dependencias y Requisitos

- Laravel 12
- Blade templates con Tailwind CSS
- Heroicons (ya instalado)
- Los controladores ya existen, solo necesitan métodos adicionales

---

## Métricas de Éxito

- [ ] Responsables: CRUD completo con todas las vistas
- [ ] Navegación incluye enlace a Responsables
- [ ] Historial de movimientos visible desde vista de bien
- [ ] (Opcional) Gestión de Roles desde UI
- [ ] (Opcional) Gestión de Tipos Responsable desde UI
