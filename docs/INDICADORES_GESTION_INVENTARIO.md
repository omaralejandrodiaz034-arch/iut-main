# Especificación de indicadores de gestión del inventario

**Sistema:** Sistema de Gestión de Inventario de Bienes de la UPTOS  
**Versión del documento:** 2.0  
**Estado:** Propuesta técnica para validación institucional  
**Fecha:** 04/10/2026

## 1. Propósito

Definir un catálogo medible de indicadores de gestión para evaluar la
integridad, disponibilidad, asignación, evidencia y trazabilidad de los bienes
institucionales registrados en el sistema. El documento especifica objetivos,
fórmulas, universos, metas iniciales, rangos de alerta, fuentes de datos y
requisitos para su futura implementación en el Dashboard y los reportes.

La revisión se basa en las entidades y campos actuales de la aplicación:
`bienes`, `dependencias`, `responsables`, `movimientos`, estados y tipos de
bien. No implica que los indicadores ya estén implementados. El Dashboard
actual expone estadísticas descriptivas; las métricas, metas y semáforos de
este documento requieren ser calculados y presentados como se especifica aquí.

## 2. Objetivos de gestión

### Objetivo general

Fortalecer el control patrimonial, la disponibilidad para el servicio y la
trazabilidad de los bienes mediante datos completos, consistentes y oportunos
que faciliten la toma de decisiones y la rendición de cuentas.

### Objetivos específicos

1. Mantener identificados los bienes y asociados a una dependencia institucional.
2. Reducir la proporción de bienes dañados, en mantenimiento o extraviados.
3. Asegurar que las dependencias con bienes tengan un responsable asignado y
   que los registros cuenten con evidencia suficiente.
4. Mejorar la cobertura y confiabilidad de la valoración monetaria del
   inventario.
5. Hacer seguimiento a los movimientos de bienes y a la evolución del
   inventario por período.

## 3. Convenciones comunes de cálculo

### 3.1 Universo y estados

- **Bien vigente:** fila de `bienes` cuyo estado no es `DESINCORPORADO`.
  Los registros con estado nulo o no reconocido no deben desaparecer del
  denominador: se incluyen como vigentes y se reportan como problemas de
  calidad de datos.
- **Bien desincorporado:** bien en estado `DESINCORPORADO`. Se excluye de los
  indicadores operativos, pero debe conservarse en reportes históricos.
- Los valores de estado válidos de la aplicación son `ACTIVO`, `DAÑADO`,
  `EN_MANTENIMIENTO`, `EN_CAMINO`, `EXTRAVIADO` y `DESINCORPORADO`.
- Los estados y tipos deben compararse con sus valores canónicos almacenados,
  no con etiquetas traducidas del Dashboard.

### 3.2 Fórmulas y presentación

- Para una tasa porcentual: `100 × numerador / denominador`.
- Si el denominador es cero, mostrar `N/A` y no `0%`.
- El sistema debe conservar los conteos enteros de numerador y denominador.
  El porcentaje puede mostrarse con dos decimales, pero la clasificación del
  semáforo se determina con el valor sin redondear.
- En agregaciones por institución, unidad o dependencia se suman numeradores y
  denominadores y luego se calcula la tasa. **No** se promedian porcentajes
  calculados por dependencia.
- Toda consulta debe aplicar el mismo corte de tiempo y ámbito a numerador y
  denominador. Debe indicarse si el resultado representa el estado actual o
  un período histórico.

### 3.3 Semáforo

- **Verde:** cumple la meta o permanece en el rango aceptable.
- **Amarillo:** alerta preventiva; revisar causas y definir acción correctiva.
- **Rojo:** desviación; asignar responsable, acción y fecha de seguimiento.
- Los umbrales de esta propuesta no constituyen una norma legal ni una meta
  institucional aprobada. Deben validarse con la autoridad patrimonial y
  revisarse una vez obtenida la línea base.

## 4. Catálogo de indicadores

### 4.1 Indicadores calculables con la estructura actual

