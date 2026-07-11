# Plan: Sistema de notificaciones/alertas + campana + sección "Actas pendientes"

## Contexto

El sistema ya emite actas de **traslado**, **donación** y **desincorporación** marcándolas como
`PENDIENTE_FIRMA` con `fecha_limite_acta = now()+2 días` (ver `BienController` y `ActaRegressionService`).
Si el acta no se sube firmada y sellada en 2 días, la operación se cancela y revierte.

Hoy **no existe ningún sistema de notificaciones** (solo el trait `Notifiable` sin usar en el modelo
`App\Models\User`, que no es el modelo de usuario real). El admin solo se entera del acta pendiente si
abre el detalle del bien. Se requiere:

1. Un sistema de alertas/notificaciones persistidas.
2. Una **campanita** junto al menú de perfil (`resources/views/layouts/head.blade.php`, bloque líneas 142‑186)
   que muestre todas las notificaciones con contador de no leídas.
3. Al presionar una notificación, llevar a **la acción que corresponde** (subir el acta firmado).
4. Una **sección/formulario central** para ingresar el acta firmada y sellada (PDF o imagen) de traslado,
   donación o desincorporación.

## Decisiones confirmadas con el usuario

- **Almacenamiento:** Notificaciones DB de Laravel (tabla `notifications` + trait `Notifiable` en `Usuario`).
- **Formulario acta:** Sección central **"Actas pendientes"** que lista todas las actas `PENDIENTE_FIRMA`
  con cuenta regresiva y un formulario de subida por cada una. La campana enlaza a esta sección (o al acta concreta).
- **Actualización:** Recarga al navegar + **polling AJAX** (~30 s) vía Alpine, sin servicios externos.
- **Destinatarios:** Todas las notificaciones de actas pendientes se envían a **todos los administradores**.

## Modelo de datos

- Generar migración con `php artisan notifications:table` (crea `database/migrations/..._create_notifications_table.php`)
  y ejecutar `php artisan migrate`. Tabla `notifications`: `id` (uuid), `type`, `notifiable_type`,
  `notifiable_id`, `data` (json), `read_at`, timestamps.
- `app/Models/Usuario.php`: añadir `use Illuminate\Notifications\Notifiable;` y `use Notifiable;`.
  (El modelo ya extiende `Authenticatable`, compatible con `Notifiable`.)

### Contenido de `data` (JSON) de cada notificación
- `titulo`, `mensaje`, `tipo` (`TRASLADO`|`DONACION`|`DESINCORPORACION`|`ACTA_CANCELADA`),
  `bien_id`, `movimiento_id`, `action_url` (ruta a la acción), `vencimiento` (ISO).

## Backend

### Nueva clase de notificación
- `app/Notifications/ActaNotificacion.php` (extiende `Notification`):
  - `via()`: `['database']`.
  - `toArray()`: devuelve los campos `data` arriba según el tipo.
  - Constructor recibe `Movimiento $movimiento` y `string $tipoEvento`, y calcula `action_url`:
    - Para pendiente: `route('actas.pendientes')` (o `route('bienes.show', $bien)` anclado al formulario).
    - Para cancelada: `route('bienes.show', $bien)`.

### Servicio de disparo
- `app/Services/ActaNotificacionService.php` con:
  - `notificarActaPendiente(Movimiento $m)`: notifica a `Usuario::where('is_admin', true)->get()`
    con `ActaNotificacion` tipo pendiente.
  - `notificarActaCancelada(Movimiento $m)`: igual, tipo `ACTA_CANCELADA`.
  - `resolverPendientesDe(Movimiento $m)`: marca como leídas las notificaciones cuyo
    `data->movimiento_id == $m->id` y `tipo` pendiente (se usa al firmar o al cancelar).

### Controladores
- `app/Http/Controllers/NotificacionController.php`:
  - `index()` → vista `notificaciones/index.blade.php` (lista completa, botón "marcar todas").
  - `ir(DatabaseNotification $notification)` → `auth()->user()->notifications()->findOrFail($id)`,
    `markAsRead()`, `return redirect($notification->data['action_url'])`.
  - `marcarTodas(Request)` → `auth()->user()->unreadNotifications->markAsRead()`, redirect back.
  - `contar()` → JSON `{ count: unread, ultimas: [...] }` para el polling.
- `app/Http/Controllers/ActaController.php`:
  - `pendientes(Request)` → lista `Movimiento::where('acta_estado', 'PENDIENTE_FIRMA')`
    con `bien` y `usuario`, ordenada por `fecha_limite_acta` asc; vista `actas/pendientes.blade.php`.
  - Reutiliza la subida existente: el formulario de cada acta apunta a
    `route('bienes.acta-firmada', $movimiento->bien)` (ya existe en `BienController::subirActaFirmada`).

