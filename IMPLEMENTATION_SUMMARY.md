# Sistema de Gestión de Inventario de Bienes - Documentación de Implementación

## Fecha: 2026-04-27

---

## Resumen Ejecutivo

Este documento describe la implementación del **Sistema de Gestión de Inventario de Bienes**, una aplicación web desarrollada con **Laravel 12** para la administración integral de activos físicos (bienes) pertenecientes a organismos gubernamentales o institucionales.

El sistema implementa una arquitectura jerárquica de cuatro niveles: **Organismo → Unidad Administradora → Dependencia → Bien**, con trazabilidad completa de movimientos y auditoría de cambios.

---

## 1. Arquitectura Tecnológica

### 1.1 Stack Principal
- **Framework**: Laravel 12
- **Lenguaje**: PHP 8.2+
- **Base de Datos**: SQLite (database.sqlite)
- **Motor de Plantillas**: Blade
- **Frontend**: Tailwind CSS, Vite
- **Generación de PDFs**: DomPDF (barryvdh/laravel-dompdf), FPDF

### 1.2 Paquetes y Dependencias Clave
- **barryvdh/laravel-dompdf**: Generación de reportes PDF en HTML
- **blade-ui-kit/blade-heroicons**: Componentes de iconos
- **doctrine/dbal**: Manipulación de esquemas de base de datos
- **maatwebsite/excel**: Importación/Exportación Excel
- **setasign/fpdf**: Generación de PDFs personalizados
- **laravel/pint**: Formateo de código
- **phpunit/phpunit**: Pruebas unitarias

---

## 2. Modelo de Datos

### 2.1 Entidades Principales

#### Organismo (Nivel 1)
- Tabla: organismos
- Atributos: codigo, nombre
- Relación: 1 → N Unidades Administradoras

#### UnidadAdministradora (Nivel 2)
- Tabla: unidades_administradoras
- Atributos: codigo, nombre, organismo_id, responsable_id
- Relación: N → 1 Organismo, 1 → N Dependencias

#### Dependencia (Nivel 3)
- Tabla: dependencias
- Atributos: codigo, nombre, unidad_administradora_id, responsable_id, code_min, code_max
- **Novedad**: Rangos de códigos (code_min, code_max) para validación de códigos secuenciales
- Relación: N → 1 Unidad, 1 → N Bienes, N → 1 Responsable

#### Bien (Entidad Central - Nivel 4)
- Tabla: bienes
- Atributos:
  - Básicos: codigo (8 dígitos), descripcion, precio, fotografia, ubicacion, fecha_registro
  - Estado: estado (Enum: ACTIVO, DAÑADO, EN_MANTENIMIENTO, EN_CAMINO, EXTRAVIADO, DESINCORPORADO)
  - Tipo: tipo_bien (Enum: ELECTRONICO, INMUEBLE, MOBILIARIO, VEHICULO, OTROS)
  - Características: caracteristicas (JSON polimórfico)
- Relaciones:
  - N → 1 Dependencia
  - 1 → N Movimientos
  - 1 → 1 BienElectronico/Mobiliario/Vehiculo/Otro (polimorfismo por tipo)

#### Responsable
- Tabla: responsables
- Atributos: cedula, nombre, correo, telefono, tipo
- Asignado a Dependencias (no directamente a Bienes)

#### Usuario
- Tabla: usuarios
- Atributos: cedula, nombre, apellido, correo, hash_password, rol, activo, admin, foto_perfil
- Autenticación personalizada (no usa Laravel Breeze/UI por defecto)

#### Movimiento
- Tabla: movimientos
- Registra todas las acciones sobre bienes
- Atributos: bien_id, subject_type, subject_id, tipo, fecha, observaciones, usuario_id
- Soporte para polimorfismo (movimientos de cualquier entidad)

#### BienDesincorporado
- Tabla: bienes_desincorporados
- Almacena bienes dados de baja
- Mantiene el codigo original para evitar reutilización

---

## 3. Patrones de Diseño y Arquitectura

### 3.1 Service Layer Pattern

#### CodigoUnicoService
- **Responsabilidad**: Generación de códigos únicos secuenciales
- **Métodos clave**:
  - `obtenerSiguienteCodigo()`: Busca huecos globales en todas las tablas
  - `recomendarSiguienteCodigoParaDependencia()`: Genera código secuencial por dependencia con control de concurrencia
  - **Características**:
    - Transacciones con `DB::transaction()`
    - Locking pesimista (`lockForUpdate()`)
    - Validación de rangos (`code_min`, `code_max`)
    - Reintentos automáticos (3 intentos para deadlocks)

#### BienTypeService
- **Responsabilidad**: Sincronización de atributos específicos por tipo de bien
- **Polimorfismo**: Crea/actualiza registros en tablas específicas:
  - `bienes_electronicos`: subtipo, procesador, memoria, etc.
  - `bienes_vehiculos`: marca, modelo, placa, etc.
  - `bienes_mobiliarios`: material, dimensiones, etc.
  - `bienes_otros`: especificaciones, cantidad
- **Normalización**: Convierte strings vacíos a `null`

#### MovimientoService
- **Responsabilidad**: Registro centralizado de movimientos
- **Resolución de usuario**: Maneja múltiples fuentes (Auth::user(), Auth::id(), email)
- **Tolerancia a fallos**: Registra sin error si no hay usuario autenticado

