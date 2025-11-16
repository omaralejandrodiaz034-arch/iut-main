<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * Listar todos los reportes.
     */
    public function index()
    {
        // Incluimos la relación con usuario para evitar N+1
        $reportes = Reporte::with('usuario')->paginate(10);

        return response()->json($reportes);
    }

    /**
     * Guardar un nuevo reporte.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,id'],
            'tipo' => ['required', 'string', 'max:100'],
            'fecha_generado' => ['required', 'date'],
            'archivo_pdf_path' => ['nullable', 'string', 'max:255'],
        ]);

        $reporte = Reporte::create($validated);

        return response()->json($reporte, 201);
    }

    /**
     * Mostrar un reporte específico.
     */
    public function show(Reporte $reporte)
    {
        $reporte->load('usuario');

        return response()->json($reporte);
    }

    /**
     * Actualizar un reporte.
     */
    public function update(Request $request, Reporte $reporte)
    {
        $validated = $request->validate([
            'usuario_id' => ['sometimes', 'exists:usuarios,id'],
            'tipo' => ['sometimes', 'string', 'max:100'],
            'fecha_generado' => ['sometimes', 'date'],
            'archivo_pdf_path' => ['nullable', 'string', 'max:255'],
        ]);

        $reporte->update($validated);

        return response()->json($reporte);
    }

    /**
     * Eliminar un reporte.
     */
    public function destroy(Reporte $reporte)
    {
        // Archivar reporte antes de eliminar
        \App\Services\EliminadosService::archiveModel($reporte, auth()->id());
        $reporte->delete();

        return response()->json(null, 204);
    }
}
