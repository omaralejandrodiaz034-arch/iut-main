# Fase de Construcción
## Diseño arquitectónico
- Patrón MVC (Laravel).
- Capa de presentación con Blade/Tailwind.
- Capa de negocio en controladores y servicios.
- Capa de persistencia con Eloquent y migraciones.
## Servicios relevantes
- `CodigoJerarquicoService`
- `BienTypeService`
- `FpdfReportService`
- `ActaDesincorporacionService`
- `ActaTrasladoService`
- `ActaDonacionService`
- `EliminadosService`
- `MovimientoService`
## Diagrama de clases
```mermaid
classDiagram
direction LR
class Organismo
class UnidadAdministradora
class Dependencia
class Bien
class BienElectronico
class BienMobiliario
class BienVehiculo
class BienOtro
class BienDesincorporado
class Responsable
class TipoResponsable
class Usuario
class Rol
class Movimiento
class HistorialMovimiento
class Reporte
class Auditoria
class Eliminado

Organismo "1" --> "0..*" UnidadAdministradora
UnidadAdministradora "1" --> "0..*" Dependencia
Dependencia "1" --> "0..*" Bien
Responsable "1" --> "0..*" Dependencia
TipoResponsable "1" --> "0..*" Responsable
Rol "1" --> "0..*" Usuario
Usuario "1" --> "0..*" Movimiento
Usuario "1" --> "0..*" Reporte
Usuario "1" --> "0..*" Auditoria
Bien "1" --> "0..*" Movimiento
Movimiento "1" --> "0..*" HistorialMovimiento
Bien "1" --> "0..1" BienElectronico
Bien "1" --> "0..1" BienMobiliario
Bien "1" --> "0..1" BienVehiculo
Bien "1" --> "0..1" BienOtro
Bien "1" --> "0..1" BienDesincorporado
```
## Diagrama entidad-relación
```mermaid
erDiagram
  ORGANISMOS ||--o{ UNIDADES_ADMINISTRADORAS : tiene
  UNIDADES_ADMINISTRADORAS ||--o{ DEPENDENCIAS : tiene
  TIPOS_RESPONSABLES ||--o{ RESPONSABLES : clasifica
  RESPONSABLES ||--o{ DEPENDENCIAS : asignado_a
  DEPENDENCIAS ||--o{ BIENES : contiene
  ROLES ||--o{ USUARIOS : asigna
  USUARIOS ||--o{ MOVIMIENTOS : registra
  BIENES ||--o{ MOVIMIENTOS : recibe
  MOVIMIENTOS ||--o{ HISTORIAL_MOVIMIENTOS : historiza
  USUARIOS ||--o{ REPORTES : genera
  USUARIOS ||--o{ AUDITORIA : ejecuta
  USUARIOS ||--o{ ELIMINADOS : elimina
  BIENES ||--o| BIENES_ELECTRONICOS : detalle
  BIENES ||--o| BIENES_MOBILIARIOS : detalle
  BIENES ||--o| BIENES_VEHICULOS : detalle
  BIENES ||--o| BIENES_OTROS : detalle
  BIENES ||--o| BIENES_DESINCORPORADOS : historico
```
