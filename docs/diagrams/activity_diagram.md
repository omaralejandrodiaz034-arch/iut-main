# Diagrama de Actividad — Flujos principales del sistema

Este diagrama cubre los flujos principales: autenticación, gestión de bienes, movimientos (traslado/desincorporación), búsqueda de responsables, import/export y generación de PDFs.

```mermaid
flowchart TD
  %% Actores / sistemas
  U[Usuario]
  B[Browser]
  Web[App Laravel (Controllers/Views)]
  Service[Servicios (Acta, CodigoJerarquico, Excel, Reportes)]
  Job[Cola / Workers]
  DB[(Base de datos MySQL)]
  FS[Storage (public) -> public/storage]
  External[API externa (People API)]

  %% Inicio
  U --> B
  B --> Web

  %% Login / Perfil
  B -->|visita /login| WebLogin[Mostrar login]
  WebLogin -->|credenciales| Web --> Auth[AuthController]
  Auth --> DB
  Auth --> B

  %% Dashboard y acciones principales
  Web -->|navega| Dashboard[Dashboard]
  Dashboard -->|Ver bienes| Web --> BienIndex[BienController@index]
  Dashboard -->|Crear bien| Web --> BienCreate[BienController@create]

  %% CRUD Bienes
  BienCreate -->|submit| Web --> BienStore[BienController@store]
  BienStore --> DB
  BienStore -->|si hay import| Service

  BienIndex -->|Seleccionar bien| Web --> BienShow[BienController@show]
  BienShow -->|Transferir| TransferForm[Mostrar formulario transferir]
  TransferForm -->|confirmar| Web --> Transfer[BienController@transferir]

  %% Flujo de traslado/desincorporación
  Transfer --> Service[ActaTrasladoService]
  Transfer --> DB[Registrar movimiento (tipo TRASLADO)]
  Service -->|genera PDF| FS
  Service --> DB
  Service --> B[Descarga / Ver acta]

  DesincForm[Formulario desincorporar] -->|confirmar| Web --> Desinc[BienController@desincorporar]
  Desinc --> Service[ActaDesincorporacionService]
  Desinc --> DB[Registrar movimiento (DESINCORPORACION)]
  Service --> FS

  %% Import / Export
  Web -->|Importar Excel| ExcelForm[BienExcelController@showImportForm]
  ExcelForm -->|subir archivo| Web --> ExcelImport[BienExcelController@importar]
  ExcelImport --> Job[Dispatch job to process import]
  Job --> DB

  %% Reportes y PDF generales
  Web -->|Generar reporte| Report[ReporteController@generarPdf]
  Report --> Service[Report generator (DomPDF / Twig)]
  Service --> FS
  Service --> B

  %% Búsqueda de responsables
  Web -->|Buscar responsable| ResponsableSearch[ResponsableController@buscar]
  ResponsableSearch --> External
  External -->|fallback| FS[storage/app/respuesta.json]
  ResponsableSearch --> DB[Crear/Actualizar responsable]

  %% Background / Workers
  Job -->|colas| Job
  Job --> DB

  classDef sys fill:#f3f4f6,stroke:#333,stroke-width:1px
  class Web,Service,Job,DB,FS,External sys

```

Notas: este diagrama refleja, a alto nivel, cómo interactúan las capas: interfaz (views/controllers), servicios (generación de códigos y actas), la cola de trabajo y los recursos externos.

