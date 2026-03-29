# Diagrama Entidad-Relación - Sistema de Gestión de Inventario de Bienes

## Vista General del Modelo de Datos

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                           SISTEMA DE GESTIÓN DE INVENTARIO DE BIENES                        │
│                        Universidad Politécnica Territorial de Oriente                         │
└─────────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐       ┌──────────────────────────┐       ┌─────────────────────┐
│     ORGANISMOS      │       │ UNIDADES ADMINISTRADORAS │       │    DEPENDENCIAS     │
├─────────────────────┤       ├──────────────────────────┤       ├─────────────────────┤
│ PK id               │ 1:N   │ PK id                    │ 1:N   │ PK id               │
│    codigo (UQ)     │───────│    organismo_id (FK)     │───────│    unidad_id (FK)   │
│    nombre          │       │    codigo                │       │    responsable_id   │
│    timestamps      │       │    nombre                │       │    codigo           │
└─────────────────────┘       │    timestamps           │       │    nombre           │
                              └──────────────────────────┘       │    timestamps       │
                                                                  └──────────┬──────────┘
                                                                             │
                                                                             │ 1:N
                                                                             ▼
┌─────────────────────┐       ┌──────────────────────────┐       ┌─────────────────────┐
│  TIPOS_RESPONSABLE │       │      RESPONSABLES        │       │       BIENES        │
├─────────────────────┤       ├──────────────────────────┤       ├─────────────────────┤
│ PK id               │ 1:N   │ PK id                    │       │ PK id               │
│    nombre (UQ)     │◄──────│    tipo_id (FK)         │       │    dependencia_id   │
└─────────────────────┘       │    cedula (UQ)          │ 1:1   │    codigo (UQ)      │
                              │    nombre               │◄──────│    descripcion      │
                              │    correo               │       │    precio           │
                              │    telefono            │       │    fotografia       │
                              └──────────────────────────┘       │    ubicacion        │
                                                                   │    estado           │
                                                                   │    fecha_registro   │
┌─────────────────────┐       ┌──────────────────────────┐       │    tipo_bien        │
│        ROLES        │       │       USUARIOS           │       │    caracteristicas  │
├─────────────────────┤       ├──────────────────────────┤       │    timestamps      │
│ PK id               │ 1:N   │ PK id                    │       └──────────┬──────────┘
│    nombre (UQ)     │◄──────│    rol_id (FK)           │                  │
│    permisos (JSON)  │       │    cedula (UQ)          │                  │
└─────────────────────┘       │    nombre               │                  │
                              │    apellido             │                  │
┌─────────────────────┐       │    foto_perfil         │ 1:1   ┌──────────┴──────────┐
│    BIENES           │       │    correo (UQ)         │◄──────│  BIENES_ELECTRONICOS │
│    ELECTRONICOS     │       │    hash_password       │       ├─────────────────────┤
├─────────────────────┤       │    activo              │       │ PK id               │
│ PK id               │       │    is_admin            │       │    bien_id (UQ,FK)  │
│    bien_id (UQ,FK) │◄──────│    timestamps          │       │    subtipo          │
│    subtipo          │       └──────────────────────────┘       │    procesador       │
│    procesador       │                                       │    memoria          │
│    memoria          │       ┌──────────────────────────┐       │    almacenamiento   │
│    almacenamiento   │       │       MOVIMIENTOS        │       │    pantalla         │
│    pantalla         │       ├──────────────────────────┤       │    serial           │
│    serial           │       │ PK id                   │       │    garantia         │
│    garantia         │       │    bien_id (FK) <<NULL>>│◄──────│    timestamps       │
│    timestamps       │       │    subject_type (Poly)  │       └─────────────────────┘
└─────────────────────┘       │    subject_id (Poly)    │
                              │    tipo                │       ┌─────────────────────┐
┌─────────────────────┐       │    fecha               │       │  BIENES_MOBILIARIOS │
│    BIENES            │       │    observaciones       │       ├─────────────────────┤
│    VEHICULOS        │       │    usuario_id (FK)     │◄──────│ PK id               │
├─────────────────────┤       │    descripcion         │       │    bien_id (UQ,FK)  │
│ PK id               │       │    acta_path           │       │    material         │
│    bien_id (UQ,FK)  │◄──────│    timestamps         │       │    dimensiones      │
│    marca            │       └──────────────────────────┘       │    color            │
│    modelo           │                                       │    timestamps       │
│    ano              │       ┌──────────────────────────┐       └─────────────────────┘
│    placa            │       │HISTORIAL_MOVIMIENTOS    │
│    serial_carroceria │       ├──────────────────────────┤       ┌─────────────────────┐
│    serial_motor     │       │ PK id                   │       │   BIENES_OTROS     │
│    timestamps       │       │    movimiento_id (FK)   │ 1:N   ├─────────────────────┤
└─────────────────────┘       │    bien_id (FK)         │◄──────│ PK id               │
                              │    tipo                │       │    bien_id (UQ,FK)  │
