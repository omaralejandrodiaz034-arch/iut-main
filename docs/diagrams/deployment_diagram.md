# Diagrama de Despliegue — Arquitectura completa del sistema

Diagrama detallado que cubre: Internet → CDN/WAF → balanceadores → capas web (Nginx+PHP-FPM) en múltiples zonas, workers/colas, base de datos con réplicas, almacenamiento de objetos, CI/CD, observabilidad, backups y servicios externos.

```mermaid
flowchart LR
  %% --- Entradas externas ---
  Internet[(Internet)]
  CDN[CDN (Cloudflare / Fastly)]
  WAF[WAF (ModSecurity / Cloud WAF)]

  %% --- Edge / Ingress ---
  LB[Load Balancer / Ingress (ALB / Traefik)]
  TLS[TLS Termination (Let's Encrypt / ACM)]

  %% --- Web / App tier ---
  subgraph Prod[Producción - Cluster]
    direction TB
    subgraph WebTier[Web Tier]
      Nginx1[Nginx (node-1)]
      Nginx2[Nginx (node-2)]
      Nginx3[Nginx (node-N)]
    end

    subgraph AppTier[PHP-FPM Pool]
      PHP1[PHP-FPM worker-1]
      PHP2[PHP-FPM worker-2]
      PHPn[PHP-FPM worker-n]
    end

    subgraph WorkerTier[Background Workers]
      Horizon[Queue workers (Horizon/Worker)]
      Scheduler[Cron / Scheduler]
    end
  end

  %% --- Data tier / Storage ---
  subgraph Data[Datos y Almacenamiento]
    DBPrimary[(MySQL Primary)]
    DBReplica[(MySQL Read Replica(s))]
    ProxySQL[ProxySQL / HA DB Proxy]
    Redis[(Redis) - cache & queues]
    S3[(S3 / MinIO) - object storage]
    FS[Persistent FS (NFS) -> storage/app]
    Backups[Backup Service (snapshots, dumps)]
  end

  %% --- Supporting services ---
  Mail[SMTP (SES / Postfix / Mailgun)]
  Metrics[Prometheus + Grafana]
  Logs[ELK / Loki / EFK]
  Tracing[Jaeger / OpenTelemetry]
  Secrets[Secrets Manager (Vault / Parameter Store)]
  Monitoring[Healthchecks / UptimeRobot]
  CDNStorage[CDN for static/actas]

  %% --- CI / Infra ---
  subgraph CI[CI / Repo]
    GitHub[GitHub Repo]
    CIAction[CI/CD (Actions / GitLab CI)]
    Registry[Container Registry]
    IaC[Terraform / Pulumi]
  end

  %% --- Development / Staging ---
  subgraph DevEnv[Dev / Staging]
    DevMachine[Developer Laptop / Docker-Compose]
    Staging[Staging Cluster]
  end

  %% Conexiones principales
  Internet --> CDN --> WAF --> LB --> TLS
  TLS --> LB --> Nginx1
  LB --> Nginx2
  LB --> Nginx3

  Nginx1 --> PHP1
  Nginx2 --> PHP2
  Nginx3 --> PHPn

  PHP1 --> ProxySQL --> DBPrimary
  PHP2 --> ProxySQL --> DBPrimary
  PHPn --> ProxySQL --> DBPrimary
  ProxySQL --> DBReplica

  PHP1 --> Redis
  PHP2 --> Redis
  PHPn --> Redis

  PHP1 --> S3
  PHP2 --> S3
  PHPn --> S3

  PHP1 --> Mail
  PHP2 --> Mail

  Horizon --> Redis
  Horizon --> DBPrimary
  Scheduler --> Horizon

  PHP1 --> Metrics
  PHP2 --> Metrics
  Horizon --> Metrics

  PHP1 --> Logs
  PHP2 --> Logs
  Horizon --> Logs

  PHP1 --> Tracing
  PHP2 --> Tracing

  S3 --> CDNStorage
  CDNStorage --> Internet

  Backups --> S3
  Backups --> Backups

  %% CI/CD flujo
  DevMachine --> GitHub
  GitHub --> CIAction --> Registry
  CIAction --> IaC
  CIAction --> Staging
  CIAction --> Prod

  %% Staging y Dev conexiones
  DevMachine --> DevMachine
  DevMachine --> Staging

  %% Observabilidad
  Metrics --> Grafana[Grafana]
  Logs --> Kibana[Kibana / Loki UI]
  Tracing --> Jaeger

  %% Secrets + Monitoring
  PHP1 --> Secrets
  PHP2 --> Secrets
  PHPn --> Secrets
  Monitoring --> LB

  classDef infra fill:#f8fafc,stroke:#1f2937
  class Prod,Data,CI,DevEnv infra

```

Operaciones y recomendaciones clave:

- Arquitectura:
  - Nginx actúa como reverse proxy estático y gestor de assets; PHP-FPM escala horizontalmente para manejar carga.
  - ProxySQL o un pool de conexiones DB reduce la carga y permite réplicas de lectura.

- Almacenamiento de actas/PDFs:
  - Guardar en S3/MinIO y servir vía CDNStorage. Mantener `public/storage` como respaldo local para entornos pequeños.
  - Configurar lifecycle en S3 para purgar PDFs antiguos si es necesario.

- Colas y tareas en background:
  - Redis para colas; Horizon (Laravel) para monitorización de jobs.
  - Workers dedicados generan actas y realizan imports, para evitar bloquear peticiones HTTP.

- CI/CD, Infra y despliegue:
  - Usar pipelines para build → test → build image → push registry → deploy.
  - IaC (Terraform) para provisionar infra; soportar blue/green o canary deploys.

- Observabilidad y seguridad:
  - Prometheus + Grafana para métricas; ELK/Loki para logs; Jaeger/OpenTelemetry para trazas.
  - TLS en el edge, WAF, y Secrets Manager para credenciales (DB, S3, API tokens).

- Backups y DR:
  - Dump diario de BD con retención; snapshots del FS y backups de S3.
  - Procedures de recuperación y runbook documentado.

- Entorno local reproducible:
  - `docker-compose` con servicios mínimos: nginx, php-fpm, mysql, redis, mailhog, minio.

Si quieres, genero el `docker-compose.yml` base que refleje esta arquitectura mínima (dev), y/o el `pipeline` de GitHub Actions y un `README` operativo.

