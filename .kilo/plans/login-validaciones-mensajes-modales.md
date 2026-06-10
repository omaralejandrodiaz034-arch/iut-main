# Plan: Validaciones, Mensajes y Modales en `resources/views/auth/login.blade.php`

## Estado actual
- La vista `login.blade.php` **no muestra ningún mensaje de error ni feedback** al usuario.
- El formulario usa IMask para cédula, toggle de password, spinner de carga, pero carece de validaciones visuales.
- El controlador `AuthController::login()` envía mensajes `with('error', ...)` y también hay validaciones Laravel, pero nada se renderiza en la vista.

---

## Validaciones existentes en `AuthController::login()`

| Campo | Regla | Mensaje default Laravel (es) |
|-------|-------|-------------------------------|
| cedula | required, string | "El campo cédula de identidad es requerido." |
| password | nullable, string, min:8 | "El campo contraseña debe tener al menos 8 caracteres." (solo si se envía texto < 8) |
| remember | sometimes, boolean | — |

## Flujos que envían mensajes a login.blade.php

| Código (AuthController) | Tipo | Mensaje |
|-------------------------|------|---------|
| `return back()->with('error', ...)` | error | "Cédula de identidad no registrada o inválida." |
| `return back()->with('error', ...)` | error | "Debe ingresar su contraseña para iniciar sesión." |
| `return back()->with('error', ...)` | error | "Las credenciales no coinciden con nuestros registros" |
| `return redirect()->route('login')->with('error', ...)` | error | "Sesión expirada. Por favor, ingrese su cédula nuevamente." |
| Validaciones fallidas | `$errors` | Mensajes default Laravel por campo |

---

## Propuesta de implementación

### 1. Agregar bloque de mensajes de sesión e errores de validación
Ubicar **antes del formulario** (dentro de `.glass-card`, arriba del `<form>`):

```blade
{{-- Errores globales de sesión --}}
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
@endif

{{-- Errores de validación --}}
@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
        <p class="text-sm font-bold mb-2">Por favor corrija los siguientes errores:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

### 2. Agregar `@error()` por campo para feedback inline
- **Cédula**: mostrar error debajo del input, cambiar borde a rojo
- **Password**: mostrar error debajo del input, cambiar borde a rojo

Actualizar clases de inputs para incluir `@error()`:

```blade
class="input-elegant w-full px-4 py-3.5 rounded-xl bg-white/50 text-gray-900 placeholder:text-gray-400 outline-none @error('cedula') border-red-500 @enderror"
```

Y agregar después de cada input:
```blade
@error('cedula')
    <p class="text-xs text-red-600 mt-1 ml-1">{{ $message }}</p>
@enderror
```

### 3. Evaluar uso de modal vs alertas inline
- **Patrón actual del proyecto**: la mayoría de vistas usan **alertas inline** (`div` con clases de color) para mensajes de sesión y errores.
- El componente `<x-modal>` existe pero se usa para confirmaciones y resultados complejos (ej. `usuarios/edit.blade.php`).
- **Recomendación**: usar **alertas inline** en `login.blade.php` para mantener consistencia con el resto del sistema. Un modal es excesivo para un formulario de login donde el usuario debe poder corregir y reenviar rápidamente.

### 4. Mejorar feedback de carga (ya existe)
- El spinner y texto "Validando..." ya están implementados en el submit handler.
- Se puede agregar un mensaje sutil debajo del formulario durante la carga: `{{-- El texto "Validando..." ya se muestra en el botón --}}`

### 5. Considerar agregar mensaje de éxito (opcional)
- No hay flujo que redirija a login con `session('success')`, pero por completitud se puede agregar un bloque verde similar al de error.

---

## Cambios concretos en `resources/views/auth/login.blade.php`

### A. Dentro de `.glass-card`, antes de `<form>`:
- Insertar bloque `@if(session('error'))` con ícono y estilo rojo.
- Insertar bloque `@if($errors->any())` con lista de errores.

### B. Input de cédula (línea 61-63):
- Agregar `@error('cedula') border-red-500 @enderror` a la clase del `<input>`.
- Agregar `<p class="text-xs text-red-600 mt-1 ml-1">@error('cedula'){{ $message }}@enderror</p>` después del input.

### C. Input de password (línea 73-75):
- Agregar `@error('password') border-red-500 @enderror` a la clase del `<input>`.
- Agregar `<p class="text-xs text-red-600 mt-1 ml-1">@error('password'){{ $message }}@enderror</p>` después del input.

### D. Estilos adicionales (opcional):
- Considerar agregar clase `.alert-error` y `.alert-success` en el `<style>` para reutilizar en otras vistas.

---

## Preguntas para el usuario

1. **¿Deseas que los errores de validación también resalten el borde de los inputs en rojo?** (Sí, propuesto arriba)
2. **¿Prefieres mantener solo alertas inline o te gustaría evaluar un modal tipo "toast" para mensajes?** (El proyecto actual usa inline)
3. **¿Debo incluir también un bloque para `session('info')`** (usado en `set_password.blade.php`) por si en el futuro se reutiliza login para otros flujos?
