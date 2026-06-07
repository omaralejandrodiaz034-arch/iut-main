# LÓGICA DE RESERVA DE CÓDIGOS Y ASIGNACIÓN AUTOMÁTICA

## 1. ARQUITECTURA GENERAL

### 1.1 Estructura Jerárquica del Sistema
```
Organismo (3 dígitos)
    ↓ (tiene muchas)
Unidad Administradora (4 dígitos)  
    ↓ (tiene muchas)
Dependencia (3 dígitos)
    ↓ (tiene muchos)
Bien (5 dígitos - secuencial)
```

### 1.2 Formato de Códigos
- **Organismo**: 8 dígitos (ej: `00143001`), con rango definido por `code_min`/`code_max`
- **Unidad Administradora**: 8 dígitos (ej: `00143001`), con rango definido
- **Dependencia**: 8 dígitos (ej: `00143001`), con rango definido
- **Bien**: Formato jerárquico `XXX-XXXX-XXX-XXXXX` (ej: `001-1430-000-00001`)

---

## 2. MODELOS (app/Models/)

### 2.1 Organismo
```php
// app/Models/Organismo.php
protected $fillable = ['codigo', 'nombre', 'code_min', 'code_max'];
// - code_min/code_max: Definen el rango de códigos para unidades
```

### 2.2 UnidadAdministradora
```php
// app/Models/UnidadAdministradora.php
protected $fillable = ['organismo_id', 'codigo', 'nombre', 'code_min', 'code_max'];
// - code_min/code_max: Definen el rango de códigos para dependencias
```

### 2.3 Dependencia
```php
// app/Models/Dependencia.php
protected $fillable = ['unidad_administradora_id', 'codigo', 'nombre', 'responsable_id', 'code_min', 'code_max'];
// - code_min = 1 (siempre inicia en 1)
// - code_max: Máximo 99999 bienes por dependencia
```

### 2.4 Bien
```php
// app/Models/Bien.php
protected $fillable = ['dependencia_id', 'codigo', 'descripcion', 'precio', 'fotografia', 'estado', 'fecha_registro', 'tipo_bien', 'caracteristicas'];
// - codigo: Campo de 8 dígitos con formato XXX-XXXX-XXX-XXXXX
```

---

## 3. SERVICIO PRINCIPAL (app/Services/CodigoUnicoService.php)

### 3.1 Clase: CodigoJerarquicoService

**Métodos de Generación Jerárquica:**

#### `generarCodigoBien(int $dependenciaId): array`
- Obtiene la jerarquía completa (organismo → unidad → dependencia)
- Calcula el siguiente secuencial disponible
- Retorna código completo con formato `XXX-XXXX-XXX-XXXXX`
- Usa `lockForUpdate()` para prevenir race conditions

#### `obtenerSiguienteSecuencial(int $dependenciaId): string`
- Busca el último bien registrado en la dependencia
- Extrae el secuencial (últimos 5 dígitos)
- Retorna secuencial + 1 formateado a 5 dígitos

#### `calcularDisponiblesRestantes(int $dependenciaId, string $secuencialActual): int`
- Calcula: `99999 - secuencialActual`
- Retorna cuántos códigos quedan disponibles

**Métodos de Reserva de Rangos:**

#### `reservarRangoOrganismo(int $organismoId, int $cantidadUnidades = 100): array`
- Fórmula: `base = codigo_organismo × 10000`
- Rango: `base + 1` a `base + cantidadUnidades`
- Ejemplo: Organismo 001 → rango 10001-10100 para unidades

#### `reservarRangoUnidad(int $unidadId, int $cantidadDependencias = 50): array`
- Fórmula: `base = codigo_unidad × 1000`
- Rango: `base + 1` a `base + cantidadDependencias`
- Valida que no exceda el rango del organismo padre

#### `reservarRangoDependencia(int $dependenciaId, ?int $limiteBienes = null): array`
- `code_min = 1` (inicia en 1)
- `code_max = limiteBienes` (máximo 99999)

**Métodos de Validación:**

#### `validarCodigoBien(string $codigo): array`
- Valida formato regex: `/^(\d{3})-(\d{4})-(\d{3})-(\d{5})$/`
- Verifica jerarquía existe (organismo → unidad → dependencia)
- Retorna si el código está disponible

#### `sugerirCodigoParaDependencia(int $dependenciaId): array`
- Wrapper de `generarCodigoBien()` con manejo de errores
- Fallback sin transacción si falla

#### `obtenerEstadisticasDependencia(int $dependenciaId): array`
- Total de bienes registrados
- Último secuencial usado
- Porcentaje de uso del rango
- Estado de agotamiento

