<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$deps = DB::table('dependencias')->get();
echo count($deps) . " dependencias\n";
foreach ($deps as $d) {
    $c = DB::table('bienes')->where('dependencia_id', $d->id)->count();
    echo "dep {$d->codigo} (id {$d->id}): {$c} bienes\n";
}
