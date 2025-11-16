<?php

namespace App\Http\Controllers;

use App\Models\Eliminado;
use App\Services\EliminadosService;

class EliminadoController extends Controller
{
    public function index()
    {
        $this->authorizeIndex();

        $eliminados = Eliminado::orderByDesc('deleted_at')->paginate(20);

        return view('eliminados.index', compact('eliminados'));
    }

    public function show(Eliminado $eliminado)
    {
        $this->authorizeIndex();

        return view('eliminados.show', compact('eliminado'));
    }

    public function restore(Eliminado $eliminado)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Solo administradores pueden restaurar registros eliminados.');
        }

        $ok = EliminadosService::restoreEliminado($eliminado);

        if (! $ok) {
            return redirect()->back()->with('error', 'No se pudo restaurar el registro. Revisa los logs.');
        }

        return redirect()->route('eliminados.index')->with('success', 'Registro restaurado correctamente.');
    }

    protected function authorizeIndex()
    {
        if (! auth()->user() || ! auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para ver los registros eliminados.');
        }
    }
}
