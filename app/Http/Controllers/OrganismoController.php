<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganismoController extends Controller
{
    /**
     * Listar todos los organismos.
     */
    public function index()
    {
        $organismos = Organismo::paginate(10);

        return view('organismos.index', compact('organismos'));
    }

    /**
     * Mostrar formulario para crear organismo.
     */
    public function create()
    {
        return view('organismos.create');
    }

    /**
     * Guardar un nuevo organismo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:organismos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $organismo = Organismo::create($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo creado correctamente');
    }

    /**
     * Mostrar un organismo específico.
     */
    public function show(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras');

        return view('organismos.show', compact('organismo'));
    }

    /**
     * Descargar los detalles del organismo en PDF.
     */
    public function exportPdf(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras');

        $pdf = Pdf::loadView('organismos.pdf', [
            'organismo' => $organismo,
        ])->setPaper('letter');

        $safeNombre = Str::slug($organismo->nombre, '_');
        $safeCodigo = Str::slug($organismo->codigo, '_');
        $fileName = "organismo_{$safeCodigo}_{$safeNombre}.pdf";

        return $pdf->download($fileName);
    }

    /**
     * Mostrar el formulario de edición de un organismo.
     */
    public function edit(Organismo $organismo)
    {
        // Cargar relaciones que el formulario pueda necesitar
        $organismo->load('unidadesAdministradoras');

        return view('organismos.edit', compact('organismo'));
    }

    /**
     * Actualizar un organismo.
     */
    public function update(Request $request, Organismo $organismo)
    {
        $validated = $request->validate([
            'codigo' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('organismos', 'codigo')->ignore($organismo->getKey()),
            ],
            'nombre' => ['sometimes', 'string', 'max:255'],
        ]);

        $organismo->update($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo actualizado correctamente');
    }

    /**
     * Eliminar un organismo.
     */
    public function destroy(Organismo $organismo)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar datos del sistema.'], 403);
            }

            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Archivar organismo y luego eliminar
        \App\Services\EliminadosService::archiveModel($organismo, auth()->id());
        $organismo->delete();

        return response()->json(null, 204);
    }
}
