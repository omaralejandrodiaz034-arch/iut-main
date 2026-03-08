<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\Responsable;
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

        $like = '%' . $q . '%';
        $results = [];

        // Bienes (limitado a 5 resultados)
        Bien::where('codigo', 'LIKE', $like)
            ->orWhere('descripcion', 'LIKE', $like)
            ->orWhere('ubicacion', 'LIKE', $like)
            ->limit(5)->get()
            ->each(fn ($b) => $results[] = [
                'type'  => 'Bien',
                'icon'  => '📦',
                'label' => "{$b->codigo} — {$b->descripcion}",
                'url'   => route('bienes.show', $b),
            ]);

        // Dependencias (limitado a 4 resultados)
        Dependencia::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(4)->get()
            ->each(fn ($d) => $results[] = [
                'type'  => 'Dependencia',
                'icon'  => '📂',
                'label' => $d->nombre,
                'url'   => route('dependencias.show', $d),
            ]);

        // Unidades (limitado a 3 resultados)
        UnidadAdministradora::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(3)->get()
            ->each(fn ($u) => $results[] = [
                'type'  => 'Unidad',
                'icon'  => '🏢',
                'label' => $u->nombre,
                'url'   => route('unidades.show', $u),
            ]);

        // Usuarios (limitado a 3 resultados)
        Usuario::where('nombre', 'LIKE', $like)
            ->orWhere('apellido', 'LIKE', $like)
            ->orWhere('cedula', 'LIKE', $like)
            ->limit(3)->get()
            ->each(fn ($u) => $results[] = [
                'type'  => 'Usuario',
                'icon'  => '👤',
                'label' => "{$u->nombre} {$u->apellido} — {$u->cedula}",
                'url'   => route('usuarios.show', $u),
            ]);

        // Limitar total de resultados para mejor rendimiento
        $results = array_slice($results, 0, 15);

        return response()->json(['results' => $results]);
    }
}