#### `codigoDisponible(string $codigo): bool`
- Combina validación de formato y existencia

---

## 4. CONTROLADORES

### 4.1 BienController (app/Http/Controllers/BienController.php)

#### `recomendarCodigo(Dependencia $dependencia)`
- Endpoint: `GET /bienes/{dependencia}/recomendar-codigo`
- Llama a `CodigoUnicoService::recomendarSiguienteCodigoParaDependencia()`
- Retorna JSON con código sugerido y estadísticas

#### Validación en `store()` y `update()`
```php
// Regla personalizada para validar código único
'codigo' => [
    'required',
    'string',
    'size:8',
    'regex:/^\d{8}$/',
    function ($attribute, $value, $fail) {
        if (CodigoUnicoService::codigoExiste($value)) {
            $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
            $fail("El código '{$value}' ya está asignado...");
        }
    }
]
```

#### `validarCodigoEnRango(string $codigo, int $dependenciaId)`
- Verifica que el código esté dentro del `code_min`/`code_max` de la dependencia

### 4.2 OrganismoController

#### `create()`
- Obtiene código sugerido con `CodigoUnicoService::obtenerSiguienteCodigo()`

#### `store()`
- Después de crear organismo, ejecuta `reservarCodigosParaOrganismo($organismo->id, 50)`

### 4.3 UnidadAdministradoraController

#### `create()`
- Calcula código sugerido basado en organismo
- Busca existentes con `codigoExiste()` para evitar colisiones

#### `store()`
- Ejecuta `reservarCodigosParaOrganismo($organismo->id, 50)`
- Ejecuta `reservarCodigosParaUnidad($unidad->id, 50)`

---

## 5. VISTAS

### 5.1 bienes/create.blade.php

**Funcionalidad JavaScript:**
```javascript
// Al cambiar dependencia:
depSelect.addEventListener('change', function() {
    // 1. Limpiar campo código
    // 2. Ocultar sugerencia
    // 3. Llamar a /bienes/{dependencia}/recomendar-codigo
    // 4. Mostrar botón con código sugerido
});

// Al hacer clic en botón de sugerencia:
btnSugerencia.addEventListener('click', function() {
    // Asigna código sugerido al campo
});
```

---

## 6. MIGRACIONES RELEVANTES

### 6.1 add_code_range_to_organismos_and_unidades_tables.php
```php
Schema::table('organismos', function (Blueprint $table) {
    $table->integer('code_min')->default(1);
    $table->integer('code_max')->default(50);
});
```

### 6.2 add_code_range_to_dependencias_table.php
```php
Schema::table('dependencias', function (Blueprint $table) {
    $table->integer('code_min')->default(1);  // Siempre 1
    $table->integer('code_max')->default(99999);  // Máximo bienes
});
```

---

## 7. FLUJO DE RESERVA Y ASIGNACIÓN

### 7.1 Creación de Organismo
```
1. Usuario ingresa código manualmente
2. Se valida unicidad con codigoExiste()
3. Se crea el organismo
4. Se llama reservarCodigosParaOrganismo() para reservar rango de unidades
```

### 7.2 Creación de Unidad Administradora
```
1. Usuario elige organismo
2. Sistema calcula código sugerido = codigo_organismo × 10000 + 1
3. Se verifica que esté dentro del rango del organismo
4. Se crea la unidad
5. Se llama reservarCodigosParaUnidad() para reservar rango de dependencias
```

### 7.3 Creación de Dependencia
```
1. Se crea la dependencia con codigo_min=1, codigo_max=99999 (por defecto)
2. Opcionalmente se llama reservarRangoDependencia() con límite personalizado
```

### 7.4 Creación de Bien
```
1. Usuario selecciona dependencia
2. JavaScript llama recomendarCodigo() vía AJAX
3. Servicio busca último bien y calcula secuencial + 1
4. Formulario muestra código sugerido
5. Usuario acepta o escribe manualmente
6. Validación verifica rango y unicidad
7. Se guarda el bien
```

---

## 8. CONSIDERACIONES TÉCNICAS

### 8.1 Concurrencia
- `lockForUpdate()` en consultas dentro de transacciones
- Operaciones atómicas con `DB::transaction()`
- Previene race conditions en generación de códigos

### 8.2 Formato de Códigos
| Entidad | Longitud | Formato |
|---------|----------|---------|
| Organismo | 8 dígitos | Numérico puro |
| Unidad | 8 dígitos | Numérico puro |
| Dependencia | 8 dígitos | Numérico puro |
| Bien | 19 chars (con guiones) | `XXX-XXXX-XXX-XXXXX` |

### 8.3 Límites por Nivel

