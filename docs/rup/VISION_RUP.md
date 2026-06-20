# Documento de Visión RUP

## Sistema de Gestión de Inventario de Bienes

| Campo | Valor |
|---|---|
| Proyecto | Sistema de Gestión de Inventario de Bienes |
| Institución | Universidad Politécnica Territorial de Oriente “Clodosbaldo Russián” |
| Organismo rector | Ministerio del Poder Popular para la Educación Universitaria |
| Versión del documento | 1.0 |
| Fecha | 14 de junio de 2026 |
| Elaborado por | Kilo |

---

## 1. Introducción

### 1.1 Propósito

Este documento define la visión del **Sistema de Gestión de Inventario de Bienes** bajo el enfoque RUP. Su objetivo es describir el propósito, alcance, stakeholders, funcionalidades principales, restricciones, riesgos y criterios de éxito del sistema.

El documento sirve como referencia para alinear a los responsables institucionales, administradores, usuarios patrimoniales, auditores y equipo de desarrollo sobre el valor esperado del producto.

### 1.2 Alcance

El sistema permite gestionar el inventario patrimonial de una institución educativa bajo una estructura jerárquica:

```text
Organismo → Unidad Administradora → Dependencia → Bien
```

El sistema cubre el registro, actualización, consulta, traslado, desincorporación, reincorporación, auditoría, reportes y exportación de bienes patrimoniales.

### 1.3 Definiciones

| Término | Definición |
|---|---|
| Organismo | Entidad superior que agrupa instituciones. |
| Unidad Administradora | Subdivisión administrativa dentro de un organismo. |
| Dependencia | Departamento, oficina, laboratorio o área donde se ubican bienes. |
| Bien | Activo institucional registrado en el inventario. |
| Responsable | Persona encargada del cuidado de bienes asociados a una dependencia. |
| Movimiento | Registro de cambio de ubicación, estado o situación de un bien. |
| Desincorporación | Proceso formal de baja de un bien patrimonial. |
| Reincorporación | Proceso de retorno de un bien previamente desincorporado. |
| Trazabilidad | Historial completo de los cambios y movimientos de un bien. |

### 1.4 Referencias

- `LOGICA_SISTEMA.txt`
- `LOGICA_CODIGOS.md`
- `docs/scrum/VISION_DEL_PRODUCTO.md`
- `docs/scrum/PRODUCT_BACKLOG.md`
- `docs/MANUAL_USUARIO.md`
- `routes/web.php`
- `composer.json`

---

## 2. Posicionamiento del Producto

### 2.1 Oportunidad

Las instituciones educativas enfrentan dificultades para controlar sus bienes patrimoniales cuando los registros se manejan de forma manual o dispersa. Esto genera pérdida de trazabilidad, retrasos en auditorías, duplicidad de información y dificultad para identificar responsables.

El proyecto busca reemplazar esos procesos manuales con una plataforma web centralizada, auditable y orientada a reportes oficiales.

### 2.2 Declaración de posición

Para instituciones educativas venezolanas que requieren controlar sus bienes patrimoniales, el Sistema de Gestión de Inventario de Bienes es una aplicación web que centraliza el registro, ubicación, estado, responsabilidad, movimientos y reportes de activos institucionales.

A diferencia de hojas de cálculo o registros en papel, el sistema ofrece trazabilidad jerárquica, generación automática de códigos, evidencias fotográficas, auditoría de operaciones y exportación de reportes en formatos oficiales.

---

## 3. Stakeholders

### 3.1 Stakeholders principales

| Stakeholder | Rol | Necesidad principal |
|---|---|---|
| Gerencia de Administración | Responsable institucional del control patrimonial | Visibilidad completa del inventario y reportes confiables. |
| Administradores del sistema | Gestionan usuarios, roles, configuración y auditoría | Control operativo y seguridad del sistema. |
| Usuarios normales | Registran, actualizan y consultan bienes | Acceso ágil a operaciones patrimoniales. |
| Responsables patrimoniales | Cuidan bienes asignados a dependencias | Claridad sobre bienes bajo su responsabilidad. |


### 3.2 Stakeholders secundarios

