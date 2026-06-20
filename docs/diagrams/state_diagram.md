# Diagrama de Estado — Máquinas de estado del sistema (completo)

Este diagrama ahora incluye las máquinas de estado para las entidades principales del sistema:
- `Bien`, `Movimiento`, `Usuario`, `Responsable`, `Organismo`, `UnidadAdministradora`, `Dependencia`, `ImportJob` (importación de Excel) y `Acta` (estado de generación de PDF).

```mermaid
stateDiagram-v2
  %% Estado del Bien
  state Bien {
    [*] --> ACTIVO
    ACTIVO --> EN_MANTENIMIENTO : reportar_mantenimiento
    ACTIVO --> DANADO : marcar_danado
    ACTIVO --> DESINCORPORADO : desincorporar
    EN_MANTENIMIENTO --> ACTIVO : terminar_mantenimiento
    DANADO --> EN_MANTENIMIENTO : enviar_a_reparacion
    DESINCORPORADO --> ELIMINADO : purgar_30_dias
    DESINCORPORADO --> ACTIVO : reincorporar
    ELIMINADO --> [*]
  }

  %% Estado del Movimiento
  state Movimiento {
    [*] --> CREADO
    CREADO --> PENDIENTE : procesar_pdf
    PENDIENTE --> PROCESANDO : en_cola
    PROCESANDO --> COMPLETADO : pdf_generado
    PROCESANDO --> ERROR : fallo_generacion
    ERROR --> REINTENTO : reintentar
    REINTENTO --> PROCESANDO
    COMPLETADO --> [*]
  }

  %% Estado del Usuario
  state Usuario {
    [*] --> ACTIVADO
    ACTIVADO --> BLOQUEADO : bloquear
    ACTIVADO --> INACTIVO : desactivar
    INACTIVO --> ACTIVADO : activar
    BLOQUEADO --> ACTIVADO : desbloquear
  }

  %% Responsable
  state Responsable {
    [*] --> ACTIVO_R
    ACTIVO_R --> INACTIVO_R : baja
    INACTIVO_R --> ACTIVO_R : alta
  }

  %% Organismo / Unidad / Dependencia
  state Organizacion {
    state Organismo {
      [*] --> ACTIVO_O
      ACTIVO_O --> ARCHIVADO_O : archivar
      ARCHIVADO_O --> ACTIVO_O : restaurar
    }
    state UnidadAdministradora {
      [*] --> ACTIVO_UA
      ACTIVO_UA --> CERRADA_UA : cerrar
      CERRADA_UA --> ACTIVO_UA : abrir
    }
    state Dependencia {
      [*] --> ACTIVO_D
      ACTIVO_D --> INACTIVA_D : deshabilitar
      INACTIVA_D --> ACTIVO_D : habilitar
    }
  }

  %% Import Job (Excel)
  state ImportJob {
    [*] --> QUEUEADO
    QUEUEADO --> PROCESSING : procesando
    PROCESSING --> SUCCESS : completado
    PROCESSING --> FAILED : fallo
    FAILED --> RETRY : reintentar
    RETRY --> PROCESSING
  }

  %% Acta (PDF)
  state Acta {
    [*] --> PENDIENTE
    PENDIENTE --> GENERANDO : inicio_generacion
    GENERANDO --> GENERADO : exito
    GENERANDO --> ERROR_ACTA : fallo
    ERROR_ACTA --> REINTENTAR_ACTA : reintentar
    REINTENTAR_ACTA --> GENERANDO
    GENERADO --> DISPONIBLE : publicado
  }

  %% Relaciones entre máquinas (eventos simplificados)
  ACTIVADO --> CREADO : crear_movimiento
  CREADO --> PENDIENTE : solicita_acta
  PENDIENTE --> GENERANDO : worker_procesa
  GENERADO --> COMPLETADO : acta_guardada

  note right of Bien: Ciclo de vida del bien (ACTIVO, DESINCORPORADO, ELIMINADO, etc.)
  note right of Movimiento: Flujo con cola y generación de actas
  note right of ImportJob: Importación de Excel y tareas en background

```

