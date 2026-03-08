<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Eliminado;
use App\Models\Movimiento;
use App\Models\Organismo;
use App\Models\Reporte;
use App\Models\Responsable;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ReporteController extends Controller
{
    public function __construct(
        private FpdfReportService $fpdf,
    ) {
    }
    /**
     * Pantalla principal de la sección de reportes.
     * Si la petición espera JSON, mantiene el comportamiento anterior devolviendo
     * la lista paginada de registros de la tabla `reportes`.
     */
    public function index(Request $request)
    {
        // Soporte para uso tipo API anterior
        if ($request->wantsJson()) {
            $reportes = Reporte::with('usuario')->paginate(10);

            return response()->json($reportes);
        }

        // Definición de los tipos de reporte disponibles en la UI
        $reportTypes = [
            // Bienes
            'inventario_general_bienes' => [
                'title' => 'Inventario general de bienes',
                'description' => 'Listado completo de todos los bienes registrados en el sistema.',
            ],
            'bienes_por_estado' => [
                'title' => 'Bienes por estado',
                'description' => 'Agrupa los bienes según su estado (activo, extraviado, desincorporado, etc.).',
            ],
            'bienes_con_fotografia' => [
                'title' => 'Bienes con fotografía',
                'description' => 'Muestra únicamente los bienes que tienen fotografía cargada.',
            ],
            'bienes_sin_movimientos' => [
                'title' => 'Bienes sin movimientos',
                'description' => 'Bienes que no tienen movimientos registrados en el sistema.',
            ],
            'bienes_por_dependencia' => [
                'title' => 'Bienes por dependencia',
                'description' => 'Inventario de bienes agrupados por dependencia.',
            ],
            'bienes_por_unidad' => [
                'title' => 'Bienes por unidad administradora',
                'description' => 'Inventario de bienes agrupados por unidad administradora.',
            ],
            'bienes_por_organismo' => [
                'title' => 'Bienes por organismo',
                'description' => 'Inventario de bienes agrupados por organismo.',
            ],
            'resumen_bienes_por_estado' => [
                'title' => 'Resumen de bienes por estado',
                'description' => 'Totales de bienes y montos estimados por estado.',
            ],
            'resumen_bienes_por_dependencia' => [
                'title' => 'Resumen de bienes por dependencia',
                'description' => 'Cantidad de bienes y valor estimado por dependencia.',
            ],
            'resumen_bienes_por_tipo' => [
                'title' => 'Resumen de bienes por tipo',
                'description' => 'Conteo de bienes por tipo de bien.',
            ],

            // Movimientos
            'movimientos_ultimos_30_dias' => [
                'title' => 'Movimientos de los últimos 30 días',
                'description' => 'Movimientos registrados durante el último mes.',
            ],
            'movimientos_por_tipo' => [
                'title' => 'Movimientos por tipo',
                'description' => 'Agrupa los movimientos por tipo (creación, actualización, desincorporación, etc.).',
            ],
            'movimientos_por_usuario' => [
                'title' => 'Movimientos por usuario',
                'description' => 'Listado de movimientos realizados por cada usuario.',
            ],
            'movimientos_de_bienes_desincorporados' => [
                'title' => 'Historial de bienes desincorporados',
                'description' => 'Muestra los bienes desincorporados y sus movimientos asociados.',
            ],

            // Estructura organizativa
            'organismos_y_unidades' => [
                'title' => 'Organismos y sus unidades',
                'description' => 'Listado jerárquico de organismos con sus unidades administradoras.',
            ],
            'unidades_y_dependencias' => [
                'title' => 'Unidades y sus dependencias',
                'description' => 'Relación de cada unidad administradora con sus dependencias.',
            ],
            'dependencias_y_responsables' => [
                'title' => 'Dependencias y responsables',
                'description' => 'Listado de dependencias con sus responsables asignados.',
            ],

            // Responsables y usuarios
            'responsables_y_bienes' => [
                'title' => 'Responsables y bienes asignados',
                'description' => 'Muestra cada responsable con las dependencias y bienes a su cargo.',
            ],
            'usuarios_y_roles' => [
                'title' => 'Usuarios del sistema y roles',
                'description' => 'Listado de usuarios del sistema con su rol y estado.',
            ],

            // Auditoría y eliminación
            'registros_eliminados' => [
                'title' => 'Registros eliminados (auditoría)',
                'description' => 'Historial de modelos eliminados a través del sistema de auditoría.',
            ],
        ];

        return view('reportes.index', compact('reportTypes'));
    }





    public function graficas(Request $request)
    {
        // Aplicar filtros recibidos desde la UI de bienes (si los hay)
        $applyFilters = function ($q) use ($request) {
            if ($request->filled('search')) {
                $q->where(function($sub) use ($request) {
                    $term = '%'.$request->get('search').'%';
                    $sub->where('codigo', 'like', $term)
                        ->orWhere('descripcion', 'like', $term);
                });
            }

            if ($request->filled('tipo_bien')) {
                $q->where('tipo_bien', $request->get('tipo_bien'));
            }

            if ($request->filled('fecha_desde')) {
                $q->whereDate('fecha_registro', '>=', $request->get('fecha_desde'));
            }
            if ($request->filled('fecha_hasta')) {
                $q->whereDate('fecha_registro', '<=', $request->get('fecha_hasta'));
            }

            if ($request->filled('dependencias')) {
                $deps = $request->get('dependencias');
                if (is_array($deps)) {
                    $q->whereIn('dependencia_id', $deps);
                }
            } elseif ($request->filled('unidad_id')) {
                $q->whereHas('dependencia', fn($sub) => $sub->where('unidad_administradora_id', $request->get('unidad_id')));
            } elseif ($request->filled('organismo_id')) {
                $q->whereHas('dependencia.unidadAdministradora', fn($sub) => $sub->where('organismo_id', $request->get('organismo_id')));
            }

            if ($request->filled('estado')) {
                $est = $request->get('estado');
                if (is_array($est)) $q->whereIn('estado', $est);
            }
        };
        // =====================================================
        // 1. Bienes por Tipo (aplicando filtros si vienen)
        // =====================================================
        if (Schema::hasColumn('bienes', 'tipo_bien')) {
            $q = Bien::query();
            $applyFilters($q);

            $bienesPorTipo = (clone $q)->selectRaw('tipo_bien, COUNT(*) as count')
                ->groupBy('tipo_bien')
                ->get()
                ->mapWithKeys(function ($item) {
                    $tipo = $item->tipo_bien instanceof \App\Enums\TipoBien
                        ? $item->tipo_bien
                        : \App\Enums\TipoBien::tryFrom($item->tipo_bien);

                    $label = $tipo
                        ? $tipo->label()
                        : ((string) $item->tipo_bien);

                    return [(string) $label => (int) $item->count];
                })
                ->toArray();
        } else {
            $bienesPorTipo = [];
        }

        // =====================================================
        // 2. Bienes por Estado
        // =====================================================
        $q2 = Bien::query();
        $applyFilters($q2);
        $bienesPorEstado = (clone $q2)->selectRaw('estado, COUNT(*) as count')
            ->groupBy('estado')
            ->get()
            ->mapWithKeys(function ($item) {
                $estado = $item->estado instanceof \App\Enums\EstadoBien
                    ? $item->estado
                    : \App\Enums\EstadoBien::tryFrom($item->estado);

                $label = $estado
                    ? $estado->label()
                    : (string) $item->estado;

                return [(string) $label => (int) $item->count];
            })
            ->toArray();

        // =====================================================
        // 3. Bienes por Registro (Progresivo) con granularidad
        // =====================================================
        $granularity = $request->get('granularity', 'monthly'); // daily|weekly|monthly

        $q3 = Bien::query();
        $applyFilters($q3);

        // Obtiene las fechas y agrupa en PHP para ser DB-agnóstico
        $createdDates = (clone $q3)->select('created_at')->get()->pluck('created_at')->filter();

        $grouped = $createdDates->map(function ($d) use ($granularity) {
            if (!$d) return null;
            $dt = \Illuminate\Support\Carbon::parse($d);
            return match ($granularity) {
                'daily' => $dt->format('Y-m-d'),
                'weekly' => $dt->copy()->startOfWeek()->format('Y-m-d'), // week bucket by start date
                default => $dt->format('Y-m'),
            };
        })->filter()->countBy()->sortKeys();

        // Construir acumulado progresivo
        $acumulado = [];
        $total = 0;
        foreach ($grouped as $period => $count) {
            $total += $count;
            $acumulado[(string) $period] = (int) $total;
        }
        $bienesPorRegistro = $acumulado;

        // =====================================================
        // 4. Bienes Desincorporados (progresivo según granularidad)
        // =====================================================
        $deletedDates = Eliminado::select('deleted_at')->get()->pluck('deleted_at')->filter();
        $groupedDeleted = $deletedDates->map(function ($d) use ($granularity) {
            if (!$d) return null;
            $dt = Carbon::parse($d);
            return match ($granularity) {
                'daily' => $dt->format('Y-m-d'),
                'weekly' => $dt->copy()->startOfWeek()->format('Y-m-d'),
                default => $dt->format('Y-m'),
            };
        })->filter()->countBy()->sortKeys();

        $acumDel = [];
        $t = 0;
        foreach ($groupedDeleted as $period => $count) {
            $t += $count;
            $acumDel[(string) $period] = (int) $t;
        }
        $bienesDesincorporados = $acumDel;

        // =====================================================
        // Series de registro de entidades (organismos, unidades, dependencias)
        // Agrupar por granularidad y construir acumulados
        // =====================================================
        $collectAndAccumulate = function ($model, $dateColumn = 'created_at') use ($granularity) {
            $dates = $model::select($dateColumn)->get()->pluck($dateColumn)->filter();
            $grouped = $dates->map(function ($d) use ($granularity) {
                if (!$d) return null;
                $dt = Carbon::parse($d);
                return match ($granularity) {
                    'daily' => $dt->format('Y-m-d'),
                    'weekly' => $dt->copy()->startOfWeek()->format('Y-m-d'),
                    default => $dt->format('Y-m'),
                };
            })->filter()->countBy()->sortKeys();

            $acc = [];
            $s = 0;
            foreach ($grouped as $p => $c) {
                $s += $c;
                $acc[(string) $p] = (int) $s;
            }
            return $acc;
        };

        $registroOrganismos = $collectAndAccumulate(Organismo::class, 'created_at');
        $registroUnidades = $collectAndAccumulate(UnidadAdministradora::class, 'created_at');
        $registroDependencias = $collectAndAccumulate(Dependencia::class, 'created_at');

        // =====================================================
        // Métricas adicionales recomendadas
        // =====================================================
        // 1) Valor total por Estado
        $qValorEstado = Bien::query();
        $applyFilters($qValorEstado);
        $valorPorEstado = (clone $qValorEstado)
            ->selectRaw('estado, SUM(precio) as total')
            ->groupBy('estado')
            ->get()
            ->mapWithKeys(function ($item) {
                $estado = $item->estado instanceof \App\Enums\EstadoBien
                    ? $item->estado
                    : \App\Enums\EstadoBien::tryFrom($item->estado);

                $label = $estado
                    ? $estado->label()
                    : ((string) $item->estado);

                return [(string) $label => (float) $item->total];
            })
            ->toArray();

        // 2) Top 10 Dependencias por Valor
        $qTopDeps = Bien::query();
        $applyFilters($qTopDeps);
        $topDependenciasValor = (clone $qTopDeps)
            ->whereNotNull('dependencia_id')
            ->join('dependencias', 'dependencias.id', '=', 'bienes.dependencia_id')
            ->selectRaw('dependencias.nombre as nombre, COUNT(*) as count, SUM(precio) as total')
            ->groupBy('dependencias.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->mapWithKeys(fn($item) => [(string) $item->nombre => (float) $item->total])
            ->toArray();

        // 3) Porcentaje de bienes con/sin fotografía
        $qFoto = Bien::query();
        $applyFilters($qFoto);
        $totalBienes = (clone $qFoto)->count();
        $withFoto = (clone $qFoto)->whereNotNull('fotografia')->count();
        $withoutFoto = max(0, $totalBienes - $withFoto);
        $fotoCoverage = [
            'Con fotografía' => $withFoto,
            'Sin fotografía' => $withoutFoto,
        ];

        // 4) Bienes sin movimientos en los últimos 12 meses (útil para auditoría)
        $since = Carbon::now()->subYear();
        $qMov = Bien::query();
        $applyFilters($qMov);
        $sinMovimientos = (clone $qMov)->whereDoesntHave('movimientos', fn($q) => $q->where('fecha', '>=', $since))->count();
        $conMovimientos = max(0, $totalBienes - $sinMovimientos);
        $movimientoCoverage = [
            'Con movimientos 12m' => $conMovimientos,
            'Sin movimientos 12m' => $sinMovimientos,
        ];


        return view('reportes.graficas', compact(
            'bienesPorTipo',
            'bienesPorEstado',
            'bienesPorRegistro',
            'bienesDesincorporados',
            'registroOrganismos',
            'registroUnidades',
            'registroDependencias',
            // Nuevas métricas
            'valorPorEstado',
            'topDependenciasValor',
            'fotoCoverage',
            'movimientoCoverage'
        , 'granularity'));

    }

    /**
     * Generar PDF con los datos de una gráfica específica.
     * Parámetros: chart (nombre lógico), mantiene los filtros de la UI.
     */
    public function graficasPdf(Request $request)
    {
        $chart = $request->get('chart');
        $granularity = $request->get('granularity', 'monthly');

        // Helper para aplicar filtros como en graficas()
        $applyFilters = function ($q) use ($request) {
            if ($request->filled('search')) {
                $q->where(function($sub) use ($request) {
                    $term = '%'.$request->get('search').'%';
                    $sub->where('codigo', 'like', $term)
                        ->orWhere('descripcion', 'like', $term);
                });
            }
            if ($request->filled('tipo_bien')) $q->where('tipo_bien', $request->get('tipo_bien'));
            if ($request->filled('fecha_desde')) $q->whereDate('fecha_registro', '>=', $request->get('fecha_desde'));
            if ($request->filled('fecha_hasta')) $q->whereDate('fecha_registro', '<=', $request->get('fecha_hasta'));
            if ($request->filled('dependencias')) {
                $deps = $request->get('dependencias');
                if (is_array($deps)) $q->whereIn('dependencia_id', $deps);
            } elseif ($request->filled('unidad_id')) {
                $q->whereHas('dependencia', fn($sub) => $sub->where('unidad_administradora_id', $request->get('unidad_id')));
            } elseif ($request->filled('organismo_id')) {
                $q->whereHas('dependencia.unidadAdministradora', fn($sub) => $sub->where('organismo_id', $request->get('organismo_id')));
            }
            if ($request->filled('estado')) {
                $est = $request->get('estado'); if (is_array($est)) $q->whereIn('estado', $est);
            }
        };

        $data = [];
        $title = '';

        switch ($chart) {
            case 'valorPorEstado':
                $q = Bien::query(); $applyFilters($q);
                $items = $q->selectRaw('estado, SUM(precio) as total')->groupBy('estado')->get();
                foreach ($items as $it) {
                    $estado = $it->estado instanceof \App\Enums\EstadoBien ? $it->estado->label() : (string)$it->estado;
                    $data[$estado] = (float)$it->total;
                }
                $title = 'Valor total por Estado';
                break;
            case 'topDependencias':
                $q = Bien::query(); $applyFilters($q);
                $items = $q->whereNotNull('dependencia_id')
                    ->join('dependencias','dependencias.id','=','bienes.dependencia_id')
                    ->selectRaw('dependencias.nombre as nombre, SUM(precio) as total')
                    ->groupBy('dependencias.nombre')
                    ->orderByDesc('total')
                    ->limit(100)
                    ->get();
                foreach ($items as $it) $data[$it->nombre] = (float)$it->total;
                $title = 'Dependencias por Valor';
                break;
            case 'fotos':
                $q = Bien::query(); $applyFilters($q);
                $total = (clone $q)->count();
                $with = (clone $q)->whereNotNull('fotografia')->count();
                $data = ['Con fotografía' => $with, 'Sin fotografía' => max(0, $total - $with)];
                $title = 'Cobertura de Fotografías';
                break;
            case 'movimientos':
                $q = Bien::query(); $applyFilters($q);
                $total = (clone $q)->count();
                $since = \Illuminate\Support\Carbon::now()->subYear();
                $sin = (clone $q)->whereDoesntHave('movimientos', fn($q) => $q->where('fecha', '>=', $since))->count();
                $data = ['Con movimientos 12m' => max(0, $total - $sin), 'Sin movimientos 12m' => $sin];
                $title = 'Bienes con/sin movimientos 12m';
                break;
            case 'bienesPorRegistro':
                $q = Bien::query(); $applyFilters($q);
                $dates = (clone $q)->select('created_at')->get()->pluck('created_at')->filter();
                $grouped = $dates->map(function($d) use ($granularity) {
                    $dt = \Illuminate\Support\Carbon::parse($d);
                    return match ($granularity) {
                        'daily' => $dt->format('Y-m-d'),
                        'weekly' => $dt->copy()->startOfWeek()->format('Y-m-d'),
                        default => $dt->format('Y-m'),
                    };
                })->filter()->countBy()->sortKeys();
                $acc=[]; $s=0; foreach($grouped as $k=>$c){$s+=$c;$acc[$k]=(int)$s;} $data=$acc;
                $title = 'Registro progresivo de Bienes';
                break;
            case 'bienesDesincorporados':
                $dates = Eliminado::select('deleted_at')->get()->pluck('deleted_at')->filter();
                $grouped = $dates->map(function($d) use ($granularity) {
                    $dt = \Illuminate\Support\Carbon::parse($d);
                    return match ($granularity) {
                        'daily' => $dt->format('Y-m-d'),
                        'weekly' => $dt->copy()->startOfWeek()->format('Y-m-d'),
                        default => $dt->format('Y-m'),
                    };
                })->filter()->countBy()->sortKeys();
                $acc=[]; $s=0; foreach($grouped as $k=>$c){$s+=$c;$acc[$k]=(int)$s;} $data=$acc;
                $title = 'Bienes Desincorporados';
                break;
            case 'bienesPorTipo':
                $q = Bien::query(); $applyFilters($q);
                $items = (clone $q)->selectRaw('tipo_bien, COUNT(*) as count')->groupBy('tipo_bien')->get();
                foreach ($items as $it) {
                    $tipo = $it->tipo_bien instanceof \App\Enums\TipoBien
                        ? $it->tipo_bien
                        : \App\Enums\TipoBien::tryFrom($it->tipo_bien);

                    $label = $tipo ? $tipo->label() : ((string) $it->tipo_bien);
                    $data[(string)$label] = (int)$it->count;
                }
                $title = 'Bienes por Tipo';
                break;
            case 'bienesPorEstado':
                $q = Bien::query(); $applyFilters($q);
                $items = (clone $q)->selectRaw('estado, COUNT(*) as count')->groupBy('estado')->get();
                foreach ($items as $it) {
                    $estado = $it->estado instanceof \App\Enums\EstadoBien
                        ? $it->estado
                        : \App\Enums\EstadoBien::tryFrom($it->estado);

                    $label = $estado ? $estado->label() : ((string) $it->estado);
                    $data[(string)$label] = (int)$it->count;
                }
                $title = 'Bienes por Estado';
                break;
            default:
                abort(400, 'Parámetro "chart" inválido');
        }

        // Render a simple table PDF
        $pdf = Pdf::loadView('reportes.pdf_grafica', [
            'title' => $title,
            'data' => $data,
            'filters' => $request->all(),
        ])->setPaper('letter');

        $fileName = 'grafica_' . ($chart ?? 'datos') . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }




    /**
     * Punto único para generar los distintos PDFs de reportes según el tipo solicitado.
     */
    public function generarPdf(string $tipo)
    {
        $now = Carbon::now();

        return match ($tipo) {
            'inventario_general_bienes' => $this->pdfInventarioGeneralBienes($now),
            'bienes_por_estado' => $this->pdfBienesPorEstado($now),
            'bienes_con_fotografia' => $this->pdfBienesConFotografia($now),
            'bienes_sin_movimientos' => $this->pdfBienesSinMovimientos($now),
            'bienes_por_dependencia' => $this->pdfBienesPorDependencia($now),
            'bienes_por_unidad' => $this->pdfBienesPorUnidad($now),
            'bienes_por_organismo' => $this->pdfBienesPorOrganismo($now),
            'resumen_bienes_por_estado' => $this->pdfResumenBienesPorEstado($now),
            'resumen_bienes_por_dependencia' => $this->pdfResumenBienesPorDependencia($now),
            'resumen_bienes_por_tipo' => $this->pdfResumenBienesPorTipo($now),

            'movimientos_ultimos_30_dias' => $this->pdfMovimientosUltimos30Dias($now),
            'movimientos_por_tipo' => $this->pdfMovimientosPorTipo($now),
            'movimientos_por_usuario' => $this->pdfMovimientosPorUsuario($now),
            'movimientos_de_bienes_desincorporados' => $this->pdfMovimientosDeBienesDesincorporados($now),

            'organismos_y_unidades' => $this->pdfOrganismosYUnidades($now),
            'unidades_y_dependencias' => $this->pdfUnidadesYDependencias($now),
            'dependencias_y_responsables' => $this->pdfDependenciasYResponsables($now),

            'responsables_y_bienes' => $this->pdfResponsablesYBienes($now),
            'usuarios_y_roles' => $this->pdfUsuariosYRoles($now),

            'registros_eliminados' => $this->pdfRegistrosEliminados($now),

            default => abort(404, 'Tipo de reporte no definido.'),
        };
    }

    /* =========================
     *  BLOQUE: BIENES
     * ========================= */

    protected function pdfInventarioGeneralBienes(Carbon $now)
    {
        $bienes = Bien::with(['dependencia.unidadAdministradora.organismo'])
            ->orderBy('codigo')
            ->get();

        return $this->fpdf->downloadBienesListado(
            'reporte_inventario_general_bienes.pdf',
            'Inventario general de bienes',
            'Listado completo de bienes registrados',
            $now->format('d/m/Y H:i'),
            $bienes,
        );
    }

    protected function pdfBienesPorEstado(Carbon $now)
    {
        $bienes = Bien::with(['dependencia.unidadAdministradora.organismo'])
            ->orderBy('estado')
            ->orderBy('codigo')
            ->get();

        return $this->fpdf->downloadBienesListado(
            'reporte_bienes_por_estado.pdf',
            'Bienes por estado',
            'Listado de bienes agrupados por estado',
            $now->format('d/m/Y H:i'),
            $bienes,
        );
    }

    protected function pdfBienesConFotografia(Carbon $now)
    {
        $bienes = Bien::with(['dependencia.unidadAdministradora.organismo'])
            ->whereNotNull('fotografia')
            ->where('fotografia', '!=', '')
            ->orderBy('codigo')
            ->get();

        return $this->fpdf->downloadBienesListado(
            'reporte_bienes_con_fotografia.pdf',
            'Bienes con fotografía',
            'Bienes que cuentan con registro fotográfico',
            $now->format('d/m/Y H:i'),
            $bienes,
        );
    }

    protected function pdfBienesSinMovimientos(Carbon $now)
    {
        $bienes = Bien::doesntHave('movimientos')
            ->with(['dependencia.unidadAdministradora.organismo'])
            ->orderBy('codigo')
            ->get();

        return $this->fpdf->downloadBienesListado(
            'reporte_bienes_sin_movimientos.pdf',
            'Bienes sin movimientos',
            'Bienes que nunca han tenido movimientos registrados',
            $now->format('d/m/Y H:i'),
            $bienes,
        );
    }

    protected function pdfBienesPorDependencia(Carbon $now)
    {
        $dependencias = Dependencia::with(['unidadAdministradora.organismo', 'bienes'])
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.bienes_por_dependencia', [
            'titulo' => 'Bienes por dependencia',
            'generadoEn' => $now,
            'dependencias' => $dependencias,
        ])->setPaper('letter');

        return $pdf->download('reporte_bienes_por_dependencia.pdf');
    }

    protected function pdfBienesPorUnidad(Carbon $now)
    {
        $unidades = UnidadAdministradora::with(['organismo', 'dependencias.bienes'])
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.bienes_por_unidad', [
            'titulo' => 'Bienes por unidad administradora',
            'generadoEn' => $now,
            'unidades' => $unidades,
        ])->setPaper('letter');

        return $pdf->download('reporte_bienes_por_unidad.pdf');
    }

    protected function pdfBienesPorOrganismo(Carbon $now)
    {
        $organismos = Organismo::with(['unidadesAdministradoras.dependencias.bienes'])
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.bienes_por_organismo', [
            'titulo' => 'Bienes por organismo',
            'generadoEn' => $now,
            'organismos' => $organismos,
        ])->setPaper('letter');

        return $pdf->download('reporte_bienes_por_organismo.pdf');
    }

    protected function pdfResumenBienesPorEstado(Carbon $now)
    {
        $resumen = Bien::selectRaw('estado, COUNT(*) as cantidad, SUM(precio) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.resumen_bienes', [
            'titulo' => 'Resumen de bienes por estado',
            'generadoEn' => $now,
            'dimension' => 'estado',
            'resumen' => $resumen,
        ])->setPaper('letter');

        return $pdf->download('reporte_resumen_bienes_por_estado.pdf');
    }

    protected function pdfResumenBienesPorDependencia(Carbon $now)
    {
        $resumen = Bien::selectRaw('dependencia_id, COUNT(*) as cantidad, SUM(precio) as total')
            ->groupBy('dependencia_id')
            ->with('dependencia')
            ->orderBy('dependencia_id')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.resumen_bienes', [
            'titulo' => 'Resumen de bienes por dependencia',
            'generadoEn' => $now,
            'dimension' => 'dependencia',
            'resumen' => $resumen,
        ])->setPaper('letter');

        return $pdf->download('reporte_resumen_bienes_por_dependencia.pdf');
    }

    protected function pdfResumenBienesPorTipo(Carbon $now)
    {
        $resumen = Bien::selectRaw('tipo_bien, COUNT(*) as cantidad, SUM(precio) as total')
            ->groupBy('tipo_bien')
            ->orderBy('tipo_bien')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.resumen_bienes', [
            'titulo' => 'Resumen de bienes por tipo de bien',
            'generadoEn' => $now,
            'dimension' => 'tipo_bien',
            'resumen' => $resumen,
        ])->setPaper('letter');

        return $pdf->download('reporte_resumen_bienes_por_tipo.pdf');
    }

    /* =========================
     *  BLOQUE: MOVIMIENTOS
     * ========================= */

    protected function pdfMovimientosUltimos30Dias(Carbon $now)
    {
        $desde = $now->copy()->subDays(30)->startOfDay();

        $movimientos = Movimiento::with(['usuario', 'bien', 'subject'])
            ->where('fecha', '>=', $desde)
            ->orderByDesc('fecha')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.movimientos_listado', [
            'titulo' => 'Movimientos de los últimos 30 días',
            'generadoEn' => $now,
            'movimientos' => $movimientos,
        ])->setPaper('letter');

        return $pdf->download('reporte_movimientos_ultimos_30_dias.pdf');
    }

    protected function pdfMovimientosPorTipo(Carbon $now)
    {
        $movimientos = Movimiento::with(['usuario', 'bien', 'subject'])
            ->orderBy('tipo')
            ->orderByDesc('fecha')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.movimientos_listado', [
            'titulo' => 'Movimientos por tipo',
            'generadoEn' => $now,
            'movimientos' => $movimientos,
            'agruparPorTipo' => true,
        ])->setPaper('letter');

        return $pdf->download('reporte_movimientos_por_tipo.pdf');
    }

    protected function pdfMovimientosPorUsuario(Carbon $now)
    {
        $movimientos = Movimiento::with(['usuario', 'bien', 'subject'])
            ->orderBy('usuario_id')
            ->orderByDesc('fecha')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.movimientos_listado', [
            'titulo' => 'Movimientos por usuario',
            'generadoEn' => $now,
            'movimientos' => $movimientos,
            'agruparPorUsuario' => true,
        ])->setPaper('letter');

        return $pdf->download('reporte_movimientos_por_usuario.pdf');
    }

    protected function pdfMovimientosDeBienesDesincorporados(Carbon $now)
    {
        $movimientos = Movimiento::with(['usuario', 'bien'])
            ->whereHas('bien', function ($q) {
                $q->where('estado', 'desincorporado');
            })
            ->orderByDesc('fecha')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.movimientos_listado', [
            'titulo' => 'Historial de bienes desincorporados',
            'generadoEn' => $now,
            'movimientos' => $movimientos,
        ])->setPaper('letter');

        return $pdf->download('reporte_movimientos_bienes_desincorporados.pdf');
    }

    /* =========================
     *  BLOQUE: ESTRUCTURA ORGANIZATIVA
     * ========================= */

    protected function pdfOrganismosYUnidades(Carbon $now)
    {
        $organismos = Organismo::with('unidadesAdministradoras')->orderBy('nombre')->get();

        $pdf = Pdf::loadView('reportes.pdf.organismos_y_unidades', [
            'titulo' => 'Organismos y sus unidades administradoras',
            'generadoEn' => $now,
            'organismos' => $organismos,
        ])->setPaper('letter');

        return $pdf->download('reporte_organismos_y_unidades.pdf');
    }

    protected function pdfUnidadesYDependencias(Carbon $now)
    {
        $unidades = UnidadAdministradora::with('dependencias')->orderBy('nombre')->get();

        $pdf = Pdf::loadView('reportes.pdf.unidades_y_dependencias', [
            'titulo' => 'Unidades y dependencias asociadas',
            'generadoEn' => $now,
            'unidades' => $unidades,
        ])->setPaper('letter');

        return $pdf->download('reporte_unidades_y_dependencias.pdf');
    }

    protected function pdfDependenciasYResponsables(Carbon $now)
    {
        $dependencias = Dependencia::with(['unidadAdministradora.organismo', 'responsable'])
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.dependencias_y_responsables', [
            'titulo' => 'Dependencias y responsables',
            'generadoEn' => $now,
            'dependencias' => $dependencias,
        ])->setPaper('letter');

        return $pdf->download('reporte_dependencias_y_responsables.pdf');
    }

    /* =========================
     *  BLOQUE: RESPONSABLES Y USUARIOS
     * ========================= */

    protected function pdfResponsablesYBienes(Carbon $now)
    {
        $responsables = Responsable::with(['dependencias.bienes', 'tipo'])
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.responsables_y_bienes', [
            'titulo' => 'Responsables y bienes asignados',
            'generadoEn' => $now,
            'responsables' => $responsables,
        ])->setPaper('letter');

        return $pdf->download('reporte_responsables_y_bienes.pdf');
    }

    protected function pdfUsuariosYRoles(Carbon $now)
    {
        $usuarios = Usuario::with('rol')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.usuarios_y_roles', [
            'titulo' => 'Usuarios del sistema y roles',
            'generadoEn' => $now,
            'usuarios' => $usuarios,
        ])->setPaper('letter');

        return $pdf->download('reporte_usuarios_y_roles.pdf');
    }

    /* =========================
     *  BLOQUE: AUDITORÍA / ELIMINADOS
     * ========================= */

    protected function pdfRegistrosEliminados(Carbon $now)
    {
        $eliminados = Eliminado::orderByDesc('deleted_at')->limit(500)->get();

        $pdf = Pdf::loadView('reportes.pdf.registros_eliminados', [
            'titulo' => 'Registros eliminados (auditoría)',
            'generadoEn' => $now,
            'eliminados' => $eliminados,
        ])->setPaper('letter');

        return $pdf->download('reporte_registros_eliminados.pdf');
    }

    /**
     * Guardar un nuevo reporte (uso tipo API).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,id'],
            'tipo' => ['required', 'string', 'max:100'],
            'fecha_generado' => ['required', 'date'],
            'archivo_pdf_path' => ['nullable', 'string', 'max:255'],
        ]);

        $reporte = Reporte::create($validated);

        return response()->json($reporte, 201);
    }

    /**
     * Mostrar un reporte específico (uso tipo API).
     */
    public function show(Reporte $reporte)
    {
        $reporte->load('usuario');

        return response()->json($reporte);
    }

    /**
     * Actualizar un reporte (uso tipo API).
     */
    public function update(Request $request, Reporte $reporte)
    {
        $validated = $request->validate([
            'usuario_id' => ['sometimes', 'exists:usuarios,id'],
            'tipo' => ['sometimes', 'string', 'max:100'],
            'fecha_generado' => ['sometimes', 'date'],
            'archivo_pdf_path' => ['nullable', 'string', 'max:255'],
        ]);

        $reporte->update($validated);

        return response()->json($reporte);
    }

    /**
     * Eliminar un reporte (uso tipo API).
     */
    public function destroy(Reporte $reporte)
    {
        // Archivar reporte antes de eliminar
        \App\Services\EliminadosService::archiveModel($reporte, auth()->id());
        $reporte->delete();

        return response()->json(null, 204);
    }
}
