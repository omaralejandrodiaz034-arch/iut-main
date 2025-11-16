<?php

namespace App\Http\Controllers;

use App\Models\HistorialMovimiento;
use Illuminate\Http\Request;

class HistorialMovimientoController extends Controller
{
    /**
     * Listar todos los registros de historial de movimientos.
     */
    public function index()
    {
        // Incluimos la relación con movimiento para evitar N+1
        $historiales = HistorialMovimiento::with('movimiento')->paginate(10);

        return response()->json($historiales);
    }

    /**
     * Guardar un nuevo registro de historial de movimiento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movimiento_id' => ['required', 'exists:movimientos,id'],
            'fecha' => ['required', 'date'],
            'detalle' => ['required', 'string', 'max:500'],
        ]);

        $historial = HistorialMovimiento::create($validated);

        return response()->json($historial, 201);
    }

    /**
     * Mostrar un registro específico de historial de movimiento.
     */
    public function show(HistorialMovimiento $historialMovimiento)
    {
        $historialMovimiento->load('movimiento');

        return response()->json($historialMovimiento);
    }

    /**
     * Actualizar un registro de historial de movimiento.
     */
    public function update(Request $request, HistorialMovimiento $historialMovimiento)
    {
        $validated = $request->validate([
            'movimiento_id' => ['sometimes', 'exists:movimientos,id'],
            'fecha' => ['sometimes', 'date'],
            'detalle' => ['sometimes', 'string', 'max:500'],
        ]);

        $historialMovimiento->update($validated);

        return response()->json($historialMovimiento);
    }

    /**
     * Eliminar un registro de historial de movimiento.
     */
    public function destroy(HistorialMovimiento $historialMovimiento)
    {
        // Archivar historial antes de eliminar
        \App\Services\EliminadosService::archiveModel($historialMovimiento, auth()->id());
        $historialMovimiento->delete();

        return response()->json(null, 204);
    }
}
