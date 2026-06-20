# Plan de Artefactos RUP - Sistema de Gestión de Inventario de Bienes

## Contexto del Proyecto
- **Duración estimada**: 7 meses (fase de análisis y codificación)
- **Tipo**: Proyecto de titulación para Técnico Superior en Informática
- **Alcance**: Limitado a lo implementado actualmente en el proyecto
- **Framework**: Laravel 12, PHP 8.2, Blade, Tailwind CSS, SQLite

## Artefactos RUP Existentes (en docs/rup/) - COMPLETADOS

| Nº | Archivo | Estado | Comentario |
|---|--------|--------|------------|
| 1 | `VISION_RUP.md` | ✅ Completo | Documento de visión detallado |
| 2 | `Business_Case_RUP.md` | ✅ Completo | Caso de negocio y ROI |
| 3 | `Glosario_RUP.md` | ✅ Completo | Términos del dominio patrimonial |
| 4 | `requirements.md` | ✅ Completo | Requisitos funcionales/no funcionales |
| 5 | `use_cases.md` | ✅ Completo | Modelo de casos de uso |
| 6 | `use_case_descriptions.md` | ✅ Completo | Descripciones resumidas |
| 7 | `01-Inception.md` | ✅ Completo | Fase de inicio |
| 8 | `02-Elaboration.md` | ✅ Completo | Fase de elaboración |
| 9 | `03-Construction.md` | ✅ Completo | Fase de construcción |
| 10 | `SAD_RUP.md` | ✅ Completo | Arquitectura de software |
| 11 | `er_diagram.md` | ✅ Completo | Diagrama ER básico |
| 12 | `class_diagram.puml` | ✅ Completo | Diagrama de clases básico |
| 13 | `Modelo_Pruebas_RUP.md` | ✅ Completo | Estrategia de pruebas |
| 14 | `Modelo_Despliegue_RUP.md` | ✅ Completo | Modelo de despliegue |
| 15 | `Manual_Usuario_RUP.md` | ✅ Completo | Manual resumido |

## Artefactos RUP a Generar/Actualizar

### 1. Modelo_Datos_RUP.md - COMPLETO (refinar ER existente)
**Tablas identificadas en migraciones:**
- `organismos` (id, codigo, nombre, timestamps)
- `unidades_administradoras` (id, organismo_id, codigo, nombre, timestamps)
- `dependencias` (id, unidad_administradora_id, responsable_id, codigo, nombre, timestamps)
- `bienes` (id, dependencia_id, codigo, descripcion, precio, fotografia, estado, fecha_registro, timestamps)
- `bienes_electronicos` (id, bien_id, subtipo, procesador, memoria, almacenamiento, pantalla, serial, garantia)
- `bienes_mobiliarios` (id, bien_id, material, dimensiones, color, capacidad, cantidad_piezas, acabado)
- `bienes_vehiculos` (id, bien_id, marca, modelo, anio, placa, motor, chasis, combustible, kilometraje)
- `bienes_otros` (id, bien_id, especificaciones, cantidad, presentacion)
- `bienes_desincorporados` (id, bien_id, dependencia_id, responsable_id, codigo, descripcion, precio, motivo_desincorporacion, acta_desincorporacion, timestamps)
- `usuarios` (id, rol_id, cedula, nombre, apellido, foto_perfil, correo, hash_password, activo, is_admin, timestamps)
- `roles` (id, nombre, permisos)
- `responsables` (id, tipo_id, cedula, nombre, correo, telefono)
- `tipos_responsables` (id, nombre)
- `movimientos` (id, bien_id, subject_type, subject_id, tipo, fecha, observaciones, usuario_id, acta_path)
- `historial_movimientos` (id, movimiento_id, campo, valor_anterior, valor_nuevo, timestamps)
- `auditoria` (id, usuario_id, tabla, registro_id, operacion, valores_anteriores, valores_nuevos, descripcion, ip_address, user_agent, created_at)
- `reportes` (id, usuario_id, titulo, tipo, datos_filtro, archivo_path, created_at)
- `eliminados` (id, model_type, model_id, data, deleted_by, deleted_at)

### 2. Riesgos_Iniciales_RUP.md - GENERAR
**Riesgos identificados del código:**
- RT-01: Datos históricos inconsistentes (mala calidad de importación)
- RT-02: Códigos duplicados o fuera de rango jerárquico
- RT-03: Pérdida de información por falta de respaldo
- RT-04: Resistencia al cambio por parte de usuarios
- RT-05: Errores en generación de reportes PDF
- RT-06: Fallos en transferencia de bienes entre dependencias

### 3. Plan_Desarrollo_Preliminar_RUP.md - GENERAR
**Cronograma 7 meses (aprox. 30 semanas):**
- Semanas 1-4: Inception (visión, requisitos, riesgos, stakeholders)
- Semanas 5-12: Elaboration (arquitectura, diseño, prototipo funcional)
- Semanas 13-30: Construction (desarrollo iterativo, pruebas, despliegue, documentación)