### Puntos de disparo (editar `BienController` y `ActaRegressionService`)
- `BienController::desincorporar`: tras crear el `Movimiento` DESINCORPORACION → `ActaNotificacionService::notificarActaPendiente($movimiento)`.
- `BienController::transferir`: tras crear el `Movimiento` TRASLADO → notificar pendiente.
- `BienController::store` (rama donación): tras crear el `Movimiento` DONACION → notificar pendiente.
- `BienController::subirActaFirmada`: al marcar `FIRMADA` → `ActaNotificacionService::resolverPendientesDe($movimiento)`.
- `ActaRegressionService::cancelarActaPendiente`: tras crear el `Movimiento` CANCELACION_ACTA →
  `resolverPendientesDe($movimiento)` + `notificarActaCancelada($movimiento)`.

### Rutas (`routes/web.php`, dentro del grupo auth)
- `Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');`
- `Route::get('notificaciones/{notification}/ir', [NotificacionController::class, 'ir'])->name('notificaciones.ir');`
- `Route::post('notificaciones/marcar-todas', [NotificacionController::class, 'marcarTodas'])->name('notificaciones.marcar-todas');`
- `Route::get('notificaciones/contar', [NotificacionController::class, 'contar'])->name('notificaciones.contar');`
- `Route::get('actas/pendientes', [ActaController::class, 'pendientes'])->name('actas.pendientes');`
  (proteger con `abort_unless(auth()->user()?->isAdmin(), 403)` dentro del controlador).

## Frontend

### Campana en el navbar
- Nuevo componente `resources/views/components/campana-notificaciones.blade.php` (Alpine.js, coincide con el
  estilo del dropdown de perfil ya existente):
  - Botón campana con **badge** de `auth()->user()->unreadNotifications()->count()`.
  - Dropdown con lista de `auth()->user()->unreadNotifications` (ó 10 últimas) mostrando `data.titulo`,
    `data.mensaje`, tiempo relativo y enlace a `route('notificaciones.ir', $n)`.
  - Botón "Marcar todas como leídas" → POST `notificaciones.marcar-todas`.
  - **Polling:** `setInterval` cada 30 s hace `fetch(route('notificaciones.contar'))` y actualiza badge + lista.
- Insertar `<x-campana-notificaciones />` en `layouts/head.blade.php` **justo antes** del bloque de perfil
  (línea 142, desktop `sm:flex`) y una versión enlace en el menú móvil (bloque ~270).
- La campana es visible para **cualquier usuario autenticado**; los no admin simplemente no tendrán
  notificaciones de actas.

### Sección central "Actas pendientes"
- `resources/views/actas/pendientes.blade.php` (reusa `layouts/base`, breadcrumbs, estilos del sistema):
  - Tabla/lista de actas `PENDIENTE_FIRMA`: tipo (badge), bien (código + descripción), emitido por,
    **cuenta regresiva** hasta `fecha_limite_acta` (`diffForHumans` / "vence en X").
  - Por cada acta, **formulario de subida** (file `acta_firmada`, acepta `application/pdf,image/*`,
    etiqueta "Acta firmada y sellada") que POST a `route('bienes.acta-firmada', $movimiento->bien)`.
  - Filtro opcional por tipo (TRASLADO/DONACION/DESINCORPORACION).
- Mejorar etiquetas en `show.blade.php` (ya tiene el form embebido) a "acta firmada y sellada" para consistencia.

## Validación / riesgos
- El route `bienes.acta-firmada` ya valida `acta_firmada` (pdf/jpg/jpeg/png, max 8192) y revierte si venció.
- Asegurar que `NotificacionController::ir` solo resuelva notificaciones del usuario autenticado
  (`auth()->user()->notifications()->findOrFail`).
- La notificación pendiente debe desaparecer/leerse al firmar o al cancelar (usar `resolverPendientesDe`).
- No romper el flujo existente: reutilizar rutas/controladores de subida ya existentes.

## Pasos de implementación (orden)
1. `php artisan notifications:table` + `php artisan migrate`.
2. Añadir `Notifiable` a `Usuario`.
3. Crear `ActaNotificacion` y `ActaNotificacionService`.
4. Crear `NotificacionController` y `ActaController`; añadir rutas.
5. Conectar disparos en `BienController` (desincorporar, transferir, store-donación, subirActaFirmada) y
   `ActaRegressionService::cancelarActaPendiente`.
6. Crear componente `campana-notificaciones`, insertar en `head.blade.php` (desktop + móvil).
7. Crear vistas `notificaciones/index.blade.php` y `actas/pendientes.blade.php`.
8. `vendor/bin/pint` y `php artisan test`.

## Plan de validación
- Test Feature `NotificacionesActasTest`:
  - Admin recibe notificación al desincorporar/transferir/donar.
  - El badge/conteo es > 0 y la notificación apunta a `actas.pendientes`.
  - `notificaciones.ir` marca como leída y redirige a `action_url`.
  - Tras subir acta firmada, la notificación pendiente queda resuelta (count baja).
  - Tras cancelación automática (acta vencida), se genera notificación `ACTA_CANCELADA` y la pendiente se resuelve.
- Verificar manualmente la campana en navbar y la sección "Actas pendientes".
