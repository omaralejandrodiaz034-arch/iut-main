```mermaid
title: Diagrama de Casos de Uso
---
flowchart TD
    actorUsuario["Usuario"]
    actorAdmin["Administrador"]

    subgraph Sistema
        login["Iniciar Sesión"]
        logout["Cerrar Sesión"]
        gestionarBienes["Gestionar Bienes"]
        gestionarUsuarios["Gestionar Usuarios"]
        generarReportes["Generar Reportes"]
        verGraficas["Ver Gráficas"]
        verUsuarios["Ver Usuarios"]
        filtrarUsuarios["Filtrar Usuarios"]
        crearUsuario["Crear Usuario"]
    end

    actorUsuario --> login
    actorUsuario --> logout
    actorUsuario --> gestionarBienes
    actorUsuario --> generarReportes

    actorAdmin --> login
    actorAdmin --> logout
    actorAdmin --> gestionarBienes
    actorAdmin --> gestionarUsuarios
    actorAdmin --> generarReportes
    actorAdmin --> verGraficas
    actorAdmin --> verUsuarios
    actorAdmin --> filtrarUsuarios
    actorAdmin --> crearUsuario
```
