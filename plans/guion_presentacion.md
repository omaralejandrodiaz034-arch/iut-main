# Guion de Presentación del Sistema de Gestión de Inventario de Bienes

## Contexto de la Presentación

La presentación se realiza en una reunión Scrum o sesión de kickoff, donde se explica el sistema a los tres roles clave del equipo: Programador (Desarrollador Backend), Jefe de Proyecto (Scrum Master/Project Manager), y Frontier (Desarrollador Frontend). La ubicación es una sala de reuniones virtual o presencial en la oficina del proyecto.

**Duración Estimada**: 60-90 minutos (20-30 minutos por rol, más Q&A).

**Materiales Necesarios**: Proyector para mostrar diagramas, documentos impresos o digitales de las explicaciones.

**Estructura**:
1. Introducción general (5 min) - Presentador (Tú o el Líder Técnico).
2. Explicación por rol (15-20 min cada uno) - Presentador asignado.
3. Sesión de preguntas y respuestas (15-30 min) - Todos los participantes.
4. Cierre y próximos pasos (5 min) - Presentador.

## Quién Dice Qué y Dónde

### 1. Introducción General (Presentador: Tú o Arquitecto del Sistema)
   - **Dónde**: Sala de reuniones principal, frente al equipo.
   - **Qué decir**:
     "Buenos días/tardes, equipo. Gracias por asistir a esta sesión de explicación del Sistema de Gestión de Inventario de Bienes. Este sistema es una aplicación web desarrollada en Laravel 12, diseñada para gestionar el inventario de activos patrimoniales en instituciones educativas venezolanas, específicamente en las Universidades Politécnicas Territoriales (UPTOS). Su objetivo principal es garantizar la trazabilidad, transparencia y control total de los bienes públicos, facilitando auditorías gubernamentales y optimizando la administración de activos.

     Hoy explicaremos el sistema desde tres perspectivas clave en nuestro equipo Scrum: el Programador, quien se enfoca en el desarrollo backend; el Jefe de Proyecto, responsable de la gestión y facilitación del proceso Scrum; y el Frontier, encargado del frontend y la experiencia de usuario.

     La sesión se divide en: una introducción general (que acabo de dar), explicaciones específicas por rol, una sesión de preguntas y respuestas, y un cierre con próximos pasos. Usaremos diagramas y documentos para ilustrar los puntos clave. ¿Alguna pregunta antes de comenzar?"

### 2. Explicación para el Programador (Desarrollador Backend)
   - **Quién dice**: El Arquitecto o Desarrollador Senior (Tú).
   - **Dónde**: Misma sala, usando el proyector para mostrar diagramas.
   - **Qué decir** (Resumir [`plans/explicacion_programador.md`](plans/explicacion_programador.md:1)):
     "Programador, el backend está en Laravel 12 con SQLite. La jerarquía es Organismo → Unidad → Dependencia → Bien. Modelos usan Eloquent con traits como AuditableTrait. Controladores validan con $request->validate(), usan eager loading. Servicios manejan lógica compleja como BienTypeService. Incluye diagramas de jerarquía y flujo. ¿Preguntas?"

### 3. Explicación para el Jefe de Proyecto (Scrum Master/Project Manager)
   - **Quién dice**: El Product Owner o Gerente de Proyecto (Si no eres tú, coordina).
   - **Dónde**: Continúa en la sala, mostrando el roadmap y backlog.
   - **Qué decir** (Resumir [`plans/explicacion_jefe_proyecto.md`](plans/explicacion_jefe_proyecto.md:1)):
     "Jefe de Proyecto, como Scrum Master, tu rol es facilitar el proceso. La visión es garantizar trazabilidad de bienes para auditorías. Product Backlog prioriza HU críticas. Estamos en Sprint 2 de 4, con velocity de 35-45 puntos. Roadmap incluye funcionalidades como QR y notificaciones. Monitorea burndown charts. ¿Comentarios sobre gestión?"

### 4. Explicación para el Frontier (Desarrollador Frontend)
   - **Quién dice**: El Diseñador UX o Desarrollador Frontend Senior.
   - **Dónde**: Sala, demostrando vistas en pantalla o mockups.
   - **Qué decir** (Resumir [`plans/explicacion_frontier.md`](plans/explicacion_frontier.md:1)):
     "Frontier, el frontend usa Blade con Tailwind CSS y Vite. Layouts incluyen navegación y breadcrumbs. Formularios validan en tiempo real, con modo oscuro. Componentes como show-actions son reutilizables. UX es responsive y accesible. Flujo incluye login, gestión de bienes y reportes. ¿Ideas para mejoras?"

### 5. Sesión de Q&A
   - **Quién dice**: Todos los participantes, moderado por el Presentador.
   - **Dónde**: Sala, en ronda.
   - **Qué decir**: "Ahora abrimos preguntas. Programador, ¿dudas técnicas? Jefe de Proyecto, ¿sobre proceso? Frontier, ¿sobre UI?"

### 6. Cierre
   - **Quién dice**: Presentador.
   - **Dónde**: Sala.
   - **Qué decir**: "Gracias por la atención. Los documentos completos están disponibles en /plans/. Próximos pasos: revisión de sprints y asignación de tareas. ¿Algo más?"

## Notas Adicionales

- **Adaptación**: Si es virtual, usar Zoom con pantalla compartida.
- **Idioma**: Español, ya que el sistema y documentos están en español.
- **Feedback**: Anota preguntas para refinar los documentos si es necesario.
- **Tiempo**: Mantén el ritmo para no exceder el tiempo.