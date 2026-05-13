<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Responsable;
use App\Models\UnidadAdministradora;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DependenciaController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

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

        return view('dependencias.index', [
            'dependencias' => $query->paginate(10)->withQueryString(),
            'unidades' => \App\Models\UnidadAdministradora::all(),
            'responsables' => \App\Models\Responsable::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_administradora_id' => 'required|exists:unidades_administradoras,id',
            'codigo' => [
                'required',
                'string',
                'max:8',
                'regex:/^[0-9]+$/',
                Rule::unique('dependencias')
                    ->where(function ($query) use ($request) {
                        return $query->where('unidad_administradora_id', $request->unidad_administradora_id);
                    }),
            ],
            'nombre' => 'required|string|max:40',
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        Dependencia::create($validated);

        return redirect()
            ->route('dependencias.index')
            ->with('success', '✅ Dependencia registrada exitosamente.');
    }

    public function create(Request $request)
    {
        $unidades = UnidadAdministradora::all();
        $responsables = Responsable::all();

        // Calcular el próximo código global y sugerencias por unidad
        $sugerenciasPorUnidad = [];
        foreach ($unidades as $unidad) {
            $ultimoCodigo = Dependencia::where('unidad_administradora_id', $unidad->id)
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->max(DB::raw('CAST(codigo AS UNSIGNED)'));

            $siguiente = $ultimoCodigo ? $ultimoCodigo + 1 : 1;
            $sugerenciasPorUnidad[$unidad->id] = str_pad((string) $siguiente, 8, '0', STR_PAD_LEFT);
        }

        $proximoCodigo = $sugerenciasPorUnidad[$unidades->first()?->id] ?? '00000001';

        return view('dependencias.create', compact('unidades', 'responsables', 'proximoCodigo', 'sugerenciasPorUnidad'));
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
        // ... código existente sin cambios ...
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

    /**
     * Generar reporte PDF de dependencias con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'unidad_id' => ['nullable', 'integer'],
            'responsable_id' => ['nullable', 'integer'],
        ]);

        $query = Dependencia::with(['unidadAdministradora', 'responsable', 'bienes']);

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