| Stakeholder | Rol | Necesidad principal |
|---|---|---|
| Equipo de desarrollo | Construye y mantiene el sistema | Código mantenible, documentación clara y arquitectura estable. |
| Mesa de soporte | Atiende incidencias de usuarios | Manuales, bitácoras y procedimientos operativos. |
| Personal administrativo | Usa reportes para procesos internos | Consultas e impresiones confiables. |

---

## 4. Descripción General del Producto

### 4.1 Perspectiva

El sistema es una aplicación web desarrollada en **Laravel 12** con vistas Blade, Tailwind CSS, Vite y base de datos SQLite. También utiliza bibliotecas para generación de PDF y Excel.

El sistema se integra con el flujo patrimonial institucional mediante módulos de estructura organizativa, bienes, movimientos, reportes y auditoría.

### 4.2 Funcionalidades principales

El sistema permite:

- Gestionar organismos, unidades administradoras y dependencias.
- Registrar usuarios y controlar acceso mediante roles.
- Registrar bienes patrimoniales con código, descripción, precio, fotografía, estado, tipo y características.
- Asignar responsables a dependencias.
- Generar códigos jerárquicos para bienes.
- Registrar movimientos de traslado, cambio de estado, desincorporación y reincorporación.
- Mantener historial de movimientos.
- Generar reportes PDF por organismo, unidad, dependencia, usuario, bien y movimiento.
- Exportar e importar bienes mediante Excel.
- Consultar registros de auditoría.
- Visualizar gráficas y métricas generales del inventario.

### 4.3 Funcionalidades fuera de alcance

Para la versión actual no se contemplan:

- Aplicación móvil nativa.
- Escaneo QR desde dispositivos móviles.
- Firmas digitales electrónicas.
- Integración con sistemas de compras.
- Notificaciones automáticas por correo.
- Geolocalización de bienes.
- Gestión de garantías, seguros o depreciación contable.

---

## 5. Características del Producto

| ID | Característica | Descripción | Prioridad |
|---|---|---|---|
| CP-01 | Estructura organizacional | Gestión de organismos, unidades administradoras y dependencias. | Alta |
| CP-02 | Gestión de usuarios | Registro, autenticación, roles, perfil y configuración de contraseña. | Alta |
| CP-03 | Registro de bienes | Alta de bienes con datos generales, fotografías, estado, tipo y ubicación. | Alta |
| CP-04 | Códigos jerárquicos | Generación y validación de códigos únicos con estructura organizacional. | Alta |
| CP-05 | Responsables patrimoniales | Registro y clasificación de responsables primarios y por uso. | Alta |
| CP-06 | Movimientos | Registro de traslados, cambios de estado, desincorporaciones y reincorporaciones. | Alta |
| CP-07 | Historial de movimientos | Consulta cronológica del historial de cambios de cada bien. | Alta |
| CP-08 | Reportes PDF | Generación de reportes oficiales por entidad patrimonial. | Alta |
| CP-09 | Exportación Excel | Exportación de inventarios para análisis externo o respaldo. | Media |
| CP-10 | Importación Excel | Carga masiva de bienes desde archivo estructurado. | Media |
| CP-11 | Auditoría | Registro automático de acciones críticas del sistema. | Alta |
| CP-12 | Dashboard y gráficas | Visualización de métricas generales e indicadores de inventario. | Media |
| CP-13 | Búsqueda global | Consulta de bienes y registros mediante criterios combinados. | Media |

---

## 6. Requisitos Funcionales de Alto Nivel

| ID | Requisito | Descripción |
|---|---|---|
| RF-01 | Autenticar usuarios | El sistema debe permitir iniciar sesión con credenciales válidas. |
| RF-02 | Gestionar usuarios | El sistema debe permitir registrar, editar, listar y eliminar usuarios según permisos. |
| RF-03 | Gestionar estructura organizacional | El sistema debe permitir administrar organismos, unidades y dependencias. |
| RF-04 | Registrar bienes | El sistema debe permitir crear bienes con datos patrimoniales básicos y fotografías. |
| RF-05 | Consultar bienes | El sistema debe permitir listar, filtrar y ver detalles de bienes. |
| RF-06 | Editar bienes | El sistema debe permitir actualizar información válida de un bien. |
| RF-07 | Generar códigos | El sistema debe generar o validar códigos jerárquicos únicos para bienes. |
| RF-08 | Gestionar responsables | El sistema debe permitir registrar responsables y asignarlos a dependencias. |
| RF-09 | Registrar movimientos | El sistema debe registrar traslados, cambios de estado y situaciones patrimoniales. |
| RF-10 | Mantener historial | El sistema debe conservar trazabilidad de movimientos por bien. |
| RF-11 | Generar reportes | El sistema debe producir reportes PDF por entidad seleccionada. |
| RF-12 | Exportar inventario | El sistema debe exportar datos de bienes a Excel. |
| RF-13 | Importar inventario | El sistema debe permitir importar bienes desde una plantilla Excel. |
| RF-14 | Auditar operaciones | El sistema debe registrar acciones críticas realizadas por usuarios. |
| RF-15 | Desincorporar bienes | El sistema debe permitir dar de baja un bien y generar acta correspondiente. |
| RF-16 | Reincorporar bienes | El sistema debe permitir reincorporar bienes previamente desincorporados. |
| RF-17 | Transferir bienes | El sistema debe permitir trasladar bienes entre dependencias. |

