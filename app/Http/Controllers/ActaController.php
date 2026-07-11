<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class ActaController extends Controller
{
    public function pendientes(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $query = Movimiento::query()
            ->whereIn('acta_estado', ['PENDIENTE_FIRMA', 'RECHAZADA'])
            ->whereNotNull('fecha_limite_acta')
            ->with(['bien.dependencia', 'usuario'])
            ->orderBy('fecha_limite_acta', 'asc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $actas = $query->paginate(20)->appends($request->only('tipo'));

        return view('actas.pendientes', compact('actas'));
    }
}