#### FpdfReportService
- **Responsabilidad**: Generación de reportes PDF
- **Variantes de reporte**:
  - `downloadBienesListado()`: Listado vertical estándar
  - `generarPorDependencia()`: Agrupado por dependencia
  - `generarPorUnidad()`: Agrupado por unidad administrativa
  - `generarPorOrganismo()`: Agrupado por organismo
  - `generarPorTipo()`: Agrupado por tipo de bien
  - `generarPorEstado()`: Agrupado por estado
  - `generarPorFecha()`: Por rango de fechas
- **Características**:
  - Encabezado con banner institucional
  - Pie de página con número de página
  - Tablas formateadas con colores, bordes, subtotales

---

## 4. Observer Pattern

#### ModelObserver
- Escucha: Eventos created, updated, deleting de modelos Eloquent
- Registro automático de movimientos:
  - Detalle de cambios (old → new) en actualizaciones
  - Historial de movimientos para auditoría detallada
- Campo especial: _observaciones permite pasar texto personalizado desde controladores

#### GeneratesMovimiento (Trait)
- Aplicado a: Dependencia, Responsable, Organismo, UnidadAdministradora
- Registra: Movimientos automáticos en create/update/delete
- Exclusión: Bien (tiene lógica especial en observer)

#### AuditableTrait
- Aplicado a: Todos los modelos principales
- Registra en tabla auditoria:
  - CREATE: Guarda todos los atributos nuevos
  - UPDATE: Guarda valores anteriores y cambios
  - DELETE: Guarda snapshot completo antes de eliminar

---

## 5. Controladores y Rutas

### Rutas Protegidas Clave
- GET /bienes: Listado con filtros avanzados
- GET /bienes/{bien}/pdf: Exportar PDF individual
- GET /bienes/importar: Importación Excel
- POST /bienes/desincorporar: Baja de bien (genera Acta)
- PATCH /bienes/transferir: Transferencia entre dependencias
- GET /reportes/pdf/{tipo}: Generar PDF específico
- GET /auditoria: Historial de cambios (solo admin)

### Controladores Clave
- **BienController**: Filtros avanzados, recomendación de códigos, desincorporación, transferencia
- **AuthController**: Autenticación personalizada
- **SearchController**: Búsqueda global
- **DashboardController**: Estadísticas y alertas

---

## 6. Middleware

- **auth**: Verifica sesión activa
- **redirigir.rol**: Redirige según rol (admin/user)
- **prevent-back**: Evita navegación por historial post-logout
- **is-admin**: Verifica admin == 1 para rutas críticas

---

## 7. Sistema de Autenticación

- Tabla personalizada: `usuarios` (cedula, correo, hash_password, rol, admin, foto_perfil)
- No usa tabla users ni modelo User por defecto de Laravel
- Password hashing: Bcrypt
- Session driver: File

---

## 8. Validaciones y Reglas de Negocio

### Códigos de Bienes
- Formato: 8 dígitos numéricos (ceros a la izquierda)
- Secuencialidad: Por dependencia
- Rangos: code_min y code_max por dependencia
- Validación: CodigoUnicoService::codigoExiste()

### Estados y Tipos
- EstadoBien: ACTIVO, DAÑADO, EN_MANTENIMIENTO, EN_CAMINO, EXTRAVIADO, DESINCORPORADO
- TipoBien: ELECTRONICO, INMUEBLE, MOBILIARIO, VEHICULO, OTROS

---

## 9. Reportes

### Formatos
- PDF: DomPDF, FPDF
- Excel: Maatwebsite Excel

### Tipos de Reportes
- Individual, Listado General, Por Dependencia, Por Unidad, Por Organismo, Por Tipo, Por Estado, Por Fecha
- Acta Desincorporación, Acta Traslado

---

## 10. Auditoría y Trazabilidad

### Tablas
- **auditoria**: Bitácora completa (CREATE/UPDATE/DELETE) con valores anteriores/nuevos
- **movimientos**: Acciones sobre bienes
- **historial_movimientos**: Detalle de actualizaciones de movimientos
- **eliminados**: Soft-deletes con información

### Automatización
- AuditableTrait: Registra automáticamente en create/update/delete
- ModelObserver: Registra movimientos con detalle de cambios

---

## 11. Migraciones Destacadas

- 2026_04_27_125410_add_code_range_to_dependencias_table: Añade code_min, code_max
- 2026_03_13_000000_expand_bienes_desincorporados_table: Expande tabla de desincorporados
- 2026_02_17_000001_normalize_nullable_columns: Normaliza columnas anulables
- 2026_01_25_*: Creación de tablas específicas por tipo de bien
- 2025_11_05_000000_move_responsable_to_dependencias: Mueve responsable a dependencias

---

## 12. Flujo de Trabajo Típico

### Registro de Bien
1. Seleccionar dependencia
2. Sistema sugiere código (CodigoUnicoService)
3. Completar formulario
4. Guardar → Crea registro, sincroniza tipo, registra movimiento y auditoría

### Desincorporación
1. Acceder a desincorporar
2. Ingresar motivo y fecha
3. Confirmar → Actualiza estado, crea registro en bienes_desincorporados, genera Acta PDF

### Transferencia
1. Acceder a transferir
2. Seleccionar nueva dependencia
3. Confirmar → Actualiza dependencia_id, registra movimiento, genera Acta opcional

---

## 13. Conclusión

El Sistema de Gestión de Inventario de Bienes es una implementación sólida con arquitectura modular (Services, Traits, Observers) que cumple con los requerimientos de trazabilidad, auditoría y control de inventario para la gestión de activos gubernamentales. Destaca en trazabilidad completa, prevención de concurrencia en códigos, reportes flexibles y auditoría detallada.

--- documento generado - 27/04/2026 ---
