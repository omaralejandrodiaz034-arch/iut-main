<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$deps = DB::table('dependencias')->get();
foreach ($deps as $d) {
    $count = DB::table('bienes')->where('dependencia_id', $d->id)->count();
    if ($count > 30) {
        $toDelete = $count - 30;
        // eliminar los bienes más recientes para mantener los 30 más antiguos
        $ids = DB::table('bienes')->where('dependencia_id', $d->id)->orderBy('fecha_registro', 'desc')->limit($toDelete)->pluck('id')->toArray();
        if (! empty($ids)) {
            DB::table('bienes')->whereIn('id', $ids)->delete();
            echo "Dep {$d->codigo} (id {$d->id}): eliminados ".count($ids)." bienes\n";
        }
    } else {
        echo "Dep {$d->codigo} (id {$d->id}): {$count} bienes (sin cambios)\n";
    }
}
