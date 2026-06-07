<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\CodigoJerarquicoService;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnidadAdministradoraController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $organismo_id = $request->input('organismo_id');

        $query = UnidadAdministradora::with(['organismo', 'dependencias']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('codigo', 'LIKE', "%{$search}%");
            });
        }

        if ($organismo_id) {
            $query->where('organismo_id', $organismo_id);
        }

        $unidades = $query->paginate(10)->appends($request->only(['search', 'organismo_id']));

    // ✅ AGREGAR: Formatear códigos para mostrar
    foreach ($unidades as $unidad) {
        $unidad->codigo_legible = CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo);
    }

        $organismos = Organismo::orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('unidades.index', compact('unidades', 'organismos', 'search'))->render();
        }

        return view('unidades.index', compact('unidades', 'organismos', 'search'));
    }

    public function create(Request $request)
{
    $organismos = Organismo::orderBy('nombre')->get();

    // Preparar sugerencias por organismo
    $sugerenciasPorOrganismo = [];
    $estadisticasPorOrganismo = [];

    foreach ($organismos as $org) {
        try {
            // ✅ CAMBIAR: usar generarCodigoUnidad en lugar de obtenerSiguienteCodigoParaUnidad
            $codigoSugerido = CodigoJerarquicoService::generarCodigoUnidad($org->id);
            $sugerenciasPorOrganismo[$org->id] = [
                'codigo' => $codigoSugerido,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($codigoSugerido)
            ];

            // ✅ AGREGAR: estadísticas de uso
            $estadisticasPorOrganismo[$org->id] = CodigoJerarquicoService::obtenerEstadisticas(
                $org->codigo,
                'unidades'
            );
        } catch (\Throwable $e) {
            $sugerenciasPorOrganismo[$org->id] = null;
            $estadisticasPorOrganismo[$org->id] = [
                'error' => $e->getMessage(),
                'usados' => 0,
                'disponibles' => 0,
                'porcentaje_uso' => 0
            ];
            \Log::warning("No se pudo sugerir código para organismo {$org->id}: " . $e->getMessage());
        }
    }

    $siguienteCodigo = null;
    $siguienteCodigoLegible = null;
    $organismoSeleccionado = $request->input('organismo_id') ?? ($organismos->first()?->id);

    if ($organismoSeleccionado && isset($sugerenciasPorOrganismo[$organismoSeleccionado])) {
        $siguienteCodigo = $sugerenciasPorOrganismo[$organismoSeleccionado]['codigo'];
        $siguienteCodigoLegible = $sugerenciasPorOrganismo[$organismoSeleccionado]['codigo_legible'];
    }

    $estadisticas = $estadisticasPorOrganismo[$organismoSeleccionado] ?? null;

    return view('unidades.create', compact(
        'organismos',
        'siguienteCodigo',
        'siguienteCodigoLegible',
        'sugerenciasPorOrganismo',
        'estadisticasPorOrganismo',
        'organismoSeleccionado',
        'estadisticas'
    ));
}

    public function edit(UnidadAdministradora $unidadAdministradora)
{
    $organismos = Organismo::all();

    // ✅ AGREGAR: código legible y estadísticas
    $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($unidadAdministradora->codigo);

    $stats = CodigoJerarquicoService::obtenerEstadisticas(
        $unidadAdministradora->codigo,
        'dependencias'
    );

    return view('unidades.edit', compact(
        'unidadAdministradora',
        'organismos',
        'codigoLegible',
        'stats'
    ));
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'organismo_id' => ['required', 'exists:organismos,id'],
        'codigo' => [
            'required',
            'string',
            function ($attribute, $value, $fail) use ($request) {
                // ✅ CAMBIAR: validar longitud de 8 dígitos
                if (strlen($value) !== CodigoJerarquicoService::TOTAL_UNIDAD) {
                    $fail("El código debe tener exactamente " . CodigoJerarquicoService::TOTAL_UNIDAD . " dígitos.");
                    return;
                }

                if (!preg_match('/^[0-9]+$/', $value)) {
                    $fail("El código solo puede contener números.");
                    return;
                }

                $organismo = Organismo::find($request->organismo_id);
                if (!$organismo) {
                    $fail("Organismo no encontrado.");
                    return;
                }

                // ✅ CAMBIAR: validar que comience con el código del organismo (primer dígito)
                $prefijoOrganismo = substr($organismo->codigo, 0, CodigoJerarquicoService::LONG_ORGANISMO);
                if (!str_starts_with($value, $prefijoOrganismo)) {
                    $fail("El código debe comenzar con el código del organismo ({$prefijoOrganismo}).");
                    return;
                }

                // ✅ AGREGAR: validar que la parte de unidad no sea 0000
                $parteUnidad = substr($value, CodigoJerarquicoService::LONG_ORGANISMO, CodigoJerarquicoService::LONG_UNIDAD);
                if ((int)$parteUnidad === 0) {
                    $fail("El código de unidad no puede ser 0000.");
                    return;
                }

                // ✅ CAMBIAR: usar codigoExiste del nuevo servicio
                if (CodigoJerarquicoService::codigoExiste($value)) {
                    $fail('Este código ya está en uso por otra unidad.');
                }
            },
        ],
        'nombre' => ['required', 'string', 'max:255'],
    ], [
        'organismo_id.required' => 'Debe seleccionar un organismo.',
        'codigo.required' => 'El código de la unidad es obligatorio.',
        'nombre.required' => 'El nombre de la unidad es obligatorio.',
    ]);

    // ✅ ELIMINAR: toda la validación de rangos (ya no es necesaria)
    // ✅ ELIMINAR: reservarCodigosParaOrganismo y reservarCodigosParaUnidad

    $unidad = UnidadAdministradora::create($validated);

    return redirect()->route('unidades.index')
        ->with('success', 'Unidad creada correctamente. Código: ' .
            CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo));
}

    public function show(UnidadAdministradora $unidadAdministradora)
{
    $unidadAdministradora->load(['organismo', 'dependencias']);

    // ✅ AGREGAR: formatear código legible
    $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($unidadAdministradora->codigo);

    // ✅ AGREGAR: estadísticas jerárquicas
    $stats = CodigoJerarquicoService::obtenerEstadisticas(
        $unidadAdministradora->codigo,
        'dependencias'
    );

    // ✅ AGREGAR: calcular total de bienes
    $totalBienes = 0;
    foreach ($unidadAdministradora->dependencias as $dependencia) {
        $totalBienes += $dependencia->bienes()->count();
    }

    // ✅ AGREGAR: jerarquía completa
    $jerarquia = [
        'organismo' => [
            'codigo' => $unidadAdministradora->organismo->codigo,
            'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidadAdministradora->organismo->codigo),
            'nombre' => $unidadAdministradora->organismo->nombre
        ],
        'unidad' => [
            'codigo' => $unidadAdministradora->codigo,
            'codigo_legible' => $codigoLegible,
            'nombre' => $unidadAdministradora->nombre
        ]
    ];

    return view('unidades.show', compact(
        'unidadAdministradora',
        'codigoLegible',
        'stats',
        'totalBienes',
        'jerarquia'
    ));
}
/**
 * API: Obtener el siguiente código para una unidad (AJAX)
 */
