# Casos de Uso Detallado RUP

## Contexto del proyecto

Este documento describe los casos de uso detallados del sistema de gestión de inventario de bienes. El sistema es un proyecto de titulación para un Técnico Superior en Informática y el contenido se limita estrictamente a las funcionalidades efectivamente implementadas en el repositorio.

Duración del proceso de análisis y codificación: 7 meses.

---

## Actores

- Administrador: usuario con permisos de configuración, gestión de usuarios, auditoría y entidades organizativas.
- Usuario operativo: persona que registra, consulta y administra bienes y movimientos.
- Auditor: usuario o rol que consulta el histórico de auditoría y reportes.
- Responsable: persona asignada a dependencias y bienes.

---

## Casos de uso detallados

### CU-01: Iniciar sesión
- Actor principal: Usuario operativo / Administrador.
- Propósito: Acceder al sistema mediante credenciales válidas.
- Precondición: El usuario debe tener una cuenta activa en `usuarios`.
- Flujo principal:
  1. El usuario accede al formulario de login.
  2. Ingresa correo y contraseña.
  3. El sistema valida las credenciales con `hash_password`.
  4. Si es correcto, se redirige al dashboard.
- Poscondición: El usuario queda autenticado y con sesión activa.
- Alternativas:
  - Credenciales inválidas: se muestra mensaje de error.

### CU-02: Gestionar usuarios
- Actor principal: Administrador.
- Propósito: Crear, editar, listar, eliminar y generar reportes de usuarios.
- Precondición: El administrador debe estar autenticado.
- Flujo principal:
  1. El administrador abre la lista de usuarios.
  2. Selecciona crear o editar un usuario.
  3. Completa los campos obligatorios (`cedula`, `nombre`, `apellido`, `correo`, `rol`).
  4. El sistema valida y guarda los datos en la tabla `usuarios`.
  5. Puede generar un reporte PDF de usuarios.
- Poscondición: Se actualiza el registro en la base de datos.
- Extiende: CU-12 Generar reporte.

### CU-03: Gestionar organismos
- Actor principal: Administrador.
- Propósito: Registrar y mantener organismos institucionales.
- Flujo principal:
  1. El administrador abre la sección de organismos.
  2. Crea, edita o borra un organismo.
  3. El sistema valida el código y nombre.
  4. Guarda los datos en la tabla `organismos`.
- Poscondición: Se mantiene la jerarquía institucional.

### CU-04: Gestionar unidades administradoras
- Actor principal: Administrador.
- Propósito: Mantener unidades administrativas asociadas a un organismo.
- Flujo principal:
  1. Selecciona un organismo.
  2. Crea, edita o elimina una unidad administradora.
  3. El sistema valida asociación y código.
  4. Registra los datos en `unidades_administradoras`.

### CU-05: Gestionar dependencias
- Actor principal: Administrador.
- Propósito: Mantener dependencias dentro de unidades administradoras.
- Flujo principal:
  1. El administrador selecciona una unidad.
  2. Crea, edita o elimina una dependencia.
  3. Puede asignar un responsable existente.
  4. El sistema guarda la información en `dependencias`.
- Poscondición: Las dependencias quedan vinculadas a una unidad y a un responsable.

### CU-06: Registrar bien
- Actor principal: Usuario operativo.
- Propósito: Agregar un nuevo activo patrimonial al inventario.
- Flujo principal:
  1. Accede al formulario de creación de bienes.
  2. Selecciona organismo, unidad administradora y dependencia.
  3. Opcionalmente ingresa datos de tipo de bien y características.
  4. El sistema genera o valida el código jerárquico.
  5. Se guarda el bien en `bienes` y, si aplica, en la subtabla de tipo.
- Poscondición: El bien se registra con trazabilidad y estado inicial.

### CU-07: Consultar bien
- Actor principal: Usuario operativo.
- Propósito: Buscar y ver detalles de un bien existente.
- Flujo principal:
  1. El usuario usa la búsqueda global o filtra bienes por dependencias.
  2. Selecciona un bien de la lista.
  3. El sistema muestra la ficha del bien con su historial y datos relacionados.