| ID | Indicador y objetivo | Definición y fórmula | Meta y rangos iniciales | Fuente, corte y desagregación |
|---|---|---|---|---|
| IG-01 | **Integridad básica del registro**. Obj. 1 | Numerador: bienes vigentes con `codigo` y `descripcion` no vacíos, estado perteneciente al catálogo válido y `dependencia_id` asociado a una dependencia existente. Denominador: total de bienes vigentes. Fórmula: numerador ÷ denominador × 100. | **Meta ≥98%**. Verde: ≥98%; amarillo: ≥95% y <98%; rojo: <95%. | `bienes.codigo`, `descripcion`, `estado`, `dependencia_id`; existencia en `dependencias`. Estado al corte; mensual. Institución, unidad y dependencia. |
| IG-02 | **Disponibilidad administrativa**. Obj. 2 | Bienes vigentes con estado `ACTIVO` ÷ total de bienes vigentes × 100. Mide la clasificación capturada, no disponibilidad física comprobada. | **Meta ≥95%**. Verde: ≥95%; amarillo: ≥90% y <95%; rojo: <90%. | `bienes.estado`; corte actual y mensual. Institución, unidad, dependencia y tipo de bien. |
| IG-03 | **Bienes que requieren intervención**. Obj. 2 | Bienes vigentes con estado `DAÑADO` o `EN_MANTENIMIENTO` ÷ total de bienes vigentes × 100. No suma bienes `EXTRAVIADO` ni `EN_CAMINO`; estos se muestran por separado. | **Meta ≤5%**. Verde: ≤5%; amarillo: >5% y ≤10%; rojo: >10%. | `bienes.estado`; corte actual y mensual. Institución, unidad, dependencia y tipo. |
| IG-04 | **Tasa de bienes extraviados**. Obj. 2 | Bienes vigentes con estado `EXTRAVIADO` ÷ total de bienes vigentes × 100. Complementar siempre con el conteo absoluto. | **Meta 0%**. Verde: 0%; amarillo: >0% y ≤1%; rojo: >1%. Cada caso requiere verificación individual, aunque el resultado global sea verde o amarillo. | `bienes.estado`; alerta inmediata y resumen mensual. Institución y ubicación organizativa. |
| IG-05 | **Cobertura de responsables en dependencias con bienes**. Obj. 3 | Dependencias que tienen uno o más bienes vigentes y un `responsable_id` que apunta a un responsable existente ÷ dependencias con uno o más bienes vigentes × 100. | **Meta 100%**. Verde: ≥98%; amarillo: ≥95% y <98%; rojo: <95%. Además del porcentaje, listar las dependencias sin responsable. | `dependencias.responsable_id`, `responsables.id`, `bienes.dependencia_id` y estado. Corte actual; mensual. Por unidad y dependencia. |
| IG-06 | **Cobertura de evidencia fotográfica**. Obj. 3 | Bienes vigentes cuyo campo `fotografia` no es nulo/vacío **y cuyo archivo existe y es accesible** en el almacenamiento configurado ÷ total de bienes vigentes × 100. Si solo se revisa la ruta almacenada, reportar como “cobertura de ruta”, no como evidencia válida. | **Meta ≥95%**. Verde: ≥95%; amarillo: ≥85% y <95%; rojo: <85%. | `bienes.fotografia` y almacenamiento de archivos. Corte actual; mensual. Por institución, unidad y dependencia. |
| IG-07 | **Cobertura de valoración registrada**. Obj. 4 | Bienes vigentes con `precio` mayor que cero ÷ total de bienes vigentes × 100. Precio cero se considera “sin valoración” para este indicador, aunque la columna tenga valor por defecto cero. | **Meta ≥98%**. Verde: ≥98%; amarillo: ≥95% y <98%; rojo: <95%. | `bienes.precio`; corte actual; mensual. Por institución, unidad, dependencia y tipo. |
| IG-08 | **Valor registrado del inventario vigente**. Obj. 4 | Suma de `precio` de bienes vigentes con precio mayor que cero. Reportar junto con cobertura IG-07, cantidad de bienes incluidos, moneda y fecha de corte. No es un porcentaje, por lo que no lleva semáforo de cumplimiento hasta que se apruebe una meta presupuestaria. | **Meta:** no fijar una meta de monto sin política de valoración y plan patrimonial aprobados. Mostrar tendencia comparada en períodos equivalentes. | `bienes.precio`, `bienes.estado`; corte actual y evolución mensual solo si hay históricos guardados. La suma actual no equivale a valor contable, depreciado ni de reposición. |
| IG-09 | **Bienes registrados en el período**. Obj. 5 | Conteo de bienes cuya `fecha_registro` está dentro del intervalo `[inicio, fin]`. Presentar conteo mensual y acumulado del año. No interpretar un incremento del conteo como mejora por sí solo. | **Meta:** derivada de un plan anual de levantamiento o incorporación aprobado. Sin ese plan, mostrar tendencia sin semáforo. | `bienes.fecha_registro`; mensual y anual. Por institución, unidad, dependencia y tipo. |
| IG-10 | **Movimientos de bienes registrados en el período**. Obj. 5 | Conteo de movimientos asociados a un bien y cuya fecha está en `[inicio, fin]`. Presentar también desglose por tipo de movimiento. Es una medida de actividad, no de calidad ni de eficiencia. | **Meta:** no se fija una cuota general; depende de las operaciones y del plan de trabajo. Investigar variaciones atípicas, no asumir que más movimientos equivalen a mejor gestión. | `movimientos.bien_id`, `tipo`, `fecha`; únicamente movimientos con asociación inequívoca a un bien. Mensual y por ámbito organizativo del bien. |