public function obtenerSiguienteCodigo(Request $request)
{
    $request->validate([
        'organismo_id' => ['required', 'exists:organismos,id']
    ]);

    try {
        $organismo = Organismo::findOrFail($request->organismo_id);
        $codigo = CodigoJerarquicoService::generarCodigoUnidad($organismo->id);
        $stats = CodigoJerarquicoService::obtenerEstadisticas($organismo->codigo, 'unidades');

        return response()->json([
            'success' => true,
            'codigo' => $codigo,
            'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($codigo),
            'stats' => $stats
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function exportPdf(UnidadAdministradora $unidadAdministradora)
{
    $unidadAdministradora->load(['organismo', 'dependencias.bienes']);

    // ✅ AGREGAR: código legible y estadísticas
    $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($unidadAdministradora->codigo);

    $stats = [
        'total_dependencias' => $unidadAdministradora->dependencias()->count(),
        'total_bienes' => 0,
        'capacidad_maxima' => pow(10, CodigoJerarquicoService::LONG_DEPENDENCIA),
        'codigo_legible' => $codigoLegible
    ];

    foreach ($unidadAdministradora->dependencias as $dependencia) {
        $stats['total_bienes'] += $dependencia->bienes()->count();
    }

    $pdf = Pdf::loadView('unidades.pdf', [
        'unidadAdministradora' => $unidadAdministradora,
        'codigoLegible' => $codigoLegible,
        'stats' => $stats
    ])->setPaper('letter');

    $fileName = sprintf(
        'unidad_%s_%s.pdf',
        Str::slug($unidadAdministradora->codigo, '_'),
        Str::slug($unidadAdministradora->nombre, '_')
    );

    return $pdf->download($fileName);
}

    public function update(Request $request, UnidadAdministradora $unidadAdministradora)
{
    if ($request->has('codigo') && $request->codigo !== $unidadAdministradora->codigo) {
        if ($unidadAdministradora->dependencias()->count() > 0) {
            return back()->withErrors(['codigo' => 'No se puede cambiar el código porque la unidad ya tiene dependencias asociadas.'])->withInput();
        }
    }

    $validated = $request->validate([
        'organismo_id' => ['sometimes', 'exists:organismos,id'],
        'codigo' => [
            'sometimes',
            'string',
            function ($attribute, $value, $fail) use ($request, $unidadAdministradora) {
                // ✅ CAMBIAR: validar longitud de 8 dígitos
                if (strlen($value) !== CodigoJerarquicoService::TOTAL_UNIDAD) {
                    $fail("El código debe tener exactamente " . CodigoJerarquicoService::TOTAL_UNIDAD . " dígitos.");
                    return;
                }

                if (!preg_match('/^[0-9]+$/', $value)) {
                    $fail("El código solo puede contener números.");
                    return;
                }

                $organismoId = $request->organismo_id ?? $unidadAdministradora->organismo_id;
                $organismo = Organismo::find($organismoId);

                if (!$organismo) {
                    $fail("Organismo no encontrado.");
                    return;
                }

                $prefijoOrganismo = substr($organismo->codigo, 0, CodigoJerarquicoService::LONG_ORGANISMO);
                if (!str_starts_with($value, $prefijoOrganismo)) {
                    $fail("El código debe comenzar con el código del organismo ({$prefijoOrganismo}).");
                    return;
                }

                if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD) !== str_repeat('0', CodigoJerarquicoService::LONG_DEPENDENCIA + CodigoJerarquicoService::LONG_BIEN)) {
                    $fail('El código de unidad debe terminar con 000.');
                    return;
                }

                // ✅ CAMBIAR: usar codigoExiste ignorando actual
                if ($value !== $unidadAdministradora->codigo && CodigoJerarquicoService::codigoExiste($value)) {
                    $fail('Este código ya está en uso por otra unidad.');
                }
            },
        ],
        'nombre' => ['sometimes', 'string', 'max:255'],
    ]);

    $unidadAdministradora->update($validated);

    return redirect()->route('unidades.index')->with('success', 'Unidad actualizada correctamente');
}

    public function destroy(UnidadAdministradora $unidadAdministradora)
    {
        return response()->json(['message' => 'No se pueden eliminar unidades administrativas.'], 403);
    }

    /**
     * Generar reporte PDF de unidades con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'organismo_id' => ['nullable', 'integer'],
        ]);

        $query = UnidadAdministradora::with(['organismo', 'dependencias']);

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['organismo_id'])) {
            $query->where('organismo_id', $validated['organismo_id']);
        }

        $unidades = $query->orderBy('nombre')->get();
        $now = now();

        $tipoReporte = $this->determinarTipoReporte($validated);

        return match ($tipoReporte) {
            'organismo' => $this->fpdf->generarUnidadesPorOrganismo(
                'reporte_unidades_por_organismo_'.$now->format('dmY_His').'.pdf',
                'UNIDADES POR ORGANISMO',
                'Listado de unidades agrupadas por organismo',
                $now->format('d/m/Y H:i'),
                $unidades
            ),
            default => $this->fpdf->downloadUnidadesListado(
                'reporte_unidades_general_'.$now->format('dmY_His').'.pdf',
                'REPORTE DE UNIDADES ADMINISTRADORAS',
                'Listado general de unidades',
                $now->format('d/m/Y H:i'),
                $unidades
            ),
        };
    }

    /**
     * Determina el tipo de reporte según los filtros aplicados.
     */
    private function determinarTipoReporte(array $filtros): string
    {
        if (! empty($filtros['organismo_id'])) {
            return 'organismo';
        }

        return 'general';
    }
}
