<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienElectronico;
use App\Models\BienMobiliario;
use App\Models\BienOtro;
use App\Models\BienVehiculo;
use App\Models\Dependencia;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%'.$q.'%';
        $results = [];

        // Bienes (limitado a 5 resultados)
        $bienes = Bien::where('codigo', 'LIKE', $like)
            ->orWhere('descripcion', 'LIKE', $like)
            ->limit(5)->get();

        foreach ($bienes as $b) {
            $results[] = [
                'type' => 'Bien',
                'icon' => '📦',
                'label' => "{$b->codigo} — {$b->descripcion}",
                'url' => route('bienes.show', $b),
            ];
        }

        // Bienes Electrónicos - buscar en subtipo, procesador, memoria, almacenamiento, pantalla, serial
        $electronicos = BienElectronico::where('subtipo', 'LIKE', $like)
            ->orWhere('procesador', 'LIKE', $like)
            ->orWhere('memoria', 'LIKE', $like)
            ->orWhere('almacenamiento', 'LIKE', $like)
            ->orWhere('pantalla', 'LIKE', $like)
            ->orWhere('serial', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($electronicos as $e) {
            $bien = $e->bien;
            if ($bien) {
                $details = trim(($e->procesador ?? '').' '.($e->memoria ?? '').' '.($e->almacenamiento ?? ''));
                $results[] = [
                    'type' => 'Bien (Electrónico)',
                    'icon' => '💻',
                    'label' => "{$bien->codigo} — {$bien->descripcion}".($details ? " ($details)" : ''),
                    'url' => route('bienes.show', $bien),
                ];
            }
        }

        // Bienes Mobiliarios
        $mobiliarios = BienMobiliario::where('material', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($mobiliarios as $m) {
            $bien = $m->bien;
            if ($bien) {
                $results[] = [
                    'type' => 'Bien (Mobiliario)',
                    'icon' => '🪑',
                    'label' => "{$bien->codigo} — {$bien->descripcion}",
                    'url' => route('bienes.show', $bien),
                ];
            }
        }

        // Bienes Vehículos
        $vehiculos = BienVehiculo::where('marca', 'LIKE', $like)
            ->orWhere('modelo', 'LIKE', $like)
            ->orWhere('placa', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($vehiculos as $v) {
            $bien = $v->bien;
            if ($bien) {
                $results[] = [
                    'type' => 'Bien (Vehículo)',
                    'icon' => '🚗',
                    'label' => "{$bien->codigo} — {$v->marca} {$v->modelo} ({$v->placa})",
                    'url' => route('bienes.show', $bien),
                ];
            }
        }

        // Bienes Otros
        $otros = BienOtro::where('especificaciones', 'LIKE', $like)
            ->orWhere('presentacion', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($otros as $o) {
            $bien = $o->bien;
            if ($bien) {
                $results[] = [
                    'type' => 'Bien (Otro)',
                    'icon' => '📦',
                    'label' => "{$bien->codigo} — {$bien->descripcion}".($o->especificaciones ? " ({$o->especificaciones})" : ''),
                    'url' => route('bienes.show', $bien),
                ];
            }
        }

        // Dependencias (limitado a 4 resultados)
        Dependencia::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(4)->get()
            ->each(fn ($d) => $results[] = [
                'type' => 'Dependencia',
                'icon' => '📂',
                'label' => $d->nombre,
                'url' => route('dependencias.show', $d),
            ]);

        // Unidades (limitado a 3 resultados)
        UnidadAdministradora::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(3)->get()
            ->each(fn ($u) => $results[] = [
                'type' => 'Unidad',
                'icon' => '🏢',
                'label' => $u->nombre,
                'url' => route('unidades.show', $u),
            ]);

        // Usuarios (limitado a 3 resultados)
        Usuario::where('nombre', 'LIKE', $like)
            ->orWhere('apellido', 'LIKE', $like)
            ->orWhere('cedula', 'LIKE', $like)
            ->limit(3)->get()
            ->each(fn ($u) => $results[] = [
                'type' => 'Usuario',
                'icon' => '👤',
                'label' => "{$u->nombre} {$u->apellido} — {$u->cedula}",
                'url' => route('usuarios.show', $u),
            ]);

        // Limitar total de resultados para mejor rendimiento
        $results = array_slice($results, 0, 15);

        return response()->json(['results' => $results]);
    }
}
