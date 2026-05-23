# Casos de Uso - Sistema de Gestión de Inventario de Bienes

## 1. Diagrama General de Casos de Uso

```mermaid
graph TD
    Admin[Administrador]
    Gerente[Gerente de Bienes]
    Responsable[Responsable de Dependencia]
    Auditor[Auditor Externo]
    
    subgraph Sistema {
        GestionarOrganismos[Gestionar Organismos]
        GestionarUnidades[Gestionar Unidades Administradoras]
        GestionarDependencias[Gestionar Dependencias]
        RegistrarUsuario[Registrar Usuarios]
        AsignarRoles[Asignar Roles]
        
        RegistrarBien[Registrar Bien]
        EditarBien[Editar Bien]
        BuscarBienes[Buscar Bienes]
        VerDetalle[Ver Detalle de Bien]
        GenerarReporte[Generar Reporte PDF]
        ExportarExcel[Exportar a Excel]
        ImportarExcel[Importar desde Excel]
        
        RegistrarMovimiento[Registrar Movimiento]
        VerHistorial[Ver Historial de Movimientos]
        CambiarResponsable[Cambiar Responsable]
        
        VerAuditoria[Ver Registros de Auditoría]
        VerDashboard[Ver Dashboard de Métricas]
        
        IniciarSesion[Iniciar Sesión]
        CerrarSesion[Cerrar Sesión]
        GestionarPerfil[Gestionar Perfil]
    }
    
    Admin --> GestionarOrganismos
    Admin --> GestionarUnidades
    Admin --> GestionarDependencias
    Admin --> RegistrarUsuario
    Admin --> AsignarRoles
    
    Gerente --> RegistrarBien
    Gerente --> EditarBien
    Gerente --> BuscarBienes
    Gerente --> VerDetalle
    Gerente --> GenerarReporte
    Gerente --> ExportarExcel
    Gerente --> ImportarExcel
    Gerente --> RegistrarMovimiento
    Gerente --> VerHistorial
    Gerente --> CambiarResponsable
    
    Responsable --> VerDetalle
    Responsable --> BuscarBienes
    
    Auditor --> VerAuditoria
    Auditor --> VerDashboard
    Auditor --> GenerarReporte
    
    Admin --> VerDashboard
    Gerente --> VerDashboard
    
    IniciarSesion --> Admin
    IniciarSesion --> Gerente
    IniciarSesion --> Responsable
    IniciarSesion --> Auditor
    
    CerrarSesion --> Admin
    CerrarSesion --> Gerente
    CerrarSesion --> Responsable
    CerrarSesion --> Auditor
    
    GestionarPerfil --> Admin
    GestionarPerfil --> Gerente
    GestionarPerfil --> Responsable
```

## 2. Tabla de Trazabilidad Requisitos-Historias-Pruebas

| Nº | Requisito | Historia | Caso de Uso | Prueba Asociada |
|----|-----------|----------|-------------|-----------------|
| R01 | RF-01: Registro de usuarios | HU-001 | Registrar Usuario | test_usuario_registro |
| R02 | RF-02: Autenticación | HU-002 | Iniciar Sesión | test_login_valido |
| R03 | RF-03: Cerrar sesión | HU-003 | Cerrar Sesión | test_logout |
| R04 | RF-04: Crear organismo | HU-004 | Gestionar Organismos | test_organismo_crud |
| R05 | RF-05: Crear unidad | HU-005 | Gestionar Unidades | test_unidad_crud |
| R06 | RF-06: Crear dependencia | HU-006 | Gestionar Dependencias | test_dependencia_crud |
| R07 | RF-07: Registrar bien | HU-007 | Registrar Bien | test_bien_registro |
| R08 | RF-08: Listar bienes | HU-008 | Buscar Bienes | test_bien_listado |
| R09 | RF-09: Ver detalle | HU-009 | Ver Detalle | test_bien_detalle |
| R10 | RF-10: Editar bien | HU-010 | Editar Bien | test_bien_edicion |
| R11 | RF-11: Registrar movimiento | HU-011 | Registrar Movimiento | test_movimiento |
| R12 | RF-12: Cambiar responsable | HU-012 | Cambiar Responsable | test_responsable_cambio |
| R13 | RF-13: Ver historial | HU-013 | Ver Historial | test_historial |
| R14 | RF-14: Generar reporte | HU-014 | Generar Reporte | test_reporte_pdf |
| R15 | RF-15: Búsqueda global | HU-015 | Buscar Bienes | test_busqueda_avanzada |
| R16 | RF-16: Dashboard admin | HU-016 | Ver Dashboard | test_dashboard |
| R17 | RF-17: Tipos responsables | HU-017 | Asignar Roles | test_tipos_responsable |
| R18 | RF-18: Registrar responsable | HU-018 | Gestionar Dependencias | test_responsable_registro |
| R19 | RF-19: Auditoría | HU-019 | Ver Auditoría | test_auditoria |
| R20 | RF-20: Estado de bien | HU-020 | Editar Bien | test_estado_bien |
| R21 | RF-21: Exportar Excel | HU-023 | Exportar Excel | test_excel_export |
| R22 | RF-22: Importar Excel | HU-022 | Importar Excel | test_excel_import |