┌─────────────────────┐       │    descripcion         │       │    descripcion_     │
│BIENES_DESINCORPORAD│       │    fecha               │       │    adicional        │
├─────────────────────┤       └──────────────────────────┘       │    timestamps       │
│ PK id               │                                       └─────────────────────┘
│    bien_id (UQ,FK)  │◄──────┐
│    motivo           │       │
│    descripcion      │       │
│    fecha_desinc.    │       │       ┌─────────────────────┐
│    acta_path        │       │       │    ELIMINADOS       │
│    usuario_resp_id  │       │       ├─────────────────────┤
│    timestamps       │       │       │ PK id               │
└─────────────────────┘       │       │    bien_id (FK)    │◄──────┘
                              │       │    deleted_at       │
                              │       └─────────────────────┘
                              │
                              │       ┌─────────────────────┐
                              │       │     REPORTES        │
                              │       ├─────────────────────┤
                              └───────│ PK id               │
                                      │    usuario_id (FK)  │◄──────┐
                                      │    tipo            │       │
                                      │    parametros      │       │
                                      │    resultado       │       │
                                      │    generado_en     │       │
                                      └─────────────────────┘

┌─────────────────────┐
│     AUDITORIA       │
├─────────────────────┤
│ PK id               │
│    usuario_id (FK)  │◄──────┐
│    tabla            │       │
│    registro_id      │       │
│    operacion        │       │
│    valores_antes    │       │
│    valores_despues  │       │
│    descripcion      │       │
│    ip_address       │       │
│    user_agent       │       │
│    created_at       │       │
└─────────────────────┘
```

## Descripción de Tablas

### Tablas Principales (Jerarquía Organizacional)

| Tabla | Descripción | Claves |
|-------|-------------|--------|
| `organismos` | Entidades rectoras (ej: MPP Educación) | PK: id |
| `unidades_administradoras` | Instituciones/departamentos (ej: UPTOS) | PK: id, FK: organismo_id |
| `dependencias` | Salones, laboratorios, oficinas | PK: id, FK: unidad_id, FK: responsable_id |

### Tablas de Bienes

| Tabla | Descripción | Claves |
|-------|-------------|--------|
| `bienes` | Activos registrados | PK: id, FK: dependencia_id |
| `bienes_electronicos` | Características de bienes tecnológicos | FK: bien_id (UQ) |
| `bienes_mobiliarios` | Características de mobiliario | FK: bien_id (UQ) |
| `bienes_vehiculos` | Características de vehículos | FK: bien_id (UQ) |
| `bienes_otros` | Otros tipos de bienes | FK: bien_id (UQ) |
| `bienes_desincorporados` | Bienes dados de baja | FK: bien_id (UQ) |
| `eliminados` | Papelera de bienes | FK: bien_id |

### Tablas de Personas

| Tabla | Descripción | Claves |
|-------|-------------|--------|
| `usuarios` | Usuarios del sistema | PK: id, FK: rol_id |
| `responsables` | Responsables de bienes | PK: id, FK: tipo_id |
| `tipos_responsables` | Tipos de responsable | PK: id |
| `roles` | Roles de usuario | PK: id |

### Tablas de Movimientos

| Tabla | Descripción | Claves |
|-------|-------------|--------|
| `movimientos` | Trazabilidad de bienes | FK: bien_id, FK: usuario_id, Polymorphic: subject |
| `historial_movimientos` | Historia detallada | FK: movimiento_id, FK: bien_id |

### Tablas de Sistema

| Tabla | Descripción | Claves |
|-------|-------------|--------|
| `reportes` | Reportes generados | FK: usuario_id |
| `auditoria` | Log de operaciones | FK: usuario_id |

## Tipos de Relaciones

```
CARDINALIDAD:
  1:N  - Uno a muchos (un organismo tiene muchas unidades)
  1:1  - Uno a uno (un bien tiene una descripción de electrónico)
  N:M  - Muchos a muchos (no hay en este modelo)

TIPO:
  FK    - Foreign Key (clave foránea)
  PK    - Primary Key (clave primaria)
  UQ    - Unique (valor único)
  NULL  - Nullable (puede ser nulo)
  Poly  - Polymorphic (polimórfico)
```

## Estados de Bienes

Los bienes pueden tener los siguientes estados:
- **ACTIVO**: En uso institucional
- **DAÑADO**: Requiere reparación
- **EN_MANTENIMIENTO**: En taller técnico
- **EN_CAMINO**: En traslado
- **EXTRAVIADO**: Sin localización
- **DESINCORPORADO**: Dado de baja

## Tipos de Bien

- **ELECTRÓNICO**: Computadoras, impresoras, equipos de red
- **MOBILIARIO**: Escritorios, sillas, estantes
- **VEHÍCULO**: Automóviles, motos, camionetas
- **OTROS**: Herramientas, equipos agrícolas
