<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\CodigoUnicoService;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $unidades = $query->paginate(10)
            ->appends($request->only(['search', 'organismo_id']));

        $organismos = Organismo::orderBy('nombre')->get();

        if ($request->ajax()) {
            return view('unidades.index', compact('unidades', 'organismos', 'search'))->render();
        }

        return view('unidades.index', compact('unidades', 'organismos', 'search'));
    }

    public function create(Request $request)
    {
        $organismos = Organismo::all();

        $sugerenciasPorOrganismo = [];
        foreach ($organismos as $org) {
            $ultimoCodigo = UnidadAdministradora::where('organismo_id', $org->id)
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->max(DB::raw('CAST(codigo AS UNSIGNED)'));

            if ($ultimoCodigo) {
                $siguiente = $ultimoCodigo + 1;
            } else {
                $codigoOrgNum = (int) $org->codigo;
                $siguiente = $codigoOrgNum * 10000 + 1;
            }

            $codigoFormateado = str_pad((string) $siguiente, 8, '0', STR_PAD_LEFT);
            while (CodigoUnicoService::codigoExiste($codigoFormateado)) {
                $siguiente++;
                $codigoFormateado = str_pad((string) $siguiente, 8, '0', STR_PAD_LEFT);
                if ($org->code_max > 0 && $siguiente > $org->code_max) {
                    break;
                }
            }

            $sugerenciasPorOrganismo[$org->id] = $codigoFormateado;
        }

        $siguienteCodigo = null;
        $organismoSeleccionado = $request->input('organismo_id') ?? ($organismos->first()?->id);

        if ($organismoSeleccionado && isset($sugerenciasPorOrganismo[$organismoSeleccionado])) {
            $siguienteCodigo = $sugerenciasPorOrganismo[$organismoSeleccionado];
        }

        if (! $siguienteCodigo) {
            $siguienteCodigo = CodigoUnicoService::obtenerSiguienteCodigo();
        }

        return view('unidades.create', compact('organismos', 'siguienteCodigo', 'sugerenciasPorOrganismo'));
    }

    public function edit(UnidadAdministradora $unidadAdministradora)
    {
        $organismos = Organismo::all();

        return view('unidades.edit', compact('unidadAdministradora', 'organismos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organismo_id' => ['required', 'exists:organismos,id'],
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail('Este código ya está en uso por: '.$ubicacion['tabla'].' ('.$ubicacion['nombre'].')');
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

        $organismo = Organismo::find($validated['organismo_id']);
        if ($organismo && $organismo->code_min == 1 && $organismo->code_max == 50) {
            try {
                CodigoUnicoService::reservarCodigosParaOrganismo($organismo->id, 50);
                $organismo->refresh();
            } catch (\Exception $e) {
                \Log::warning("No se pudo asignar rango al organismo {$organismo->id}: ".$e->getMessage());
            }
        }

        $unidad = UnidadAdministradora::create($validated);

        try {
            CodigoUnicoService::reservarCodigosParaUnidad($unidad->id, 50);
        } catch (\Exception $e) {
            \Log::warning("No se pudieron reservar códigos para unidad {$unidad->id}: ".$e->getMessage());
        }

        return redirect()->route('unidades.index')->with('success', 'Unidad creada correctamente');
    }

    public function show(UnidadAdministradora $unidadAdministradora)
    {
        $unidadAdministradora->load(['organismo', 'dependencias']);

        return view('unidades.show', compact('unidadAdministradora'));
    }

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
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($unidadAdministradora) {
                    if (CodigoUnicoService::codigoExiste($value, 'unidades', $unidadAdministradora->id)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail('No puedes usar este código. Ya pertenece a: '.$ubicacion['tabla']);
                    }

                    $organismo = \App\Models\Organismo::find($unidadAdministradora->organismo_id);
                    if ($organismo && $organismo->code_max > 0) {
                        $codigoNum = (int) $value;
                        if ($codigoNum < $organismo->code_min || $codigoNum > $organismo->code_max) {
                            $fail('El código debe estar dentro del rango del organismo: '.
                                  str_pad((string) $organismo->code_min, 8, '0', STR_PAD_LEFT).' - '.
                                  str_pad((string) $organismo->code_max, 8, '0', STR_PAD_LEFT));
                        }
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
