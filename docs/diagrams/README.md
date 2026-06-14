# Diagramas del sistema — Asesoría IUT

Esta carpeta contiene diagramas en formato Mermaid para documentar la arquitectura y el modelo de datos del sistema.

## Estructura de carpetas

### Arquitectura y Modelo
- `architecture.mmd` — Diagrama de arquitectura general (web, app, queue, DB, storage).
- `er_diagram.mmd` — Diagrama ER simplificado con las entidades principales.
- `er_detailed.mmd` — Diagrama ER detallado generado desde las migrations.
- `class_diagram.mmd` — Diagrama de clases (modelo simplificado).
- `class_diagram_full.mmd` — Diagrama de clases completo.
- `components.mmd` — Diagrama de componentes (visión de alto nivel).
- `layers.mmd` — Diagrama de capas (Presentation, Application, Domain, Infrastructure).
- `deployment.mmd` — Diagrama de despliegue (entornos, servidores, CI/CD).
- `deployment_diagram.mmd` — Diagrama de despliegue alternativo.
- `state_bien.mmd` — Diagrama de estados para la entidad `Bien`.
- `architecture_components.mmd` — Componentes de arquitectura.

### Autenticación
- `sequence_login.mmd` — Secuencia del proceso de login / autenticación.

### Diagramas por entidad

#### usuarios/
- `sequence_usuarios_listar.mmd` — Listar usuarios con filtros.
- `sequence_usuarios_crear.mmd` — Crear nuevo usuario.
- `sequence_usuarios_ver.mmd` — Ver detalle de usuario.
- `sequence_usuarios_editar.mmd` — Editar usuario existente.
- `sequence_usuarios_eliminar.mmd` — Eliminar usuario.
- `sequence_usuarios_exportar_pdf.mmd` — Exportar usuario a PDF.
- `sequence_usuarios_importar_api.mmd` — Importar usuario desde API externa.

#### responsables/
- `sequence_responsables_listar.mmd` — Listar responsables con filtros.
- `sequence_responsables_crear.mmd` — Crear nuevo responsable.
- `sequence_responsables_ver.mmd` — Ver detalle de responsable.
- `sequence_responsables_editar.mmd` — Editar responsable existente.
- `sequence_responsables_eliminar.mmd` — Eliminar responsable.

#### bienes/
- `sequence_bienes_listar.mmd` — Listar bienes con filtros.
- `sequence_bienes_crear.mmd` — Crear nuevo bien.
- `sequence_bienes_ver.mmd` — Ver detalle de bien.
- `sequence_bienes_editar.mmd` — Editar bien existente.
- `sequence_bienes_eliminar.mmd` — Desincorporar bien (eliminación lógica).
- `sequence_bienes_galeria.mmd` — Ver galería de bienes.
- `sequence_bienes_trasladar.mmd` — Trasladar bien entre dependencias.

#### otros/
- `sequence_codigos_jerarquicos.mmd` — Generación de códigos jerárquicos (organismo → unidad → dependencia → bien).

#### movimientos/
- `sequence_movimientos_listar.mmd` — Listar movimientos con filtros.
- `sequence_movimientos_crear.mmd` — Crear nuevo movimiento.
- `sequence_movimientos_ver.mmd` — Ver detalle de movimiento.
- `sequence_movimientos_editar.mmd` — Editar movimiento existente.
- `sequence_movimientos_eliminar.mmd` — Eliminar movimiento.
- `sequence_movimientos_restaurar.mmd` — Restaurar registro eliminado.
- `sequence_movimientos_reintegrar.mmd` — Reintegrar bien desincorporado.
- `sequence_movimientos_eliminados.mmd` — Ver bienes desincorporados.

#### formatos/
- `sequence_formato_bien_pdf.mmd` — Exportar bien a PDF.
- `sequence_formato_movimiento_pdf.mmd` — Exportar movimiento a PDF.
- `sequence_formato_dependencia_pdf.mmd` — Exportar dependencia a PDF.
- `sequence_formato_reporte_bienes.mmd` — Generar reporte de bienes.
- `sequence_formato_unidad_pdf.mmd` — Exportar unidad a PDF.
- `sequence_formato_acta_traslado.mmd` — Generar acta de traslado (traslado de bienes).

