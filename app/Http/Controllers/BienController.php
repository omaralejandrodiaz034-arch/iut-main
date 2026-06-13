<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Services\BienTypeService;
use App\Services\CodigoJerarquicoService;
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

            'organismo_id' => ['nullable', 'array'],
            'organismo_id.*' => ['integer', 'exists:organismos,id'],

            'unidad_id' => ['nullable', 'array'],
            'unidad_id.*' => ['integer', 'exists:unidades_administradoras,id'],

            'dependencias' => ['nullable', 'array'],
            'dependencias.*' => ['integer', 'exists:dependencias,id'],

            'estado' => ['nullable', 'array'],
            'estado.*' => ['string', Rule::in(array_map(fn ($e) => $e->value, EstadoBien::cases()))],

            'tipo_bien' => ['nullable', 'array'],
            'tipo_bien.*' => ['string', Rule::in(array_map(fn ($t) => $t->value, TipoBien::cases()))],

            'fecha_desde' => ['nullable', 'date', 'before_or_equal:today'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde', 'before_or_equal:today'],

            'precio_desde' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'precio_hasta' => ['nullable', 'numeric', 'min:0', 'max:999999999.99', 'gte:precio_desde'],

            'sort' => ['nullable', 'string', Rule::in(['codigo', 'descripcion', 'precio', 'fecha_registro', 'estado'])],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],

            'solo_desincorporados' => ['nullable', 'boolean'],
            'descripcion' => ['nullable', 'string', 'max:255'],

        ], [
            // Mensajes personalizados
            'organismo_id.*.exists' => 'Uno o más organismos seleccionados no son válidos.',
            'unidad_id.*.exists' => 'Una o más unidades seleccionadas no son válidas.',
            'dependencias.*.exists' => 'Una o más dependencias seleccionadas no son válidas.',
            'estado.*.in' => 'Uno o más estados seleccionados no son válidos.',
            'tipo_bien.*.in' => 'Uno o más tipos de bien seleccionados no son válidos.',
            'fecha_hasta.after_or_equal' => 'La fecha "hasta" debe ser igual o posterior a la fecha "desde".',
            'fecha_hasta.before_or_equal' => 'La fecha "hasta" no puede ser futura.',
            'fecha_desde.before_or_equal' => 'La fecha "desde" no puede ser futura.',
            'precio_hasta.gte' => 'El precio "hasta" debe ser mayor o igual al precio "desde".',
            'precio_desde.min' => 'El precio "desde" no puede ser negativo.',
            'precio_hasta.min' => 'El precio "hasta" no puede ser negativo.',
        ]);

        // 2. Construcción de la consulta con relaciones
        $query = Bien::with([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
        ]);

        // 🔎 Filtros dinámicos
        if (! empty($validated['search'])) {
            $query->search($validated['search']);
        }

        // Búsqueda específica en descripción
        if (! empty($validated['descripcion'])) {
            $query->where('descripcion', 'like', '%'.$validated['descripcion'].'%');
        }

        // Múltiples estados
        if (! empty($validated['estado']) && is_array($validated['estado'])) {
            $query->whereIn('estado', $validated['estado']);
        }

        // Múltiples tipos de bien
        if (! empty($validated['tipo_bien']) && is_array($validated['tipo_bien'])) {
            $query->whereIn('tipo_bien', $validated['tipo_bien']);
        }

        // Rango de precios
        if (isset($validated['precio_desde']) && $validated['precio_desde'] !== '') {
            $query->where('precio', '>=', (float) $validated['precio_desde']);
        }

        if (isset($validated['precio_hasta']) && $validated['precio_hasta'] !== '') {
            $query->where('precio', '<=', (float) $validated['precio_hasta']);
        }

        // Filtrado por Fechas
        $query->when($validated['fecha_desde'] ?? null, fn ($q, $f) => $q->whereDate('fecha_registro', '>=', $f))
            ->when($validated['fecha_hasta'] ?? null, fn ($q, $f) => $q->whereDate('fecha_registro', '<=', $f));

        // Filtrado por Relaciones (Jerarquía) - AHORA SOPORTANDO MÚLTIPLES VALORES
        if (! empty($validated['dependencias']) && is_array($validated['dependencias'])) {
            // Filtro directo por dependencias
            $query->whereIn('dependencia_id', $validated['dependencias']);
        } elseif (! empty($validated['unidad_id']) && is_array($validated['unidad_id'])) {
            // Múltiples unidades administradoras
            $query->whereHas('dependencia', function ($q) use ($validated) {
                $q->whereIn('unidad_administradora_id', $validated['unidad_id']);
            });
        } elseif (! empty($validated['organismo_id']) && is_array($validated['organismo_id'])) {
            // Múltiples organismos
            $query->whereHas('dependencia.unidadAdministradora', function ($q) use ($validated) {
                $q->whereIn('organismo_id', $validated['organismo_id']);
            });
        }

        // Filtrado de Desincorporados
        $soloDesincorporados = filter_var($validated['solo_desincorporados'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $filtroEstadoExplicito = ! empty($validated['estado']) && in_array('DESINCORPORADO', $validated['estado']);

        if ($soloDesincorporados) {
            $query->where('estado', 'DESINCORPORADO');
        } elseif (! $filtroEstadoExplicito && empty($validated['estado'])) {
            // Solo excluir desincorporados si no hay filtro de estado activo
            $query->where('estado', '!=', 'DESINCORPORADO');
        }

        // ⚡️ Ordenamiento y Paginación
        $sort = $validated['sort'] ?? 'fecha_registro';
        $direction = $validated['direction'] ?? 'desc';

        $bienes = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->appends($request->only([
                'search', 'descripcion', 'organismo_id', 'unidad_id', 'dependencias',
                'estado', 'tipo_bien', 'fecha_desde', 'fecha_hasta',
                'precio_desde', 'precio_hasta', 'sort', 'direction', 'solo_desincorporados',
            ]));

        // 3. Respuesta AJAX
        if ($request->ajax()) {
            return view('bienes.partials.table', compact('bienes'))->render();
        }

        // 4. Carga de datos para selectores (AHORA SOPORTANDO MÚLTIPLES VALORES)
        $organismos = Organismo::orderBy('nombre')->get();

        // Unidades: si hay organismos seleccionados, filtrar por ellos
        $unidadesQuery = UnidadAdministradora::query();
        if (! empty($validated['organismo_id']) && is_array($validated['organismo_id'])) {
            $unidadesQuery->whereIn('organismo_id', $validated['organismo_id']);
        }
        $unidades = $unidadesQuery->orderBy('nombre')->get();

        // Dependencias: filtrar por unidades o organismos seleccionados
        $dependenciasQuery = Dependencia::query()->with('unidadAdministradora');

        if (! empty($validated['unidad_id']) && is_array($validated['unidad_id'])) {
            // Filtrar por unidades seleccionadas
            $dependenciasQuery->whereIn('unidad_administradora_id', $validated['unidad_id']);
        } elseif (! empty($validated['organismo_id']) && is_array($validated['organismo_id'])) {
            // Filtrar por organismos seleccionados (a través de unidades)
            $dependenciasQuery->whereHas('unidadAdministradora', function ($q) use ($validated) {
                $q->whereIn('organismo_id', $validated['organismo_id']);
            });
        }

        $dependencias = $dependenciasQuery->orderBy('nombre')->get();

        $estados = collect(EstadoBien::cases())->mapWithKeys(
            fn (EstadoBien $estado) => [$estado->value => $estado->label()]
        );

        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
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
        $dependencias = Dependencia::with(['responsable', 'unidadAdministradora.organismo'])->get();

        // Formatear códigos legibles para mostrar en el select
        foreach ($dependencias as $dependencia) {
            $dependencia->codigo_legible = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);
            $dependencia->jerarquia = $this->obtenerJerarquiaTexto($dependencia);
        }

        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        return view('bienes.create', compact('dependencias', 'tiposBien'));
    }

    /**
     * Obtener el siguiente código recomendado para una dependencia específica.
     */

    /**
     * Guardar un nuevo bien.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo_secuencial' => ['required', 'string', 'regex:/^\d{4}$/'],
        ]);

        $dependenciaId = $request->input('dependencia_id');
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $prefijoDependencia = substr($dependencia->codigo, 0, CodigoJerarquicoService::LONG_PREFIJO_BIEN);

        $secuencial = str_pad($request->input('codigo_secuencial'), CodigoJerarquicoService::LONG_BIEN, '0', STR_PAD_LEFT);
        $codigoCompleto = $prefijoDependencia.$secuencial;

        $request->merge([
            'codigo' => $codigoCompleto,
            'dependencia_id' => $dependenciaId,
        ]);

        $rules = $this->getBaseValidationRules();

        $tipo = $request->input('tipo_bien');
        $rules = array_merge($rules, $this->getSpecificValidationRules($tipo));

        $validated = $request->validate($rules);

        $this->validarCodigoEnRango($validated['codigo'], $validated['dependencia_id']);

        DB::beginTransaction();

        try {
            if ($request->hasFile('fotografia')) {
                $validated['fotografia'] = $this->procesarFotografia($request);
            }

            $datosBien = $this->extractBienData($validated);

            $bien = Bien::create($datosBien);

            if ($tipo) {
                $this->bienTypeService->sync($bien, $tipo, $validated);
            }

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
                'usuario_id' => auth()->id(),
            ]);

            $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($bien->codigo);

            $mensaje = sprintf(
                '✅ Bien "%s" (Código: %s) ha sido registrado exitosamente.',
                $bien->descripcion,
                $codigoLegible
            );

            return redirect()->route('bienes.index')->with('success', $mensaje);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Error de BD al guardar bien', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withErrors([
                'error' => 'Error al guardar el bien. Por favor, verifique los datos e intente nuevamente.',
            ])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al guardar bien', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado: '.$e->getMessage(),
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
            'movimientos' => fn ($q) => $q->orderByDesc('fecha')->orderByDesc('created_at'),
        ]);

        // Formatear código legible
        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($bien->codigo);

        // Obtener jerarquía completa
        $jerarquia = $this->obtenerJerarquiaCompleta($bien->dependencia);

        // Cargar datos específicos del tipo
        $this->cargarDatosEspecificos($bien);

        return view('bienes.show', compact('bien', 'codigoLegible', 'jerarquia'));
    }

    /**
     * Exportar detalle a PDF.
     */
    public function exportPdf(Bien $bien)
    {
        $bien->loadMissing([
            'dependencia.responsable',
            'dependencia.unidadAdministradora.organismo',
            'movimientos.usuario',
        ]);

        $this->cargarDatosEspecificos($bien);

        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($bien->codigo);
        $movimientos = $bien->movimientos()->orderByDesc('fecha')->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('bienes.pdf', [
            'bien' => $bien,
            'codigoLegible' => $codigoLegible,
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
            'dependencia.unidadAdministradora.organismo',
        ]);

        $this->cargarDatosEspecificos($bien);

        $dependencias = Dependencia::with('responsable')->get();

        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        $estados = collect(EstadoBien::cases())->mapWithKeys(
            fn (EstadoBien $estado) => [$estado->value => $estado->label()]
        );

        $subtipoData = $this->obtenerDatosSubtipo($bien);

        return view('bienes.edit', compact('bien', 'dependencias', 'tiposBien', 'estados', 'subtipoData'));
    }

    /**
     * Actualizar un bien.
     */
    public function update(Request $request, Bien $bien)
    {
        $request->merge([
            'codigo' => $request->input('codigo', $bien->codigo),
        ]);

        $rules = $this->getUpdateValidationRules($bien);

        $tipo = $request->input('tipo_bien', $bien->tipo_bien?->value);
        $rules = array_merge($rules, $this->getSpecificValidationRules($tipo));

        $validated = $request->validate($rules);

        $dependenciaId = $validated['dependencia_id'] ?? $bien->dependencia_id;
        $codigoAVerificar = $validated['codigo'] ?? $bien->codigo;

        if (($validated['codigo'] ?? null) !== null || ($validated['dependencia_id'] ?? null) !== null) {
            $this->validarCodigoEnRango($codigoAVerificar, $dependenciaId);
        }

        DB::beginTransaction();

        try {
            if ($request->hasFile('fotografia')) {
                $validated['fotografia'] = $this->procesarFotografia($request, $bien);
            }

            $datosBien = $this->extractBienData($validated);

            $tipoAnterior = $bien->tipo_bien?->value;
            if ($tipo && $tipoAnterior && strtoupper($tipo) !== strtoupper($tipoAnterior)) {
                $this->eliminarSubtipoAnterior($bien, $tipoAnterior);
            }

            $bien->update($datosBien);

            if ($tipo) {
                $this->bienTypeService->sync($bien, $tipo, $validated);
            }

            DB::commit();

            Log::info('Bien actualizado', [
                'bien_id' => $bien->id,
                'usuario_id' => auth()->id(),
            ]);

            return redirect()->route('bienes.index')->with('success', '✅ Bien actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al actualizar: '.$e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar formulario de desincorporación.
     */
    public function showDesincorporarForm(Bien $bien)
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Solo los administradores pueden desincorporar bienes.');

        $this->cargarDatosEspecificos($bien);

        return view('bienes.desincorporar', compact('bien'));
    }

    /**
     * Mostrar formulario de reincorporación.
     */
    public function showReincorporarForm(Bien $bien)
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Solo los administradores pueden reincorporar bienes.');

        // Solo permitir reincorporar si el bien está desincorporado
        abort_unless($bien->estado === EstadoBien::DESINCORPORADO, 400, 'El bien no está en estado desincorporado.');

        $this->cargarDatosEspecificos($bien);

        return view('bienes.reincorporar', compact('bien'));
    }

    /**
     * Procesar desincorporación.
     */
    public function desincorporar(Request $request, Bien $bien)
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Solo los administradores pueden desincorporar bienes.');
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

            // Marcar el bien como desincorporado sin eliminarlo
            $bien->estado = EstadoBien::DESINCORPORADO;
            $bien->save();

            DB::commit();

            Log::info('Bien desincorporado', [
                'bien_id' => $bien->id,
                'codigo' => $bien->codigo,
                'usuario_id' => auth()->id(),
            ]);

            return redirect()->route('bienes.index')->with(
                'success',
                '✅ Bien desincorporado exitosamente. El acta ha sido generada y guardada.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al desincorporar bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al desincorporar: '.$e->getMessage()]);
        }
    }

    /**
     * Procesar reincorporación.
     */
    public function reincorporar(Request $request, Bien $bien)
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Solo los administradores pueden reincorporar bienes.');

        $validated = $request->validate([
            'motivo' => 'required|string|min:10|max:2000',
            'acta_reincorporacion' => ['nullable', 'file', 'mimes:pdf', 'max:4096'],
        ]);

        DB::beginTransaction();

        try {
            // Guardar acta si se sube
            $actaPath = null;
            if ($request->hasFile('acta_reincorporacion')) {
                $actaPath = $request->file('acta_reincorporacion')->store('actas_reincorporacion', 'public');
            }

            // Cambiar estado del bien a ACTIVO (reincorporado)
            $bien->estado = EstadoBien::ACTIVO;
            $bien->save();

            // Registrar movimiento
            \App\Models\Movimiento::create([
                'bien_id' => $bien->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'REINCORPORACION',
                'observaciones' => $request->motivo,
                'fecha' => now(),
                'acta_path' => $actaPath,
            ]);

            DB::commit();

            Log::info('Bien reincorporado', [
                'bien_id' => $bien->id,
                'codigo' => $bien->codigo,
                'usuario_id' => auth()->id(),
            ]);

            return redirect()->route('bienes.show', $bien)->with('success', '✅ Bien reincorporado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al reincorporar bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al reincorporar: '.$e->getMessage()]);
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
            ->map(fn ($b) => (object) [
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
    /**
     * Generar reporte en PDF.
     */
    /**
     * Generar reporte en PDF.
     */
    /**
     * Generar reporte en PDF.
     */
    public function generarReporte(Request $request)
    {
        try {
            \Log::info('=== INICIO GENERAR REPORTE ===');
            \Log::info('URL completa: '.$request->fullUrl());
            \Log::info('Todos los parámetros recibidos:', $request->all());
            \Log::info('unidad_id raw: '.print_r($request->input('unidad_id'), true));
            \Log::info('unidad_id type: '.gettype($request->input('unidad_id')));

            // Validación flexible
            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:255'],
                'dependencias' => ['nullable', 'array'],
                'dependencias.*' => ['integer', 'exists:dependencias,id'],
                'estado' => ['nullable', 'array'],
                'estado.*' => ['string', Rule::in(array_map(fn ($e) => $e->value, EstadoBien::cases()))],
                'tipo_bien' => ['nullable', 'array'],
                'tipo_bien.*' => ['string', Rule::in(array_map(fn ($t) => $t->value, TipoBien::cases()))],
                'organismo_id' => ['nullable', 'array'],
                'organismo_id.*' => ['integer', 'exists:organismos,id'],
                'unidad_id' => ['nullable', 'array'],
                'unidad_id.*' => ['integer', 'exists:unidades_administradoras,id'],
                'fecha_desde' => ['nullable', 'date'],
                'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
                'precio_desde' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
                'precio_hasta' => ['nullable', 'numeric', 'min:0', 'max:999999999.99', 'gte:precio_desde'],
                'solo_desincorporados' => ['nullable', 'boolean'],
            ]);

            \Log::info('Validación pasada. Datos validados:', $validated);

            // Validación manual de unidad_id
            if (isset($validated['unidad_id']) && ! empty($validated['unidad_id'])) {
                $unidadIds = is_array($validated['unidad_id']) ? $validated['unidad_id'] : [$validated['unidad_id']];
                \Log::info('Unidades a procesar: ', $unidadIds);

                foreach ($unidadIds as $id) {
                    if (! UnidadAdministradora::where('id', $id)->exists()) {
                        \Log::error("Unidad con ID {$id} no existe");

                        return back()->with('error', "La unidad con ID {$id} no existe");
                    }
                }
            }

            $query = Bien::with([
                'dependencia.responsable',
                'dependencia.unidadAdministradora.organismo',
            ]);

            \Log::info('Aplicando filtros...');
            $this->aplicarFiltrosReporteFinal($query, $validated);

            \Log::info('Ejecutando consulta...');
            $bienes = $query->get();

            \Log::info('Cantidad de bienes encontrados: '.$bienes->count());

            if ($bienes->isEmpty()) {
                \Log::warning('No se encontraron bienes con los filtros aplicados');

                return back()->with('warning', 'No hay bienes que coincidan con los filtros seleccionados.');
            }

            $reporteService = new \App\Services\FpdfReportService;

            $tipoReporte = $this->determinarTipoReporteFinal($validated);
            \Log::info('Tipo de reporte seleccionado: '.$tipoReporte);

            $resultado = $this->generarReportePorTipo($reporteService, $bienes, $tipoReporte, $validated);
            \Log::info('Reporte generado exitosamente');

            return $resultado;

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación: ', $e->errors());

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error CRÍTICO en reporte: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Error al generar reporte: '.$e->getMessage());
        }
    }

    public function generarPorUnidad(string $fileName, string $title, ?string $subtitle, string $generatedAt, iterable $bienes)
    {
        \Log::info('=== INICIO generarPorUnidad ===');
        \Log::info('Título: '.$title);
        \Log::info('Cantidad de bienes: '.(is_countable($bienes) ? count($bienes) : 'iterable'));

        try {
            $pdf = $this->make('L');
            \Log::info('PDF creado correctamente');

            // Agrupar bienes por dependencia
            $agrupados = [];
            $bienesArray = $bienes instanceof \Illuminate\Support\Collection ? $bienes->all() : iterator_to_array($bienes);

            foreach ($bienesArray as $bien) {
                $uniNombre = optional(optional($bien->dependencia)->unidadAdministradora)->nombre ?? 'Sin Unidad';
                if (! isset($agrupados[$uniNombre])) {
                    $agrupados[$uniNombre] = [];
                }
                $agrupados[$uniNombre][] = $bien;
            }

            \Log::info('Unidades agrupadas: '.count($agrupados));

            $this->renderHeader($pdf, $title, $subtitle, $generatedAt, []);
            \Log::info('Header renderizado');

            // ... resto del código ...

            $output = $pdf->Output('S');
            \Log::info('PDF generado, tamaño: '.strlen($output).' bytes');

            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en generarPorUnidad: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Aplica filtros a la consulta de reporte (VERSIÓN FINAL - SOPORTA ARRAYS)
     */
    private function aplicarFiltrosReporteFinal($query, array $filtros): void
    {
        // Búsqueda
        if (! empty($filtros['search'])) {
            $query->search($filtros['search']);
        }

        // Estado (array)
        if (! empty($filtros['estado']) && is_array($filtros['estado'])) {
            $estadosValidos = array_filter($filtros['estado']);
            if (! empty($estadosValidos)) {
                $query->whereIn('estado', $estadosValidos);
            }
        } elseif (! empty($filtros['solo_desincorporados'])) {
            $query->where('estado', 'DESINCORPORADO');
        } elseif (empty($filtros['estado'])) {
            $query->where('estado', '!=', 'DESINCORPORADO');
        }

        // Tipo de bien (soporta array o valor simple)
        if (! empty($filtros['tipo_bien'])) {
            if (is_array($filtros['tipo_bien'])) {
                $tiposValidos = array_filter($filtros['tipo_bien']);
                if (! empty($tiposValidos)) {
                    $query->whereIn('tipo_bien', $tiposValidos);
                }
            } else {
                $query->where('tipo_bien', $filtros['tipo_bien']);
            }
        }

        // Jerarquía (mismo orden de prioridad que en index):
        // dependencias > unidad > organismo
        $dependenciasIds = array_values(array_filter((array) ($filtros['dependencias'] ?? [])));
        $unidadesIds = array_values(array_filter((array) ($filtros['unidad_id'] ?? [])));
        $organismosIds = array_values(array_filter((array) ($filtros['organismo_id'] ?? [])));

        if (! empty($dependenciasIds)) {
            $query->whereIn('dependencia_id', $dependenciasIds);
        } elseif (! empty($unidadesIds)) {
            $query->whereHas('dependencia', function ($q) use ($unidadesIds) {
                $q->whereIn('unidad_administradora_id', $unidadesIds);
            });
        } elseif (! empty($organismosIds)) {
            $query->whereHas('dependencia.unidadAdministradora', function ($q) use ($organismosIds) {
                $q->whereIn('organismo_id', $organismosIds);
            });
        }

        // Fechas
        if (! empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $filtros['fecha_desde']);
        }
        if (! empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $filtros['fecha_hasta']);
        }

        // Precio
        if (isset($filtros['precio_desde']) && $filtros['precio_desde'] !== '') {
            $query->where('precio', '>=', (float) $filtros['precio_desde']);
        }
        if (isset($filtros['precio_hasta']) && $filtros['precio_hasta'] !== '') {
            $query->where('precio', '<=', (float) $filtros['precio_hasta']);
        }
    }

    /**
     * Determinar tipo de reporte (VERSIÓN FINAL)
     */
    /**
     * Determinar tipo de reporte (VERSIÓN FINAL)
     */
    private function determinarTipoReporteFinal(array $filtros): string
    {
        // 🔥 Si hay filtro de unidad (incluso múltiples), agrupar por unidad
        if (! empty($filtros['unidad_id'])) {
            return 'unidad';
        }

        // Si hay filtro de organismo
        if (! empty($filtros['organismo_id'])) {
            return 'organismo';
        }

        // Múltiples dependencias
        if (! empty($filtros['dependencias']) && count($filtros['dependencias']) > 1) {
            return 'dependencia';
        }

        // Múltiples estados
        if (! empty($filtros['estado']) && count($filtros['estado']) > 1) {
            return 'estado';
        }

        // Rango de fechas
        if (! empty($filtros['fecha_desde']) || ! empty($filtros['fecha_hasta'])) {
            return 'fecha';
        }

        // Tipo de bien específico
        if (! empty($filtros['tipo_bien'])) {
            return 'tipo_bien';
        }

        return 'default';
    }

    /**
     * Nueva versión específica para aplicar filtros de unidad
     */
    private function aplicarFiltrosReporteCorregido($query, array $filtros): void
    {
        // Búsqueda
        if (! empty($filtros['search'])) {
            $query->search($filtros['search']);
        }

        // Estado (array)
        if (! empty($filtros['estado']) && is_array($filtros['estado'])) {
            $estadosValidos = array_filter($filtros['estado']);
            if (! empty($estadosValidos)) {
                $query->whereIn('estado', $estadosValidos);
            }
        } elseif (! empty($filtros['solo_desincorporados'])) {
            $query->where('estado', 'DESINCORPORADO');
        } elseif (empty($filtros['estado'])) {
            $query->where('estado', '!=', 'DESINCORPORADO');
        }

        // Tipo de bien (string simple)
        if (! empty($filtros['tipo_bien'])) {
            $query->where('tipo_bien', $filtros['tipo_bien']);
        }

        // 🔥 CORRECCIÓN: Unidad Administradora (como string o integer)
        if (! empty($filtros['unidad_id'])) {
            $query->whereHas('dependencia', function ($q) use ($filtros) {
                $q->where('unidad_administradora_id', $filtros['unidad_id']);
            });
        }
        // Organismo (string simple)
        elseif (! empty($filtros['organismo_id'])) {
            $query->whereHas('dependencia.unidadAdministradora', function ($q) use ($filtros) {
                $q->where('organismo_id', $filtros['organismo_id']);
            });
        }
        // Dependencias (array)
        elseif (! empty($filtros['dependencias']) && is_array($filtros['dependencias'])) {
            $depsValidas = array_filter($filtros['dependencias']);
            if (! empty($depsValidas)) {
                $query->whereIn('dependencia_id', $depsValidas);
            }
        }

        // Fechas
        if (! empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $filtros['fecha_desde']);
        }
        if (! empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $filtros['fecha_hasta']);
        }
    }

    private function obtenerJerarquiaTexto(Dependencia $dependencia): string
    {
        $organismo = $dependencia->unidadAdministradora->organismo;
        $unidad = $dependencia->unidadAdministradora;

        $orgCode = CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo);
        $uniCode = CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo);
        $depCode = CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo);

        return sprintf(
            '%s - %s > %s - %s > %s - %s',
            $orgCode, $organismo->nombre,
            $uniCode, $unidad->nombre,
            $depCode, $dependencia->nombre
        );
    }

    /**
     * Determinar tipo de reporte con unidad
     */
    private function determinarTipoReporteUnidad(array $filtros): string
    {
        // 🔥 Si hay filtro de unidad, generar reporte agrupado por unidad
        if (! empty($filtros['unidad_id'])) {
            return 'unidad';
        }

        // Si hay filtro de organismo
        if (! empty($filtros['organismo_id'])) {
            return 'organismo';
        }

        // Múltiples dependencias
        if (! empty($filtros['dependencias']) && count($filtros['dependencias']) > 1) {
            return 'dependencia';
        }

        // Múltiples estados
        if (! empty($filtros['estado']) && count($filtros['estado']) > 1) {
            return 'estado';
        }

        // Rango de fechas
        if (! empty($filtros['fecha_desde']) || ! empty($filtros['fecha_hasta'])) {
            return 'fecha';
        }

        // Tipo de bien específico
        if (! empty($filtros['tipo_bien'])) {
            return 'tipo_bien';
        }

        return 'default';
    }

    /**
     * Determina el tipo de reporte basado en los filtros aplicados
     */
    /**
     * Determina el tipo de reporte basado en los filtros aplicados
     */
    /**
     * Determina el tipo de reporte basado en los filtros aplicados
     */
    /**
     * Determina el tipo de reporte basado en los filtros (CORREGIDO)
     */
    private function determinarTipoReporte(array $filtros): string
    {
        // Limpiar filtros vacíos
        $filtrosLimpios = array_filter($filtros, function ($value) {
            if (is_array($value)) {
                return ! empty(array_filter($value));
            }

            return ! empty($value);
        });

        // Sin filtros = reporte default
        if (empty($filtrosLimpios)) {
            return 'default';
        }

        // Prioridad para reportes agrupados

        // Múltiples unidades
        if (! empty($filtros['unidad_id']) && count(array_filter($filtros['unidad_id'])) > 1) {
            return 'unidad';
        }

        // Múltiples organismos
        if (! empty($filtros['organismo_id']) && count(array_filter($filtros['organismo_id'])) > 1) {
            return 'organismo';
        }

        // Múltiples dependencias
        if (! empty($filtros['dependencias']) && count(array_filter($filtros['dependencias'])) > 1) {
            return 'dependencia';
        }

        // Múltiples estados
        if (! empty($filtros['estado']) && count(array_filter($filtros['estado'])) > 1) {
            return 'estado';
        }

        // Múltiples tipos de bien
        if (! empty($filtros['tipo_bien']) && count(array_filter($filtros['tipo_bien'])) > 1) {
            return 'tipo_bien';
        }

        // Rango de fechas (sin importar si hay otros filtros)
        if (! empty($filtros['fecha_desde']) || ! empty($filtros['fecha_hasta'])) {
            return 'fecha';
        }

        // Si solo hay una dependencia o un estado, reporte filtrado simple
        return 'default';
    }

    /**
     * Verifica si hay filtros aplicados (mejorado para arrays)
     */
    private function sinFiltrosAplicados(array $filtros): bool
    {
        $filtrosRelevantes = array_filter($filtros, function ($valor, $key) {
            // Ignorar parámetros vacíos
            if (is_null($valor) || $valor === '' || $valor === []) {
                return false;
            }

            // Para arrays, verificar que no estén vacíos
            if (is_array($valor)) {
                return ! empty($valor);
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        return empty($filtrosRelevantes);
    }

    private function obtenerJerarquiaCompleta(Dependencia $dependencia): array
    {
        $organismo = $dependencia->unidadAdministradora->organismo;
        $unidad = $dependencia->unidadAdministradora;

        return [
            'organismo' => [
                'codigo' => $organismo->codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo),
                'nombre' => $organismo->nombre,
            ],
            'unidad' => [
                'codigo' => $unidad->codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($unidad->codigo),
                'nombre' => $unidad->nombre,
            ],
            'dependencia' => [
                'codigo' => $dependencia->codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($dependencia->codigo),
                'nombre' => $dependencia->nombre,
            ],
        ];
    }

    /**
     * Aplica filtros a la consulta de reporte (mejorado para arrays)
     */
    /**
     * Aplica filtros a la consulta de reporte (CORREGIDO)
     */
    private function aplicarFiltrosReporte($query, array $filtros): void
    {
        // Búsqueda rápida
        if (! empty($filtros['search'])) {
            $query->search($filtros['search']);
        }

        // 🔥 CORRECCIÓN PRINCIPAL: Estado (manejo de arrays)
        if (! empty($filtros['estado']) && is_array($filtros['estado'])) {
            // Filtrar valores vacíos
            $estadosValidos = array_filter($filtros['estado']);
            if (! empty($estadosValidos)) {
                $query->whereIn('estado', $estadosValidos);
            }
        } elseif (! empty($filtros['solo_desincorporados'])) {
            $query->where('estado', 'DESINCORPORADO');
        } elseif (empty($filtros['estado']) && empty($filtros['solo_desincorporados'])) {
            // Por defecto, excluir desincorporados
            $query->where('estado', '!=', 'DESINCORPORADO');
        }

        // 🔥 CORRECCIÓN: Tipo de bien (ahora soporta múltiples)
        if (! empty($filtros['tipo_bien']) && is_array($filtros['tipo_bien'])) {
            $tiposValidos = array_filter($filtros['tipo_bien']);
            if (! empty($tiposValidos)) {
                $query->whereIn('tipo_bien', $tiposValidos);
            }
        }

        // 🔥 CORRECCIÓN: Dependencias (múltiples)
        if (! empty($filtros['dependencias']) && is_array($filtros['dependencias'])) {
            $depsValidas = array_filter($filtros['dependencias']);
            if (! empty($depsValidas)) {
                $query->whereIn('dependencia_id', $depsValidas);
            }
        }
        // Unidad Administradora (múltiples)
        elseif (! empty($filtros['unidad_id']) && is_array($filtros['unidad_id'])) {
            $unidadesValidas = array_filter($filtros['unidad_id']);
            if (! empty($unidadesValidas)) {
                $query->whereHas('dependencia', function ($q) use ($unidadesValidas) {
                    $q->whereIn('unidad_administradora_id', $unidadesValidas);
                });
            }
        }
        // Organismo (múltiples)
        elseif (! empty($filtros['organismo_id']) && is_array($filtros['organismo_id'])) {
            $organismosValidos = array_filter($filtros['organismo_id']);
            if (! empty($organismosValidos)) {
                $query->whereHas('dependencia.unidadAdministradora', function ($q) use ($organismosValidos) {
                    $q->whereIn('organismo_id', $organismosValidos);
                });
            }
        }

        // Fechas
        if (! empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_registro', '>=', $filtros['fecha_desde']);
        }

        if (! empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_registro', '<=', $filtros['fecha_hasta']);
        }
    }

    /**
     * Obtiene los datos del subtipo del bien
     */ /**
 * Elimina el subtipo anterior cuando cambia el tipo de bien
 */
    private function eliminarSubtipoAnterior(Bien $bien, string $tipoAnterior): void
    {
        switch (strtoupper($tipoAnterior)) {
            case 'ELECTRONICO':
                if ($bien->electronico) {
                    $bien->electronico->delete();
                }
                break;
            case 'VEHICULO':
                if ($bien->vehiculo) {
                    $bien->vehiculo->delete();
                }
                break;
            case 'MOBILIARIO':
                if ($bien->mobiliario) {
                    $bien->mobiliario->delete();
                }
                break;
            case 'OTROS':
                if ($bien->otro) {
                    $bien->otro->delete();
                }
                break;
        }
    }

    private function obtenerDatosSubtipo(Bien $bien): array
    {
        $subtipoData = [];
        $tipo = $bien->tipo_bien?->value;

        if ($tipo) {
            switch (strtoupper($tipo)) {
                case 'ELECTRONICO':
                    if ($bien->electronico) {
                        $subtipoData = $bien->electronico->toArray();
                    }
                    break;
                case 'VEHICULO':
                    if ($bien->vehiculo) {
                        $subtipoData = $bien->vehiculo->toArray();
                    }
                    break;
                case 'MOBILIARIO':
                    if ($bien->mobiliario) {
                        $subtipoData = $bien->mobiliario->toArray();
                    }
                    break;
                case 'OTROS':
                    if ($bien->otro) {
                        $subtipoData = $bien->otro->toArray();
                    }
                    break;
            }
        }

        return $subtipoData;
    }

    /**
     * Métodos auxiliares para mejorar el código
     */
    private function getEstadosBienes(): array
    {
        // Si usas PHP 8.1+ con Backed Enums
        if (method_exists(EstadoBien::class, 'cases')) {
            return array_map(fn ($e) => $e->value, EstadoBien::cases());
        }

        // Si usas enum tradicional o constantes
        return [
            EstadoBien::ACTIVO,
            EstadoBien::MANTENIMIENTO,
            EstadoBien::BAJA,
            // ... otros estados
        ];
    }

    private function getTiposBienes(): array
    {
        if (method_exists(TipoBien::class, 'cases')) {
            return array_map(fn ($t) => $t->value, TipoBien::cases());
        }

        return [
            TipoBien::MUEBLE,
            TipoBien::INMUEBLE,
            TipoBien::VEHICULO,
            // ... otros tipos
        ];
    }

    private function esTipoReporteValido($tipoReporte): bool
    {
        $tiposValidos = ['default', 'dependencia', 'unidad', 'organismo', 'tipo_bien', 'estado', 'fecha'];

        return in_array($tipoReporte, $tiposValidos);
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
                'different:'.$bien->dependencia_id,
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
                    'Traslado desde [%s] a [%s]. Motivo: %s',
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
                'usuario_id' => auth()->id(),
            ]);

            return redirect()->route('movimientos.show', $movimiento)
                ->with('success', '✅ Traslado registrado exitosamente. El acta ha sido generada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al transferir bien', [
                'bien_id' => $bien->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al transferir: '.$e->getMessage()]);
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
                'size:'.CodigoJerarquicoService::TOTAL_BIEN, // 10 dígitos
                'regex:/^[0-9]+$/', // Solo números, sin guiones en BD
                function ($attribute, $value, $fail) {
                    if (CodigoJerarquicoService::codigoExiste($value)) {
                        $fail("El código '{$value}' ya está asignado a otro bien.");
                    }
                },
                // Nota: el sistema ahora usa códigos planos de longitud fija (ver CodigoJerarquicoService::TOTAL_BIEN)
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
            'dependencia_id' => ['required', 'exists:dependencias,id'],
            'codigo' => [
                'sometimes',
                'string',
                'size:'.CodigoJerarquicoService::TOTAL_BIEN,
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($bien) {
                    if ($value !== $bien->codigo && CodigoJerarquicoService::codigoExiste($value)) {
                        $fail("El código '{$value}' ya está asignado a otro bien.");
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
                'serial' => ['required', 'string', 'max:255', 'unique:bienes_electronicos,serial'],
                'modelo' => ['nullable', 'string', 'max:255'],
                'procesador' => ['nullable', 'string', 'max:255'],
                'memoria' => ['nullable', 'string', 'max:255'],
                'almacenamiento' => ['nullable', 'string', 'max:255'],
                'pantalla' => ['nullable', 'string', 'max:50'],
                'garantia' => ['nullable', 'date', 'after:fecha_registro'],
            ],
            'VEHICULO' => [
                'placa' => ['required', 'string', 'max:20', 'unique:bienes_vehiculos,placa'],
                'marca' => ['required', 'string', 'max:100'],
                'modelo' => ['required', 'string', 'max:100'],
                'anio' => ['required', 'string', 'max:10', 'regex:/^\d{4}$/'],
                'motor' => ['nullable', 'string', 'max:100', 'unique:bienes_vehiculos,motor'],
                'chasis' => ['nullable', 'string', 'max:100', 'unique:bienes_vehiculos,chasis'],
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
            'especificaciones', 'cantidad', 'presentacion',
        ];

        return collect($data)->except($camposExcluir)->toArray();
    }

    /**
     * Valida que el código esté dentro del rango permitido por la dependencia.
     */
    private function validarCodigoEnRango(string $codigo, int $dependenciaId): void
    {
        $dependencia = Dependencia::find($dependenciaId);

        if (! $dependencia) {
            throw new \RuntimeException('Dependencia no encontrada');
        }

        // Verificar que el código comience con el prefijo de la dependencia (organismo+unidad)
        $prefijoDependencia = substr($dependencia->codigo, 0, CodigoJerarquicoService::LONG_PREFIJO_BIEN);
        if (! str_starts_with($codigo, $prefijoDependencia)) {
            throw new \RuntimeException(
                "El código debe comenzar con el prefijo de la dependencia ({$prefijoDependencia}). ".
                "Código ingresado: {$codigo}"
            );
        }

        // Extraer el secuencial (últimos 4 dígitos)
        $secuencial = (int) substr($codigo, -CodigoJerarquicoService::LONG_BIEN);

        // Validar que el secuencial esté dentro del rango permitido (1 - 9999)
        $maximoBienes = pow(10, CodigoJerarquicoService::LONG_BIEN) - 1;
        if ($secuencial < 1 || $secuencial > $maximoBienes) {
            throw new \RuntimeException(
                "El número secuencial ({$secuencial}) está fuera del rango permitido (1 - {$maximoBienes})."
            );
        }

        // Opcional: Validar contra el code_max si existe en la dependencia
        if ($dependencia->code_max && $secuencial > $dependencia->code_max) {
            throw new \RuntimeException(
                "El número secuencial ({$secuencial}) excede el límite de la dependencia ({$dependencia->code_max})."
            );
        }
    }

    public function recomendarCodigo(Dependencia $dependencia)
    {
        try {
            $resultado = CodigoJerarquicoService::recomendarSiguienteCodigoParaDependencia($dependencia->id);

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
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'rango_exhausto',
                'mensaje' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error inesperado al recomendar código', [
                'dependencia_id' => $dependencia->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'error_general',
                'mensaje' => 'Error al generar el código. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Procesa la fotografía del bien.
     */
    private function procesarFotografia(Request $request, ?Bien $bien = null): ?string
    {
        if (! $request->hasFile('fotografia')) {
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
                        throw new \RuntimeException('La fotografía ya está asociada a otro bien (ID: '.$ex->id.').');
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Eliminar foto anterior si existe
        if ($bien && $bien->fotografia && ! str_starts_with($bien->fotografia, 'http')) {
            Storage::disk('public')->delete($bien->fotografia);
        }

        $filename = uniqid('bien_').'.'.$file->getClientOriginalExtension();

        return $file->storeAs('bienes', $filename, 'public');
    }

    /**
     * Carga los datos específicos del tipo de bien.
     */
    private function cargarDatosEspecificos(Bien $bien): void
    {
        $tipo = $bien->tipo_bien?->value;

        if (! $tipo) {
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

    /**
     * Genera el reporte según el tipo.
     */
    /**
     * Genera el reporte según el tipo.
     */
    /**
     * Genera el reporte según el tipo.
     */
    private function generarReportePorTipo($reporteService, $bienes, string $tipoReporte, array $filtros)
    {
        $titulo = 'REPORTE DE BIENES E INVENTARIO';
        $fecha = now()->format('d/m/Y H:i');
        $nombreArchivo = 'reporte_bienes_'.now()->format('dmY_His').'.pdf';

        // Construir subtítulo descriptivo
        $subtitulo = $this->construirSubtituloReporte($filtros);

        // Datos institucionales para reportes filtrados
        $datosInstitucionales = $this->obtenerDatosInstitucionales($filtros, $bienes);

        switch ($tipoReporte) {
            case 'unidad':
                // Reporte agrupado por unidad administradora
                return $reporteService->generarPorUnidad(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'organismo':
                // Reporte agrupado por organismo
                return $reporteService->generarPorOrganismo(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'dependencia':
                // Reporte agrupado por dependencia
                return $reporteService->generarPorDependencia(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'tipo_bien':
                // Reporte agrupado por tipo de bien
                return $reporteService->generarPorTipo(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'estado':
                // Reporte agrupado por estado
                return $reporteService->generarPorEstado(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'fecha':
                // Reporte por rango de fecha
                return $reporteService->generarPorFecha(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes
                );

            case 'default':
            default:
                // Reporte listado simple (filtrado o general)
                return $reporteService->downloadBienesListado(
                    $nombreArchivo,
                    $titulo,
                    $subtitulo,
                    $fecha,
                    $bienes,
                    $datosInstitucionales
                );
        }
    }

    /**
     * Obtiene los datos institucionales para mostrar en el encabezado del reporte
     */
    /**
     * Obtiene los datos institucionales para mostrar en el encabezado del reporte
     */
    private function obtenerDatosInstitucionales(array $filtros, $bienes): array
    {
        $datos = [];
        $dependenciasIds = array_values(array_filter((array) ($filtros['dependencias'] ?? [])));
        $unidadesIds = array_values(array_filter((array) ($filtros['unidad_id'] ?? [])));
        $organismosIds = array_values(array_filter((array) ($filtros['organismo_id'] ?? [])));

        // Si hay una dependencia específica (solo una)
        if (count($dependenciasIds) === 1) {
            $dependenciaId = $dependenciasIds[0];
            $dependencia = Dependencia::with(['responsable', 'unidadAdministradora.organismo'])->find($dependenciaId);

            if ($dependencia) {
                $datos = [
                    'org_nombre' => $dependencia->unidadAdministradora?->organismo?->nombre ?? '',
                    'uni_nombre' => $dependencia->unidadAdministradora?->nombre ?? '',
                    'dep_nombre' => $dependencia->nombre ?? '',
                    'res_u_nombre' => $dependencia->responsable?->nombre_completo ?? '',
                    'res_u_cedula' => $dependencia->responsable?->cedula ?? '',
                ];
            }
        }
        // Si hay una unidad específica (NO mostrar datos institucionales porque se agrupará)
        elseif (! empty($unidadesIds)) {
            // Para reportes agrupados por unidad, no mostramos datos en el encabezado
            // ya que se mostrarán dentro de cada grupo
            return [];
        }
        // Si hay un organismo específico
        elseif (count($organismosIds) === 1 && empty($unidadesIds)) {
            $organismo = Organismo::find($organismosIds[0]);

            if ($organismo) {
                $datos = [
                    'org_nombre' => $organismo->nombre ?? '',
                    'uni_nombre' => '',
                    'dep_nombre' => '',
                    'res_u_nombre' => '',
                    'res_u_cedula' => '',
                ];
            }
        }

        return $datos;
    }

    /**
     * Construye un subtítulo para el reporte basado en los filtros aplicados.
     */
    /**
     * Construye un subtítulo descriptivo basado en los filtros aplicados
     */
    private function construirSubtituloReporte(array $filtros): string
    {
        $descripciones = [];
        $organismosIds = array_values(array_filter((array) ($filtros['organismo_id'] ?? [])));
        $unidadesIds = array_values(array_filter((array) ($filtros['unidad_id'] ?? [])));
        $dependenciasIds = array_values(array_filter((array) ($filtros['dependencias'] ?? [])));
        $estados = array_values(array_filter((array) ($filtros['estado'] ?? [])));
        $tipos = array_values(array_filter((array) ($filtros['tipo_bien'] ?? [])));

        // Organismo
        if (! empty($organismosIds) && empty($unidadesIds)) {
            if (count($organismosIds) === 1) {
                $organismo = Organismo::find($organismosIds[0]);
                if ($organismo) {
                    $descripciones[] = 'Organismo: '.$organismo->nombre;
                }
            } else {
                $descripciones[] = 'Organismos: '.count($organismosIds).' seleccionados';
            }
        }

        // Unidad Administradora
        if (! empty($unidadesIds)) {
            if (count($unidadesIds) === 1) {
                $unidad = UnidadAdministradora::with('dependencias')->find($unidadesIds[0]);
                if ($unidad) {
                    $cantDependencias = $unidad->dependencias->count();
                    $descripciones[] = 'Unidad: '.$unidad->nombre.' ('.$cantDependencias.' dependencias)';
                }
            } else {
                $descripciones[] = 'Unidades: '.count($unidadesIds).' seleccionadas';
            }
        }

        // Dependencias
        if (! empty($dependenciasIds)) {
            if (count($dependenciasIds) === 1) {
                $dependencia = Dependencia::find($dependenciasIds[0]);
                if ($dependencia) {
                    $descripciones[] = 'Dependencia: '.$dependencia->nombre;
                }
            } else {
                $descripciones[] = 'Dependencias: '.count($dependenciasIds).' seleccionadas';
            }
        }

        // Estados
        if (! empty($estados)) {
            if (count($estados) === 1) {
                $descripciones[] = 'Estado: '.$estados[0];
            } else {
                $descripciones[] = 'Estados: '.count($estados).' seleccionados';
            }
        }

        // Tipo de bien (array)
        if (! empty($tipos)) {
            $tiposBien = collect(TipoBien::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]);
            if (count($tipos) === 1) {
                $tipo = $tipos[0];
                $descripciones[] = 'Tipo: '.($tiposBien[$tipo] ?? $tipo);
            } else {
                $descripciones[] = 'Tipos: '.count($tipos).' seleccionados';
            }
        }

        // Rango de fechas
        if (! empty($filtros['fecha_desde']) || ! empty($filtros['fecha_hasta'])) {
            $desde = $filtros['fecha_desde'] ?? 'inicio';
            $hasta = $filtros['fecha_hasta'] ?? 'actualidad';
            $descripciones[] = "Período: {$desde} al {$hasta}";
        }

        // Búsqueda
        if (! empty($filtros['search'])) {
            $descripciones[] = "Búsqueda: {$filtros['search']}";
        }

        // Solo desincorporados
        if (! empty($filtros['solo_desincorporados'])) {
            $descripciones[] = 'Solo bienes desincorporados';
        }

        if (empty($descripciones)) {
            return 'Listado general de todos los bienes del sistema';
        }

        return 'Filtros aplicados: '.implode(' | ', $descripciones);
    }
}