**Notas de interpretación para IG-01 a IG-10:**

- IG-01 mide la presencia y consistencia de campos seleccionados; no valida
  físicamente el bien ni acredita que su descripción sea correcta.
- IG-05 usa como universo las dependencias que actualmente tienen bienes
  vigentes, de modo que una dependencia sin bienes no reduzca la cobertura.
  Conviene mostrar aparte cuántas dependencias totales no tienen responsable.
- IG-06 requiere comprobar existencia del archivo. Una ruta presente puede
  apuntar a un archivo eliminado o inaccesible.
- IG-08 excluye precios nulos y cero del valor presentado; se debe mostrar
  explícitamente cuántos bienes quedaron sin valorar. No aplicar depreciación,
  conversión monetaria ni revalorización sin reglas aprobadas y campos de
  soporte.
- IG-10 excluye movimientos genéricos que no se puedan asociar sin ambigüedad
  a un bien. El modelo permite `bien_id` nulo y sujeto polimórfico; la
  consulta debe documentar la regla de asociación y evitar contar dos veces
  un movimiento que tenga ambas referencias.

### 4.2 Indicadores recomendados después de ampliar la captura de datos

| ID propuesto | Indicador | Fórmula y utilidad | Datos/requisitos adicionales |
|---|---|---|---|
| IG-11 | **Cumplimiento de verificación física** | Bienes verificados físicamente en el período ÷ bienes programados para verificación en el período × 100. Permite medir el avance del inventario físico. | Crear campañas/actas de verificación con período, universo congelado, fecha, bien, resultado, verificador y observación. No inferir verificación por consulta o fotografía antigua. |
| IG-12 | **Tiempo medio de resolución de mantenimiento** | Promedio de días entre inicio de mantenimiento y fecha de retorno a `ACTIVO`, para casos cerrados en el período. Mostrar también mediana y casos abiertos. | Registrar eventos de inicio/fin de cada episodio, estado previo/posterior y bien; el estado actual por sí solo no permite reconstruir duración ni reincidencias. |
| IG-13 | **Tasa de movimientos con trazabilidad completa** | Movimientos de bien con bien identificable, tipo válido, fecha, usuario responsable y observación/acta cuando el procedimiento la exige ÷ movimientos de bien del período × 100. | Estandarizar catálogo de tipos, obligatoriedad de usuario, reglas de acta por tipo y asociación del sujeto. Definir formalmente qué campos son obligatorios para cada movimiento. |
| IG-14 | **Antigüedad patrimonial** | Distribución de bienes por bandas de antigüedad, o proporción con edad superior al umbral aprobado. Fecha base de edad: adquisición/incorporación patrimonial, no fecha de digitación. | Agregar `fecha_adquisicion` o `fecha_incorporacion_patrimonial`, política de bandas por tipo de bien y tratamiento de fecha desconocida. `fecha_registro` no debe usarse como sustituto automático. |
| IG-15 | **Costo y recurrencia de mantenimiento** | Costo de reparación del período y tasa de bienes con dos o más episodios de mantenimiento en una ventana definida. | Registrar órdenes/episodios de mantenimiento, costos, fechas y bien asociado. No puede deducirse del estado `EN_MANTENIMIENTO` actual. |