### 4. Casos_de_Uso_Detallado_RUP.md - GENERAR
**Casos de uso con flujos completos:**
- CU-01: Autenticarse (credenciales `usuarios.correo`/`hash_password`)
- CU-02: Gestionar Organismos (CRUD con validación de código)
- CU-03: Gestionar Unidades Administradoras (CRUD con FK a organismo)
- CU-04: Gestionar Dependencias (CRUD con FK a unidad y responsable)
- CU-05: Registrar Bien (con código jerárquico automático)
- CU-06: Consultar Bien (filtro múltiple por jerarquía)
- CU-07: Editar Bien (actualización con sync de subtipos)
- CU-08: Transferir Bien (traslado con movimiento)
- CU-09: Desincorporar Bien (generación de acta)
- CU-10: Reincorporar Bien (cambio de estado + movimiento)
- CU-11: Registrar Movimiento (polimórfico con subject_type)
- CU-12: Generar Reporte PDF (filtrado por organismo/unidad/dependencia)
- CU-13: Importar/Exportar Excel (BienesImport/BienesExcelController)
- CU-14: Ver Historial de Movimientos (consulta cronológica)
- CU-15: Consultar Auditoría (búsqueda por tabla/usuario/fecha)

### 5. Modelo_Analisis_Disenio_RUP.md - GENERAR
**Componentes del sistema:**
- Modelos: Organismo, UnidadAdministradora, Dependencia, Bien, Usuario, Rol, Responsable, Movimiento, Auditoria, Reporte, Eliminado, HistorialMovimiento, TipoResponsable
- Subtipos Bien: BienElectronico, BienMobiliario, BienVehiculo, BienOtro, BienDesincorporado
- Enums: EstadoBien, TipoBien
- Traits: AuditableTrait (auditoría automática), GeneratesMovimiento
- Servicios: CodigoJerarquicoService, BienTypeService, ActaDonacionService, ActaDesincorporacionService, ActaTrasladoService, FpdfReportService, EliminadosService, MovimientoService

### 6. iteration_plan.md - GENERAR
**7 iteraciones × ~4 semanas:**
- Iteración 1: Infraestructura, auth, organismos
- Iteración 2: Unidades administradoras, dependencias
- Iteración 3: Registro básico de bienes (CRUD)
- Iteración 4: Tipos de bienes (electrónicos, muebles, vehículos, otros)
- Iteración 5: Movimientos, traslados, desincorporación
- Iteración 6: Reportes PDF y exportación Excel
- Iteración 7: Auditoría, pruebas, documentación final

### 7. traceability.md - GENERAR
**Matriz trazabilidad:**
| RF | CU | Clases | Tests |
|----|----|--------|-------|
| RF-01 | CU-01 | Usuario, Rol | BienesPdfReportTest, CodigoJerarquicoServiceTest |
| RF-02 | CU-02 | Organismo | Implícito en controladores |
| RF-03 | CU-03 | UnidadAdministradora | Implícito en controladores |
| RF-04 | CU-05 | Dependencia, Bien, CodigoJerarquicoService | BienesTypeCreationTest |
| RF-05-07 | CU-05,06,07 | Bien, BienElectronico, BienMobiliario, BienVehiculo, BienOtro | BienesTypeCreationTest |
| RF-08 | CU-04 | Responsable | Implícito en controladores |
| RF-09-11 | CU-11,12,14 | Movimiento, HistorialMovimiento | Implícito |
| RF-12-13 | CU-13 | BienesImport | Implícito en BienExcelController |
| RF-14 | CU-15 | Auditoria | Implícito en observers |
| RF-15-17 | CU-09,10,08 | BienDesincorporado, ActaDesincorporacionService, ActaTrasladoService | Implícito |

## Diagramas a Actualizar

### class_diagram.puml (PlantUML)
**Contenido a agregar:**
```plantuml
enum EstadoBien { ACTIVO, DANADO, EN_MANTENIMIENTO, EN_CAMINO, EXTRAVIADO, DESINCORPORADO }
enum TipoBien { ELECTRONICO, MOBILIARIO, VEHICULO, OTROS }

trait AuditableTrait { registraOperacion(), scopeSinEliminados() }
trait GeneratesMovimiento { creaMovimientoAutomatico() }

class CodigoJerarquicoService { generarCodigoOrganismo(), generarCodigoUnidad(), generarCodigoDependencia(), generarCodigoBien(), formatearCodigoLegible(), validarJerarquia() }
class BienTypeService { sync() }
class ActaDonacionService { generar() }
class ActaDesincorporacionService { generar() }
class ActaTrasladoService { generar() }
class FpdfReportService { renderHeader(), generarReporte() }
class MovimientoService { registrar() }
class EliminadosService { archive() }
```

### er_diagram.md (Mermaid)
**Actualizar con columnas exactas:**

```mermaid
erDiagram
    ORGANISMOS {
        bigint id PK
        varchar codigo UK
        varchar nombre
        timestamp created_at
        timestamp updated_at
    }
    UNIDADES_ADMINISTRADORAS {
        bigint id PK
        bigint organismo_id FK
        varchar codigo
        varchar nombre
        timestamp created_at
        timestamp updated_at
    }
    // ... todas las tablas con sus columnas exactas
```

## Estado Final del Plan

Todos los artefactos RUP están identificados. Los documentos existentes en `docs/rup/` cubren la mayoría de los requerimientos metodológicos. Los archivos que requieren generación adicional son:
- Riesgos_Iniciales_RUP.md (tabla de riesgos con mitigaciones)
- Plan_Desarrollo_Preliminar_RUP.md (cronograma 7 meses)
- Casos_de_Uso_Detallado_RUP.md (flujos completos por CU)
- Modelo_Analisis_Disenio_RUP.md (componentes y responsabilidades)
- iteration_plan.md (7 iteraciones con entregables)
- traceability.md (matriz RF→CU→Clases→Tests)

Los diagramas existentes (`class_diagram.puml`, `er_diagram.md`) deben actualizarse con información más detallada identificada en el análisis del código.