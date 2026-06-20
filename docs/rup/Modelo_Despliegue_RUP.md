# Modelo de Despliegue - RUP

## Objetivo

Describir la configuración de despliegue y los entornos recomendados para el sistema.

## Entornos

- Desarrollo: máquina local con PHP, Composer, Node.js y SQLite.
- Pruebas: servidor con acceso controlado para pruebas de integración.
- Producción: servidor LAMP/LEMP con MySQL/Postgres, almacenamiento persistente y backups.

## Requisitos de software

- PHP 8.2
- Composer
- Node.js 18+
- NPM o Yarn
- Servidor web (Nginx/Apache)
- Base de datos (MySQL/Postgres recomendado en producción)

## Procedimiento de despliegue

1. Clonar repositorio.
2. `composer install`.
3. `cp .env.example .env` y configurar variables de entorno.
4. `php artisan key:generate`.
5. `php artisan migrate --seed`.
6. `npm install && npm run build`.
7. Configurar servidor web para apuntar a `public/`.

## Backup y mantenimiento

- Programar backups de la base de datos y del directorio `storage/`.
- Revisar y rotar logs periódicamente.
- Mantener dependencias actualizadas.

## Observaciones

- Para despliegues en entornos con más tráfico, usar Postgres/MySQL y considerar caché/Queue.
- Contenerización con Docker es opcional y recomendable para reproducibilidad.
