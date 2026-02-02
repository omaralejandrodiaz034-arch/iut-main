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
        // Cargar los organismos para el select
        $organismos = Organismo::all();

        // --- Lógica agregada para el código secuencial ---
        // Obtenemos el código más alto, lo convertimos a número, sumamos 1 y rellenamos con ceros a la izquierda
        $ultimoCodigo = UnidadAdministradora::max('codigo');
        $siguienteCodigo = str_pad((int) $ultimoCodigo + 1, 8, '0', STR_PAD_LEFT);

        // Retornar la vista del formulario enviando la sugerencia
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
            // Validamos que el ID exista en la tabla 'organismos'
            'organismo_id' => ['required', 'exists:organismos,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:unidades_administradoras,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
        ], [
            // Mensajes personalizados para el Organismo
            'organismo_id.required' => 'Debe seleccionar un organismo.',
            'organismo_id.exists' => 'El organismo seleccionado no existe en nuestra base de datos.',

            // Mensajes para el Código de la unidad
            'codigo.required' => 'El código de la unidad es obligatorio.',
            'codigo.unique' => 'Este código ya pertenece a otra unidad administradora.',
            'codigo.max' => 'El código es demasiado largo (máximo 50 caracteres).',

            // Mensajes para el Nombre de la unidad
            'nombre.required' => 'El nombre de la unidad es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
        ]);

        $unidad = UnidadAdministradora::create($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad creada correctamente');
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
        return response()->json(['message' => 'No se pueden eliminar unidades administrativas.'], 403);
    }
}
