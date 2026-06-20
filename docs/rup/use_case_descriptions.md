Descripciones de Casos de Uso (resumidas)

1) Iniciar sesión
- Actor: Usuario
- Propósito: Permitir acceso al sistema usando la tabla `usuarios` (campo `correo` y `hash_password`).
- Flujo principal: El usuario ingresa credenciales → sistema valida → acceso concedido.

2) Registrar Bien
- Actor: Administrador / Usuario operativo
- Propósito: Crear un nuevo registro en `bienes` y sus detalles según tipo (p. ej. `bienes_electronicos`).
- Flujo principal: Formulario de ingreso → validaciones → crear registro y asociar `dependencia_id`.

3) Registrar Movimiento
- Actor: Usuario operativo
- Propósito: Registrar transacciones sobre bienes (`movimientos`), con relación opcional a `bien_id` y sujeto polimórfico.

4) Gestionar Dependencia / Unidades / Organismos
- Actor: Administrador
- Propósito: CRUD sobre entidades organizativas.

5) Generar Reporte
- Actor: Usuario / Administrador
- Propósito: Crear reportes basados en `reportes` y consultas sobre bienes/movimientos.
