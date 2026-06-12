# Discrepancias en la Lógica de Códigos (Formato 8 dígitos)

## Estructura actual del código (8 dígitos total)

Según `CodigoJerarquicoService.php`:
- **Total**: 8 dígitos
- **Organismo**: 1 dígito
- **Unidad**: 4 dígitos
- **Dependencia**: 1 dígito
- **Bien**: 2 dígitos (secuencial)

Formato: `X.XXXX.X.XX` (ej: `1.0010.1.01`)

## Discrepancias encontradas

### 1. **Código duplicado/fichero con nombre incorrecto**

**Archivo**: `app/Services/CodigoUnicoService.php`
- La clase dentro se llama `CodigoJerarquicoService` (línea 13)
- **Problema**: Esto causa colisión de nombres si ambos archivos existen
- Este archivo está OBSOLETO (tiene formato de 19 dígitos) y debería eliminarse

### 2. **División incorrecta del código en vistas**

**bienes/create.blade.php (líneas 238, 326-327)**:
```javascript
prefijoInput.value = codigo.substring(0, 6);  // Primeros 6 caracteres
secuencialInput.value = codigo.substring(6);   // Últimos 2 caracteres
```

**Problema**: El prefijo debería ser 6 (organismo+unidad+dependencia), PERO el código mostrado en `spanSugerencia` (línea 239) incluye todo el código de 8 dígitos, no solo el prefijo.

### 3. **dependencias/create.blade.php - parsing incorrecto**

**Líneas 162-164**:
```javascript
prefijoInput.value = codigo.substring(0, 5);   // Debería ser 5 (1+4)
depInput.value = codigo.substring(5, 6);       // 1 dígito
sufijoInput.value = codigo.substring(6);       // 2 dígitos (para bienes)
```

**Problema**: El sufijo muestra 2 dígitos pero según la lógica la dependencia debería terminar con `00` (dependencia + bien).

### 4. **unidades/create.blade.php - parsing parcialmente correcto**

**Líneas 122-124**:
```javascript
prefijoInput.value = codigo.substring(0, 1);   // ✓ Correcto
unidad.value = codigo.substring(1, 5);         // ✓ Correcto
sufijo.value = codigo.substring(5);            // ✗ Debería tener 3 dígitos
```

**Problema**: El sufijo debería tener 3 dígitos (dependencia + bien), pero se muestra hardcodeado como `000` en el input readonly.

### 5. **Validación de código de organismo - lógica contraintuitiva**

**OrganismoController.php (líneas 84-89)**:
```php
// Valida que los últimos 7 dígitos sean ceros
if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO) !== str_repeat('0', 7))
```

**Problema**: Con `LONG_ORGANISMO = 1`, esto exige formato `X0000000`, pero no se está guardando el código con ese padding siempre. La generación automática (línea 54) usa `STR_PAD_RIGHT`, no `STR_PAD_LEFT`.

## Resumen

Las discrepancias clave son:
1. Archivo `CodigoUnicoService.php` obsoleto que colisiona con `CodigoJerarquicoService`
2. Parsing incorrecto en vistas de JavaScript para la división 1-4-1-2
3. Validación de organismo pide `X0000000` pero generación automática no aplica este formato