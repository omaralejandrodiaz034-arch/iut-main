# Extensión de Código Jerárquico a 10 Dígitos

## Problema Identificado

El formato actual de código jerárquico de **8 dígitos** tiene limitaciones:
- **Dependencia**: 1 dígito (máximo 9 dependencias por unidad)
- Si una unidad supera las 9 dependencias, el sistema falla

## Propuesta de Solución

Extender a **10 dígitos** con la siguiente distribución:
- **Organismo**: 1 dígito (máximo 9)
- **Unidad**: 4 dígitos (máximo 9999)
- **Dependencia**: 3 dígitos (máximo 999) ← **Ampliado de 1 a 3**
- **Bien**: 2 dígitos (máximo 99)
- **Total**: 10 dígitos

Formato: `X.XXXX.XXX.XX` (ej: `1.0010.123.01`)

## Archivos a Modificar

### 1. `app/Services/CodigoJerarquicoService.php`
- Cambiar `LONG_DEPENDENCIA` de 1 a 3 (ampliar segmento)
- Actualizar todas las constantes `TOTAL_*` para reflejar 10 dígitos
- Modificar métodos de generación y decodificación de códigos
- Actualizar validaciones y estadísticas
- Actualizar el método `validarJerarquia` y `obtenerCodigoPadre`

### 2. `resources/views/unidades/create.blade.php`
- JavaScript línea 124: el sufijo debe ser 5 dígitos `codigo.substring(5)` (dependencia 2 dígitos + bien 2 dígitos)
- JavaScript línea 255: validación del código cambiar de 8 a 10 dígitos
- Input `codigo_unidad` línea 55: maxlength 4 correcto, pero el sufijo muestra 3 dígitos hardcodeado (línea 59)

### 3. `resources/views/dependencias/create.blade.php`
- JavaScript línea 162-164: prefijo 5, dependencia 3, sufijo 2
- Input `codigo_dependencia` línea 55: maxlength de 1 a 3
- JavaScript línea 220, 253: validación de código de 8 a 10 dígitos
- Campo `sufijo_dependencia` línea 59: debe mostrar 2 dígitos (actualmente vacío)

### 4. `resources/views/dependencias/edit.blade.php`
- Línea 52, 56, 61: parsing de código con ancho incorrecto (debe ser 5-3-2)
- JavaScript línea 158: límite de 1 dígito a 3 dígitos
- JavaScript línea 172: padding de 1 a 3 dígitos

### 5. `resources/views/bienes/create.blade.php`
- JavaScript línea 238: prefijo debe ser 8 dígitos en lugar de 6
- JavaScript línea 267: parsing correcto pero con longitud incorrecta
- JavaScript línea 326-327: parsing de código sugerido ajustar a 10 dígitos
- JavaScript línea 505: validación de código de 8 a 10 dígitos

### 6. `app/Http/Controllers/DependenciaController.php`
- Línea 148: regex `^\d{1}$` cambiar a `^\d{3}$`
- Línea 155-156: `str_pad` de 1 a 3 dígitos
- Línea 186: validación de código termina con `00` (correcto, pero ahora serán 2 dígitos)
- Línea 220: validación longitud mínima a 10

## Cambios Específicos

### CodigoJerarquicoService.php
```php
// Antes:
public const LONG_DEPENDENCIA = 1;
public const TOTAL_DEPENDENCIA = 8;
public const TOTAL_BIEN = 8;

// Después:
public const LONG_DEPENDENCIA = 3;
public const TOTAL_DEPENDENCIA = 10;
public const TOTAL_BIEN = 10;
```

### dependencias/create.blade.php (líneas 162-164)
```javascript
// Antes:
prefijoInput.value = codigo.substring(0, 5);   // 1+4
depInput.value = codigo.substring(5, 6);      // 1 dígito
sufijoInput.value = codigo.substring(6);        // 2 dígitos

// Después:
prefijoInput.value = codigo.substring(0, 5);   // 1+4
depInput.value = codigo.substring(5, 8);      // 3 dígitos
sufijoInput.value = codigo.substring(8);        // 2 dígitos
```

## Resumen de Modificaciones por Archivo

| Archivo | Cambios Clave |
|---------|---------------|
| `CodigoJerarquicoService.php` | LONG_DEPENDENCIA: 1→3, TOTAL_DEPENDENCIA/BIEN: 8→10 |
| `unidades/create.blade.php` | Líneas 124, 255: validación 8→10 dígitos |
| `dependencias/create.blade.php` | Líneas 55, 162-164: maxlength y parsing de 1→3 dígitos |
| `dependencias/edit.blade.php` | Parsing y validación JS ajustada |
| `bienes/create.blade.php` | Líneas 238, 326-327, 505: parsing a 8 dígitos prefijo |
| `DependenciaController.php` | Regex `^\d{1}$` → `^\d{3}$` |
| `CodigoUnicoService.php` | ELIMINAR (obsoleto, colisión de nombres) |

## Orden de Implementación

1. Eliminar `app/Services/CodigoUnicoService.php` obsoleto (colisión de nombres)
2. Modificar `app/Services/CodigoJerarquicoService.php` 
3. Crear migración de datos para actualizar códigos existentes (8→10 dígitos)
4. Actualizar vistas (unidades, dependencias, bienes)
5. Actualizar controladores (DependenciaController)
6. Ejecutar `php artisan migrate` y `vendor/bin/pint`