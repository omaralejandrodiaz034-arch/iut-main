# Diagrama de Componentes - Sistema de Gestión de Inventario de Bienes

## Arquitectura General

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        SISTEMA DE GESTIÓN DE INVENTARIO DE BIENES           │
│                      Universidad Politécnica Territorial de Oriente          │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                        CAPA DE PRESENTACIÓN (Views)                        │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Welcome    │  │   Perfil     │  │    Bienes    │  │   Layouts    │ │
│  │   Blade     │  │   Blade      │  │   Blade      │  │   Blade      │ │
│  └──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                    CAPA DE CONTROLADOR (HTTP Controllers)                   │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐                │
│  │ AuthController │  │BienController  │  │MovimientoCtrl  │                │
│  └────────────────┘  └────────────────┘  └────────────────┘                │
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐                │
│  │ DependenciaC   │  │OrganismoC     │  │UnidadCtrl     │                │
│  └────────────────┘  └────────────────┘  └────────────────┘                │
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐                │
│  │ UsuarioController│ │ReporteCtrl    │  │DashboardCtrl  │                │
│  └────────────────┘  └────────────────┘  └────────────────┘                │
│  ┌────────────────┐  ┌────────────────┐                                    │
│  │ProfileController│  │AuditoriaCtrl  │                                    │
│  └────────────────┘  └────────────────┘                                    │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        CAPA DE MODELO (Dominio)                            │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   ORGANISMOS              UNIDADES             DEPENDENCIAS                 │
│   ┌───────────┐         ┌───────────┐        ┌───────────────┐            │
│   │Organismo  │──1:N──►│Unidad    │──1:N──►│Dependencia    │            │
│   │           │         │Admin     │        │               │            │
│   └───────────┘         └───────────┘        └───────┬───────┘            │
│                                                      │                    │
│                                                      │ 1:N                │
│                                                      ▼                    │
│   BIENES                      RESPONSABLES           │                    │
│   ┌───────────┐         ┌───────────────┐          │                    │
│   │   Bien    │◄──1:N──│  Responsable  │◄──0:1──┘                    │
│   │           │         │               │                                │
│   └─────┬─────┘         └───────────────┘                                │
│         │                                                                    │
│   1:N   │            USUARIOS                    TIPOS                     │
│         ▼         ┌───────────────┐         ┌───────────────┐              │
│   ┌───────────┐  │   Usuario     │         │TipoResponsable│              │
│   │Movimiento │  │               │         └───────────────┘              │
│   └─────┬─────┘  └───────┬───────┘                                        │
│         │                 │                                               │
│   1:N  │           1:1   │            ROLES                              │
│         ▼                 ▼           ┌───────────┐                        │
│   ┌───────────┐   ┌───────────┐      │   Rol    │                        │
│   │Historial  │   │  Reporte  │      └───────────┘                        │
│   │Movimiento │   │           │                                            │
│   └───────────┘   └───────────┘                                            │
│                                                                             │
│   TIPOS DE BIEN (Herencia STI):                                            │
│   ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐    │
│   │BienElectronico│ │BienMobiliario│ │BienVehiculo │ │   BienOtro   │    │
│   └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘    │
│                                                                             │
│   OTROS:                                                                    │
│   ┌─────────────────┐  ┌─────────────┐  ┌──────────────┐                   │
│   │BienDesincorporado│  │ Auditoria  │  │  Eliminado  │                   │
│   └─────────────────┘  └─────────────┘  └──────────────┘                   │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                            SERVICIOS                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌────────────────────────┐  ┌────────────────────────┐                   │
│  │  CodigoUnicoService    │  │ ActaDesincorporacionS │                   │
│  └────────────────────────┘  └────────────────────────┘                   │
│  ┌────────────────────────┐  ┌────────────────────────┐                   │
│  │   ActaTrasladoService  │  │   BienTypeService     │                   │
│  └────────────────────────┘  └────────────────────────┘                   │
│  ┌────────────────────────┐  ┌────────────────────────┐                   │
│  │   MovimientoService    │  │   EliminadosService    │                   │
│  └────────────────────────┘  └────────────────────────┘                   │
│  ┌────────────────────────┐                                                │
│  │   FpdfReportService   │                                                │
│  └────────────────────────┘                                                │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         RECURSOS EXTERNOS                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐                │
│  │    MySQL       │  │  File Storage │  │    DOMPDF     │                │
│  │   Database     │  │    (Fotos,    │  │   Library     │                │
│  │                │  │    Actas)     │  │               │                │
│  └────────────────┘  └────────────────┘  └────────────────┘                │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Descripción de Componentes

### Capa de Presentación (Views)
- **welcome.blade.php**: Página de inicio/landing
- **perfil/show.blade.php**: Vista del perfil de usuario
- **bienes/index.blade.php**: Listado de bienes
- **bienes/create.blade.php**: Formulario de creación de bienes
- **layouts/head.blade.php**: Navegación principal
- **layouts/base.blade.php**: Layout base de la aplicación

### Capa de Controlador
| Controlador | Función |
|------------|---------|
| AuthController | Autenticación de usuarios |
| BienController | CRUD de bienes |
| MovimientoController | Control de movimientos |
| DependenciaController | Gestión de dependencias |
| OrganismoController | Gestión de organismos |
| UnidadAdministradoraController | Gestión de unidades |
| UsuarioController | Gestión de usuarios |
| ReporteController | Generación de reportes |
| DashboardController | Panel principal |
| ProfileController | Perfil de usuario |
| AuditoriaController | Registro de auditoría |
| SearchController | Búsqueda global |

### Capa de Modelo
| Modelo | Descripción |
|--------|-------------|
| Organismo | Entidad rectora (UPTOS) |
| UnidadAdministradora | Departamentos |
| Dependencia | Salones, Laboratorios |
| Bien | Activos registrados |
| Responsable | Persona responsable |
| Usuario | Usuarios del sistema |
| Movimiento | Trazabilidad de bienes |
| Auditoria | Registro de operaciones |

### Servicios
- **CodigoUnicoService**: Genera códigos únicos para bienes
- **ActaDesincorporacionService**: Genera actas de desincorporación
- **ActaTrasladoService**: Genera actas de traslado
- **BienTypeService**: Maneja tipos específicos de bienes
- **MovimientoService**: Lógica de movimientos
- **EliminadosService**: Gestión de papelera
- **FpdfReportService**: Generación de PDFs

## Flujo de Datos

```
Usuario → AuthController → Usuario Model → MySQL
                                        ↓
                                  Auditoria

Usuario → BienController → Bien Model → MySQL
                ↓                    ↓
         CodigoUnicoService    File Storage (fotos)

Usuario → ReporteController → FpdfReportService → PDF
                ↓
           MySQL (datos)
```

## Tecnologías Utilizadas

- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS + Vite
- **Base de Datos**: MySQL
- **Almacenamiento**: Laravel Storage
- **PDF**: DOMPDF
- **Excel**: PhpSpreadsheet
