<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\CodigoUnicoService;
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
        // Usamos tu servicio para obtener el código real disponible en todo el sistema
        $codigoSugerido = CodigoUnicoService::obtenerSiguienteCodigo();

        return view('organismos.create', compact('codigoSugerido'));
    }
    /**
     * Guardar un nuevo organismo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    // Validación cruzada usando tu servicio
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("Este código ya está en uso por: " . $ubicacion['tabla'] . " (" . $ubicacion['nombre'] . ")");
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ], [
            'codigo.required' => 'El código del organismo es requerido',
            'codigo.size' => 'El código debe tener exactamente 8 dígitos',
            'codigo.regex' => 'El código solo puede contener números',
        ]);

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
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($organismo) {
                    // Validar si existe en otros sitios, pero ignorar el registro actual
                    if (CodigoUnicoService::codigoExiste($value, 'organismos', $organismo->id)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("No puedes usar este código. Ya pertenece a: " . $ubicacion['tabla']);
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $organismo->update($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo actualizado');
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
