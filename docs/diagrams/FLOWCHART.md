# Diagrama de Flujo - Sistema de Gestión de Inventario de Bienes

## Flujo Principal del Sistema

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SISTEMA DE GESTIÓN DE INVENTARIO DE BIENES              │
│                         Flujo Principal de Operación                       │
└─────────────────────────────────────────────────────────────────────────────┘

                                    ┌──────────────┐
                                    │   INICIO     │
                                    └──────┬───────┘
                                           │
                                           ▼
                              ┌──────────────────────┐
                              │  Usuario accede al    │
                              │       sistema         │
                              └──────────┬───────────┘
                                         │
                                         ▼
                              ┌──────────────────────┐
                              │ ¿Usuario autenticado? │
                              └──────────┬───────────┘
                                         │
                    ┌──────────────────────┴──────────────────────┐
                    │                                               │
                   NO                                              SÍ
                    │                                               │
                    ▼                                               ▼
      ┌─────────────────────┐                     ┌──────────────────────┐
      │  Mostrar formulario  │                     │   Cargar Dashboard  │
      │      de login       │                     │  (Panel principal)   │
      └─────────────────────┘                     └──────────┬───────────┘
                    │                                        │
                    ▼                                        │
      ┌─────────────────────┐                               │
      │    Fin (Login)      │                               │
      └─────────────────────┘                               │
                                                              │
                                                              ▼
                                             ┌──────────────────────┐
                                             │  Menú Principal:     │
                                             │  • Bienes            │
                                             │  • Movimientos       │
                                             │  • Dependencias      │
                                             │  • Responsables      │
                                             │  • Reportes          │
                                             │  • Mi Perfil         │
                                             └──────────┬───────────┘
                                                        │
                              ┌───────────────────────────┴───────────────────┐
                              │                                                       │
                              ▼                                                       ▼
                ┌─────────────────────┐                               ┌─────────────────────┐
                │  REGISTRO DE BIEN    │                               │   VER BIENES        │
                └──────────┬───────────┘                               └──────────┬───────────┘
                           │                                                       │
                           ▼                                                       ▼
              ┌─────────────────────────┐                        ┌─────────────────────────┐
              │  Formulario de registro │                        │  Listar bienes          │
              │  (Tipo, datos, fotos)  │                        │  (Con filtros)          │
              └──────────┬──────────────┘                        └───────────┬─────────────┘
                         │                                                    │
                         ▼                                                    ▼
              ┌─────────────────────────┐                        ┌─────────────────────────┐
              │ ¿Tiene características   │                        │ ¿Buscar bien específico?│
              │    específicas?         │                        └───────────┬─────────────┘
              └──────────┬──────────────┘                                    │
                         │                                                    ▼
              ┌──────────┴──────────┐                         ┌─────────────────────────┐
              │                     │                         │    Ver detalle bien     │
             SÍ                    NO                        │    + Historial          │
              │                     │                         └─────────────────────────┘
              ▼                     │
   ┌─────────────────────┐          │
   │ Completar según tipo│          │
   │ (procesador, marca)│          │
   └─────────────────────┘          │
              │                     │
              └──────────┬──────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │   Subir fotografías     │
              │    (máximo 5 fotos)    │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Guardar en base de    │
              │       datos             │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Generar código único   │
              │   automático            │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Registrar en auditoría  │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Mostrar mensaje de     │
              │       éxito             │
              └─────────────────────────┘


══════════════════════════════════════════════════════════════════════════════

                    FLUJO DE MOVIMIENTOS/TRANSFERENCIAS

