# Glosario RUP

## Términos clave

- **Organismo**: Entidad superior que agrupa unidades administrativas y dependencias. En el sistema, `organismos` es la tabla que contiene códigos y nombres de estas entidades.
- **Unidad Administradora**: Subdivisión institucional dependiente de un organismo. Gestiona una o varias dependencias.
- **Dependencia**: Área operativa donde se ubican bienes, como oficinas, laboratorios o talleres. Cada dependencia puede tener un responsable.
- **Bien**: Activo patrimonial o recurso físico registrado en el inventario. Los bienes tienen código jerárquico, descripción, estado, precio y tipo.
- **Responsable**: Persona encargada de vigilar el buen uso y custodia de bienes asignados a una dependencia.
- **Usuario**: Persona con acceso al sistema que puede autenticarse, consultar y ejecutar operaciones según su rol.
- **Rol**: Clasificación de acceso y permisos de usuario, como administrador o usuario normal.
- **Movimiento**: Registro de un cambio de ubicación, estado o situación de un bien.
- **Desincorporación**: Proceso formal de baja de un bien patrimonial, que puede incluir generación de acta y registro de motivo.
- **Reincorporación**: Proceso que habilita nuevamente un bien previamente desincorporado.
- **Trazabilidad**: Capacidad del sistema para conservar el historial de cambios y movimientos de un bien.
- **Código jerárquico**: Código único de 10 dígitos que representa la jerarquía Organismo → Unidad Administradora → Dependencia → Bien.
- **Códigos legibles**: Representación con separadores de un código jerárquico, por ejemplo `1.02.003.0004`.
- **Auditoría**: Registro de operaciones críticas realizadas por usuarios, incluyendo acciones CREATE, UPDATE y DELETE.
- **Reporte PDF**: Documento generado por el sistema que presenta información de inventarios y movimientos en formato imprimible.
- **Exportación Excel**: Salida de datos de inventario en formato .xlsx para uso externo.
- **Importación Excel**: Proceso de carga masiva de bienes mediante plantilla estructurada.
- **MVC**: Patrón arquitectónico Modelo-Vista-Controlador, usado por Laravel.
- **SQLite**: Base de datos liviana usada en el proyecto para almacenamiento local.
- **Laravel**: Framework PHP usado para el desarrollo de la aplicación.
- **Blade**: Motor de plantillas de Laravel para vistas HTML.
- **Tailwind CSS**: Framework de utilidades CSS usado para el diseño visual.
