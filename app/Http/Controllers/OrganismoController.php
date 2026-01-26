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
            'codigo' => ['nullable', 'string', 'max:8'],
            'nombre' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Organismo::query();

        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        if (!empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%' . $validated['codigo'] . '%');
        }

        if (!empty($validated['nombre'])) {
            $query->where('nombre', 'like', '%' . $validated['nombre'] . '%');
        }

        $organismos = $query->orderBy('nombre')->paginate(10)->appends($request->query());

        return view('organismos.index', compact('organismos', 'validated'));
    }

    /**
     * Mostrar formulario para crear organismo con código sugerido.
     */
    public function create()
    {
        // Obtener el último registro para seguir la secuencia
        $ultimo = Organismo::latest('id')->first();
        $nuevoNumero = $ultimo ? (int) $ultimo->codigo + 1 : 1;

        // Formatear a 8 dígitos con ceros (ej: 00000001)
        $codigoSugerido = str_pad($nuevoNumero, 8, '0', STR_PAD_LEFT);

        return view('organismos.create', compact('codigoSugerido'));
    }
    /**
     * Guardar un nuevo organismo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                // Validamos que sean exactamente 8 números
                'codigo' => ['required', 'string', 'size:8', 'unique:organismos,codigo', 'regex:/^[0-9]+$/'],
                'nombre' => ['required', 'string', 'max:255'],
            ],
            [
                'codigo.required' => 'El código del organismo es requerido',
                'codigo.unique' => 'Este código ya existe en el sistema',
                'codigo.size' => 'El código debe tener exactamente 8 dígitos',
                'codigo.regex' => 'El código solo puede contener números',
                'nombre.required' => 'El nombre del organismo es requerido',
            ]
        );

        Organismo::create($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo creado correctamente');
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(Organismo $organismo)
    {
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
                    'required',
                    'string',
                    'size:8',
                    'regex:/^[0-9]+$/',
                    Rule::unique('organismos', 'codigo')->ignore($organismo->id),
                ],
                'nombre' => ['required', 'string', 'max:255'],
            ],
            [
                'codigo.unique' => 'Este código ya existe',
                'codigo.size' => 'Debe tener 8 dígitos',
            ]
        );

        $organismo->update($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo actualizado correctamente');
    }

    // Los demás métodos (show, exportPdf, destroy) se mantienen igual...

    public function show(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras');
        return view('organismos.show', compact('organismo'));
    }

    public function exportPdf(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras');
        $pdf = Pdf::loadView('organismos.pdf', ['organismo' => $organismo])->setPaper('letter');
        $fileName = "organismo_" . Str::slug($organismo->codigo) . ".pdf";
        return $pdf->download($fileName);
    }

    public function destroy(Organismo $organismo)
    {
        return response()->json(['message' => 'No está permitido eliminar organismos.'], 403);
    }
}