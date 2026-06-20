# Diagrama Entidad-Relación — Sistema de Gestión de Inventario de Bienes

> Generado a partir de las migraciones de base de datos y modelos Eloquent del proyecto.  
> Incluye cardinalidades, claves primarias (PK), claves foráneas (FK), índices relevantes y el polimorfismo de `movimientos`.

```mermaid
erDiagram
    ORGANISMOS {
        bigint id PK
        varchar(50) codigo UK
        varchar(150) nombre
        integer code_min
        integer code_max
        timestamp created_at
        timestamp updated_at
    }
    UNIDADES_ADMINISTRADORAS {
        bigint id PK
        bigint organismo_id FK
        varchar(50) codigo
        varchar(150) nombre
        integer code_min
        integer code_max
        timestamp created_at
        timestamp updated_at
        index idx_ua_organismo "organismo_id"
        unique uq_ua_org_codigo "organismo_id, codigo"
    }
    DEPENDENCIAS {
        bigint id PK
        bigint unidad_administradora_id FK
        bigint responsable_id FK "nullable"
        varchar(50) codigo
        varchar(150) nombre
        integer code_min
        integer code_max
        timestamp created_at
        timestamp updated_at
        index idx_dep_ua "unidad_administradora_id"
        index idx_dep_responsable "responsable_id"
        unique uq_dep_ua_codigo "unidad_administradora_id, codigo"
    }
    BIENES {
        bigint id PK
        bigint dependencia_id FK
        varchar(50) codigo UK
        varchar(255) descripcion
        decimal(15,2) precio "default 0"
        varchar(255) fotografia "nullable"
        varchar(255) ubicacion "nullable"
        varchar(50) estado "default ACTIVO"
        date fecha_registro "nullable"
        varchar(50) tipo_bien "default OTROS"
        json caracteristicas "nullable"
        boolean es_donacion "default false"
        varchar(50) tipo_donante "nullable"
        varchar(150) donante_nombre "nullable"
        varchar(50) donante_documento "nullable"
        varchar(255) donante_direccion "nullable"
        varchar(255) acta_donacion "nullable"
        timestamp created_at
        timestamp updated_at
        index idx_bien_estado "estado"
        index idx_bien_tipo_bien "tipo_bien"
        index idx_bien_fecha_registro "fecha_registro"
        index idx_bien_dep_estado "dependencia_id, estado"
    }
    BIENES_ELECTRONICOS {
        bigint id PK
        bigint bien_id FK "unique"
        varchar(20) subtipo "nullable"
        varchar(255) procesador "nullable"
        varchar(255) memoria "nullable"
        varchar(255) almacenamiento "nullable"
        varchar(255) pantalla "nullable"
        varchar(255) serial "nullable"
        date garantia "nullable"
        timestamp created_at
        timestamp updated_at
    }
    BIENES_MOBILIARIOS {
        bigint id PK
        bigint bien_id FK "unique"
        varchar(255) material "nullable"
        varchar(255) dimensiones "nullable"
        varchar(100) color "nullable"
        varchar(100) capacidad "nullable"
        integer cantidad_piezas "nullable"
        varchar(100) acabado "nullable"
        timestamp created_at
        timestamp updated_at
    }
    BIENES_VEHICULOS {
        bigint id PK
        bigint bien_id FK "unique"
        varchar(100) marca "nullable"
        varchar(100) modelo "nullable"
        varchar(10) anio "nullable"
        varchar(50) placa "nullable"
        varchar(100) motor "nullable"
        varchar(100) chasis "nullable"
        varchar(50) combustible "nullable"
        varchar(50) kilometraje "nullable"
        timestamp created_at
        timestamp updated_at
    }
    BIENES_OTROS {
        bigint id PK
        bigint bien_id FK "unique"
        text especificaciones "nullable"
        integer cantidad "nullable"
        varchar(255) presentacion "nullable"
        timestamp created_at
        timestamp updated_at
    }
    BIENES_DESINCORPORADOS {
        bigint id PK
        bigint bien_id FK "unique"
        bigint dependencia_id FK "nullable"
        bigint responsable_id FK "nullable"
        varchar(50) codigo
        varchar(255) descripcion
        decimal(15,2) precio "default 0"
        varchar(255) fotografia "nullable"
        varchar(255) ubicacion "nullable"
        varchar(50) estado "default ACTIVO"
        date fecha_registro "nullable"
        varchar(50) tipo_bien "nullable"
        json caracteristicas "nullable"
        varchar(500) motivo_desincorporacion
        varchar(500) acta_desincorporacion "nullable"
        timestamp fecha_desincorporacion "useCurrent"
        timestamp created_at
        timestamp updated_at
    }
    USUARIOS {
        bigint id PK
        bigint rol_id FK
        varchar(20) cedula UK
        varchar(150) nombre
        varchar(150) apellido
        varchar(255) foto_perfil "nullable"
        varchar(150) correo UK
        varchar(255) hash_password "nullable"
        boolean activo "default true"
        boolean is_admin
        timestamp created_at
        timestamp updated_at
        index idx_usuario_rol "rol_id"
    }
    ROLES {
        bigint id PK
        varchar(80) nombre UK
        json permisos "nullable"
    }
    RESPONSABLES {
        bigint id PK
        bigint tipo_id FK
        varchar(20) cedula UK
        varchar(150) nombre
        varchar(150) correo "nullable"
        varchar(50) telefono "nullable"
        index idx_responsable_tipo "tipo_id"
    }
    TIPOS_RESPONSABLES {
        bigint id PK
        varchar(80) nombre UK
    }
    MOVIMIENTOS {
        bigint id PK
        bigint bien_id FK "nullable, nullOnDelete"
        varchar(255) subject_type "nullable, polimórfico"
        unsignedBigInteger subject_id "nullable, polimórfico"
        varchar(80) tipo
        timestamp fecha "useCurrent"
        text observaciones "nullable"
        bigint usuario_id FK "nullable, nullOnDelete"
        varchar(255) descripcion
        varchar(255) acta_path
        index idx_mov_bien "bien_id"
        index idx_mov_fecha "fecha"
        index idx_mov_subject "subject_type, subject_id"
    }
    HISTORIAL_MOVIMIENTOS {
        bigint id PK
        bigint movimiento_id FK
        timestamp fecha "useCurrent"
        text detalle
        timestamp created_at
        timestamp updated_at
        index idx_hist_mov "movimiento_id"
    }
    REPORTES {
        bigint id PK
        bigint usuario_id FK "nullable, nullOnDelete"
        varchar(80) tipo
        timestamp fecha_generado "useCurrent"
        varchar(255) archivo_pdf_path "nullable"
        timestamp created_at
        timestamp updated_at
        index idx_reporte_usuario "usuario_id"
    }
    AUDITORIA {
        bigint id PK
        bigint usuario_id FK "nullable, nullOnDelete"
        varchar(100) tabla
        unsignedBigInteger registro_id
        enum operacion "CREATE | UPDATE | DELETE"
        json valores_anteriores "nullable"
        json valores_nuevos "nullable"
        text descripcion "nullable"
        varchar(45) ip_address "nullable"
        text user_agent "nullable"
        timestamp created_at "useCurrent"
        index idx_auditoria_tabla_registro "tabla, registro_id"
        index idx_auditoria_usuario_fecha "usuario_id, created_at"
        index idx_auditoria_tabla_operacion "tabla, operacion"
        index idx_auditoria_fecha "created_at"
    }
    ELIMINADOS {
        bigint id PK
        varchar(255) model_type
        unsignedBigInteger model_id "nullable"
        json data "nullable"
        bigint deleted_by FK "nullable, nullOnDelete"
        timestamp deleted_at "nullable"
        timestamp created_at
        timestamp updated_at
    }

    ORGANISMOS ||--o{ UNIDADES_ADMINISTRADORAS : "tiene"
    UNIDADES_ADMINISTRADORAS ||--o{ DEPENDENCIAS : "tiene"
    DEPENDENCIAS ||--o{ BIENES : "contiene"
    DEPENDENCIAS }|--|| RESPONSABLES : "tiene_asignado"
    TIPOS_RESPONSABLES ||--o{ RESPONSABLES : "clasifica"
    BIENES ||--o| BIENES_ELECTRONICOS : "posee_detalle"
    BIENES ||--o| BIENES_MOBILIARIOS : "posee_detalle"
    BIENES ||--o| BIENES_VEHICULOS : "posee_detalle"
    BIENES ||--o| BIENES_OTROS : "posee_detalle"
    BIENES ||--o| BIENES_DESINCORPORADOS : "genera"
    BIENES ||--o{ MOVIMIENTOS : "registra"
    RESPONSABLES ||--o{ MOVIMIENTOS : "subject_polimórfico"
    DEPENDENCIAS ||--o{ MOVIMIENTOS : "subject_polimórfico"
    USUARIOS ||--o{ MOVIMIENTOS : "ejecuta"
    MOVIMIENTOS ||--o{ HISTORIAL_MOVIMIENTOS : "contiene"
    ROLES ||--o{ USUARIOS : "asigna"
    USUARIOS ||--o{ REPORTES : "genera"
    USUARIOS ||--o{ AUDITORIA : "registra"
    USUARIOS ||--o{ ELIMINADOS : "elimina"
    BIENES ||--o{ ELIMINADOS : "eliminado"
```

**Notas sobre el diseño:**
- `movimientos.subject_type` / `movimientos.subject_id` implementan una relación polimórfica que permite que cualquier entidad del dominio (Organismo, UnidadAdministradora, Dependencia, Bien, Usuario, Responsable) sea el “sujeto” de un movimiento.
- `bienes_desincorporados` no es una soft-delete simple: almacena un snapshot completo del bien al momento de la desincorporación, incluyendo `fecha_desincorporacion` y datos jerárquicos copiados.
- `bienes` tiene `ubicacion` (columna heredada de migraciones tempranas) y campos de donación (`es_donacion` … `acta_donacion`).
- `responsables` no tiene timestamps; el vínculo con `dependencias` es opcional (`responsable_id` nullable).
