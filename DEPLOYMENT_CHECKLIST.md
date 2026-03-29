# Deployment Checklist — Normalización de la base de datos

Objetivo: aplicar la migración `2026_02_17_000001_normalize_nullable_columns.php` en entornos de staging/producción sin pérdida de datos.

Pasos recomendados:

1. Backup completo
   - Hacer dump de la base de datos (mysqldump / pg_dump / sqlite copy). Verificar tamaño y checksum.
   - Exportar esquema y datos de tablas críticas (`bienes`, `bienes_electronicos`, `bienes_vehiculos`, `bienes_mobiliarios`, `bienes_otros`, `reportes`).

2. Validación en staging
   - Clonar la base de datos a un entorno de staging.
   - Ejecutar la migración en staging `php artisan migrate --path=database/migrations/2026_02_17_000001_normalize_nullable_columns.php`.
   - Ejecutar la suite de tests (`php artisan test`) y revisar manualmente listados y formularios de `bienes`.

3. Revisión de datos post-migración
   - Ejecutar queries para detectar valores vacíos remanentes:
     - `SELECT * FROM bienes_electronicos WHERE procesador = '' OR memoria = '' ...` (adaptar columnas)
     - Comprobar `NULL` donde corresponda.
   - Revisar integridad referencial y triggers.

4. Plan de rollback
   - Mantener el backup SQL disponible.
   - Si hay problemas, restaurar backup y ejecutar `php artisan migrate:rollback --step=1` según el esquema de migraciones.
   - Documentar cualquier diferencia y preparar un plan de corrección manual para los registros afectados.

5. Comunicaciones y ventana de mantenimiento
   - Programar ventana breve con stakeholders.
   - Informar a usuarios y bloquear operaciones concurrentes de escritura si es necesario.

6. Monitorización post-despliegue
   - Revisar logs de la aplicación y métricas de errores durante 24 horas.
   - Ejecutar tests de aceptación manuales en los flujos de creación/edición de `bienes`.

Notas:
- La migración convierte cadenas vacías/ceros a `NULL` y cambia nullabilidad. Hacer backup antes de aplicar.
- Evitar ejecutar en producción sin staging y respaldo verificable.
