<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Acceso no autorizado.');

        $query = Auditoria::with('usuario')->orderByDesc('created_at');

        if ($request->filled('tabla')) {
            $query->where('tabla', $request->tabla);
        }

        if ($request->filled('operacion')) {
            $query->where('operacion', $request->operacion);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $registros = $query->paginate(50)->withQueryString();

        $tablas      = Auditoria::distinct()->orderBy('tabla')->pluck('tabla');
        $operaciones = Auditoria::distinct()->orderBy('operacion')->pluck('operacion');

        return view('auditoria.index', compact('registros', 'tablas', 'operaciones'));
    }
}
