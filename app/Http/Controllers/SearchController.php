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
            return response()->json([]);
        }

        $like = '%' . $q . '%';
        $results = [];

        // Bienes (limitado a 5 resultados)
        $bienes = Bien::where('codigo', 'LIKE', $like)
            ->orWhere('descripcion', 'LIKE', $like)
            ->limit(5)->get();

        foreach ($bienes as $b) {
            $results[] = [
                'type'     => 'Bien',
                'icon'     => '📦',
                'title'    => $b->codigo,
                'subtitle' => $b->descripcion,
                'url'      => route('bienes.show', $b),
            ];
        }

        // Dependencias (limitado a 4 resultados)
        $dependencias = Dependencia::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(4)->get();

        foreach ($dependencias as $d) {
            $results[] = [
                'type'     => 'Dependencia',
                'icon'     => '📂',
                'title'    => $d->nombre,
                'subtitle' => $d->codigo ?? '',
                'url'      => route('dependencias.show', $d),
            ];
        }

        // Unidades (limitado a 3 resultados)
        $unidades = UnidadAdministradora::where('nombre', 'LIKE', $like)
            ->orWhere('codigo', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($unidades as $u) {
            $results[] = [
                'type'     => 'Unidad',
                'icon'     => '🏢',
                'title'    => $u->nombre,
                'subtitle' => $u->codigo ?? '',
                'url'      => route('unidades.show', $u),
            ];
        }

        // Usuarios (limitado a 3 resultados)
        $usuarios = Usuario::where('nombre', 'LIKE', $like)
            ->orWhere('apellido', 'LIKE', $like)
            ->orWhere('cedula', 'LIKE', $like)
            ->limit(3)->get();

        foreach ($usuarios as $u) {
            $results[] = [
                'type'     => 'Usuario',
                'icon'     => '👤',
                'title'    => "{$u->nombre} {$u->apellido}",
                'subtitle' => $u->cedula,
                'url'      => route('usuarios.show', $u),
            ];
        }

        // Limitar total de resultados para mejor rendimiento
        $results = array_slice($results, 0, 15);

        return response()->json($results);
    }
}
