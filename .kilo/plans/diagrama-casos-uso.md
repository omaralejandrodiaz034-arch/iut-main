# Plan: Diagrama de Casos de Uso del Sistema

## Objetivo
Generar un diagrama de casos de uso completo y actualizado del sistema de gestión de inventario de bienes, integrando actores, casos de uso, relaciones y flujos documentados.

## Análisis Realizado

### Actores Identificados (del modelo, docs y código):
- **Administrador** — Acceso completo a todos los módulos
- **Usuario Normal** — Acceso limitado a bienes y movimientos
- **Responsable de Dependencia** — Consulta de bienes asignados, reporte de incidencias
- **Auditor Externo** — Consulta de historial de movimientos y registros eliminados

### Casos de Uso Identificados (de routes, controllers y docs):

#### 1. Autenticación y Perfil
- Iniciar Sesión
- Cerrar Sesión
- Gestionar Perfil

#### 2. Estructura Organizacional
- Gestionar Organismos
- Gestionar Unidades Administradoras
- Gestionar Dependencias
- Gestionar Responsables

#### 3. Gestión de Bienes
- Registrar Bien
- Editar Bien
- Ver Detalle de Bien
- Listar/Buscar Bienes
- Transferir Bien entre Dependencias
- Desincorporar Bien (con acta)
- Reincorporar Bien
- Ver Galería de Bienes

#### 4. Movimientos
- Registrar Movimiento
- Ver Historial de Movimientos
- Restaurar Movimiento Eliminado

#### 5. Usuarios
- Gestionar Usuarios (CRUD)
- Importar Usuario desde API

#### 6. Reportes y Análisis
- Generar Reporte PDF (bienes, dependencias, organismos, movimientos, usuarios)
- Exportar a Excel
- Importar desde Excel
- Ver Dashboard de Métricas
- Ver Gráficas

#### 7. Auditoría
- Ver Registros de Auditoría
- Ver Historial de Eliminados

## Acciones a Ejecutar

1. **Generar archivo Mermaid** `docs/diagrams/diagrama_casos_uso_completo.mmd` con:
   - Gráfico principal de casos de uso por actor (graph TD)
   - Incluir generalización/especialización de actores
   - Usar `use case` shapes para los casos
   - Relaciones de inclusión/extensión entre casos relacionados
   - Agrupación por paquetes (Organizacional, Bienes, Movimientos, Reportes, Auditoría)

2. **Validar sintaxis Mermaid** verificando que el archivo se renderice correctamente.

## Notas
- Se mantendrán las convenciones del proyecto Mermaid existentes.
- Se priorizará la correspondencia con el código actual (no incluir casos de uso del CASOS_DE_USO.md que no existan en el código).
- El diagrama distinguirá claramente entre casos de uso exclusivos de Administrador vs. compartidos.