══════════════════════════════════════════════════════════════════════════════

                                    ┌──────────────┐
                                    │   INICIO     │
                                    └──────┬───────┘
                                           │
                                           ▼
                              ┌──────────────────────┐
                              │  Seleccionar bien   │
                              └──────────┬───────────┘
                                         │
                                         ▼
                              ┌──────────────────────┐
                              │  Elegir tipo de     │
                              │    movimiento        │
                              └──────────┬───────────┘
                                         │
         ┌──────────────────────────────┴──────────────────────────────┐
         │                │                   │                      │
         ▼                ▼                   ▼                      ▼
   ┌───────────┐  ┌───────────────┐  ┌────────────────┐  ┌──────────────┐
   │ TRASLADO   │  │  CAMBIO DE   │  │DESINCORPORACIÓN│  │  ASIGNACIÓN  │
   │            │  │    ESTADO     │  │                │  │  RESPONSABLE │
   └─────┬──────┘  └──────┬───────┘  └───────┬────────┘  └──────┬───────┘
         │                │                  │                   │
         ▼                ▼                  ▼                   ▼
   ┌───────────┐  ┌───────────────┐  ┌────────────────┐  ┌──────────────┐
   │ Seleccionar│  │  Seleccionar │  │ Seleccionar    │  │ Seleccionar  │
   │ nueva de-  │  │  nuevo estado│  │    motivo     │  │   nuevo      │
   │ pendencia │  │              │  │               │  │  responsable │
   └─────┬──────┘  └──────┬───────┘  └───────┬────────┘  └──────┬───────┘
         │                │                  │                   │
         ▼                ▼                  ▼                   ▼
   ┌───────────┐  ┌───────────────┐  ┌────────────────┐  ┌──────────────┐
   │ Agregar   │  │  Ingresar    │  │  Descripción   │  │ Confirmar    │
   │observacio-│  │    motivo    │  │   detallada    │  │asignación   │
   │ nes       │  │              │  │               │  │              │
   └─────┬──────┘  └──────┬───────┘  └───────┬────────┘  └──────┬───────┘
         │                │                  │                   │
         └───────────────┬┴──────────────────┴───────────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Confirmar movimiento  │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Generar movimiento en  │
              │        BD              │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Actualizar historial   │
              └──────────┬──────────────┘
                         │
              ┌─────────┴─────────────┐
              │                      │
             SÍ                     NO
              │                   (Desinc.)
              │                      │
              └───────┬──────────────┘
                      │
                      ▼
              ┌─────────────────────────┐
              │  Generar Acta PDF      │
              │ (Traslado/Desinc.)    │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Registrar auditoría    │
              └──────────┬──────────────┘
                         │
                         ▼
              ┌─────────────────────────┐
              │  Mostrar mensaje de    │
              │       éxito            │
              └─────────────────────────┘


══════════════════════════════════════════════════════════════════════════════

                    FLUJO DE REPORTES

══════════════════════════════════════════════════════════════════════════════

                                    ┌──────────────┐
                                    │   INICIO     │
                                    └──────┬───────┘
                                           │
                                           ▼
                              ┌──────────────────────┐
                              │  Seleccionar tipo de │
                              │      reporte          │
                              └──────────┬───────────┘
                                         │
         ┌──────────────────────────────┴──────────────────────────────┐
         │           │               │               │                   │
         ▼           ▼               ▼               ▼                   ▼
   ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐         ┌──────────┐
   │Por Depend-│ │   Por    │ │  Por    │ │Por estado│         │ General  │
   │ encia    │ │Responsable│ │ tipo bien│ │          │         │ Invent. │
   └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘         └────┬─────┘
        │             │             │             │                   │
        └─────────────┴─────────────┴─────────────┴───────────────────┘
                                   │
                                   ▼
                          ┌──────────────────────┐
                          │  Configurar parámetros│
                          │   (fechas, filtros)  │
                          └──────────┬───────────┘
                                     │
                                     ▼
                          ┌──────────────────────┐
                          │   Generar reporte    │
                          │   (consulta SQL)    │
                          └──────────┬───────────┘
                                     │
                                     ▼
                          ┌──────────────────────┐
                          │ ¿Formato PDF o Excel?│
                          └──────────┬───────────┘
                                     │
                    ┌────────────────┴────────────────┐
                    │                                 │
                   PDF                              Excel
                    │                                 │
                    ▼                                 ▼
          ┌──────────────────┐           ┌──────────────────┐
          │ Generar documento│           │ Generar archivo │
          │       PDF        │           │      Excel       │
          └────────┬─────────┘           └────────┬─────────┘
                   │                               │
                   └───────────────┬───────────────┘
                                   │
                                   ▼
                          ┌──────────────────────┐
                          │    Descargar         │
                          └──────────────────────┘
                                   │
                                   ▼
                          ┌──────────────────────┐
                          │   Fin proceso        │
                          └──────────────────────┘
```

## Descripción de Flujos

### 1. Flujo de Registro de Bien
1. Usuario accede al formulario de registro
2. Selecciona tipo de bien (electrónico, mobiliario, etc.)
3. Ingresa datos generales (código, descripción, precio)
4. Si aplica, completa características específicas
5. Sube fotografías (máximo 5)
6. Sistema genera código único automáticamente
7. Se guarda en base de datos
8. Se registra en auditoría

### 2. Flujo de Movimiento/Transferencia
1. Usuario selecciona bien a mover
2. Elige tipo de movimiento:
   - **Traslado**: Cambio de dependencia
   - **Cambio de estado**: Daño, mantenimiento, etc.
   - **Desincorporación**: Baja del bien
   - **Asignación**: Cambio de responsable
3. Completa los datos según tipo
4. Confirma el movimiento
5. Sistema genera movimiento en BD
6. Actualiza historial
7. Si es traslado o desincorporación, genera Acta PDF

### 3. Flujo de Reportes
1. Usuario selecciona tipo de reporte
2. Configura parámetros (fechas, filtros)
3. Sistema genera consulta
4. Elige formato (PDF o Excel)
5. Descarga el archivo

### 4. Flujo de Búsqueda
1. Usuario utiliza búsqueda global o filtros
2. Sistema consulta en bienes, usuarios, dependencias
3. Muestra resultados con paginación
4. Permite ver detalle de cada resultado
