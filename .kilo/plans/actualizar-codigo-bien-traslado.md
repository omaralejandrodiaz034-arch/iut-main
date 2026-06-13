# Actualización de Código de Bien al Trasladar entre Dependencias

## Objetivo

Al trasladar un bien a otra dependencia, actualizar la correlación estructural de su código para reflejar la nueva dependencia destino. Si el número secuencial del bien ya está ocupado en la dependencia destino, asignar automáticamente el siguiente número disponible.

## Análisis del Estado Actual

### Formato de Código Vigente
- **10 dígitos**: `X.XX.XXX.XXXX` (Organismo 1 + Unidad 2 + Dependencia 3 + Bien 4)
- El servicio `CodigoJerarquicoService` ya opera con este formato (LONG_BIEN=4, TOTAL_BIEN=10)
- `LONG_PREFIJO_BIEN = 6` (Organismo 1 + Unidad 2 + Dependencia 3)

### Método de Transferencia Actual (`BienController::transferir`, línea ~1324)
Solo actualiza `dependencia_id` sin modificar el código del bien, lo que genera inconsistencias en la jerarquía.

## Cambios a Realizar

### 1. `app/Http/Controllers/BienController.php`

Modificar el método `transferir()` para incluir lógica de reasignación de código:

```php
// Antes de actualizar el bien:
// 1. Obtener prefijo de la dependencia destino
$prefijoDestino = CodigoJerarquicoService::buildPrefijoBien($dependenciaNueva);

// 2. Extraer secuencial actual del bien
$secuencialActual = substr($bien->codigo, -CodigoJerarquicoService::LONG_BIEN);

// 3. Verificar si el secuencial está disponible en la nueva dependencia
$codigoConPrefijo = $prefijoDestino . $secuencialActual;
$conflicto = Bien::where('codigo', $codigoConPrefijo)
    ->where('id', '!=', $bien->id)
    ->exists();

if ($conflicto) {
    // 4. Buscar siguiente secuencial disponible en la dependencia destino
    $maxNumero = Bien::where('codigo', 'LIKE', $prefijoDestino . '%')
        ->max(DB::raw('CAST(SUBSTR(codigo, -' . CodigoJerarquicoService::LONG_BIEN . ') AS UNSIGNED)'));
    $nuevoSecuencial = $maxNumero ? ((int) $maxNumero + 1) : 1;
} else {
    $nuevoSecuencial = (int) $secuencialActual;
}

// 5. Construir nuevo código completo
$nuevoCodigo = $prefijoDestino . str_pad((string) $nuevoSecuencial, CodigoJerarquicoService::LONG_BIEN, '0', STR_PAD_LEFT);

// 6. Actualiar el código del bien junto con la dependencia
$bien->update([
    'dependencia_id' => $request->dependencia_id,
    'codigo' => $nuevoCodigo,
]);
```

### 2. `app/Services/CodigoJerarquicoService.php`

Agregar método auxiliar para verificar disponibilidad de secuencial en una dependencia:

```php
/**
 * Verifica si un secuencial específico está disponible en una dependencia.
 */
public static function isSecuencialDisponibleEnDependencia(int $dependenciaId, string $secuencial): bool
{
    $dependencia = Dependencia::findOrFail($dependenciaId);
    $prefijo = self::buildPrefijoBien($dependencia);
    $codigo = $prefijo . str_pad($secuencial, self::LONG_BIEN, '0', STR_PAD_LEFT);

    return ! Bien::where('codigo', $codigo)->exists();
}

/**
 * Obtiene el siguiente secuencial disponible en una dependencia.
 */
public static function getSiguienteSecuencialDisponible(int $dependenciaId): int
{
    $dependencia = Dependencia::findOrFail($dependenciaId);
    $prefijo = self::buildPrefijoBien($dependencia);

    $maxNumero = Bien::where('codigo', 'LIKE', $prefijo . '%')
        ->max(DB::raw('CAST(SUBSTR(codigo, -' . self::LONG_BIEN . ') AS UNSIGNED)'));

    return $maxNumero ? ((int) $maxNumero + 1) : 1;
}
```

### 3. Actualizar mensaje de log

Incluir el código anterior y nuevo en el log de transferencia para trazabilidad.

### 4. Validación adicional

Antes de reasignar, verificar que el nuevo código no exista en ninguna otra tabla (organismo, unidad, dependencia) usando `codigoExiste()`.

## Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `app/Http/Controllers/BienController.php` | Modificar método `transferir()` para recalcular código |
| `app/Services/CodigoJerarquicoService.php` | Agregar métodos auxiliares de disponibilidad |

## Consideraciones

- El cambio de código debe ser atómico dentro de la transacción existente
- Se debe preservar el historial de movimientos con el código anterior en la descripción
- El acta de traslado debe reflejar ambos códigos (anterior y nuevo)
- No requiere migración de datos ni cambios en vistas (el cambio es transparente para el usuario)
