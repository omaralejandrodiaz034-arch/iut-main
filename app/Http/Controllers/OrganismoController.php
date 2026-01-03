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
     * Listar todos los organismos con filtros.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50'],
            'nombre' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Organismo::query();

        // Búsqueda general
        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        // Filtro por código
        if (!empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%' . $validated['codigo'] . '%');
        }

        // Filtro por nombre
        if (!empty($validated['nombre'])) {
            $query->where('nombre', 'like', '%' . $validated['nombre'] . '%');
        }

        $organismos = $query->orderBy('nombre')->paginate(10)->appends($request->query());

        return view('organismos.index', compact('organismos', 'validated'));
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
        $validated = $request->validate(
            [
                'codigo' => ['required', 'string', 'max:50', 'unique:organismos,codigo'],
                'nombre' => ['required', 'string', 'max:255'],
            ],
            [
                'codigo.required' => 'El código del organismo es requerido',
                'codigo.unique' => 'Este código ya existe en el sistema',
                'codigo.max' => 'El código no puede exceder 50 caracteres',
                'nombre.required' => 'El nombre del organismo es requerido',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            ]
        );

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
        $validated = $request->validate(
            [
                'codigo' => [
                    'sometimes',
                    'string',
                    'max:50',
                    Rule::unique('organismos', 'codigo')->ignore($organismo->getKey()),
                ],
                'nombre' => ['sometimes', 'string', 'max:255'],
            ],
            [
                'codigo.unique' => 'Este código ya existe en el sistema',
                'codigo.max' => 'El código no puede exceder 50 caracteres',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            ]
        );

        $organismo->update($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo actualizado correctamente');
    }

    /**
     * Eliminar un organismo.
     */
    public function destroy(Organismo $organismo)
    {
        return response()->json(['message' => 'No está permitido eliminar organismos del sistema.'], 403);
    }
}
