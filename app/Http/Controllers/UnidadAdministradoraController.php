<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UnidadAdministradoraController extends Controller
{
    /**
     * Listar todas las Unidades Administradoras.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $unidades = UnidadAdministradora::with(['organismo', 'dependencias'])
            ->search($search)
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('unidades.index', compact('unidades', 'search'));
    }

    public function create()
    {
        // Cargar los organismos para el select
        $organismos = Organismo::all();

        // Retornar la vista del formulario
        return view('unidades.create', compact('organismos'));
    }

    public function edit(UnidadAdministradora $unidadAdministradora)
    {
        $organismos = Organismo::all();

        return view('unidades.edit', compact('unidadAdministradora', 'organismos'));
    }

    /**
     * Guardar una nueva Unidad Administradora.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organismo_id' => ['required', 'exists:organismos,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:unidades_administradoras,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $unidad = UnidadAdministradora::create($validated);

        return redirect()->route('unidades.index')->with('success', 'Unidad creada correctamente');
    }

    /**
     * Mostrar una Unidad Administradora específica.
     */
    public function show(UnidadAdministradora $unidadAdministradora)
    {
        $unidadAdministradora->load(['organismo', 'dependencias']);

        return view('unidades.show', compact('unidadAdministradora'));
    }

    /**
     * Descargar los detalles de la unidad en PDF.
     */
    public function exportPdf(UnidadAdministradora $unidadAdministradora)
    {
        $unidadAdministradora->load(['organismo', 'dependencias']);

        $pdf = Pdf::loadView('unidades.pdf', [
            'unidadAdministradora' => $unidadAdministradora,
        ])->setPaper('letter');

        $fileName = sprintf(
            'unidad_%s_%s.pdf',
            Str::slug($unidadAdministradora->codigo, '_'),
            Str::slug($unidadAdministradora->nombre, '_')
        );

        return $pdf->download($fileName);
    }

    /**
     * Actualizar una Unidad Administradora.
     */
    public function update(Request $request, UnidadAdministradora $unidadAdministradora)
    {
        $validated = $request->validate([
            'organismo_id' => ['sometimes', 'exists:organismos,id'],
            'codigo' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('unidades_administradoras', 'codigo')->ignore($unidadAdministradora->getKey()),
            ],
            'nombre' => ['sometimes', 'string', 'max:255'],
        ]);

        $unidadAdministradora->update($validated);

        return redirect()->route('unidades.index')->with('success', 'Unidad actualizada correctamente');
    }

    /**
     * Eliminar una Unidad Administradora.
     */
    public function destroy(UnidadAdministradora $unidadAdministradora)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar datos del sistema.'], 403);
            }

            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Archivar unidad antes de eliminar
        \App\Services\EliminadosService::archiveModel($unidadAdministradora, auth()->id());
        $unidadAdministradora->delete();

        return response()->json(null, 204);
    }
}