Estos indicadores de fase posterior no deben aparecer como cero cuando falten
los datos requeridos. Deben mostrarse como **No disponible** hasta que exista
captura suficiente y validada.

## 5. Evaluación del Dashboard actual

La implementación actual del Dashboard ya calcula y presenta:

- Conteos de bienes y distribución por estado y tipo.
- Conteos de organismos, unidades administradoras, dependencias y usuarios
  activos.
- Suma del precio de bienes cuyo estado no es `DESINCORPORADO`.
- Cinco dependencias con más bienes.
- Últimos diez movimientos y registros de bienes por mes en una ventana de
  doce meses.

La distribución por estados/tipos y el conteo de registros son estadísticas
descriptivas; no tienen metas, numerador/denominador o semáforo. El valor
monetario actual incluye el efecto del precio por defecto en cero y no presenta
en paralelo la cobertura de valoración. Tampoco valida la existencia de
archivos fotográficos ni el estado de los responsables. El Dashboard debe
ampliarse para materializar IG-01 a IG-10 con las reglas del catálogo; los
indicadores IG-11 a IG-15 dependen primero de captura adicional.

## 6. Especificación técnica de implementación

### 6.1 Fuentes y campos relevantes

| Entidad | Campos usados | Consideraciones |
|---|---|---|
| `bienes` | `id`, `codigo`, `descripcion`, `dependencia_id`, `estado`, `tipo_bien`, `precio`, `fotografia`, `fecha_registro` | `dependencia_id` puede ser nulo. `precio` puede tener cero como valor por defecto. Los bienes se vinculan a estados y tipos representados por valores canónicos. |
| `dependencias` | `id`, `unidad_administradora_id`, `responsable_id` | `responsable_id` es opcional. El alcance organizativo se obtiene a través de la unidad administradora. |
| `responsables` | `id` y datos de identidad | La asignación del responsable se realiza desde la dependencia, no directamente desde el bien. |
| `movimientos` | `id`, `bien_id`, `subject_type`, `subject_id`, `tipo`, `fecha`, `usuario_id`, `observaciones` | `bien_id` y `usuario_id` pueden ser nulos; hay asociación polimórfica. Acordar una única regla de elegibilidad antes de agregar. |
| Almacenamiento público | ruta indicada por `bienes.fotografia` | La existencia del campo no acredita existencia, legibilidad ni vigencia del archivo. Validar de forma controlada y evitar una lectura de disco por cada fila en el renderizado del Dashboard. |

### 6.2 Reglas de consulta y consistencia

1. Centralizar la población de bienes vigentes y sus reglas en un componente de
   dominio reutilizable; no duplicar filtros diferentes entre Dashboard,
   reportes y exportaciones.
2. Para IG-01, contar un bien como completo únicamente si cada condición de la
   definición es verdadera. Los valores nulos y referencias huérfanas cuentan
   en el denominador y no en el numerador.
3. Para IG-05, usar `EXISTS`/conteos agrupados para determinar dependencias con
   bienes vigentes y comprobar que `responsable_id` referencia un registro
   válido; no multiplicar bienes por uniones uno-a-muchos.
4. Para IG-06, verificar archivos con el servicio de almacenamiento de Laravel
   y diseñar el cálculo para lotes/caché, de modo que no se realice una llamada
   independiente al almacenamiento remoto por bien en cada carga de página.