#### otros/
- `sequence_organismos_listar.mmd` — Listar organismos con filtros.
- `sequence_organismos_crear.mmd` — Crear nuevo organismo.
- `sequence_organismos_ver.mmd` — Ver detalle de organismo.
- `sequence_organismos_editar.mmd` — Editar organismo existente.
- `sequence_organismos_eliminar.mmd` — Eliminar organismo (no permitido).
- `sequence_organismos_exportar_pdf.mmd` — Exportar organismo a PDF.
- `sequence_unidades_listar.mmd` — Listar unidades con filtros.
- `sequence_unidades_crear.mmd` — Crear nueva unidad.
- `sequence_unidades_ver.mmd` — Ver detalle de unidad.
- `sequence_unidades_editar.mmd` — Editar unidad existente.
- `sequence_unidades_eliminar.mmd` — Eliminar unidad (no permitido).
- `sequence_unidades_exportar_pdf.mmd` — Exportar unidad a PDF.
- `sequence_dependencias_listar.mmd` — Listar dependencias con filtros.
- `sequence_dependencias_crear.mmd` — Crear nueva dependencia.
- `sequence_dependencias_ver.mmd` — Ver detalle de dependencia.
- `sequence_dependencias_editar.mmd` — Editar dependencia existente.
- `sequence_dependencias_eliminar.mmd` — Eliminar dependencia (no permitido).
- `sequence_dependencias_exportar_pdf.mmd` — Exportar dependencia a PDF.

Cómo previsualizar
- En VS Code instale una extensión de Mermaid (por ejemplo: "Markdown Preview Enhanced" o "Mermaid Preview").
- Abrir el archivo `.mmd` y usar la vista previa de la extensión.

Exportar localmente (recomendado)
- Si tienes Node.js instalado, puedes instalar mermaid-cli globalmente o usar npx:

	```powershell
	npm install -g @mermaid-js/mermaid-cli
	mmdc -i docs/diagrams/architecture.mmd -o docs/diagrams/exported/architecture.svg
	```

- Alternativa usando npx (sin instalar globalmente):

	```powershell
	npx @mermaid-js/mermaid-cli -i docs/diagrams/er_detailed.mmd -o docs/diagrams/exported/er_detailed.svg
	```

Exportar en CI (listo)
- Se incluyó un workflow de GitHub Actions (`.github/workflows/generate-mermaid-diagrams.yml`) que instala `@mermaid-js/mermaid-cli` y exporta todos los `.mmd` de `docs/diagrams` a `docs/diagrams/exported/` en cada `push` a `main` y cuando se ejecuta manualmente.

Uso desde NPM (opcional)
- Se añadió un script `export-diagrams` a `package.json` que usa `npx` para ejecutar el export (útil en entornos locales o CI que ya tengan Node).

Validación
- Los diagramas han sido creados y validados sintácticamente con la herramienta Mermaid disponible en este entorno.

Siguientes pasos sugeridos
- Ejecutar el workflow (o ejecutar `npm run export-diagrams` localmente) para generar los SVG en `docs/diagrams/exported/`.
- Publicar los SVG resultantes en GitHub Pages o incluirlos en el README principal.
- Generar diagramas adicionales: diagrama de despliegue (Docker/Servidor), diagrama de secuencia para login/autenticación, diagramas por casos de uso.
 - Ejecutar el workflow (o ejecutar `npm run export-diagrams` localmente) para generar los SVG en `docs/diagrams/exported/`.
 - Publicar los SVG resultantes en GitHub Pages o incluirlos en el README principal.
 - Generar diagramas adicionales: diagrama de CI/CD detallado, wireframes de UI o OpenAPI completo para integraciones.

Si quieres, puedo: exportar los diagramas a SVG, añadir más detalles (campos y tipos exactos desde las migrations), o adaptar los diagramas al formato PlantUML.
