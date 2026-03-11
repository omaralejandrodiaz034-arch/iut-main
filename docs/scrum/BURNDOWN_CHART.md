# Burndown Chart - Sistema de Gestión de Inventario de Bienes

## Vista General del Proyecto

```mermaid
xychart-beta
    title "Burndown Chart - Proyecto Completo"
    x-axis [Sprint 1, Sprint 2, Sprint 3, Sprint 4]
    y-axis "Story Points" 0 --> 150
    line [139, 97, 63, 0]
```

## Detalle por Sprint

### Sprint 1: Estabilización y Core

```mermaid
xychart-beta
    title "Sprint 1 - Estabilización y Core (42 pts)"
    x-axis ["Día 1", "Día 2", "Día 3", "Día 4", "Día 5", "Día 6", "Día 7", "Día 8", "Día 9", "Día 10"]
    y-axis "Puntos Restantes" 0 --> 45
    line [42, 38, 34, 30, 26, 22, 18, 14, 8, 0]
```

### Sprint 2: Códigos QR y Móvil

```mermaid
xychart-beta
    title "Sprint 2 - Códigos QR y Móvil (34 pts)"
    x-axis ["Día 1", "Día 2", "Día 3", "Día 4", "Día 5", "Día 6", "Día 7", "Día 8", "Día 9", "Día 10"]
    y-axis "Puntos Restantes" 0 --> 40
    line [34, 30, 26, 22, 18, 14, 10, 8, 5, 0]
```

### Sprint 3: Notificaciones y Comunicación

```mermaid
xychart-beta
    title "Sprint 3 - Notificaciones (29 pts)"
    x-axis ["Día 1", "Día 2", "Día 3", "Día 4", "Día 5", "Día 6", "Día 7", "Día 8", "Día 9", "Día 10"]
    y-axis "Puntos Restantes" 0 --> 35
    line [29, 25, 21, 17, 13, 10, 7, 5, 3, 0]
```

### Sprint 4: Optimización y Despliegue

```mermaid
xychart-beta
    title "Sprint 4 - Optimización (34 pts)"
    x-axis ["Día 1", "Día 2", "Día 3", "Día 4", "Día 5", "Día 6", "Día 7", "Día 8", "Día 9", "Día 10"]
    y-axis "Puntos Restantes" 0 --> 40
    line [34, 28, 24, 20, 16, 12, 8, 5, 2, 0]
```

## Velocidad del Equipo

```mermaid
gantt
    title Velocidad del Equipo por Sprint
    dateFormat  X
    axisFormat %s
    
    section Sprint 1
    Puntos Completados    : 0, 42
    
    section Sprint 2
    Puntos Completados    : 0, 34
    
    section Sprint 3
    Puntos Completados    : 0, 29
    
    section Sprint 4
    Puntos Completados    : 0, 34
```

## Métricas de Seguimiento

| Métrica | Sprint 1 | Sprint 2 | Sprint 3 | Sprint 4 |
|---------|----------|----------|----------|----------|
| Puntos Planificados | 42 | 34 | 29 | 34 |
| Puntos Completados | 42 | 34 | 29 | 34 |
| Velocidad Promedio | | | | 34.75 |
| % Cumplimiento | 100% | 100% | 100% | 100% |

## Guía de Lectura del Burndown

- **Línea ideal**: Representa el ritmo de trabajo si todo progresara perfectamente
- **Línea real**: Muestra el progreso actual del equipo
- **Por encima de la línea ideal**: El equipo está retrasado
- **Por debajo de la línea ideal**: El equipo está adelantando
- **Pendiente negativa**: Indica progreso constante

## Acciones según Tendencia

| Situación | Acción Recomendada |
|-----------|-------------------|
| Por encima de línea ideal | Reducir scope del sprint o agregar recursos |
| Por debajo de línea ideal | Considerar agregar más trabajo al sprint |
| Variación > 20% | Revisión en retrospectiva del equipo |