| Nivel | Límite | Notas |
|-------|--------|-------|
| Unidades por Organismo | 50 (por defecto) | Configurable via `code_max` |
| Dependencias por Unidad | 50 (por defecto) | Configurable via `code_max` |
| Bienes por Dependencia | 99,999 | Límite hardcodeado |

### 8.4 Manejo de Colisiones
- Los tests indican que si hay colisión por inserción manual, el servicio usa recursividad
- Búsqueda del siguiente código disponible si el calculado está ocupado

---

## 9. MÉTODOS FALTANTES (NOTA DE IMPLEMENTACIÓN)

Según los controladores, estos métodos deben existir en `CodigoUnicoService`:

```php
// RECOMENDADO AÑADIR AL SERVICIO:
public static function recomendarSiguienteCodigoParaDependencia(int $dependenciaId): array
{
    $dependencia = Dependencia::with(['unidadAdministradora.organismo'])
        ->lockForUpdate()
        ->findOrFail($dependenciaId);
    
    $siguiente = max(
        $dependencia->code_min,
        Bien::where('dependencia_id', $dependenciaId)->max('codigo') + 1
    );
    
    // Verificar rango
    if ($siguiente > $dependencia->code_max) {
        throw new RuntimeException("Rango de códigos exhausto. Rango: {$dependencia->code_min}-{$dependencia->code_max}");
    }
    
    $orgCode = str_pad($dependencia->unidadAdministradora->organismo->codigo, 3, '0', STR_PAD_LEFT);
    $uaCode = str_pad($dependencia->unidadAdministradora->codigo, 4, '0', STR_PAD_LEFT);
    $depCode = str_pad($dependencia->codigo, 3, '0', STR_PAD_LEFT);
    
    return [
        'codigo' => "{$orgCode}-{$uaCode}-{$depCode}-" . str_pad($siguiente, 5, '0', STR_PAD_LEFT),
        'siguiente_numero' => $siguiente,
        'rango_min' => $dependencia->code_min,
        'rango_max' => $dependencia->code_max,
        'disponibles_restantes' => $dependencia->code_max - $siguiente + 1,
        'dependencia' => $dependencia,
    ];
}

public static function codigoExiste(string $codigo, ?string $tablaIgnorar = null, ?int $idIgnorar = null): bool
{
    // Verificar en organismos, unidades, dependencias y bienes
    // Ignorar según parámetros si es para actualización
}

public static function obtenerUbicacionCodigo(string $codigo): array
{
    // Retornar en qué tabla y registro existe el código
}

public static function reservarCodigosParaOrganismo(int $organismoId, int $cantidadUnidades): void
{
    self::reservarRangoOrganismo($organismoId, $cantidadUnidades);
}

public static function reservarCodigosParaUnidad(int $unidadId, int $cantidadDependencias): void
{
    self::reservarRangoUnidad($unidadId, $cantidadDependencias);
}

public static function obtenerSiguienteCodigo(): string
{
    // Obtener el siguiente código disponible a nivel de organismo
    // Busca en Organismo máximo código + 1
}
```

---

## 10. PRUEBAS UNITARIAS (tests/Unit/Services/CodigoUnicoServiceTest.php)

Las pruebas cubren:
- `test_recomendar_siguiente_codigo_con_dependencia_vacia_devuelve_code_min()`
- `test_recomendar_siguiente_codigo_con_codigos_existentes_devuelve_max_mas_uno()`
- `test_recomendar_siguiente_codigo_con_rango_exhausto_lanza_excepcion()`
- `test_formato_codigo_ocho_digitos_con_ceros_a_la_izquierda()`
- `test_rangos_por_defecto_cuando_no_se_especifican()`
- `test_acceso_concurrente_maneja_condiciones_de_carrera()`
- `test_manejo_de_colision_de_codigo_con_recursividad()`
- `test_min_debajo_del_max_existente_devuelve_max_mas_uno()`

---

## 11. RESUMEN DE ARCHIVOS CLAVE

| Archivo | Función |
|---------|---------|
| `app/Services/CodigoUnicoService.php` | Lógica de generación y reserva de códigos |
| `app/Http/Controllers/BienController.php` | Endpoint `/recomendar-codigo`, validación en store/update |
| `app/Http/Controllers/OrganismoController.php` | Sugerencia en create, reserva en store |
| `app/Http/Controllers/UnidadAdministradoraController.php` | Sugerencia y reserva de códigos |
| `resources/views/bienes/create.blade.php` | Interfaz con botón de código sugerido |
| `tests/Unit/Services/CodigoUnicoServiceTest.php` | Pruebas unitarias completas |
| `database/migrations/*_add_code_range_*.php` | Agregan columnas code_min/code_max |