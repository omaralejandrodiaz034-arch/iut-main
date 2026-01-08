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

    public function graficas()
    {
        // Datos para gráfico de bienes por tipo
        $bienesPorTipo = Bien::selectRaw('tipo_bien, COUNT(*) as count')
            ->groupBy('tipo_bien')
            ->get()
            ->mapWithKeys(function ($item) {
                $tipo = TipoBien::tryFrom($item->tipo_bien);
                $label = $tipo ? $tipo->label() : $item->tipo_bien;
                return [$label => $item->count];
            })
            ->toArray();

        // Datos para gráfico de bienes por estado
        $bienesPorEstado = Bien::selectRaw('estado, COUNT(*) as count')
            ->groupBy('estado')
            ->get()
            ->mapWithKeys(function ($item) {
                $estado = EstadoBien::tryFrom($item->estado);
                $label = $estado ? $estado->label() : $item->estado;
                return [$label => $item->count];
            })
            ->toArray();

        // Datos para gráfico de bienes por registro (por mes)
        $bienesPorRegistro = Bien::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as count')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('count', 'mes')
            ->toArray();

        return view('reportes.graficas', compact('bienesPorTipo', 'bienesPorEstado', 'bienesPorRegistro'));
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
