<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\CodigoUnicoService;
class UnidadAdministradoraController extends Controller
{
    /**
     * Listar todas las Unidades Administradoras.
     */
    public function index(Request $request)
{
    // 1. Capturamos los parámetros de búsqueda y filtro
    $search = $request->input('search');
    $organismo_id = $request->input('organismo_id');

    // 2. Construimos la consulta con filtros dinámicos
    $query = UnidadAdministradora::with(['organismo', 'dependencias']);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
              ->orWhere('codigo', 'LIKE', "%{$search}%");
        });
    }

    if ($organismo_id) {
        $query->where('organismo_id', $organismo_id);
    }

    // 3. Paginación manteniendo los parámetros en los links
    $unidades = $query->paginate(10)
                      ->appends($request->only(['search', 'organismo_id']));

    // 4. Cargamos la lista de organismos para el select del filtro
    $organismos = Organismo::orderBy('nombre')->get();

    // 5. Soporte para AJAX (Carga parcial de la vista)
    if ($request->ajax()) {
        return view('unidades.index', compact('unidades', 'organismos', 'search'))->render();
    }

    return view('unidades.index', compact('unidades', 'organismos', 'search'));
}

    public function create()
    {
        $organismos = Organismo::all();

        // CAMBIO: Ahora usamos el servicio global en lugar de solo el máximo de esta tabla
        $siguienteCodigo = CodigoUnicoService::obtenerSiguienteCodigo();

        return view('unidades.create', compact('organismos', 'siguienteCodigo'));
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
            'codigo' => [
                'required',
                'string',
                'size:8', // Forzamos 8 dígitos
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    // VALIDACIÓN GLOBAL: Verifica si el código existe en CUALQUIER tabla
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("Este código ya está en uso por: " . $ubicacion['tabla'] . " (" . $ubicacion['nombre'] . ")");
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ], [
            'organismo_id.required' => 'Debe seleccionar un organismo.',
            'codigo.required' => 'El código de la unidad es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 8 dígitos.',
            'nombre.required' => 'El nombre de la unidad es obligatorio.',
        ]);

        UnidadAdministradora::create($validated);

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
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($unidadAdministradora) {
                    // VALIDACIÓN GLOBAL: Ignora el ID actual de la unidad que se edita
                    if (CodigoUnicoService::codigoExiste($value, 'unidades', $unidadAdministradora->id)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("No puedes usar este código. Ya pertenece a: " . $ubicacion['tabla']);
                    }
                },
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
        return response()->json(['message' => 'No se pueden eliminar unidades administrativas.'], 403);
    }
}
