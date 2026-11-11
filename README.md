# Sistema de Gestión de Inventario de Bienes (IUT)

Aplicación web desarrollada con Laravel 12 para gestionar el inventario institucional.
Este documento explica cómo iniciar el entorno Docker que ya está configurado en el
repositorio.

## Requisitos

- Docker Desktop (Windows/macOS) o Docker Engine (Linux), con Docker Compose v2.
- Node.js y npm para instalar y compilar los recursos del frontend.
- Git para obtener el proyecto.

No es necesario instalar PHP, Composer ni MySQL en el equipo anfitrión: PHP y MySQL
se ejecutan en contenedores.

## Servicios Docker

La configuración de `docker-compose.yml` inicia estos servicios:

| Servicio | Función | Acceso |
| --- | --- | --- |
| `app` | PHP 8.3-FPM y comandos de Laravel | Interno de Docker, puerto 9000 |
| `web` | Nginx | [http://localhost:8080](http://localhost:8080) |
| `db` | MySQL 8.0 | Puerto 3306 del equipo |

MySQL guarda sus datos en el volumen persistente `dbdata`. La aplicación debe
conectarse al host `db` (nombre del servicio de Compose), no a `localhost`.

## Primer inicio

Ejecuta los comandos desde la carpeta raíz del proyecto.

### 1. Crear el archivo `.env`

El repositorio no incluye un `.env.example`. Crea un archivo llamado `.env` en la
raíz con esta configuración mínima para desarrollo local:

```dotenv
APP_NAME="Sistema IUT"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=iut
DB_USERNAME=laravel
DB_PASSWORD=secret

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

Los valores de base de datos deben coincidir con los que Compose entrega al
contenedor MySQL. Cambia `DB_PASSWORD` antes de usar el entorno fuera de desarrollo.
El archivo `.env` no debe subirse al repositorio.

### 2. Construir e iniciar los contenedores

```bash
docker compose up -d --build
```

La primera construcción puede tardar varios minutos. Comprueba el estado con:

```bash
docker compose ps
```

### 3. Instalar dependencias PHP y generar la clave

El contenedor incluye Composer, pero la imagen no instala por sí misma las
dependencias del proyecto. Ejecuta:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 4. Preparar la base de datos

Espera a que MySQL termine de iniciar y aplica las migraciones:

```bash
docker compose exec app php artisan migrate
```

Opcionalmente, para cargar los datos de demostración en una base local vacía:

```bash
docker compose exec app php artisan db:seed
```

No ejecutes el seeder en una base con datos reales: carga datos de demostración.

### 5. Compilar los recursos del frontend

El contenedor no incluye Node.js ni un servicio Vite. En la terminal del equipo,
desde la raíz del proyecto, ejecuta:

```bash
npm ci
npm run build
```

Después abre [http://localhost:8080](http://localhost:8080).

## Uso diario

Iniciar o reconstruir los servicios:

```bash
docker compose up -d
```

Ver los servicios y seguir los registros:

```bash
docker compose ps
docker compose logs -f
```

Ejecutar comandos de Laravel dentro del contenedor:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan <comando>
```

Detener los contenedores sin borrar la base de datos:

```bash
docker compose down
```

`docker compose down -v` también elimina el volumen `dbdata` y, por tanto, los
datos guardados en la base MySQL. Úsalo solo si quieres borrar esos datos.

## Notas y solución de problemas

- Si los puertos 8080 o 3306 ya están ocupados, libera esos puertos o cambia las
  asignaciones en `docker-compose.yml`.
- Compose inicia MySQL antes que la aplicación, pero no espera a que MySQL esté
  listo para aceptar conexiones. Si la primera migración falla por conexión,
  revisa `docker compose logs db`, espera a que MySQL termine de iniciar y repite
  `docker compose exec app php artisan migrate`.
- Si cambias las variables de conexión después de iniciar los contenedores,
  recréalos para que MySQL reciba la nueva configuración:
  `docker compose up -d --force-recreate db app`.
- En el equipo anfitrión debe existir Node.js/npm solo para compilar el frontend;
  la configuración actual no ofrece recarga en caliente de Vite mediante Docker.
- Aunque la aplicación usa una conexión MySQL en Docker, el archivo
  `database.sqlite` del equipo no se importa automáticamente. Si tienes datos
  existentes en SQLite, debes migrarlos por separado.
- Compose no inicia un trabajador de colas ni el planificador de Laravel.
  `QUEUE_CONNECTION=database` solo configura el backend de la cola; los trabajos
  y tareas programadas requieren procesos adicionales si la aplicación los usa.