- Poscondición: El usuario visualiza los atributos del bien y sus relaciones.

### CU-08: Editar bien
- Actor principal: Usuario operativo.
- Propósito: Actualizar la información de un bien registrado.
- Flujo principal:
  1. Selecciona un bien desde la lista o búsqueda.
  2. Edita los campos permitidos (descripción, precio, estado, características, etc.).
  3. El sistema valida y guarda los cambios.
- Poscondición: El bien queda actualizado en la tabla `bienes`.

### CU-09: Transferir bien
- Actor principal: Usuario operativo.
- Propósito: Trasladar un bien a otra dependencia.
- Flujo principal:
  1. Elige el bien y accede al formulario de transferencia.
  2. Selecciona la dependencia destino.
  3. Confirmar movimiento.
  4. El sistema actualiza la dependencia del bien y registra un `movimiento`.
- Poscondición: Queda registrada la nueva ubicación y el historial del bien.

### CU-10: Desincorporar bien
- Actor principal: Usuario operativo.
- Propósito: Dar de baja un bien patrimonial formalmente.
- Flujo principal:
  1. Selecciona el bien y abre la acción de desincorporar.
  2. Ingresa motivo y genera acta si aplica.
  3. El sistema guarda el registro en `bienes_desincorporados` y en `movimientos`.
- Poscondición: El bien queda marcado o registrado como desincorporado.

### CU-11: Reincorporar bien
- Actor principal: Usuario operativo.
- Propósito: Restaurar un bien previamente desincorporado.
- Flujo principal:
  1. Selecciona el bien desincorporado.
  2. Abre el formulario de reincorporación.
  3. Confirma la reincorporación.
  4. El sistema actualiza el estado y genera un movimiento.
- Poscondición: El bien vuelve a estar disponible en inventario.

### CU-12: Generar reporte
- Actor principal: Usuario operativo / Administrador.
- Propósito: Obtener reportes PDF y Excel según filtros.
- Flujo principal:
  1. El usuario selecciona filtros por organismo, unidad, dependencia, usuario, tipo, estado o movimiento.
  2. El sistema genera el reporte solicitado.
  3. Se descarga el PDF o Excel.
- Poscondición: Se crea un registro en `reportes` con metadatos.

### CU-13: Importar bienes desde Excel
- Actor principal: Usuario operativo.
- Propósito: Cargar registros masivos de bienes.
- Flujo principal:
  1. Abre el formulario de importación.
  2. Sube un archivo Excel con la plantilla correspondiente.
  3. El sistema valida filas y crea bienes.
- Poscondición: Los bienes válidos quedan registrados y los errores son reportados.

### CU-14: Exportar bienes a Excel
- Actor principal: Usuario operativo.
- Propósito: Extraer inventario en formato de hoja de cálculo.
- Flujo principal:
  1. Selecciona la opción de exportar.
  2. El sistema construye el archivo Excel.
  3. Descarga el archivo.
- Poscondición: Se obtiene un archivo con los datos actuales del inventario.

### CU-15: Consultar auditoría
- Actor principal: Administrador / Auditor.
- Propósito: Revisar acciones críticas registradas en el sistema.
- Flujo principal:
  1. Accede a la sección de auditoría.
  2. Filtra por usuario, tabla, fecha u operación.
  3. Visualiza los registros de `auditoria`.
- Poscondición: Se obtiene evidencia de cambios y quién los realizó.

### CU-16: Consultar historial de movimientos
- Actor principal: Usuario operativo / Auditor.
- Propósito: Ver el historial de cambios asociados a un movimiento o bien.
- Flujo principal:
  1. Selecciona un movimiento o bien.
  2. El sistema presenta el historial de `historial_movimientos`.
- Poscondición: Se obtiene trazabilidad de los eventos que afectaron al bien.

---

## Alcance y exclusiones

- Incluye solo funciones implementadas en el código fuente actual.
- Excluye integraciones externas no presentes, notificaciones automáticas, firma digital, geolocalización y aplicación móvil.
- El modelo se basa en la jerarquía real `Organismo → Unidad Administradora → Dependencia → Bien` y en los datos de inventario implementados.