5. Para IG-08, incluir solo precios positivos y exponer numerador de bienes
   valorados y total de bienes vigentes junto al monto.
6. Para IG-10, utilizar una sola clave de asociación del bien por movimiento y
   evitar doble conteo por `bien_id` más `subject_type`/`subject_id`. Excluir
   los eventos no relacionados con bienes.
7. Parametrizar fechas de inicio y fin con límites inclusivos y una zona
   horaria institucional explícita. Para consultas de fecha, usar intervalos
   equivalentes a `>= inicio` y `< día_siguiente_a_fin` cuando la columna
   tenga componente horario.
8. Aplicar filtros organizativos al bien asociado al movimiento, no a la
   identidad del usuario que ejecutó la operación.
9. Usar agregaciones SQL (`COUNT`, `SUM`, `EXISTS`, agrupaciones) y evitar
   cargar la colección completa de bienes o movimientos para calcular las
   métricas. Mantener consultas compatibles con el motor de base de datos
   soportado por el proyecto.
10. Los cambios o correcciones de estado afectan la cifra actual. No afirmar
    que el resultado actual representa un corte histórico que no haya sido
    guardado.

### 6.3 Contrato de resultado de cada indicador

El servicio de indicadores debería devolver, como mínimo:

| Campo | Tipo lógico | Descripción |
|---|---|---|
| `codigo` | cadena | Identificador estable, por ejemplo `IG-02`. |
| `nombre` | cadena | Etiqueta visible y accesible. |
| `periodo_inicio`, `periodo_fin` | fecha/hora o nulo | Intervalo consultado; nulo para métricas de estado actual. |
| `fecha_corte` | fecha/hora | Instante de referencia de la medición. |
| `ambito` | objeto | Tipo y clave del ámbito: institución, unidad o dependencia. |
| `numerador`, `denominador` | entero o decimal | Valores brutos utilizados; pueden ser nulos si no aplican. |
| `valor` | decimal o nulo | Porcentaje, cantidad o monto sin redondear; nulo cuando sea N/A. |
| `unidad_medida` | cadena | `%`, `bienes`, `movimientos`, `Bs.` u otra unidad acordada. |
| `meta`, `limite_amarillo`, `limite_rojo` | decimal o nulo | Umbrales versionados; nulos si el indicador no tiene meta aprobada. |
| `semaforo` | enumeración | `verde`, `amarillo`, `rojo` o `no_disponible`. |
| `version_formula` | cadena | Versión de la definición aplicada. |
| `calculado_en` | fecha/hora | Momento en que se generó el resultado. |

La capa de presentación no debe volver a implementar fórmulas o umbrales; debe
formatear el resultado producido por el servicio y ofrecer navegación a los
registros que componen las alertas, respetando permisos.

### 6.4 Persistencia de cortes e historial

El Dashboard actual permite consultar el estado vigente y los registros por
mes, pero los estados actuales no permiten reconstruir de forma fiable qué
porcentaje había en una fecha pasada. Para comparar períodos históricos de
IG-01 a IG-08:

- Calcular y guardar un corte al menos una vez al mes, con indicador, ámbito,
  numerador, denominador, valor, período, fecha de cálculo y versión de fórmula;
  o
- Conservar eventos temporales completos para reconstruir cada estado a una
  fecha.

La opción inicial recomendada es una tabla de cortes, por ejemplo
`indicadores_gestion_cortes`, con índice único por
`codigo + fecha_corte + tipo_ambito + ambito_id + version_formula`. No guardar
solo el porcentaje: los conteos y la versión de fórmula son necesarios para
auditar y recalcular resultados. Esta tabla es una recomendación de diseño,
no existe actualmente en el sistema.

## 7. Requisitos funcionales de visualización

- Mostrar tarjetas con resultado, unidad, meta, semáforo, numerador,
  denominador y fecha de corte.
- Permitir filtro por período y ámbito organizativo en indicadores que tengan
  fuente y granularidad compatibles.
