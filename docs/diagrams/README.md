# Diagramas del sistema — Asesoría IUT

Esta carpeta contiene diagramas en formato Mermaid para documentar la arquitectura y el modelo de datos del sistema.

Archivos:
- `architecture.mmd` — Diagrama de arquitectura general (web, app, queue, DB, storage).
- `er_diagram.mmd` — Diagrama ER simplificado con las entidades principales.
- `er_detailed.mmd` — Diagrama ER detallado generado desde las migrations (campos y tipos principales).
- `class_diagram.mmd` — Diagrama de clases (modelo simplificado).
- `components.mmd` — Diagrama de componentes (visión de alto nivel).
- `layers.mmd` — Diagrama de capas (Presentation, Application, Domain, Infrastructure).
- `sequence_movimiento.mmd` — Secuencia para el flujo de movimiento de un bien.
- `flow_responsable.mmd` — Flujo de creación de un Bien y asignación de responsable.
 - `deployment.mmd` — Diagrama de despliegue (entornos, servidores, CI/CD).
 - `user_journey.mmd` — User Journey / Story Map para el flujo de movimiento de bienes.
 - `state_bien.mmd` — Diagrama de estados para la entidad `Bien`.
 - `sequence_login.mmd` — Secuencia del proceso de login / autenticación.
 - `api/openapi_stub.yaml` — Stub OpenAPI para el recurso `Bien` (CRUD) y contrato para frontend.

### Diagramas de Secuencia (Nuevos)
- `sequence_bienes.mmd` — Secuencia completa para operaciones CRUD de Bienes (listar, crear, ver, editar, eliminar, exportar PDF, galería, reportes).
- `sequence_usuarios_api.mmd` — Secuencia completa para operaciones CRUD de Usuarios (listar, crear, ver, editar, eliminar, exportar PDF, importar desde API).
- `sequence_dependencias.mmd` — Secuencia completa para operaciones CRUD de Dependencias (listar, crear, ver, editar, exportar PDF).
- `sequence_organismos.mmd` — Secuencia completa para operaciones CRUD de Organismos (listar, crear, ver, editar, exportar PDF).
- `sequence_unidades_administradoras.mmd` — Secuencia completa para operaciones CRUD de Unidades Administradoras (listar, crear, ver, editar, exportar PDF).
- `sequence_responsables.mmd` — Secuencia completa para operaciones CRUD de Responsables (listar, crear, ver, editar, eliminar).
- `sequence_reportes.mmd` — Secuencia para generación de reportes y gráficas (listar tipos, generar gráficas, exportar PDF).
- `sequence_dashboard.mmd` — Secuencia para carga del dashboard principal con KPIs y métricas.
- `sequence_perfil.mmd` — Secuencia para gestión de perfil de usuario (ver perfil, actualizar contraseña, actualizar datos, gestionar foto).
- `sequence_auditoria.mmd` — Secuencia para visualización de registros de auditoría.
- `sequence_busqueda.mmd` — Secuencia para búsqueda global en el sistema.
- `sequence_movimientos_detallado.mmd` — Secuencia detallada para operaciones de Movimientos (listar, crear, ver, editar, eliminar, restaurar, exportar PDF, reintegrar bienes).

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
