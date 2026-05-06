<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\BienTypeService;
use App\Services\CodigoUnicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    private BienTypeService $bienTypeService;

    public function __construct(BienTypeService $bienTypeService)
    {
        $this->bienTypeService = $bienTypeService;
    }

    /**
     * Listar todos los bienes.
     */
    public function index(Request $request)
    {
        // 1. Validación de entrada
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'organismo_id' => ['nullable', 'integer', 'exists:organismos,id'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades_administradoras,id'],
            'dependencias' => ['nullable', 'array'],
            'dependencias.*' => ['integer', 'exists:dependencias,id'],
            'estado' => ['nullable', 'array'],
            'estado.*' => ['string', Rule::in(array_map(fn ($e) => $e->value, EstadoBien::cases()))],
            'fecha_desde' => ['nullable', 'date'],
            'tipo_bien' => ['nullable', 'string', Rule::in(array_map(fn ($t) => $t->value, TipoBien::cases()))],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
            'sort' => ['nullable', 'string', Rule::in(['codigo', 'descripcion', 'precio', 'fecha_registro', 'estado'])],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'solo_desincorporados' => ['nullable', 'boolean'],
        ]);

        // 2. Construcción de la consulta con relaciones
        $query = Bien::with([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
        ]);

        // 🔎 Filtros dinámicos
        if (!empty($validated['search'])) {
            $query->search($validated['search']);
        }

        if (!empty($validated['estado'])) {
            $query->whereIn('estado', $validated['estado']);
        }
        
        if (!empty($validated['tipo_bien'])) {
            $query->where('tipo_bien', $validated['tipo_bien']);
        }

        // Filtrado por Fechas
        $query->when($validated['fecha_desde'] ?? null, fn($q, $f) => $q->whereDate('fecha_registro', '>=', $f))
            ->when($validated['fecha_hasta'] ?? null, fn($q, $f) => $q->whereDate('fecha_registro', '<=', $f));

        // Filtrado por Relaciones (Jerarquía)
        if (!empty($validated['dependencias'])) {
            $query->whereIn('dependencia_id', $validated['dependencias']);
        } elseif (!empty($validated['unidad_id'])) {
            $query->whereHas('dependencia', fn($q) => $q->where('unidad_administradora_id', $validated['unidad_id']));
        } elseif (!empty($validated['organismo_id'])) {
            $query->whereHas('dependencia.unidadAdministradora', fn($q) => $q->where('organismo_id', $validated['organismo_id']));
        }

        // Filtrado de Desincorporados
        $soloDesincorporados = filter_var($validated['solo_desincorporados'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $filtroEstadoExplicito = !empty($validated['estado']) && in_array('DESINCORPORADO', $validated['estado']);

        if ($soloDesincorporados) {
            $query->where('estado', 'DESINCORPORADO');
        } elseif (!$filtroEstadoExplicito) {
            $query->where('estado', '!=', 'DESINCORPORADO');
        }

        // ⚡️ Ordenamiento y Paginación
        $sort = $validated['sort'] ?? 'fecha_registro';
        $direction = $validated['direction'] ?? 'desc';

        $bienes = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->appends($request->query());

        // 3. Respuesta AJAX
        if ($request->ajax()) {
            return view('bienes.partials.table', compact('bienes'))->render();
        }

        // 4. Carga de datos para selectores
        $organismos = Organismo::orderBy('nombre')->get();

        $unidades = UnidadAdministradora::query()
            ->when($validated['organismo_id'] ?? null, fn($q, $id) => $q->where('organismo_id', $id))
            ->orderBy('nombre')
            ->get();

        $dependencias = Dependencia::query()
            ->with('unidadAdministradora')
            ->when($validated['unidad_id'] ?? null, fn($q, $id) => $q->where('unidad_administradora_id', $id))
            ->when(($validated['organismo_id'] ?? null) && empty($validated['unidad_id']),
                fn($q) => $q->whereHas('unidadAdministradora', fn($sub) => $sub->where('organismo_id', $validated['organismo_id']))
            )
            ->orderBy('nombre')
            ->get();

        $estados = collect(EstadoBien::cases())->mapWithKeys(
            fn(EstadoBien $estado) => [$estado->value => $estado->label()]
        );
        
        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn(TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        return view('bienes.index', [
            'bienes' => $bienes,
            'filters' => $validated,
            'organismos' => $organismos,
            'unidades' => $unidades,
            'dependencias' => $dependencias,
            'estados' => $estados,
            'tiposBien' => $tiposBien,
        ]);
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $dependencias = Dependencia::with('responsable')->get();

        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        return view('bienes.create', compact('dependencias', 'tiposBien'));
    }

    /**
     * Obtener el siguiente código recomendado para una dependencia específica.
     */
    public function recomendarCodigo(Dependencia $dependencia)
    {
        try {
            $resultado = CodigoUnicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);

            return response()->json([
                'success' => true,
                'codigo' => $resultado['codigo'],
                'siguiente_numero' => $resultado['siguiente_numero'],
                'rango_min' => $resultado['rango_min'],
                'rango_max' => $resultado['rango_max'],
                'disponibles_restantes' => $resultado['disponibles_restantes'] ?? ($resultado['rango_max'] - $resultado['siguiente_numero'] + 1),
                'mensaje' => sprintf(
                    '✅ Código recomendado: %s | Rango: %d-%d | Disponibles: %d',
                    $resultado['codigo'],
                    $resultado['rango_min'],
                    $resultado['rango_max'],
                    $resultado['disponibles_restantes'] ?? ($resultado['rango_max'] - $resultado['siguiente_numero'] + 1)
                ),
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Error al recomendar código', [
                'dependencia_id' => $dependencia->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'rango_exhausto',
                'mensaje' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error inesperado al recomendar código', [
                'dependencia_id' => $dependencia->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'error_general',
                'mensaje' => 'Error al generar el código. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Guardar un nuevo bien.
     */
    public function store(Request $request)
    {
        // Validación base
        $rules = $this->getBaseValidationRules();
        
        // Agregar reglas específicas por tipo
        $tipo = $request->input('tipo_bien');
        $rules = array_merge($rules, $this->getSpecificValidationRules($tipo));

        $validated = $request->validate($rules);

        // Validación adicional: código dentro del rango de la dependencia
        $this->validarCodigoEnRango($validated['codigo'], $validated['dependencia_id']);

        DB::beginTransaction();
        
        try {
            // Procesar fotografía
            if ($request->hasFile('fotografia')) {
                $validated['fotografia'] = $this->procesarFotografia($request);
            }

            // Separar datos de la tabla bienes
            $datosBien = $this->extractBienData($validated);

            $bien = Bien::create($datosBien);

            // Guardar datos específicos del tipo
            if ($tipo) {
                $this->bienTypeService->sync($bien, $tipo, $validated);
            }

            // Crear registro de desincorporación si aplica
            if ($bien->estado === EstadoBien::DESINCORPORADO && $request->hasFile('acta_desincorporacion')) {
                $actaPath = $request->file('acta_desincorporacion')->store('actas_desincorporacion', 'public');
                $bien->desincorporado()->create([
                    'motivo_desincorporacion' => $validated['motivo_desincorporacion'] ?? 'Sin motivo especificado',
                    'acta_desincorporacion' => $actaPath,
                ]);
            }

            DB::commit();

            Log::info('Bien registrado exitosamente', [
                'bien_id' => $bien->id,
                'codigo' => $bien->codigo,
                'usuario_id' => auth()->id()
            ]);

            $mensaje = sprintf(
                '✅ Bien "%s" (Código: %s) ha sido registrado exitosamente.',
                $bien->descripcion,
                $bien->codigo
            );

            return redirect()->route('bienes.index')->with('success', $mensaje);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Error de BD al guardar bien', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()->withErrors([
                'error' => 'Error al guardar el bien. Por favor, verifique los datos e intente nuevamente.'
            ])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar bien', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Mostrar un bien específico.
     */
    public function show(Bien $bien)
    {
        $bien->load([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
            'movimientos' => fn($q) => $q->orderByDesc('fecha')->orderByDesc('created_at')
        ]);
        
        // Cargar datos específicos del tipo
        $this->cargarDatosEspecificos($bien);

        return view('bienes.show', compact('bien'));
    }

    /**
     * Exportar detalle a PDF.
     */
    public function exportPdf(Bien $bien)
    {
        $bien->loadMissing([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
            'movimientos.usuario'
        ]);
        
        $this->cargarDatosEspecificos($bien);
        
        $movimientos = $bien->movimientos()->orderByDesc('fecha')->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('bienes.pdf', [
            'bien' => $bien,
            'dependencia' => $bien->dependencia,
            'responsablePrimario' => $bien->responsable_primario,
            'movimientos' => $movimientos,
        ])->setPaper('letter');

        $fileName = sprintf(
            'bien_%s_%s.pdf',
            Str::slug($bien->codigo ?? 'sin_codigo', '_'),
            Str::slug(Str::limit($bien->descripcion, 50, ''), '_')
        );

        return $pdf->download($fileName);
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Bien $bien)
    {
        $bien->load([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo'
        ]);
        
        $this->cargarDatosEspecificos($bien);

        $dependencias = Dependencia::with('responsable')->get();
        
        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn(TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        $estados = collect(EstadoBien::cases())->mapWithKeys(
            fn(EstadoBien $estado) => [$estado->value => $estado->label()]
        );

        $subtipoData = $this->obtenerDatosSubtipo($bien);

        return view('bienes.edit', compact('bien', 'dependencias', 'tiposBien', 'estados', 'subtipoData'));
    }

    /**
     * Actualizar un bien.
     */
    public function update(Request $request, Bien $bien)
    {
        $rules = $this->getUpdateValidationRules($bien);
        
        $tipo = $request->input('tipo_bien', $bien->tipo_bien?->value);
        $rules = array_merge($rules, $this->getSpecificValidationRules($tipo));

        $validated = $request->validate($rules);

        // Validar código en rango si cambió
        if (isset($validated['codigo']) && $validated['codigo'] !== $bien->codigo) {
            $dependenciaId = $validated['dependencia_id'] ?? $bien->dependencia_id;
            $this->validarCodigoEnRango($validated['codigo'], $dependenciaId);
        }

        DB::beginTransaction();
        
        try {
            // Procesar fotografía
            if ($request->hasFile('fotografia')) {
                $validated['fotografia'] = $this->procesarFotografia($request, $bien);
            }

            $datosBien = $this->extractBienData($validated);

            // Si cambió el tipo de bien, eliminar subtipo anterior
            $tipoAnterior = $bien->tipo_bien?->value;
            if ($tipo && $tipoAnterior && strtoupper($tipo) !== strtoupper($tipoAnterior)) {
                $this->eliminarSubtipoAnterior($bien, $tipoAnterior);
            }

            $bien->update($datosBien);

            // Sincronizar datos específicos
            if ($tipo) {
                $this->bienTypeService->sync($bien, $tipo, $validated);
            }

            DB::commit();

            Log::info('Bien actualizado', [
                'bien_id' => $bien->id,
                'usuario_id' => auth()->id()
            ]);

            return redirect()->route('bienes.index')->with('success', '✅ Bien actualizado exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar formulario de desincorporación.
     */
    public function showDesincorporarForm(Bien $bien)
    {
        $this->cargarDatosEspecificos($bien);
        
        return view('bienes.desincorporar', compact('bien'));
    }

    /**
     * Procesar desincorporación.
     */
    public function desincorporar(Request $request, Bien $bien)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|min:10|max:2000',
        ]);

        DB::beginTransaction();
        
        try {
            // Generar acta de desincorporación
            $service = new \App\Services\ActaDesincorporacionService;
            $actaPath = $service->generar(
                bien: $bien,
                motivo: $request->motivo,
                usuario: auth()->user()
            );

            // Crear registro en bienes_desincorporados
            \App\Models\BienDesincorporado::create([
                'bien_id' => $bien->id,
                'dependencia_id' => $bien->dependencia_id,
                'responsable_id' => $bien->responsable_id,
                'codigo' => $bien->codigo,
                'descripcion' => $bien->descripcion,
                'precio' => $bien->precio,
                'fotografia' => $bien->fotografia,
                'estado' => 'DESINCORPORADO',
                'fecha_registro' => $bien->fecha_registro,
                'tipo_bien' => $bien->tipo_bien?->value,
                'caracteristicas' => $bien->caracteristicas,
                'motivo_desincorporacion' => $request->motivo,
                'acta_desincorporacion' => $actaPath,
                'fecha_desincorporacion' => now(),
            ]);

            // Registrar movimiento
            \App\Models\Movimiento::create([
                'bien_id' => $bien->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'DESINCORPORACION',
                'observaciones' => $request->motivo,
                'fecha' => now(),
                'acta_path' => $actaPath,
            ]);

            // Eliminar subtipos
            $this->eliminarTodosSubtipos($bien);

            // Eliminar bien
            $bien->delete();

            DB::commit();

            Log::info('Bien desincorporado', [
                'bien_id' => $bien->id,
                'codigo' => $bien->codigo,
                'usuario_id' => auth()->id()
            ]);

            return redirect()->route('bienes.index')->with(
                'success',
                '✅ Bien desincorporado exitosamente. El acta ha sido generada y guardada.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al desincorporar bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Error al desincorporar: ' . $e->getMessage()]);
        }
    }

    /**
     * Galería completa de imágenes.
     */
    public function galeriaCompleta()
    {
        $imagenes = Bien::whereNotNull('fotografia')
            ->where('fotografia', '!=', '')
            ->where('estado', '!=', 'DESINCORPORADO')
            ->select('id', 'codigo', 'descripcion', 'fotografia')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($b) => (object) [
                'id' => $b->id,
                'codigo' => $b->codigo,
                'descripcion' => $b->descripcion,
                'url' => Storage::url($b->fotografia),
            ]);

        return view('bienes.galeria-completa', compact('imagenes'));
    }

    /**
     * Generar reporte en PDF.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'dependencias' => ['nullable', 'array'],
            'dependencias.*' => ['exists:dependencias,id'],
            'estado' => ['nullable', 'array'],
            'estado.*' => ['string', Rule::in(array_map(fn($e) => $e->value, EstadoBien::cases()))],
            'tipo_bien' => ['nullable', 'string', Rule::in(array_map(fn($t) => $t->value, TipoBien::cases()))],
            'organismo_id' => ['nullable', 'exists:organismos,id'],
            'unidad_id' => ['nullable', 'exists:unidades_administradoras,id'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $query = Bien::with([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo'
        ]);

        // Aplicar filtros
        $this->aplicarFiltrosReporte($query, $validated);

        $bienes = $query->get();
        
        if ($bienes->isEmpty()) {
            return back()->with('warning', 'No hay bienes que coincidan con los filtros seleccionados.');
        }

        $reporteService = new \App\Services\FpdfReportService;
        $tipoReporte = $this->determinarTipoReporte($validated);
        
        return $this->generarReportePorTipo($reporteService, $bienes, $tipoReporte, $validated);
    }

    /**
     * Mostrar formulario de transferencia.
     */
    public function showTransferirForm(Bien $bien)
    {
        $bien->load('dependencia.unidadAdministradora.organismo');
        $this->cargarDatosEspecificos($bien);
        
        $dependencias = Dependencia::with('unidadAdministradora.organismo')
            ->where('id', '!=', $bien->dependencia_id)
            ->orderBy('nombre')
            ->get();

        return view('bienes.transferir', compact('bien', 'dependencias'));
    }

    /**
     * Ejecutar transferencia.
     */
    public function transferir(Request $request, Bien $bien)
    {
        $request->validate([
            'dependencia_id' => [
                'required',
                'exists:dependencias,id',
                'different:' . $bien->dependencia_id
            ],
            'motivo' => 'required|string|max:500',
        ], [
            'dependencia_id.required' => 'Debe seleccionar la dependencia de destino.',
            'dependencia_id.different' => 'La dependencia de destino debe ser diferente a la actual.',
            'motivo.required' => 'El motivo de la transferencia es requerido.',
        ]);

        $dependenciaAnterior = $bien->dependencia;
        $dependenciaNueva = Dependencia::find($request->dependencia_id);

        DB::beginTransaction();
        
        try {
            $bien->update(['dependencia_id' => $request->dependencia_id]);

            // Generar acta de traslado
            $service = new \App\Services\ActaTrasladoService;
            $actaPath = $service->generar(
                bien: $bien,
                motivo: $request->motivo,
                usuario: auth()->user(),
                dependenciaAnterior: $dependenciaAnterior->nombre,
                dependenciaNueva: $dependenciaNueva->nombre
            );

            // Registrar movimiento
            $movimiento = \App\Models\Movimiento::create([
                'bien_id' => $bien->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'TRASLADO',
                'descripcion' => sprintf(
                    "Traslado desde [%s] a [%s]. Motivo: %s",
                    $dependenciaAnterior->nombre,
                    $dependenciaNueva->nombre,
                    $request->motivo
                ),
                'fecha' => now(),
                'acta_path' => $actaPath,
            ]);

            DB::commit();

            Log::info('Bien transferido', [
                'bien_id' => $bien->id,
                'origen' => $dependenciaAnterior->id,
                'destino' => $dependenciaNueva->id,
                'usuario_id' => auth()->id()
            ]);

            return redirect()->route('movimientos.show', $movimiento)
                ->with('success', '✅ Traslado registrado exitosamente. El acta ha sido generada.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al transferir bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Error al transferir: ' . $e->getMessage()]);
        }
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Reglas de validación base.
     */
    private function getBaseValidationRules(): array
    {
        return [
            'dependencia_id' => ['required', 'exists:dependencias,id'],
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^\d{8}$/',
                function ($attribute, $value, $fail) {
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("El código '{$value}' ya está asignado a: {$info['tabla']} ({$info['nombre']}).");
                    }
                },
            ],
            'descripcion' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'fotografia' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
            'estado' => ['required', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['required', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['required', 'date', 'before_or_equal:today', 'after:2000-01-01'],
            'acta_desincorporacion' => ['nullable', 'required_if:estado,DESINCORPORADO', 'file', 'mimes:pdf', 'max:2048'],
            'motivo_desincorporacion' => ['nullable', 'required_if:estado,DESINCORPORADO', 'string', 'max:500'],
        ];
    }

    /**
     * Reglas de validación para actualización.
     */
    private function getUpdateValidationRules(Bien $bien): array
    {
        return [
            'dependencia_id' => ['nullable', 'exists:dependencias,id'],
            'codigo' => [
                'sometimes',
                'string',
                'size:8',
                'regex:/^\d{8}$/',
                function ($attribute, $value, $fail) use ($bien) {
                    if (CodigoUnicoService::codigoExiste($value, 'bienes', $bien->id)) {
                        $info = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail("El código '{$value}' ya está asignado a: {$info['tabla']} ({$info['nombre']}).");
                    }
                },
            ],
            'descripcion' => ['sometimes', 'string', 'max:255'],
            'precio' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'fotografia' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
            'estado' => ['sometimes', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['sometimes', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['sometimes', 'date', 'before_or_equal:today', 'after:2000-01-01'],
        ];
    }

    /**
     * Reglas de validación específicas por tipo de bien.
     */
    private function getSpecificValidationRules(?string $tipo): array
    {
        $tipo = strtoupper($tipo ?? '');

        return match ($tipo) {
            'ELECTRONICO' => [
                'subtipo' => ['nullable', 'string', 'max:50', Rule::in(['MONITOR', 'PC', 'IMPRESORA', 'TELEVISOR', 'LAPTOP', 'TABLET', 'OTRO'])],
                'serial' => ['required', 'string', 'max:255', 'unique:electronicos,serial'],
                'modelo' => ['nullable', 'string', 'max:255'],
                'procesador' => ['nullable', 'string', 'max:255'],
                'memoria' => ['nullable', 'string', 'max:255'],
                'almacenamiento' => ['nullable', 'string', 'max:255'],
                'pantalla' => ['nullable', 'string', 'max:50'],
                'garantia' => ['nullable', 'date', 'after:fecha_registro'],
            ],
            'VEHICULO' => [
                'placa' => ['required', 'string', 'max:20', 'unique:vehiculos,placa'],
                'marca' => ['required', 'string', 'max:100'],
                'modelo' => ['required', 'string', 'max:100'],
                'anio' => ['required', 'string', 'max:10', 'regex:/^\d{4}$/'],
                'motor' => ['nullable', 'string', 'max:100', 'unique:vehiculos,motor'],
                'chasis' => ['nullable', 'string', 'max:100', 'unique:vehiculos,chasis'],
                'combustible' => ['nullable', 'string', 'max:50', Rule::in(['GASOLINA', 'DIESEL', 'ELECTRICO', 'HIBRIDO', 'GNV'])],
                'kilometraje' => ['nullable', 'integer', 'min:0'],
            ],
            'MOBILIARIO' => [
                'material' => ['nullable', 'string', 'max:255'],
                'dimensiones' => ['nullable', 'string', 'max:255'],
                'color' => ['nullable', 'string', 'max:100'],
                'capacidad' => ['nullable', 'string', 'max:100'],
                'cantidad_piezas' => ['nullable', 'integer', 'min:1'],
                'acabado' => ['nullable', 'string', 'max:100'],
            ],
            'OTROS' => [
                'especificaciones' => ['nullable', 'string', 'max:1000'],
                'cantidad' => ['nullable', 'integer', 'min:1'],
                'presentacion' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };
    }

    /**
     * Extrae solo los datos que pertenecen a la tabla bienes.
     */
    private function extractBienData(array $data): array
    {
        $camposExcluir = [
            'acta_desincorporacion', 'motivo_desincorporacion',
            'subtipo', 'serial', 'modelo', 'procesador', 'memoria', 
            'almacenamiento', 'pantalla', 'garantia',
            'placa', 'marca', 'anio', 'motor', 'chasis', 'combustible', 'kilometraje',
            'material', 'dimensiones', 'color', 'capacidad', 'cantidad_piezas', 'acabado',
            'especificaciones', 'cantidad', 'presentacion'
        ];

        return collect($data)->except($camposExcluir)->toArray();
    }

    /**
     * Valida que el código esté dentro del rango permitido por la dependencia.
     */
    private function validarCodigoEnRango(string $codigo, int $dependenciaId): void
    {
        $dependencia = Dependencia::find($dependenciaId);
        
        if (!$dependencia) {
            throw new \RuntimeException('Dependencia no encontrada');
        }

        $codigoNumerico = (int) $codigo;
        $min = (int) $dependencia->code_min;
        $max = (int) $dependencia->code_max;

        // Solo validar si la dependencia tiene rango asignado
        if ($min > 0 && $max > 0) {
            if ($codigoNumerico < $min || $codigoNumerico > $max) {
                throw new \RuntimeException(
                    "El código {$codigo} está fuera del rango permitido para esta dependencia. " .
                    "Rango válido: {$min} - {$max}"
                );
            }
        }
    }
    /**
     * Procesa la fotografía del bien.
     */
    private function procesarFotografia(Request $request, ?Bien $bien = null): ?string
    {
        if (!$request->hasFile('fotografia')) {
            return null;
        }
        
        $file = $request->file('fotografia');
        
        // Verificar hash duplicado
        $uploadedHash = md5_file($file->getRealPath());
        
        $existing = Bien::whereNotNull('fotografia')->get(['id', 'fotografia']);
        foreach ($existing as $ex) {
            try {
                if (Storage::disk('public')->exists($ex->fotografia)) {
                    $path = Storage::disk('public')->path($ex->fotografia);
                    if (file_exists($path) && md5_file($path) === $uploadedHash) {
                        throw new \RuntimeException('La fotografía ya está asociada a otro bien (ID: ' . $ex->id . ').');
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        
        // Eliminar foto anterior si existe
        if ($bien && $bien->fotografia && !str_starts_with($bien->fotografia, 'http')) {
            Storage::disk('public')->delete($bien->fotografia);
        }
        
        $filename = uniqid('bien_') . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('bienes', $filename, 'public');
    }

    /**
     * Carga los datos específicos del tipo de bien.
     */
    private function cargarDatosEspecificos(Bien $bien): void
    {
        $tipo = $bien->tipo_bien?->value;
        
        if (!$tipo) {
            return;
        }
        
        match (strtoupper($tipo)) {
            'ELECTRONICO' => $bien->load('electronico'),
            'VEHICULO' => $bien->load('vehiculo'),
            'MOBILIARIO' => $bien->load('mobiliario'),
            'OTROS' => $bien->load('otro'),
            default => null,
        };
    }

    /**
     * Elimina todos los subtipos asociados a un bien.
     */
    private function eliminarTodosSubtipos(Bien $bien): void
    {
        if ($bien->electronico) {
            $bien->electronico->delete();
        }
        if ($bien->mobiliario) {
            $bien->mobiliario->delete();
        }
        if ($bien->vehiculo) {
            $bien->vehiculo->delete();
        }
        if ($bien->otro) {
            $bien->otro->delete();
        }
    }

    /**
     * Aplica filtros a la consulta de reporte.
     */
    private function aplicarFiltrosReporte($query, array $filtros): void
    {
        if (!empty($filtros['search'])) {
            $query->search($filtros['search']);
        }
        
        if (!empty($filtros['estado'])) {
            $estados = is_array($filtros['estado']) ? $filtros['estado'] : [$filtros['estado']];
            $query->whereIn('estado', $estados);
        }
        
        if (!empty($filtros['dependencias'])) {
            $deps = is_array($filtros['dependencias']) ? $filtros['dependencias'] : [$filtros['dependencias']];
            $query->whereIn('dependencia_id', $deps);
        }
        
        if (!empty($filtros['tipo_bien'])) {
            $query->where('tipo_bien', $filtros['tipo_bien']);
        }
        
        if (!empty($filtros['organismo_id'])) {
            $query->whereHas('dependencia.unidadAdministradora', function ($q) use ($filtros) {
                $q->where('organismo_id', $filtros['organismo_id']);
            });
        }
        
        if (!empty($filtros['unidad_id'])) {
            $query->whereHas('dependencia', function ($q) use ($filtros) {
                $q->where('unidad_administradora_id', $filtros['unidad_id']);
            });
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $filtros['fecha_desde']);
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $filtros['fecha_hasta']);
        }
    }

    /**
     * Genera el reporte según el tipo.
     */
    private function generarReportePorTipo($reporteService, $bienes, string $tipoReporte, array $filtros)
    {
        $titulo = 'REPORTE DE BIENES E INVENTARIO';
        $fecha = now()->format('d/m/Y H:i');
        $nombreArchivo = 'reporte_bienes_' . now()->format('dmY_His') . '.pdf';
        
        switch ($tipoReporte) {
            case 'dependencia':
                return $reporteService->generarPorDependencia(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR DEPENDENCIA',
                    'Listado de bienes agrupados por dependencia',
                    $fecha,
                    $bienes
                );
            case 'unidad':
                return $reporteService->generarPorUnidad(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR UNIDAD ADMINISTRADORA',
                    'Listado de bienes agrupados por unidad administradora',
                    $fecha,
                    $bienes
                );
            case 'organismo':
                return $reporteService->generarPorOrganismo(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR ORGANISMO',
                    'Listado de bienes agrupados por organismo',
                    $fecha,
                    $bienes
                );
            case 'tipo_bien':
                return $reporteService->generarPorTipo(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR TIPO',
                    'Listado de bienes agrupados por tipo de bien',
                    $fecha,
                    $bienes
                );
            case 'estado':
                return $reporteService->generarPorEstado(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR ESTADO',
                    'Listado de bienes agrupados por estado',
                    $fecha,
                    $bienes
                );
            case 'fecha':
                $rango = ($filtros['fecha_desde'] ?? 'Inicio') . ' - ' . ($filtros['fecha_hasta'] ?? 'Fin');
                return $reporteService->generarPorFecha(
                    $nombreArchivo,
                    'REPORTE DE BIENES POR RANGO DE FECHA',
                    'Listado de bienes en rango de fecha: ' . $rango,
                    $fecha,
                    $bienes
                );
            default:
                return $reporteService->downloadBienesListado(
                    $nombreArchivo,
                    'REPORTE GENERAL DE BIENES',
                    'Listado general de bienes institucionales',
                    $fecha,
                    $bienes
                );
        }
    }
}