- Ofrecer detalle de filas causantes de IG-01, IG-04, IG-05, IG-06 e IG-07,
  sujeto a permisos de usuario.
- Incluir etiquetas de ayuda para diferenciar “estado actual” de “resultado
  histórico” y para comunicar que la disponibilidad es administrativa.
- Presentar datos faltantes como “N/A” o “No disponible”; nunca reemplazarlos
  silenciosamente por cero.
- Mantener una tabla o panel de definición con responsable del indicador,
  periodicidad, última actualización y versión aprobada de meta.
- Diseñar los colores con texto/icono además del color para asegurar
  accesibilidad.

## 8. Gobierno, metas y ciclo de mejora

1. **Validación institucional:** la autoridad patrimonial aprueba definición,
   población, frecuencia, metas y responsable de cada indicador.
2. **Línea base:** calcular durante tres a seis meses antes de usar metas como
   criterio formal. Conservar tanto los valores mensuales como sus conteos.
3. **Revisión:** revisar mensualmente indicadores operativos y trimestralmente
   metas, incidencias y acciones.
4. **Acción correctiva:** cada resultado rojo debe generar una tarea con causa,
   acción, responsable, fecha límite y estado.
5. **Auditoría:** registrar cuándo se modifican fórmulas, umbrales o
   exclusiones y conservar la versión anterior para interpretar históricos.
6. **Metas con política pendiente:** IG-08, IG-09 e IG-10 no deben mostrar
   semáforo mientras no se apruebe un objetivo cuantitativo o una política de
   interpretación.

## 9. Plan recomendado de incorporación

| Fase | Trabajo | Criterio de aceptación |
|---|---|---|
| 1. Aprobación | Confirmar definiciones, universos, responsables, metas iniciales, frecuencia y moneda. | Catálogo validado por autoridad patrimonial, con responsable por indicador. |
| 2. Calidad de datos | Revisar bienes sin dependencia, estados inválidos, precios cero y fotografías faltantes o inexistentes. | Reporte de excepciones disponible y plan de corrección priorizado. |
| 3. Cálculo base | Implementar IG-01 a IG-10 en servicio único y comparar cálculos con conteos manuales controlados. | Casos de prueba para denominador cero, estados desconocidos, desincorporados, filtros organizativos y movimientos polimórficos. |
| 4. Dashboard y reportes | Añadir filtros, metas, semáforos, cortes y detalle de excepciones. | El mismo ámbito y período produce iguales valores en Dashboard y reporte. |
| 5. Históricos y expansión | Persistir cortes mensuales; evaluar captura adicional para IG-11 a IG-15. | Los cortes pueden auditarse y los indicadores no disponibles no se presentan como cero. |

## 10. Riesgos de interpretación

- Un bien registrado como `ACTIVO` no necesariamente fue inspeccionado ni está
  físicamente disponible; la confirmación exige verificación física.
- La existencia de una ruta de fotografía no garantiza evidencia accesible o
  actual.
- El valor total calculado a partir de `precio` no representa automáticamente
  valor contable actualizado, depreciación o costo de reposición.
- Un conteo de movimientos o nuevos registros no es una medida de eficiencia
  sin un plan, denominador o estándar de servicio.
- La ausencia de históricos de estados impide reconstruir tasas mensuales
  pasadas de condición y cobertura con exactitud.
- Las metas propuestas son puntos de partida gerenciales, no estándares
  oficiales ni obligaciones regulatorias.

## 11. Glosario

- **Bien vigente:** bien no desincorporado según su estado al corte.
- **Línea base:** medición inicial que sirve para conocer la situación y
  ajustar metas.
- **Ámbito:** nivel institucional sobre el que se calcula un indicador:
  institución, unidad administradora o dependencia.
- **Corte:** fecha/hora a la que corresponde el estado medido.
- **Semáforo:** clasificación del resultado respecto de los rangos aprobados.
- **Trazabilidad:** posibilidad de identificar bien, tipo, fecha, actor y
  respaldo de una operación relacionada con el bien.
