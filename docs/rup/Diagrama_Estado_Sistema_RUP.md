# Diagrama de Estado del Sistema — RUP

## Alcance

Este diagrama describe el ciclo de vida principal del `Bien` en el sistema de inventario, incluyendo las transiciones implementadas en el código fuente.

Se basa en:
- `app/Enums/EstadoBien.php`
- Acciones de `app/Http/Controllers/BienController.php`

## Diagrama de estados

```mermaid
stateDiagram-v2
    [*] --> ACTIVO

    ACTIVO --> EN_MANTENIMIENTO : reportar_mantenimiento
    ACTIVO --> DANADO : marcar_danado
    ACTIVO --> EN_CAMINO : trasladar
    ACTIVO --> EXTRAVIADO : marcar_extraviado
    ACTIVO --> DESINCORPORADO : desincorporar

    DANADO --> EN_MANTENIMIENTO : enviar_a_reparacion
    EN_MANTENIMIENTO --> ACTIVO : finalizar_mantenimiento
    EN_CAMINO --> ACTIVO : recibir
    DESINCORPORADO --> ACTIVO : reincorporar

    note right of ACTIVO: Bien disponible para uso operativo
    note right of DESINCORPORADO: Bien retirado del inventario activo
```

## Notas

- El estado `DESINCORPORADO` representa la baja administrativa registrada en la aplicación.
- El flujo actual del sistema no implementa eliminación física automática de bienes desincorporados.
- Las transiciones `trasladar` y `reincorporar` reflejan los procesos de movimiento entre dependencias y restauración del bien al inventario activo.
