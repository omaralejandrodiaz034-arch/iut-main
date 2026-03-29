# Manual de Usuario del Sistema de Gestión de Inventario de Bienes

## Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"

**Versión del Sistema:** 1.0  
**Fecha de Actualización:** Marzo 2026

---

## Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Estructura del Sistema](#3-estructura-del-sistema)
4. [Módulos del Sistema](#4-módulos-del-sistema)
5. [Gestión de Bienes](#5-gestión-de-bienes)
6. [Movimientos y Trazabilidad](#6-movimientos-y-trazabilidad)
7. [Reportes y Auditoría](#7-reportes-y-auditoría)
8. [Perfil de Usuario](#8-perfil-de-usuario)
9. [Glosario de Términos](#9-glosario-de-términos)

---

## 1. Introducción

### 1.1 Propósito del Sistema

El **Sistema de Gestión de Inventario de Bienes** es una plataforma integral desarrollada para la **Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián" (UPTOS)** que permite el registro, control y trazabilidad completa de los bienes públicos institucionales.

Este sistema cumple con las normativas de control patrimonial del Estado venezolano y proporciona:

- ✅ Registro digital de activos con fotografías
- ✅ Códigos únicos automáticos
- ✅ Trazabilidad total de movimientos
- ✅ Generación de actas oficiales en PDF
- ✅ Auditoría automática de operaciones
- ✅ Reportes por dependencia, responsable y período

### 1.2 Requisitos del Sistema

Para acceder al sistema necesitas:

- Un navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexión a Internet
- Credenciales de usuario proporcionadas por el administrador

---

## 2. Acceso al Sistema

### 2.1 Iniciar Sesión

1. Abre tu navegador y visita la página del sistema
2. Haz clic en el botón **"Iniciar Sesión"**
3. Ingresa tu correo electrónico institucional
4. Ingresa tu contraseña
5. Haz clic en **"Entrar"**

### 2.2 Recuperar Contraseña

Si olvidas tu contraseña:

1. Contacta al administrador del sistema
2. El administrador generará una nueva contraseña temporal
3. La primera vez que accedas, el sistema te pedirá configurar tu propia contraseña

### 2.3 Cerrar Sesión

Para cerrar sesión de manera segura:

1. Haz clic en tu nombre/usuario en la barra superior
2. Selecciona **"Cerrar Sesión"**
3. Confirma la acción

> ⚠️ **Importante**: Siempre cierra sesión cuando termines de usar el sistema, especialmente si compartes computadora.

---

## 3. Estructura del Sistema

### 3.1 Jerarquía Organizacional

El sistema está organizado de forma jerárquica:

```
Organismo (UPTOS "Clodosbaldo Russián")
    │
    └─► Unidad (Departamentos: Informática, Administración, etc.)
            │
            └─► Dependencia (Salones, Laboratorios, Oficinas)
                    │
                    └─► Bien (Activos registrados)
```

### 3.2 Roles de Usuario

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **Administrador** | Acceso completo al sistema | Gestionar usuarios, bienes, generar reportes, auditoría |
| **Usuario Normal** | Acceso operativo | Registrar bienes, movimientos, reportes básicos |

---

## 4. Módulos del Sistema

### 4.1 Panel de Control (Dashboard)

El Dashboard te muestra una visión general del sistema:

- **Total de Bienes**: Cantidad de bienes registrados
- **Bienes por Estado**: Distribución según estado (Activo, Dañado, etc.)
- **Últimos Movimientos**: Registro de movimientos recientes
- **Bienes Recientes**: Bienes añadidos recientemente

### 4.2 Organismos

Módulo para gestionar los organismos rectores.

**Acciones disponibles:**
- Ver lista de organismos
- Crear nuevo organismo
- Editar información
- Eliminar organismo
- Generar reporte PDF

**Campos del formulario:**
- Código del organismo
- Nombre o denominación

### 4.3 Unidades Administradoras (Departamentos)

Subdivisiones dentro de un organismo que representan los diferentes departamentos.

**Acciones disponibles:**
- Ver lista de unidades
- Crear nueva unidad
- Editar información
- Eliminar unidad
- Ver dependencias asociadas
- Generar reporte PDF

**Campos del formulario:**
- Código de la unidad
- Denominación (nombre)
- Organismo al que pertenece

### 4.4 Dependencias

Salones, laboratorios u oficinas dentro de un departamento.

**Acciones disponibles:**
- Ver lista de dependencias
- Crear nueva dependencia
- Editar información
- Eliminar dependencia
- Asignar responsable
- Ver bienes asociados
- Generar reporte PDF

**Campos del formulario:**
- Código de la dependencia
- Denominación (nombre del salón, laboratorio u oficina)
- Unidad (departamento) al que pertenece
- Responsable asignado (opcional)

### 4.5 Responsables

Personas responsables del cuidado de los bienes en una dependencia.

**Acciones disponibles:**
- Ver lista de responsables
- Crear nuevo responsable
- Editar información
- Eliminar responsable
- Buscar por cédula

**Campos del formulario:**
- Cédula de identidad
- Nombre completo
- Correo electrónico
- Teléfono
- Tipo de responsable (Primario, Por uso)

---

## 5. Gestión de Bienes

### 5.1 Tipos de Bienes

El sistema maneja diferentes tipos de bienes con características específicas:

| Tipo | Ejemplos | Características Específicas |
|------|----------|----------------------------|
| **Electrónico** | Computadoras, impresoras, equipos de red | Procesador, memoria RAM, disco duro |
| **Mobiliario** | Escritorios, sillas, estantes | Material, dimensiones |
| **Vehículo** | Automóviles, motos, camionetas | Marca, modelo, año, placa |
| **Otros** | Herramientas, equipos agrícolas | Descripción general |

### 5.2 Estados de Bienes

| Estado | Descripción | Color en Sistema |
|--------|-------------|------------------|
| **Activo** | En uso institucional | Verde |
| **Dañado** | Requiere reparación | Rojo |
| **En Mantenimiento** | En taller técnico | Amarillo |
| **En Camino** | En traslado | Azul |
| **Extraviado** | Sin localización | Gris |
| **Desincorporado** | Dado de baja oficial | Negro |

### 5.3 Registrar un Nuevo Bien

1. Navega a **Bienes** en el menú principal
2. Haz clic en el botón **"+ Nuevo Bien"**
3. Completa el formulario:

**Datos Generales:**
- Código único (se genera automáticamente)
- Descripción del bien
- Precio (valor monetario)
- Fecha de registro

**Clasificación:**
- Tipo de bien (Electrónico, Mobiliario, Vehículo, Otros)
- Estado (Activo, Dañado, etc.)

**Ubicación:**
- Dependencia asignada
- Ubicación específica (opcional)

**Características según tipo:**
- Para Electrónico: Procesador, memoria, disco duro
- Para Mobiliario: Material, dimensiones
- Para Vehículo: Marca, modelo, año, placa

**Fotografías:**
- Sube hasta 5 fotografías del bien
- Formatos aceptados: JPG, PNG
4. Haz clic en **"Guardar"**

### 5.4 Editar un Bien

1. Busca el bien en la lista
2. Haz clic en el botón **"Editar"** (ícono de lápiz)
3. Modifica los campos necesarios
4. Haz clic en **"Actualizar"**

### 5.5 Ver Detalles de un Bien

1. Haz clic en el nombre o código del bien
2. Verás toda la información incluyendo:
   - Datos completos del bien
   - Fotografías
   - Historial de movimientos
   - Responsable actual

### 5.6 Eliminar un Bien (Desincorporación)

Para dar de baja un bien:

1. Selecciona el bien
2. Haz clic en **"Desincorporar"**
3. Selecciona el motivo:
   - Obsolescencia
   - Daño irreparable
   - Robo o extravío
   - Donación
   - Otra causa
4. Agrega una descripción detallada
5. Confirma la desincorporación
6. Se generará un **Acta de Desincorporación** en PDF

### 5.7 Transferir un Bien entre Dependencias

1. Selecciona el bien
2. Haz clic en **"Transferir"**
3. Selecciona la nueva dependencia
4. Agrega una observación
5. Confirma la transferencia
6. Se registrará automáticamente un **movimiento de transferencia**

### 5.8 Importar Bienes desde Excel

1. Navega a **Bienes > Importar**
2. Descarga la plantilla (Excel)
3. Llena los datos siguiendo el formato
4. Sube el archivo
5. Verifica los resultados

### 5.9 Exportar Bienes

1. Navega a **Bienes > Exportar**
2. Selecciona el formato (Excel)
3. El sistema generará un archivo con todos los bienes

---

## 6. Movimientos y Trazabilidad

### 6.1 ¿Qué es un Movimiento?

Un **movimiento** es cualquier cambio relacionado con un bien:
- Cambio de ubicación (transferencia entre dependencias)
- Cambio de estado (daño, mantenimiento)
- Asignación a responsable
- Desincorporación

### 6.2 Registrar un Movimiento

1. Navega a **Movimientos**
2. Haz clic en **"+ Nuevo Movimiento"**
3. Selecciona el bien
4. Define el tipo de movimiento:
   - Traslado
   - Cambio de estado
   - Asignación
   - Desincorporación
5. Completa los detalles
6. Confirma el registro

### 6.3 Historial de Movimientos

Cada bien mantiene un historial completo de todos los movimientos. Para verlo:

1. Selecciona el bien
2. Haz clic en **"Ver"** o **"Historial"**
3. Visualiza la línea de tiempo con todos los cambios

### 6.4 Bienes Eliminados (Papelera)

Los bienes desincorporados se mueven a una papelera temporal:

1. Navega a **Movimientos > Eliminados**
2. Verás todos los bienes dados de baja
3. Si fue un error, puedes **restaurar** el bien
4. Los bienes eliminados se borran permanentemente después de 30 días

### 6.5 Reintegrar un Bien

Si un bien extraviado aparece:

1. Navega a **Movimientos**
2. Busca la opción **"Reintegrar"**
3. Selecciona el bien
4. Confirma la reintegración
5. El bien vuelve a estado "Activo"

---

## 7. Reportes y Auditoría

### 7.1 Generar Reportes

El sistema permite generar diversos tipos de reportes:

**Reportes disponibles:**
- Inventario general (todos los bienes)
- Por dependencia
- Por unidad administradora
- Por organismo
- Por responsable
- Por estado
- Por tipo de bien
- Histórico de movimientos

**Formatos de salida:**
- PDF (documento oficial)
- Excel (para análisis)

### 7.2 Gráficos y Estadísticas

1. Navega a **Gráficas** en el menú
2. Visualiza:
   - Bienes por tipo (gráfico circular)
   - Bienes por estado (gráfico de barras)
   - Tendencias temporales
3. Puedes exportar las gráficas a PDF

### 7.3 Auditoría (Solo Administradores)

El sistema registra todas las operaciones realizadas:

1. Navega a **Auditoría** (menú de administración)
2. Verás un registro de:
   - Usuario que realizó la acción
   - Tipo de operación
   - Fecha y hora
   - Datos afectados (antes y después)

### 7.4 Importar Usuarios

Para importar usuarios masivamente:

1. Navega a **Usuarios > Importar**
2. Prepara un archivo con los datos:
   - Cédula
   - Nombre
   - Apellido
   - Correo
3. Sube el archivo
4. El sistema procesará los datos

---

## 8. Perfil de Usuario

### 8.1 Ver tu Perfil

1. Haz clic en tu nombre en la barra superior
2. Selecciona **"Mi Perfil"**
3. Verás tu información personal

### 8.2 Editar Perfil

1. En tu perfil, haz clic en **"Editar"**
2. Modifica tu información:
   - Nombre
   - Apellido
   - Teléfono
3. Guarda los cambios

### 8.3 Cambiar Contraseña

1. En tu perfil, busca la sección de contraseña
2. Ingresa tu contraseña actual
3. Ingresa tu nueva contraseña
4. Confirma la nueva contraseña
5. Guarda los cambios

---

## 9. Glosario de Términos

| Término | Definición |
|---------|------------|
| **Organismo** | Universidad o institución principal (ej: UPTOS) |
| **Unidad** | Departamento dentro del organismo (ej: Departamento de Informática) |
| **Dependencia** | Salon, laboratorio u oficina (ej: Lab. Computación 1) |
| **Bien** | Activo físico registrado en el sistema |
| **Responsable** | Persona a cargo del cuidado de bienes |
| **Movimiento** | Cambio en la ubicación, estado o propiedad de un bien |
| **Desincorporación** | Proceso de dar de baja un bien del inventario |
| **Auditoría** | Registro automático de todas las operaciones del sistema |
| **Acta** | Documento oficial PDF generado por el sistema |
| **Trazabilidad** | Capacidad de seguir el historial completo de un bien |

---

## Información de Contacto

**Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"**

- **Sistema**: Gestión de Inventario de Bienes
- **Versión**: 1.0
- **Soporte**: Contacta al administrador del sistema

---

*Este manual fue creado para la versión actual del sistema. Las funcionalidades pueden variar con futuras actualizaciones.*
