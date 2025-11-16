<?php

namespace App\Http\Controllers;

use App\Models\TipoResponsable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoResponsableController extends Controller
{
    /**
     * Listar todos los tipos de responsables.
     */
    public function index()
    {
        // Incluimos la relación con responsables para evitar N+1
        $tipos = TipoResponsable::with('responsables')->paginate(10);

        return response()->json($tipos);
    }

    /**
     * Guardar un nuevo tipo de responsable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:tipos_responsables,nombre'],
        ]);

        $tipo = TipoResponsable::create($validated);

        return response()->json($tipo, 201);
    }

    /**
     * Mostrar un tipo de responsable específico.
     */
    public function show(TipoResponsable $tipoResponsable)
    {
        $tipoResponsable->load('responsables');

        return response()->json($tipoResponsable);
    }

    /**
     * Actualizar un tipo de responsable.
     */
    public function update(Request $request, TipoResponsable $tipoResponsable)
    {
        $validated = $request->validate([
            'nombre' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('tipos_responsables', 'nombre')->ignore($tipoResponsable->getKey()),
            ],
        ]);

        $tipoResponsable->update($validated);

        return response()->json($tipoResponsable);
    }

    /**
     * Eliminar un tipo de responsable.
     */
    public function destroy(TipoResponsable $tipoResponsable)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar datos del sistema.'], 403);
            }

            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Archivar tipo antes de eliminar
        \App\Services\EliminadosService::archiveModel($tipoResponsable, auth()->id());
        $tipoResponsable->delete();

        return response()->json(null, 204);
    }
}