---

## 7. Requisitos No Funcionales de Alto Nivel

| ID | Requisito | Descripción |
|---|---|---|
| RNF-01 | Usabilidad | La interfaz debe ser clara para usuarios administrativos no técnicos. |
| RNF-02 | Seguridad | El sistema debe restringir operaciones según rol y estado del usuario. |
| RNF-03 | Auditabilidad | Las operaciones críticas deben quedar registradas con usuario, acción y fecha. |
| RNF-04 | Trazabilidad | Cada bien debe conservar historial de movimientos y cambios relevantes. |
| RNF-05 | Integridad de datos | Los códigos de bienes deben ser únicos y válidos dentro de su jerarquía. |
| RNF-06 | Disponibilidad | El sistema debe estar disponible durante la jornada operativa institucional. |
| RNF-07 | Rendimiento | Las consultas frecuentes deben responder en tiempos aceptables para listados y reportes. |
| RNF-08 | Portabilidad | La aplicación debe ejecutarse en servidores institucionales con PHP compatible. |
| RNF-09 | Mantenibilidad | El código debe seguir la estructura MVC de Laravel y patrones del proyecto. |
| RNF-10 | Respaldo | La información debe poder respaldarse mediante copia de base de datos y almacenamiento. |

---

## 8. Casos de Uso Principales

| Caso de uso | Actor principal | Descripción |
|---|---|---|
| Iniciar sesión | Usuario registrado | Accede al sistema con credenciales válidas. |
| Gestionar usuarios | Administrador | Crea, edita, elimina y consulta usuarios. |
| Gestionar organismos | Administrador | Mantiene la entidad de mayor nivel de la jerarquía. |
| Gestionar unidades administradoras | Administrador | Crea unidades asociadas a un organismo. |
| Gestionar dependencias | Administrador | Crea dependencias dentro de unidades. |
| Registrar bien | Usuario autorizado | Incorpora un nuevo activo al inventario. |
| Consultar bien | Usuario autorizado | Visualiza información, fotos, ubicación y estado de un bien. |
| Editar bien | Usuario autorizado | Actualiza información patrimonial permitida. |
| Transferir bien | Usuario autorizado | Traslada un bien a otra dependencia. |
| Desincorporar bien | Usuario autorizado | Da de baja un bien y genera acta. |
| Reincorporar bien | Usuario autorizado | Restaura un bien previamente desincorporado. |
| Registrar movimiento | Usuario autorizado | Documenta cambios de ubicación, estado o situación. |
| Ver historial | Usuario autorizado | Consulta trazabilidad de movimientos de un bien. |
| Generar reporte | Usuario autorizado | Produce documentos PDF oficiales. |
| Exportar a Excel | Usuario autorizado | Obtiene inventario en formato tabular. |
| Importar desde Excel | Usuario autorizado | Carga masivamente bienes desde archivo. |
| Consultar auditoría | Administrador | Revisa acciones críticas registradas. |

---

## 9. Restricciones

### 9.1 Restricciones técnicas

- La aplicación está construida en Laravel 12 y requiere PHP 8.2 o superior.
- La base de datos configurada para el proyecto es SQLite.
- La interfaz usa Blade, Tailwind CSS y Vite.
- Los reportes PDF dependen de DOMPDF y FPDF.
- La exportación e importación Excel dependen de PhpSpreadsheet.
- El sistema debe operar dentro de la infraestructura institucional disponible.

