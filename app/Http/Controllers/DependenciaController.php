<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Responsable;
use App\Models\UnidadAdministradora;
use App\Services\CodigoJerarquicoService;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DependenciaController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    /**
     * Listar dependencias con filtros.
     */
    public function index(Request $request)
    {
        $query = Dependencia::with(['unidadAdministradora', 'responsable'])
            ->withCount('bienes');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('unidad_id')) {
            $query->where('unidad_administradora_id', $request->unidad_id);
        }
        if ($request->filled('responsable_id')) {
            $query->where('responsable_id', $request->responsable_id);
        }

        $dependencias = $query->paginate(10)->withQueryString();

        // Formatear códigos para mostrar
        foreach ($dependencias as $dependencia) {
            $dependencia->codigo_legible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);
        }

        return view('dependencias.index', [
            'dependencias' => $dependencias,
            'unidades' => UnidadAdministradora::with('organismo')->get(),
            'responsables' => Responsable::all(),
        ]);
    }

    /**
     * Mostrar formulario de creación con código sugerido.
     */
    public function create(Request $request)
    {
        $unidades = UnidadAdministradora::with('organismo')->get();
        $responsables = Responsable::all();

        // Calcular sugerencias por unidad según el sistema jerárquico
        $sugerenciasPorUnidad = [];
        $estadisticasPorUnidad = [];

        foreach ($unidades as $unidad) {
            try {
                // Generar código sugerido para una dependencia de esta unidad
                $codigoSugerido = CodigoJerarquicoService::generarCodigoDependencia($unidad->id);
                $sugerenciasPorUnidad[$unidad->id] = [
                    'codigo' => $codigoSugerido,
                    'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($codigoSugerido),
                ];

                // Obtener estadísticas de uso
                $estadisticasPorUnidad[$unidad->id] = CodigoJerarquicoService::obtenerEstadisticas(
                    $unidad->codigo,
                    'dependencias'
                );
            } catch (\Throwable $e) {
                $sugerenciasPorUnidad[$unidad->id] = null;
                $estadisticasPorUnidad[$unidad->id] = [
                    'error' => $e->getMessage(),
                    'usados' => 0,
                    'disponibles' => 0,
                    'porcentaje_uso' => 0,
                    'siguiente' => 1,
                ];
                \Log::warning("No se pudo sugerir código para unidad {$unidad->id}: ".$e->getMessage());
            }
        }

        // Determinar unidad seleccionada (por parámetro o primera)
        $unidadSeleccionada = $request->input('unidad_id') ?? ($unidades->first()?->id);

        // Obtener código sugerido para la unidad seleccionada
        $proximoCodigo = null;
        $proximoCodigoLegible = null;

        if ($unidadSeleccionada && isset($sugerenciasPorUnidad[$unidadSeleccionada])) {
            $proximoCodigo = $sugerenciasPorUnidad[$unidadSeleccionada]['codigo'];
            $proximoCodigoLegible = $sugerenciasPorUnidad[$unidadSeleccionada]['codigo_legible'];
        }

        // Obtener estadísticas de la unidad seleccionada
        $estadisticas = $estadisticasPorUnidad[$unidadSeleccionada] ?? null;

        // Obtener jerarquía para mostrar
        $jerarquia = null;
        if ($unidadSeleccionada) {
            $unidad = $unidades->find($unidadSeleccionada);
            if ($unidad) {
                $jerarquia = [
                    'organismo' => [
                        'codigo' => $unidad->organismo->codigo,
                        'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidad->organismo->codigo),
                        'nombre' => $unidad->organismo->nombre,
                    ],
                    'unidad' => [
                        'codigo' => $unidad->codigo,
                        'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo),
                        'nombre' => $unidad->nombre,
                    ],
                ];
            }
        }

        return view('dependencias.create', compact(
            'unidades',
            'responsables',
            'proximoCodigo',
            'proximoCodigoLegible',
            'sugerenciasPorUnidad',
            'estadisticasPorUnidad',
            'unidadSeleccionada',
            'estadisticas',
            'jerarquia'
        ));
    }

    /**
     * Guardar una nueva dependencia.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo_dependencia' => ['required', 'string', 'regex:/^\d{3}$/'],
        ]);

        $unidadId = $request->input('unidad_administradora_id');
        $unidad = UnidadAdministradora::findOrFail($unidadId);
        $prefijoUnidad = substr($unidad->codigo, 0, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD);

        $digitoDep = str_pad($request->input('codigo_dependencia'), 3, '0', STR_PAD_LEFT);
        $codigoCompleto = $prefijoUnidad.$digitoDep.'00';

        $request->merge([
            'codigo' => $codigoCompleto,
        ]);

        $validated = $request->validate([
            'unidad_administradora_id' => 'required|exists:unidades_administradoras,id',
            'codigo' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($prefijoUnidad) {
                    if (strlen($value) !== CodigoJerarquicoService::TOTAL_DEPENDENCIA) {
                        $fail('El código debe tener exactamente '.CodigoJerarquicoService::TOTAL_DEPENDENCIA.' dígitos.');

                        return;
                    }

                    if (! preg_match('/^[0-9]+$/', $value)) {
                        $fail('El código solo puede contener números.');

                        return;
                    }

                    if (! str_starts_with($value, $prefijoUnidad)) {
                        $fail("El código debe comenzar con el prefijo de la unidad ({$prefijoUnidad}).");

                        return;
                    }

                    if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD + CodigoJerarquicoService::LONG_DEPENDENCIA) !== str_repeat('0', CodigoJerarquicoService::LONG_BIEN)) {
                        $fail('El código de dependencia debe terminar con 00.');

                        return;
                    }

                    $parteDependencia = substr($value, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD, CodigoJerarquicoService::LONG_DEPENDENCIA);
                    if ((int) $parteDependencia === 0) {
                        $fail('El código de dependencia no puede ser 0.');

                        return;
                    }

                    $existe = Dependencia::where('codigo', $value)->exists();
                    if ($existe) {
                        $fail('Este código ya está en uso por otra dependencia.');
                    }
                },
            ],
            'nombre' => 'required|string|max:40',
            'responsable_id' => 'nullable|exists:responsables,id',
        ], [
            'unidad_administradora_id.required' => 'Debe seleccionar una unidad administradora.',
            'codigo.required' => 'El código de la dependencia es obligatorio.',
            'nombre.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 40 caracteres.',
        ]);

        $dependencia = Dependencia::create($validated);

        return redirect()
            ->route('dependencias.index')
            ->with('success', '✅ Dependencia registrada exitosamente. Código: '.
                CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo));
    }

    /**
     * Mostrar detalles de una dependencia.
     */
    public function show(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora.organismo', 'bienes', 'responsable']);

        // Formatear código legible
        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);

        // Obtener estadísticas jerárquicas para bienes
        $stats = CodigoJerarquicoService::obtenerEstadisticas(
            $dependencia->codigo,
            'bienes'
        );

        // Obtener jerarquía completa
        $jerarquia = [
            'organismo' => [
                'codigo' => $dependencia->unidadAdministradora->organismo->codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($dependencia->unidadAdministradora->organismo->codigo),
                'nombre' => $dependencia->unidadAdministradora->organismo->nombre,
            ],
            'unidad' => [
                'codigo' => $dependencia->unidadAdministradora->codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($dependencia->unidadAdministradora->codigo),
                'nombre' => $dependencia->unidadAdministradora->nombre,
            ],
            'dependencia' => [
                'codigo' => $dependencia->codigo,
                'codigo_legible' => $codigoLegible,
                'nombre' => $dependencia->nombre,
            ],
        ];

        return view('dependencias.show', compact('dependencia', 'codigoLegible', 'stats', 'jerarquia'));
    }

    /**
     * Exportar PDF de una dependencia específica.
     */
    public function exportPdf(Dependencia $dependencia)
    {
        $dependencia->load(['unidadAdministradora.organismo', 'bienes', 'responsable']);

        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);

        // Preparar estadísticas para el PDF
        $stats = [
            'total_bienes' => $dependencia->bienes()->count(),
            'capacidad_maxima' => pow(10, CodigoJerarquicoService::LONG_BIEN),
            'codigo_legible' => $codigoLegible,
            'porcentaje_uso' => 0,
        ];

        if ($stats['capacidad_maxima'] > 0) {
            $stats['porcentaje_uso'] = round(($stats['total_bienes'] / $stats['capacidad_maxima']) * 100, 2);
        }

        $pdf = Pdf::loadView('dependencias.pdf', [
            'dependencia' => $dependencia,
            'codigoLegible' => $codigoLegible,
            'stats' => $stats,
        ])->setPaper('letter');

        $fileName = sprintf(
            'dependencia_%s_%s.pdf',
            Str::slug($dependencia->codigo, '_'),
            Str::slug($dependencia->nombre, '_')
        );

        return $pdf->download($fileName);
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Dependencia $dependencia)
    {
        $unidades = UnidadAdministradora::with('organismo')->get();
        $responsables = Responsable::all();

        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);

        // Obtener estadísticas para mostrar advertencias
        $stats = CodigoJerarquicoService::obtenerEstadisticas(
            $dependencia->codigo,
            'bienes'
        );

        return view('dependencias.edit', compact(
            'dependencia',
            'unidades',
            'responsables',
            'codigoLegible',
            'stats'
        ));
    }

    /**
     * Actualizar una dependencia.
     */
    public function update(Request $request, Dependencia $dependencia)
    {
        $unidadId = $request->input('unidad_administradora_id', $dependencia->unidad_administradora_id);
        $unidad = UnidadAdministradora::findOrFail($unidadId);
        $prefijoUnidad = substr($unidad->codigo, 0, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD);

        if ($request->has('codigo_dependencia')) {
            $digitoDep = str_pad($request->input('codigo_dependencia'), 3, '0', STR_PAD_LEFT);
            $request->merge([
                'codigo' => $prefijoUnidad.$digitoDep.'00',
            ]);
        }

        if ($request->has('codigo') && $request->codigo !== $dependencia->codigo) {
            if ($dependencia->bienes()->count() > 0) {
                return back()->withErrors([
                    'codigo_dependencia' => 'No se puede cambiar el código porque la dependencia ya tiene bienes asociados.',
                ])->withInput();
            }
        }

        $validated = $request->validate([
            'unidad_administradora_id' => 'sometimes|exists:unidades_administradoras,id',
            'codigo' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) use ($dependencia, $prefijoUnidad) {
                    if (strlen($value) !== CodigoJerarquicoService::TOTAL_DEPENDENCIA) {
                        $fail('El código debe tener exactamente '.CodigoJerarquicoService::TOTAL_DEPENDENCIA.' dígitos.');

                        return;
                    }

                    if (! preg_match('/^[0-9]+$/', $value)) {
                        $fail('El código solo puede contener números.');

                        return;
                    }

                    if (! str_starts_with($value, $prefijoUnidad)) {
                        $fail("El código debe comenzar con el prefijo de la unidad ({$prefijoUnidad}).");

                        return;
                    }

                    if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO + CodigoJerarquicoService::LONG_UNIDAD + CodigoJerarquicoService::LONG_DEPENDENCIA) !== str_repeat('0', CodigoJerarquicoService::LONG_BIEN)) {
                        $fail('El código de dependencia debe terminar con 00.');

                        return;
                    }

                    if ($value !== $dependencia->codigo) {
                        $existe = Dependencia::where('codigo', $value)->exists();
                        if ($existe) {
                            $fail('Este código ya está en uso por otra dependencia.');
                        }
                    }
                },
            ],
            'nombre' => 'sometimes|string|max:40',
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $dependencia->update($validated);

        return redirect()
            ->route('dependencias.index')
            ->with('success', '✅ Dependencia actualizada exitosamente.');
    }

    /**
     * Eliminar una dependencia (con verificación de bienes).
     */
    public function destroy(Dependencia $dependencia)
    {
        // Verificar si tiene bienes asociados
        $totalBienes = $dependencia->bienes()->count();

        if ($totalBienes > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la dependencia porque tiene bienes asociados.',
                'total_bienes' => $totalBienes,
            ], 409);
        }

        $dependencia->delete();

        return response()->json([
            'message' => 'Dependencia eliminada correctamente',
        ], 200);
    }

    /**
     * Generar reporte PDF de dependencias con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades_administradoras,id'],
            'responsable_id' => ['nullable', 'integer', 'exists:responsables,id'],
        ]);

        $query = Dependencia::with(['unidadAdministradora.organismo', 'responsable', 'bienes']);

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['unidad_id'])) {
            $query->where('unidad_administradora_id', $validated['unidad_id']);
        }

        if (! empty($validated['responsable_id'])) {
            $query->where('responsable_id', $validated['responsable_id']);
        }

        $dependencias = $query->orderBy('nombre')->get();

        // Formatear códigos para el reporte
        foreach ($dependencias as $dependencia) {
            $dependencia->codigo_legible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);
            $dependencia->total_bienes = $dependencia->bienes()->count();

            if ($dependencia->unidadAdministradora) {
                $dependencia->organismo_nombre = $dependencia->unidadAdministradora->organismo->nombre ?? '';
            }
        }

        $now = now();

        $tipoReporte = $this->determinarTipoReporte($validated);

        return match ($tipoReporte) {
            'unidad' => $this->fpdf->generarDependenciasPorUnidad(
                'reporte_dependencias_por_unidad_'.$now->format('dmY_His').'.pdf',
                'DEPENDENCIAS POR UNIDAD ADMINISTRADORA',
                'Listado de dependencias agrupadas por unidad administradora',
                $now->format('d/m/Y H:i'),
                $dependencias
            ),
            'responsable' => $this->fpdf->generarDependenciasPorResponsable(
                'reporte_dependencias_por_responsable_'.$now->format('dmY_His').'.pdf',
                'DEPENDENCIAS POR RESPONSABLE',
                'Listado de dependencias agrupadas por responsable',
                $now->format('d/m/Y H:i'),
                $dependencias
            ),
            default => $this->fpdf->downloadDependenciasListado(
                'reporte_dependencias_general_'.$now->format('dmY_His').'.pdf',
                'REPORTE DE DEPENDENCIAS',
                'Listado general de dependencias',
                $now->format('d/m/Y H:i'),
                $dependencias
            ),
        };
    }

    /**
     * API: Obtener el siguiente código para una dependencia (AJAX).
     */
    public function obtenerSiguienteCodigo(Request $request)
    {
        $request->validate([
            'unidad_id' => ['required', 'exists:unidades_administradoras,id'],
        ]);

        try {
            $unidad = UnidadAdministradora::findOrFail($request->unidad_id);
            $codigo = CodigoJerarquicoService::generarCodigoDependencia($unidad->id);
            $stats = CodigoJerarquicoService::obtenerEstadisticas($unidad->codigo, 'dependencias');

            // Obtener jerarquía para mostrar
            $jerarquia = [
                'organismo' => [
                    'codigo' => $unidad->organismo->codigo,
                    'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidad->organismo->codigo),
                    'nombre' => $unidad->organismo->nombre,
                ],
                'unidad' => [
                    'codigo' => $unidad->codigo,
                    'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo),
                    'nombre' => $unidad->nombre,
                ],
            ];

            return response()->json([
                'success' => true,
                'codigo' => $codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($codigo),
                'stats' => $stats,
                'jerarquia' => $jerarquia,
                'unidad' => [
                    'id' => $unidad->id,
                    'codigo' => $unidad->codigo,
                    'nombre' => $unidad->nombre,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Determina el tipo de reporte según los filtros aplicados.
     */
    private function determinarTipoReporte(array $filtros): string
    {
        if (! empty($filtros['unidad_id'])) {
            return 'unidad';
        }
        if (! empty($filtros['responsable_id'])) {
            return 'responsable';
        }

        return 'general';
    }
}
