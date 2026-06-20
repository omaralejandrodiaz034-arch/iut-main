Matriz de trazabilidad (resumen)

Casos de uso -> Requisitos -> Artefactos de código

- Registrar Bien (Use Case) -> RF-05 -> `app/Models/Bien.php`, `database/migrations/*create_bienes*`, `resources/views/bienes/*`
- Registrar Movimiento -> RF-06 -> `app/Models/Movimiento.php`, `database/migrations/*create_movimientos*`
- Gestionar Dependencia -> RF-04 -> `app/Models/Dependencia.php`, `database/migrations/*create_dependencias*`
- Gestionar Usuarios -> RF-07 -> `app/Models/Usuario.php`, `database/migrations/*create_usuarios*`

Nota: revisar `routes/web.php` y los controladores en `app/Http/Controllers` para trazabilidad completa de endpoints.
