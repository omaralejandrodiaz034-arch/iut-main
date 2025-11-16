<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\UnidadAdministradora;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DependenciaController extends Controller
{
    /**
     * Listar todas las dependencias.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $dependencias = Dependencia::with(['unidadAdministradora', 'bienes', 'responsable'])
            ->search($search)
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dependencias.index', compact('dependencias', 'search'));
    }

    /**
     * Guardar una nueva dependencia.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_administradora_id' => ['required', 'exists:unidades_administradoras,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:dependencias,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:responsables,id'],
        ]);

        $dependencia = Dependencia::create($validated);

        return redirect()->route('dependencias.index')->with('success', 'Dependencia creada correctamente');
    }

    public function create()
    {
        $unidadesAdministradoras = UnidadAdministradora::all();

        // Cargamos responsables para poder asignarlos desde el formulario
        $responsables = \App\Models\Responsable::all();

        // La vista espera $unidades
        return view('dependencias.create', [
            'unidades' => $unidadesAdministradoras,
            'responsables' => $responsables,
        ]);
    }

    /**
     * Mostrar una dependencia específica.
     */
    public function show(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora', 'bienes', 'responsable']);

        return view('dependencias.show', compact('dependencia'));
        // Note: keep returning a web view so the "Ver" button renders the details page like usuarios.show
    }

    /**
     * Descargar los detalles de la dependencia en PDF.
     */
    public function exportPdf(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora', 'bienes', 'responsable']);

        $pdf = Pdf::loadView('dependencias.pdf', [
            'dependencia' => $dependencia,
        ])->setPaper('letter');

        $fileName = sprintf(
            'dependencia_%s_%s.pdf',
            Str::slug($dependencia->codigo, '_'),
            Str::slug($dependencia->nombre, '_')
        );

        return $pdf->download($fileName);
    }

    /**
     * Actualizar una dependencia.
     */
    public function update(Request $request, Dependencia $dependencia)
    {
        $validated = $request->validate([
            'unidad_administradora_id' => ['sometimes', 'exists:unidades_administradoras,id'],
            'codigo' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('dependencias', 'codigo')->ignore($dependencia->getKey()),
            ],
            'nombre' => ['sometimes', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:responsables,id'],
        ]);

        $dependencia->update($validated);

        // Redirigimos a la lista con un mensaje para la interfaz web
        return redirect()->route('dependencias.index')->with('success', 'Dependencia actualizada correctamente');
    }

    /**
     * Mostrar formulario de edición para una dependencia.
     */
    public function edit(Dependencia $dependencia)
    {
        $unidadesAdministradoras = UnidadAdministradora::all();
        $responsables = \App\Models\Responsable::all();

        return view('dependencias.edit', [
            'dependencia' => $dependencia,
            'unidades' => $unidadesAdministradoras,
            'responsables' => $responsables,
        ]);
    }

    /**
     * Eliminar una dependencia.
     */
    public function destroy(Dependencia $dependencia)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar datos del sistema.'], 403);
            }

            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Archivar dependencia antes de eliminar
        \App\Services\EliminadosService::archiveModel($dependencia, auth()->id());
        $dependencia->delete();

        return response()->json(null, 204);
    }
}
