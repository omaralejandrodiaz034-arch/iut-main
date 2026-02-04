<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\UnidadAdministradora;
use App\Models\Responsable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\CodigoUnicoService;
class DependenciaController extends Controller
{
    public function index(Request $request)
{

    $query = Dependencia::with(['unidadAdministradora', 'responsable'])
        ->withCount('bienes'); // Para usar $dep->bienes_count que es más rápido

    // Filtros
    if ($request->filled('search')) {
        $query->search($request->search);
    }
    if ($request->filled('unidad_id')) {
        $query->where('unidad_administradora_id', $request->unidad_id);
    }
    if ($request->filled('responsable_id')) {
        $query->where('responsable_id', $request->responsable_id);
    }

    return view('dependencias.index', [
        'dependencias' => $query->paginate(10)->withQueryString(),
        'unidades' => \App\Models\UnidadAdministradora::all(), // Requerido para el select
        'responsables' => \App\Models\Responsable::all(),     // Requerido para el select
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_administradora_id' => ['required', 'exists:unidades_administradoras,id'],
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    // Verificación global de código
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("Este código ya está registrado en: " . $info['tabla'] . " (" . $info['nombre'] . ")");
                    }
                }
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:responsables,id'],
        ], [
            'unidad_administradora_id.required' => 'Debe seleccionar una unidad administradora.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 8 dígitos.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        Dependencia::create($validated);

        return redirect()->route('dependencias.index')->with('success', 'Dependencia creada correctamente');
    }

    public function create()
    {
        $unidades = UnidadAdministradora::all();
        $responsables = Responsable::all();

        // CAMBIO: Sugerencia basada en el inventario GLOBAL
        $proximoCodigo = CodigoUnicoService::obtenerSiguienteCodigo();

        return view('dependencias.create', compact('unidades', 'responsables', 'proximoCodigo'));
    }

    public function show(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora', 'bienes', 'responsable']);
        return view('dependencias.show', compact('dependencia'));
    }

    public function exportPdf(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora', 'bienes', 'responsable']);
        $pdf = Pdf::loadView('dependencias.pdf', ['dependencia' => $dependencia])->setPaper('letter');

        $fileName = sprintf('dependencia_%s_%s.pdf', Str::slug($dependencia->codigo, '_'), Str::slug($dependencia->nombre, '_'));
        return $pdf->download($fileName);
    }

    public function update(Request $request, Dependencia $dependencia)
    {
        $validated = $request->validate([
            'unidad_administradora_id' => ['sometimes', 'exists:unidades_administradoras,id'],
            'codigo' => [
                'sometimes',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($dependencia) {
                    // Validar globalmente ignorando el ID actual
                    if (CodigoUnicoService::codigoExiste($value, 'dependencias', $dependencia->id)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("Código no disponible. Ya existe en: " . $info['tabla']);
                    }
                },
            ],
            'nombre' => ['sometimes', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:responsables,id'],
        ]);

        $dependencia->update($validated);

        return redirect()->route('dependencias.index')->with('success', 'Dependencia actualizada correctamente');
    }

    public function edit(Dependencia $dependencia)
    {
        $unidades = UnidadAdministradora::all();
        $responsables = Responsable::all();
        return view('dependencias.edit', compact('dependencia', 'unidades', 'responsables'));
    }

    public function destroy(Dependencia $dependencia)
    {
        return response()->json(['message' => 'No se pueden eliminar dependencias.'], 403);
    }
}