---

## 3. Descripción de Casos de Uso Principales

### CU-01: Registrar Bien en Inventario

**Actor**: Gerente de Bienes  
**Precondición**: Usuario autenticado con rol Gerente  
**Postcondición**: Bien creado en base de datos  

**Flujo Principal**:
1. El gerente accede al formulario de registro de bienes
2. Ingresa código, descripción, precio, ubicación
3. Selecciona dependencia y responsable
4. Adjunta hasta 5 fotografías
5. Sistema valida datos únicos
6. Sistema crea registro en tabla `bienes`
7. Sistema muestra confirmación de creación

**Flujos Alternativos**:
- FA1: Código duplicado → Sistema muestra error
- FA2: Validación fallida → Sistema muestra errores específicos

---

### CU-02: Registrar Movimiento de Traslado

**Actor**: Gerente de Bienes  
**Precondición**: Bien existente en dependencia origen  
**Postcondición**: Bien actualizado con nueva dependencia  

**Flujo Principal**:
1. Gerente selecciona bien del listado
2. Accede a "Registrar Movimiento"
3. Selecciona dependencia origen (auto-completado)
4. Selecciona dependencia destino
5. Ingresa motivo y documento de autorización
6. Sistema actualiza dependencia del bien
7. Sistema crea registro en tabla `movimientos`
8. Sistema actualiza historial

**Flujos Alternativos**:
- FA1: Dependencia origen no coincide → Error de validación

---

### CU-03: Importar Bienes desde Excel

**Actor**: Gerente de Bienes  
**Precondición**: Archivo Excel con formato válido  
**Postcondición**: Bienes creados en lote  

**Flujo Principal**:
1. Gerente descarga template Excel
2. Llena template con datos de bienes
3. Sube archivo mediante formulario
4. Sistema valida cada fila
5. Sistema importa registros válidos
6. Sistema muestra reporte de errores (si hay)
7. Sistema muestra mensaje de confirmación

**Flujos Alternativos**:
- FA1: Archivo con errores → Se crea reporte detallado
- FA2: Bienes duplicados → Se omiten con mensaje

---

## 4. Matriz de Trazabilidad por Épica

| Épica | Historias | Casos de Uso | Estado |
|-------|-----------|--------------|--------|
| E1: Estructura Organizacional | HU-004, HU-005, HU-006, HU-018 | CU-04 a CU-06 | ✅ Completado |
| E2: Usuarios y Accesos | HU-001, HU-002, HU-003 | CU-01, CU-02, CU-03 | ✅ Completado |
| E3: Gestión de Inventario | HU-007, HU-008, HU-009, HU-010, HU-020 | CU-07 a CU-12 | ✅ Completado |
| E4: Movimientos | HU-011, HU-012, HU-013 | CU-13 a CU-15 | ✅ Completado |
| E5: Reportes y Auditoría | HU-014, HU-015, HU-016, HU-019 | CU-16 a CU-18 | ✅ Completado |
| E6: Optimización | HU-022, HU-023 | CU-19, CU-20 | ✅ Completado |

---

## 5. Escenarios de Prueba

### EP-01: Registro Exitoso de Bien

```gherkin
Feature: Registro de Bien
  Scenario: Registrar bien nuevo exitosamente
    Given soy un usuario con rol "Gerente de Bienes" autenticado
    And navego al formulario de registro de bienes
    When ingreso código "00000001"
    And ingreso descripción "Computadora Dell"
    And ingreso precio 1500.00
    And selecciono dependencia "00000001"
    And adjunto 3 fotografías válidas
    And presiono "Guardar"
    Then debo ver el mensaje "Bien registrado exitosamente"
    And el bien debe existir en la base de datos
```

### EP-02: Importación Masiva

```gherkin
Feature: Importación Excel
  Scenario: Importar 50 bienes desde archivo Excel
    Given soy un usuario con rol "Gerente de Bienes" autenticado
    And tengo un archivo Excel válido con 50 bienes
    When navego a "Importar Bienes"
    And subo el archivo
    And confirmo la importación
    Then debo ver el mensaje "50 registros importados exitosamente"
    And los bienes deben existir en la base de datos
```

---

## 6. Métricas de Cobertura

| Métrico | Valor | Umbral |
|---------|-------|--------|
| Cobertura de código | 72% | > 70% |
| Cobertura de historias | 100% | 100% |
| Regresiones detectadas | 0 | 0 |
| Defectos críticos | 0 | 0 |

---

## Conclusiones

1. **Trazabilidad completa**: Cada requisito tiene historia y caso de uso asociado
2. **Cobertura verificada**: 22 requisitos funcionales con pruebas asociadas
3. **Casos alternativos cubiertos**: Flujos de error documentados
4. **Base para expansión**: Estructura preparada para nuevos casos de uso