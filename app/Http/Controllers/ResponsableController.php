<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use App\Models\TipoResponsable;
use App\Services\EliminadosService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResponsableController extends Controller
{
    /**
     * Listar todos los responsables.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $responsables = Responsable::with('tipo', 'dependencias')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%");
            })
            ->paginate(10);
            
        return view('responsables.index', compact('responsables', 'search'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $tipos = TipoResponsable::all();
        return view('responsables.create', compact('tipos'));
    }

    /**
     * Guardar nuevo responsable.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|unique:responsables,cedula',
            'nombre' => 'required|string|max:150',
            'correo' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'tipo_id' => 'required|exists:tipos_responsables,id',
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'Ya existe un responsable registrado con esta cédula.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 150 caracteres.',
            'correo.email' => 'El correo electrónico debe ser válido.',
            'correo.max' => 'El correo no puede exceder los 150 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'tipo_id.required' => 'Debe seleccionar un tipo de responsable.',
            'tipo_id.exists' => 'El tipo de responsable seleccionado no es válido.',
        ]);

        $responsable = Responsable::create($request->only('cedula', 'nombre', 'correo', 'telefono', 'tipo_id'));

        return redirect()->route('responsables.index')
            ->with('success', '✅ Responsable "' . $responsable->nombre . '" (Cédula: ' . $responsable->cedula . ') registrado correctamente el ' . now()->format('d/m/Y \a \l\a\s H:i') . '.');
    }

    /**
     * Ver detalle de un responsable.
     */
    public function show(Responsable $responsable)
    {
        $responsable->load('tipo', 'dependencias.unidadAdministradora.organismo', 'bienes');
        
        return view('responsables.show', compact('responsable'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Responsable $responsable)
    {
        $tipos = TipoResponsable::all();
        return view('responsables.edit', compact('responsable', 'tipos'));
    }

    /**
     * Actualizar responsable.
     */
    public function update(Request $request, Responsable $responsable)
    {
        $request->validate([
            'cedula' => ['required', Rule::unique('responsables', 'cedula')->ignore($responsable->id)],
            'nombre' => 'required|string|max:150',
            'correo' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'tipo_id' => 'required|exists:tipos_responsables,id',
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'Ya existe otro responsable con esta cédula.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 150 caracteres.',
            'correo.email' => 'El correo electrónico debe ser válido.',
            'correo.max' => 'El correo no puede exceder los 150 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'tipo_id.required' => 'Debe seleccionar un tipo de responsable.',
            'tipo_id.exists' => 'El tipo de responsable seleccionado no es válido.',
        ]);

        $responsable->update($request->only('cedula', 'nombre', 'correo', 'telefono', 'tipo_id'));

        return redirect()->route('responsables.show', $responsable)
            ->with('success', '✅ Responsable "' . $responsable->nombre . '" actualizado correctamente el ' . now()->format('d/m/Y \a \l\a\s H:i') . '.');
    }

    /**
     * Eliminar responsable.
     */
    public function destroy(Responsable $responsable)
    {
        // Verificar permisos: solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteData()) {
            abort(403, 'No tienes permisos para eliminar datos del sistema.');
        }

        // Verificar si tiene dependencias asignadas
        if ($responsable->dependencias()->count() > 0) {
            return redirect()->route('responsables.index')
                ->with('error', '⚠️ No se puede eliminar el responsable "' . $responsable->nombre . '" porque tiene ' . $responsable->dependencias()->count() . ' dependencia(s) asignada(s). Primero debe reassignar las dependencias.');
        }

        // Archivar antes de eliminar
        $nombre = $responsable->nombre;
        $cedula = $responsable->cedula;
        EliminadosService::archiveModel($responsable, auth()->id());
        $responsable->delete();

        return redirect()->route('responsables.index')
            ->with('success', '🗑️ Responsable "' . $nombre . '" (Cédula: ' . $cedula . ') eliminado correctamente el ' . now()->format('d/m/Y \a \l\a\s H:i') . '.');
    }
}

