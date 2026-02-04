<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\CodigoUnicoService;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Codedge\Fpdf\Fpdf\Fpdf;

class BienController extends Controller
{
    /**
     * Listar todos los bienes.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'organismo_id' => ['nullable', 'integer', 'exists:organismos,id'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades_administradoras,id'],
            'dependencias' => ['nullable', 'array'],
            'dependencias.*' => ['integer', 'exists:dependencias,id'],
            'estado' => ['nullable', 'array'],
            'estado.*' => ['string', Rule::in(array_map(fn(EstadoBien $estado) => $estado->value, EstadoBien::cases()))],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:255', 'regex:/^[0-9\-]+$/'],
            'sort' => [
                'nullable',
                'string',
                Rule::in([
                    'codigo',
                    'descripcion',
                    'precio',
                    'fecha_registro',
                    'estado'
                ])
            ],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ]);

        $query = Bien::with([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
        ]);

        // 🔎 Filtros
        if (!empty($validated['search'])) {
            $query->search($validated['search']);
        }

        if (!empty($validated['descripcion'])) {
            $query->where('descripcion', 'like', '%' . $validated['descripcion'] . '%');
        }

        if (!empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%' . $validated['codigo'] . '%');
        }

        if (!empty($validated['estado'])) {
            $query->whereIn('estado', $validated['estado']);
        }

        if (!empty($validated['fecha_desde']) && !empty($validated['fecha_hasta'])) {
            $query->whereBetween('fecha_registro', [$validated['fecha_desde'], $validated['fecha_hasta']]);
        } elseif (!empty($validated['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $validated['fecha_desde']);
        } elseif (!empty($validated['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $validated['fecha_hasta']);
        }

        if (!empty($validated['dependencias'])) {
            $query->whereIn('dependencia_id', $validated['dependencias']);
        }

        if (!empty($validated['unidad_id'])) {
            $unidadId = $validated['unidad_id'];
            $query->whereHas('dependencia.unidadAdministradora', fn($q) => $q->where('id', $unidadId));
        }

        if (!empty($validated['organismo_id'])) {
            $organismoId = $validated['organismo_id'];
            $query->whereHas('dependencia.unidadAdministradora.organismo', fn($q) => $q->where('id', $organismoId));
        }

        // ⚡️ Ordenamiento dinámico
        $sort = $validated['sort'] ?? 'fecha_registro';
        $direction = $validated['direction'] ?? 'desc';

        $bienes = $query
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->appends($request->query());

        $organismos = Organismo::orderBy('nombre')->get();

        $unidades = UnidadAdministradora::query()
            ->when($validated['organismo_id'] ?? null, fn($q, $organismoId) => $q->where('organismo_id', $organismoId))
            ->orderBy('nombre')
            ->get();

        $dependencias = Dependencia::query()
            ->with('unidadAdministradora')
            ->when($validated['unidad_id'] ?? null, fn($q, $unidadId) => $q->where('unidad_administradora_id', $unidadId))
            ->when(
                ($validated['organismo_id'] ?? null) && !($validated['unidad_id'] ?? null),
                fn($q) => $q->whereHas('unidadAdministradora', fn($sub) => $sub->where('organismo_id', $validated['organismo_id']))
            )
            ->orderBy('nombre')
            ->get();

        $estados = collect(EstadoBien::cases())->mapWithKeys(
            fn(EstadoBien $estado) => [$estado->value => $estado->label()]
        );

        if ($request->ajax()) {
            return view('bienes.partials.table', compact('bienes'))->render();
        }

        return view('bienes.index', [
            'bienes' => $bienes,
            'filters' => $validated,
            'organismos' => $organismos,
            'unidades' => $unidades,
            'dependencias' => $dependencias,
            'estados' => $estados,
        ]);
    }

    /**
     * Mostrar formulario de creación con lógica de código secuencial.
     */
    public function create()
    {
        // Uso del servicio para sugerir el código real disponible
        $codigoSugerido = CodigoUnicoService::obtenerSiguienteCodigo();

        $dependencias = Dependencia::with('responsable')->get();

        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn(TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        return view('bienes.create', compact('dependencias', 'tiposBien', 'codigoSugerido'));
    }

    /**
     * Guardar un nuevo bien.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // CAMBIO: 'nullable' para que la dependencia no sea obligatoria
            'dependencia_id' => ['nullable', 'exists:dependencias,id'],
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("El código ya está asignado a: " . $info['tabla'] . " (" . $info['nombre'] . ")");
                    }
                }
            ],
            'descripcion' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'fotografia' => ['nullable', 'image', 'max:2048'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['required', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['required', 'date'],
        ]);

        if ($request->hasFile('fotografia')) {
            $foto = $this->procesarFotografia($request);
            if ($foto) $validated['fotografia'] = $foto;
        }

        $bien = Bien::create($validated);

        // Lógica de observación si es un bien huérfano (sin dependencia)
        if (!$request->filled('dependencia_id')) {
            $bien->update(['ubicacion' => $request->ubicacion ?? 'Almacén Central / Tránsito']);
        }

        return redirect()->route('bienes.index')->with('success', 'Bien creado correctamente.');
    }

    /**
     * Mostrar un bien específico.
     */
    public function show(Bien $bien)
    {
        $bien->load(['dependencia.responsable', 'movimientos']);
        return view('bienes.show', compact('bien'));
    }

    /**
     * Exportar detalle a PDF.
     */
    public function exportPdf(Bien $bien)
    {
        $bien->loadMissing(['dependencia.responsable', 'movimientos.usuario']);
        $movimientos = $bien->movimientos()->orderByDesc('fecha')->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('bienes.pdf', [
            'bien' => $bien,
            'dependencia' => $bien->dependencia,
            'responsablePrimario' => $bien->responsable_primario,
            'movimientos' => $movimientos,
        ])->setPaper('letter');

        $fileName = sprintf('bien_%s_%s.pdf', Str::slug($bien->codigo ?? 'sin_codigo', '_'), Str::slug(Str::limit($bien->descripcion, 50, ''), '_'));
        return $pdf->download($fileName);
    }

    private function procesarFotografia(Request $request, ?Bien $bien = null): ?string
    {
        if (!$request->hasFile('fotografia'))
            return null;
        $file = $request->file('fotografia');
        if ($bien && $bien->fotografia && !str_starts_with($bien->fotografia, 'http')) {
            Storage::disk('public')->delete($bien->fotografia);
        }
        $filename = uniqid('bien_') . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('bienes', $filename, 'public');
    }

    public function update(Request $request, Bien $bien)
    {
        $validated = $request->validate([
            'dependencia_id' => ['nullable', 'exists:dependencias,id'],
            'codigo' => [
                'sometimes',
                'string',
                'size:8',
                function ($attribute, $value, $fail) use ($bien) {
                    if (CodigoUnicoService::codigoExiste($value, 'bienes', $bien->id)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("Código en uso por " . $info['tabla']);
                    }
                }
            ],
            'descripcion' => ['sometimes', 'string', 'max:255'],
            'precio' => ['sometimes', 'numeric', 'min:0'],
            'fotografia' => ['nullable', 'image', 'max:2048'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estado' => ['sometimes', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['sometimes', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['sometimes', 'date'],
        ]);

        if ($request->hasFile('fotografia')) {
            $foto = $this->procesarFotografia($request, $bien);
            if ($foto) $validated['fotografia'] = $foto;
        }

        $bien->update($validated);
        return redirect()->route('bienes.index')->with('success', 'Bien actualizado.');
    }

    // ... el resto de métodos (show, destroy, etc.) se mantienen ...



    public function edit(Bien $bien)
    {
        $dependencias = Dependencia::with('responsable')->get();
        return view('bienes.edit', compact('bien', 'dependencias'));
    }

    public function destroy(Bien $bien)
    {
        if (!auth()->user()->canDeleteData()) {
            return response()->json(['message' => 'No tienes permisos para desincorporar bienes.'], 403);
        }

        $userId = auth()->id();
        \App\Models\Movimiento::create([
            'bien_id' => $bien->id,
            'tipo' => 'desincorporación',
            'descripcion' => 'Bien desincorporado',
            'usuario_id' => $userId,
        ]);

        $bien->update(['estado' => EstadoBien::EXTRAVIADO]);
        \App\Services\EliminadosService::archiveModel($bien, $userId);

        return redirect()->route('bienes.index')->with('success', 'Bien desincorporado correctamente.');
    }

    public function galeriaCompleta()
    {
        $imagenes = Bien::whereNotNull('fotografia')
            ->where('fotografia', '!=', '')
            ->select('id', 'codigo', 'descripcion', 'fotografia')
            ->get()
            ->map(fn($b) => (object) [
                'id' => $b->id,
                'codigo' => $b->codigo,
                'descripcion' => $b->descripcion,
                'url' => Storage::url($b->fotografia)
            ]);

        return view('bienes.galeria-completa', compact('imagenes'));
    }

    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'dependencias' => ['nullable', 'array'],
            'estado' => ['nullable', 'array'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date'],
        ]);

        $query = Bien::with(['dependencia.responsable', 'dependencia.unidadAdministradora.organismo']);

        if (!empty($validated['search']))
            $query->search($validated['search']);
        if (!empty($validated['estado']))
            $query->whereIn('estado', $validated['estado']);
        if (!empty($validated['dependencias']))
            $query->whereIn('dependencia_id', $validated['dependencias']);

        $bienes = $query->get();
        $reporteService = new \App\Services\FpdfReportService();

        return $reporteService->downloadBienesListado(
            'reporte_bienes_' . now()->format('dmY_His') . '.pdf',
            'REPORTE DE BIENES E INVENTARIO',
            'Listado filtrado de bienes institucionales',
            now()->format('d/m/Y H:i'),
            $bienes
        );
    }
}
