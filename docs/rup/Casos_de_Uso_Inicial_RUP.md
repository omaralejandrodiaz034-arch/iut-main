# Modelo de Casos de Uso Inicial RUP

## Actores principales

- Administrador.
- Usuario autorizado.
- Auditor.

## Casos de uso esenciales

1. Iniciar sesión.
2. Registrar bien.
3. Consultar bien.
4. Transferir bien.
5. Desincorporar bien.
6. Generar reporte.

## Descripción de casos de uso iniciales

### CU-01: Iniciar sesión
- Actor: Usuario autorizado.
- Descripción: El usuario ingresa credenciales válidas para acceder al sistema.
- Flujo básico: Mostrar formulario de login → validar credenciales → redirigir al dashboard.

### CU-02: Registrar bien
- Actor: Usuario autorizado.
- Descripción: Registrar un nuevo activo con código jerárquico, ubicación, estado y datos básicos.
- Flujo básico: Mostrar formulario de creación → seleccionar dependencia → generar código sugerido → guardar bien.

### CU-03: Consultar bien
- Actor: Usuario autorizado.
- Descripción: Buscar y visualizar información de un bien registrado.
- Flujo básico: Buscar bien → mostrar lista de bienes → ver detalles.

### CU-04: Transferir bien
- Actor: Usuario autorizado.
- Descripción: Cambiar la asignación de un bien a otra dependencia.
- Flujo básico: Seleccionar bien → seleccionar dependencia destino → confirmar traslado.

### CU-05: Desincorporar bien
- Actor: Usuario autorizado.
- Descripción: Dar de baja un bien y registrar acta/motivo.
- Flujo básico: Seleccionar bien → definir motivo → generar acta.

### CU-06: Generar reporte
- Actor: Usuario autorizado.
- Descripción: Generar reportes en PDF o Excel de bienes según filtros.
- Flujo básico: Seleccionar criterios → generar reporte.

## Prioridad 80/20

Estos casos de uso representan el 20% de las funcionalidades que entregan el 80% del valor inicial del sistema, centrados en inventario, trazabilidad y reportes.
