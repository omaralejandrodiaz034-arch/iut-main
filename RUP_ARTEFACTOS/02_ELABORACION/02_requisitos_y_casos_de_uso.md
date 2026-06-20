# Fase de Elaboración
## Requisitos funcionales principales
- RF01: Iniciar sesión por cédula/credenciales.
- RF02: Configurar contraseña inicial para usuarios importados.
- RF03: Gestionar perfil (datos, contraseña, foto).
- RF04: Administrar organismos.
- RF05: Administrar unidades administradoras.
- RF06: Administrar dependencias.
- RF07: Administrar responsables.
- RF08: Administrar usuarios.
- RF09: Administrar bienes con validaciones y filtros.
- RF10: Gestionar subtipos de bien y atributos específicos.
- RF11: Registrar y consultar movimientos.
- RF12: Transferir bienes entre dependencias con acta.
- RF13: Desincorporar y reincorporar bienes.
- RF14: Gestionar donaciones con acta.
- RF15: Generar reportes PDF.
- RF16: Exportar/importar bienes en Excel.
- RF17: Consultar dashboard e indicadores.
- RF18: Ejecutar búsqueda global.
- RF19: Consultar auditoría y gestionar restauraciones.
## Requisitos no funcionales
- RNF01: Plataforma Laravel 12 y PHP 8.2.
- RNF02: Interfaz en español con mensajes de validación legibles.
- RNF03: Validaciones del lado servidor.
- RNF04: Control de acceso por rol y privilegios administrativos.
- RNF05: Persistencia relacional con migraciones versionadas.
- RNF06: Evidencia de trazabilidad en movimientos/auditoría.
## Actores
- Administrador.
- Usuario normal.
- Servicio externo de datos de personas (actor secundario).
## Casos de uso clave
- CU01 Iniciar sesión.
- CU02 Configurar contraseña inicial.
- CU03 Gestionar perfil.
- CU04 Gestionar catálogo institucional (organismo/unidad/dependencia).
- CU05 Gestionar responsables.
- CU06 Gestionar usuarios.
- CU07 Gestionar bienes.
- CU08 Transferir bien.
- CU09 Desincorporar bien.
- CU10 Reincorporar bien.
- CU11 Generar reportes.
- CU12 Exportar/importar Excel.
- CU13 Consultar auditoría.
