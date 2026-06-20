Arquitectura y despliegue

Resumen:
- Aplicación Laravel (PHP) con Blade y Vite para assets.
- Persistencia: migraciones definidas (SQLite/MySQL).
- Patrón: MVC con Eloquent para modelos y relaciones.

Componentes principales:
- Controladores HTTP (app/Http/Controllers)
- Modelos Eloquent (app/Models)
- Vistas Blade (resources/views)
- Migraciones (database/migrations)

Despliegue:
- Requisitos: PHP 8.1+, Composer, Node.js (para Vite), base de datos soportada.
- Comandos básicos:
```bash
composer install
cp .env.example .env
php artisan migrate --seed
npm install
npm run build
php artisan serve
```
