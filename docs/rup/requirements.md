Especificación de Requisitos (implementados)

Requisitos funcionales (selección basada en código):
1. RF-01: Autenticación de usuarios mediante tabla `usuarios`.
2. RF-02: CRUD de Organismos (`organismos`).
3. RF-03: CRUD de Unidades Administradoras (`unidades_administradoras`).
4. RF-04: CRUD de Dependencias (`dependencias`).
5. RF-05: Registro y gestión de Bienes (`bienes`) con subtipos (`bienes_electronicos`, `bienes_mobiliario`, `bienes_vehiculo`, `bienes_otro`).
6. RF-06: Registro de Movimientos (`movimientos`) con soporte polimórfico de sujeto.
7. RF-07: Gestión de Usuarios y Roles (`usuarios`, `roles`).
8. RF-08: Generación de reportes (`reportes`) y auditoría (`auditoria`).

Requisitos no funcionales observados:
- RNF-01: Persistencia en SQLite/MySQL vía migraciones.
- RNF-02: Uso de Blade + Vite para vistas y activos.
- RNF-03: Trazabilidad mínima mediante `auditoria` y `auditable` trait.

Restricciones y supuestos:
- El alcance se limita a las entidades y funcionalidades efectivamente implementadas en el repositorio.
- No se consideran integraciones externas ni servicios de terceros no presentes.
