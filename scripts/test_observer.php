<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
// Bootstrap the application (so observers are registered)
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Dependencia;
use App\Models\Bien;
use App\Models\Movimiento;
use App\Enums\EstadoBien;

$dep = Dependencia::first();
if (! $dep) {
    echo "No hay dependencias en la base de datos.\n";
    exit(1);
}

$b = Bien::create([
    'dependencia_id' => $dep->id,
    'codigo' => 'TEST-' . uniqid(),
    'descripcion' => 'Prueba movimiento observer',
    'precio' => 0,
    'estado' => EstadoBien::ACTIVO->value,
    'fecha_registro' => now(),
]);

echo "Bien ID: {$b->id}\n";

$count = Movimiento::where('bien_id', $b->id)->count();
echo "Movimientos for bien: {$count}\n";

$mov = Movimiento::where('bien_id', $b->id)->first();
if ($mov) {
    echo "Primer movimiento: tipo={$mov->tipo}, observaciones={$mov->observaciones}, usuario_id={$mov->usuario_id}\n";
}

return 0;