### 9.2 Restricciones de negocio

- El sistema debe respetar la jerarquía institucional Organismo → Unidad Administradora → Dependencia → Bien.
- Los bienes deben conservar trazabilidad histórica.
- Los reportes deben servir como soporte para auditorías patrimoniales.
- Los datos sensibles deben permanecer bajo control institucional.
- Las operaciones críticas deben estar protegidas por roles.

### 9.3 Restricciones de uso

- Los usuarios deben contar con credenciales válidas.
- La carga inicial de inventario requiere datos limpios y consistentes.
- Los responsables deben estar correctamente asociados a sus dependencias.
- Las fotografías deben cumplir formatos y tamaños aceptados por el sistema.

---

## 10. Supuestos y Dependencias

### 10.1 Supuestos

- La institución cuenta con usuarios capacitados para operar una aplicación web básica.
- Existe una estructura organizativa definida.
- Los bienes patrimoniales pueden clasificarse por tipo y estado.
- La información histórica puede ser migrada o cargada mediante procesos manuales o Excel.
- El servidor institucional permite ejecutar PHP, Composer, Node.js y Vite.

### 10.2 Dependencias

- Disponibilidad de la base de datos y almacenamiento local.
- Acceso a datos patrimoniales existentes.
- Definición de roles y permisos institucionales.
- Capacitación mínima a usuarios finales.
- Respaldo periódico de base de datos y archivos almacenados.

---

## 11. Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| Datos históricos inconsistentes | Media | Alto | Validar y limpiar datos antes de importar. |
| Mala asignación de responsables | Media | Alto | Mantener responsables actualizados por dependencia. |
| Uso incorrecto de estados de bienes | Media | Medio | Capacitar usuarios y usar etiquetas claras. |
| Pérdida de información por falta de respaldo | Baja | Crítico | Ejecutar respaldos periódicos de SQLite y storage. |
| Resistencia al cambio | Media | Medio | Capacitación, manual de usuario y acompañamiento. |
| Errores en generación de códigos | Baja | Alto | Mantener validaciones de unicidad y pruebas del servicio de códigos. |
| Crecimiento excesivo de reportes | Media | Medio | Aplicar filtros, paginación e índices. |

---

## 12. Criterios de Éxito

| Criterio | Meta esperada |
|---|---|
| Registro digital de bienes | 100% de bienes activos registrados en el sistema. |
| Trazabilidad | Todos los movimientos relevantes deben quedar registrados. |
| Reportes | Generación de reportes PDF y Excel sin reprocesamiento manual. |
| Auditoría | Las acciones críticas deben estar registradas. |
| Responsabilidad | Cada dependencia debe tener responsable asignado cuando corresponda. |
| Usabilidad | Usuarios administrativos pueden registrar y consultar bienes sin soporte continuo. |
| Integridad | No deben existir códigos duplicados de bienes. |
| Control patrimonial | Reducción significativa del tiempo de preparación para auditorías. |

---

## 13. Roadmap de Alto Nivel

### Fase 1: Fundación institucional

- Configuración de organismos, unidades administradoras y dependencias.
- Registro de usuarios y roles.
- Creación de responsables patrimoniales.

### Fase 2: Inventario básico

- Registro de bienes.
- Carga de fotografías.
- Consulta, edición y listado de activos.
- Generación de códigos jerárquicos.

### Fase 3: Trazabilidad patrimonial

- Traslados entre dependencias.
- Cambios de estado.
- Historial de movimientos.
- Desincorporación, reincorporación y actas.

### Fase 4: Control y auditoría

- Reportes PDF.
- Exportación e importación Excel.
- Auditoría de operaciones.
- Dashboard y gráficas.

### Fase 5: Optimización futura

- Mejoras de rendimiento.
- Notificaciones.
- Escaneo QR.
- Integraciones externas.
- App móvil o experiencia PWA.

---

## 14. Visión Final

El Sistema de Gestión de Inventario de Bienes debe convertirse en la plataforma institucional de referencia para el control patrimonial, permitiendo conocer qué bienes existen, dónde se encuentran, quién los resguarda, cuál es su estado actual y qué movimientos han tenido.

La visión del producto es consolidar un inventario transparente, auditable y actualizado que reduzca riesgos patrimoniales, facilite auditorías y apoye la toma de decisiones administrativas.
