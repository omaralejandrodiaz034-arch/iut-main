<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResponsableController extends Controller
{
    /**
     * Listar todos los responsables.
     */
    public function index()
    {
        // Incluimos relaciones para evitar N+1
        $responsables = Responsable::with(['tipo', 'bienes'])->paginate(10);

        return response()->json($responsables);
    }

    /**
     * Guardar un nuevo responsable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_id' => ['required', 'exists:tipos_responsables,id'],
            'cedula' => ['required', 'string', 'max:20', 'unique:responsables,cedula'],
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        $responsable = Responsable::create($validated);

        return response()->json($responsable, 201);
    }

    /**
     * Mostrar un responsable específico.
     */
    public function show(Responsable $responsable)
    {
        $responsable->load(['tipo', 'bienes']);

        return response()->json($responsable);
    }

    /**
     * Actualizar un responsable.
     */
    public function update(Request $request, Responsable $responsable)
    {
        $validated = $request->validate([
            'tipo_id' => ['sometimes', 'exists:tipos_responsables,id'],
            'cedula' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('responsables', 'cedula')->ignore($responsable->getKey()),
            ],
            'nombre' => ['sometimes', 'string', 'max:255'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        $responsable->update($validated);

        return response()->json($responsable);
    }

    /**
     * Eliminar un responsable.
     */
    public function destroy(Responsable $responsable)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar datos del sistema.'], 403);
            }

            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Archivar responsable antes de eliminar
        \App\Services\EliminadosService::archiveModel($responsable, auth()->id());
        $responsable->delete();

        return response()->json(null, 204);
    }
}
