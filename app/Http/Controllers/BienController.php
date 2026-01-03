<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    /**
     * Listar todos los bienes.
     */
    // BienController.php
public function index(Request $request)
{
    $validated = $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'organismo_id' => ['nullable', 'integer', 'exists:organismos,id'],
        'unidad_id' => ['nullable', 'integer', 'exists:unidades_administradoras,id'],
        'dependencias' => ['nullable', 'array'],
        'dependencias.*' => ['integer', 'exists:dependencias,id'],
        'estado' => ['nullable', 'array'],
        'estado.*' => ['string', Rule::in(array_map(fn (EstadoBien $estado) => $estado->value, EstadoBien::cases()))],
        'fecha_desde' => ['nullable', 'date'],
        'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        'descripcion' => ['nullable', 'string', 'max:255'],
        'codigo' => ['nullable', 'string', 'max:255', 'regex:/^[0-9\-]+$/'],
        // 🔽 nuevos parámetros de ordenamiento
        'sort' => ['nullable', 'string', Rule::in([
            'codigo', 'descripcion', 'precio', 'fecha_registro', 'estado'
        ])],
        'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
    ]);

    $query = Bien::with([
        'dependencia.responsable',
        'dependencia.unidadAdministradora.organismo',
    ]);

    // 🔎 Filtros
    if (! empty($validated['search'])) {
        $query->search($validated['search']);
    }

    if (! empty($validated['descripcion'])) {
        $query->where('descripcion', 'like', '%'.$validated['descripcion'].'%');
    }

    if (! empty($validated['codigo'])) {
        $query->where('codigo', 'like', '%'.$validated['codigo'].'%');
    }

    if (! empty($validated['estado'])) {
        $query->whereIn('estado', $validated['estado']);
    }

    if (! empty($validated['fecha_desde']) && ! empty($validated['fecha_hasta'])) {
        $query->whereBetween('fecha_registro', [$validated['fecha_desde'], $validated['fecha_hasta']]);
    } elseif (! empty($validated['fecha_desde'])) {
        $query->whereDate('fecha_registro', '>=', $validated['fecha_desde']);
    } elseif (! empty($validated['fecha_hasta'])) {
        $query->whereDate('fecha_registro', '<=', $validated['fecha_hasta']);
    }

    if (! empty($validated['dependencias'])) {
        $query->whereIn('dependencia_id', $validated['dependencias']);
    }

    if (! empty($validated['unidad_id'])) {
        $unidadId = $validated['unidad_id'];
        $query->whereHas('dependencia.unidadAdministradora', fn ($q) => $q->where('id', $unidadId));
    }

    if (! empty($validated['organismo_id'])) {
        $organismoId = $validated['organismo_id'];
        $query->whereHas('dependencia.unidadAdministradora.organismo', fn ($q) => $q->where('id', $organismoId));
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
        ->when($validated['organismo_id'] ?? null, fn ($q, $organismoId) => $q->where('organismo_id', $organismoId))
        ->orderBy('nombre')
        ->get();

    $dependencias = Dependencia::query()
        ->with('unidadAdministradora')
        ->when($validated['unidad_id'] ?? null, fn ($q, $unidadId) => $q->where('unidad_administradora_id', $unidadId))
        ->when(
            ($validated['organismo_id'] ?? null) && ! ($validated['unidad_id'] ?? null),
            fn ($q) => $q->whereHas('unidadAdministradora', fn ($sub) => $sub->where('organismo_id', $validated['organismo_id']))
        )
        ->orderBy('nombre')
        ->get();

    $estados = collect(EstadoBien::cases())->mapWithKeys(
        fn (EstadoBien $estado) => [$estado->value => $estado->label()]
    );

    // ⚡️ Si es AJAX devolvemos solo el parcial de la tabla
    if ($request->ajax()) {
        return view('bienes.partials.table', compact('bienes'))->render();
    }

    // Vista completa
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
     * Mostrar formulario de creación.
     */
    public function create()
    {
        // Cargamos las dependencias con su responsable para mostrar al seleccionar
        $dependencias = Dependencia::with('responsable')->get();
        $tiposBien = collect(TipoBien::cases())->mapWithKeys(
            fn (TipoBien $tipo) => [$tipo->value => $tipo->label()]
        );

        return view('bienes.create', compact('dependencias', 'tiposBien'));
    }
        /**
     * Mostrar formulario de edición.
     */



    /**
     * Guardar un nuevo bien.
     */

    public function store(Request $request)
{
    $validated = $request->validate(
        [
            'dependencia_id' => ['required', 'exists:dependencias,id'],
            'codigo' => ['required', 'string', 'max:50', 'unique:bienes,codigo', 'regex:/^[0-9\\-]+$/'],
            'descripcion' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'fotografia' => ['nullable', 'image', 'max:2048'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['required', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['required', 'date'],
            // Campos dinámicos según tipo
            'procesador' => ['nullable', 'string', 'max:255'],
            'memoria' => ['nullable', 'string', 'max:255'],
            'almacenamiento' => ['nullable', 'string', 'max:255'],
            'pantalla' => ['nullable', 'string', 'max:255'],
            'garantia' => ['nullable', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'string', 'max:255'],
            'placa' => ['nullable', 'string', 'max:255'],
            'motor' => ['nullable', 'string', 'max:255'],
            'chasis' => ['nullable', 'string', 'max:255'],
            'combustible' => ['nullable', 'string', 'max:255'],
            'kilometraje' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'capacidad' => ['nullable', 'string', 'max:255'],
            'cantidad_piezas' => ['nullable', 'string', 'max:255'],
            'acabado' => ['nullable', 'string', 'max:255'],
            'pisos' => ['nullable', 'string', 'max:255'],
            'construccion' => ['nullable', 'string', 'max:255'],
            'cantidad' => ['nullable', 'string', 'max:255'],
            'presentacion' => ['nullable', 'string', 'max:255'],
            'especificaciones' => ['nullable', 'string', 'max:1000'],
        ],
        [
            'dependencia_id.required' => 'La dependencia es requerida',
            'codigo.required' => 'El código es requerido',
            'codigo.unique' => 'El código ya existe en el sistema',
            'codigo.regex' => 'El código solo puede contener números y guiones',
            'descripcion.required' => 'La descripción es requerida',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'precio.required' => 'El precio es requerido',
            'precio.numeric' => 'El precio debe ser un número válido',
            'precio.min' => 'El precio debe ser mayor o igual a 0',
            'fotografia.image' => 'El archivo debe ser una imagen válida',
            'fotografia.max' => 'La imagen no puede superar 2MB',
            'estado.required' => 'El estado es requerido',
            'tipo_bien.required' => 'El tipo de bien es requerido',
            'fecha_registro.required' => 'La fecha de registro es requerida',
            'fecha_registro.date' => 'La fecha de registro debe ser una fecha válida',
        ]
    );

    // Procesar fotografía si se subió
    if ($request->hasFile('fotografia')) {
        $foto = $this->procesarFotografia($request);
        if ($foto) {
            $validated['fotografia'] = $foto;
        }
    }

    // Crear el bien con datos validados
    $bien = Bien::create($validated);

    // Registrar observación inicial para auditoría (ej. creación)
    $bien->setAttribute('_observaciones', 'Registro inicial del bien en el sistema');

    // El observer de Bien se encargará de registrar los movimientos
    return redirect()
        ->route('bienes.index')
        ->with('success', 'Bien creado correctamente.');
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
     * Descargar los detalles del bien en PDF.
     */
public function exportPdf(Bien $bien)
{
    // Cargar relaciones necesarias de forma segura
    $bien->loadMissing([
        'dependencia.responsable',
        'movimientos.usuario',
    ]);

    // Ordenar movimientos cronológicamente (más recientes primero)
    $movimientos = $bien->movimientos()
        ->orderByDesc('fecha')
        ->orderByDesc('created_at')
        ->get();

    // Variables adicionales
    $dependencia         = $bien->dependencia;
    $responsablePrimario = $bien->responsable_primario; // campo plano en la tabla bienes

    // Preparar datos para la vista
    $viewData = [
        'bien'               => $bien,
        'dependencia'        => $dependencia,
        'responsablePrimario'=> $responsablePrimario,
        'movimientos'        => $movimientos,
    ];

    // Renderizar PDF con plantilla institucional
    $pdf = Pdf::loadView('bienes.pdf', $viewData)
              ->setPaper('letter');

    // Generar nombre de archivo limpio y semántico
    $codigoSlug      = Str::slug($bien->codigo ?? 'sin_codigo', '_');
    $descriptionSlug = $bien->descripcion
        ? Str::slug(Str::limit($bien->descripcion, 50, ''), '_')
        : 'detalle';

    $fileName = sprintf('bien_%s_%s.pdf', $codigoSlug, $descriptionSlug);

    // Descargar PDF
    return $pdf->download($fileName);
}
private function procesarFotografia(Request $request, ?Bien $bien = null): ?string
{
    // Si no hay archivo subido, no hacemos nada
    if (! $request->hasFile('fotografia')) {
        return null;
    }

    $file = $request->file('fotografia');

    // Si el bien ya tenía una foto previa (y no es URL externa), la eliminamos
    if ($bien && $bien->fotografia && ! str_starts_with($bien->fotografia, 'http')) {
        Storage::disk('public')->delete($bien->fotografia);
    }

    // Generar un nombre único y legible para la nueva foto
    $filename = uniqid('bien_') . '.' . $file->getClientOriginalExtension();

    // Guardar en storage/app/public/bienes
    $path = $file->storeAs('bienes', $filename, 'public');

    // Retornar la ruta relativa que se guardará en la BD
    return $path;
}


    /**
     * Actualizar un bien.
     */
    public function update(Request $request, Bien $bien)
{
    $validated = $request->validate(
        [
            'dependencia_id' => ['sometimes', 'exists:dependencias,id'],
            'codigo' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('bienes', 'codigo')->ignore($bien->getKey()),
            ],
            'descripcion' => ['sometimes', 'string', 'max:255'],
            'precio' => ['sometimes', 'numeric', 'min:0'],
            'fotografia' => ['nullable', 'image', 'max:2048'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estado' => ['sometimes', Rule::enum(EstadoBien::class)],
            'tipo_bien' => ['sometimes', Rule::enum(TipoBien::class)],
            'fecha_registro' => ['sometimes', 'date'],
            // Campos dinámicos según tipo
            'procesador' => ['nullable', 'string', 'max:255'],
            'memoria' => ['nullable', 'string', 'max:255'],
            'almacenamiento' => ['nullable', 'string', 'max:255'],
            'pantalla' => ['nullable', 'string', 'max:255'],
            'garantia' => ['nullable', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'string', 'max:255'],
            'placa' => ['nullable', 'string', 'max:255'],
            'motor' => ['nullable', 'string', 'max:255'],
            'chasis' => ['nullable', 'string', 'max:255'],
            'combustible' => ['nullable', 'string', 'max:255'],
            'kilometraje' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'capacidad' => ['nullable', 'string', 'max:255'],
            'cantidad_piezas' => ['nullable', 'string', 'max:255'],
            'acabado' => ['nullable', 'string', 'max:255'],
            'pisos' => ['nullable', 'string', 'max:255'],
            'construccion' => ['nullable', 'string', 'max:255'],
            'cantidad' => ['nullable', 'string', 'max:255'],
            'presentacion' => ['nullable', 'string', 'max:255'],
            'especificaciones' => ['nullable', 'string', 'max:1000'],
        ],
        [
            'codigo.unique' => 'El código ya existe en el sistema',
            'codigo.regex' => 'El código solo puede contener números y guiones',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'precio.numeric' => 'El precio debe ser un número válido',
            'precio.min' => 'El precio debe ser mayor o igual a 0',
            'fotografia.image' => 'El archivo debe ser una imagen válida',
            'fotografia.max' => 'La imagen no puede superar 2MB',
            'estado.enum' => 'El estado seleccionado no es válido',
            'tipo_bien.enum' => 'El tipo de bien seleccionado no es válido',
            'fecha_registro.date' => 'La fecha de registro debe ser una fecha válida',
        ]
    );

    // Procesar fotografía si se subió una nueva
    if ($request->hasFile('fotografia')) {
        $foto = $this->procesarFotografia($request, $bien);
        if ($foto) {
            $validated['fotografia'] = $foto;
        }
    }

    // Capturar valores originales para detectar cambios relevantes
    $originalDependencia = $bien->dependencia_id;
    $originalEstado = $bien->estado;

    // Actualizar el bien
    $bien->update($validated);

    // Registrar observaciones semánticas para el observer
    $observaciones = [];

    // Detectar transferencia de dependencia
    if (array_key_exists('dependencia_id', $validated) && $validated['dependencia_id'] != $originalDependencia) {
        $oldDep = \App\Models\Dependencia::find($originalDependencia);
        $newDep = \App\Models\Dependencia::find($validated['dependencia_id']);

        $observaciones[] = sprintf(
            'Transferencia de dependencia: %s → %s',
            $oldDep?->nombre ?? 'N/A',
            $newDep?->nombre ?? 'N/A'
        );
    }


    // Detectar cambio de estado
    if (array_key_exists('estado', $validated)) {
        $originalValue = $originalEstado instanceof EstadoBien ? $originalEstado->value : $originalEstado;
        $nuevoValue = $validated['estado'] instanceof EstadoBien ? $validated['estado']->value : $validated['estado'];

        if ($nuevoValue !== $originalValue) {
            $labelOriginal = $originalEstado instanceof EstadoBien
                ? $originalEstado->label()
                : EstadoBien::tryFrom($originalValue)?->label() ?? (string) $originalValue;

            $labelNuevo = $validated['estado'] instanceof EstadoBien
                ? $validated['estado']->label()
                : EstadoBien::tryFrom($nuevoValue)?->label() ?? (string) $nuevoValue;

            $observaciones[] = sprintf('Cambio de estado: %s → %s', $labelOriginal, $labelNuevo);
        }
    }

    // Pasar observaciones al observer (si usas un sistema de contexto temporal)
    if (!empty($observaciones)) {
        $bien->setAttribute('_observaciones', implode(' | ', $observaciones));
    }

    return redirect()
        ->route('bienes.index')
        ->with('success', 'Bien actualizado correctamente.');
}

    public function edit(Bien $bien)
{
    $dependencias = Dependencia::with('responsable')->get();
    return view('bienes.edit', compact('bien', 'dependencias'));
}

    /**
     * Eliminar un bien.
     */
    public function destroy(Bien $bien)
    {
        // Verificar permisos: solo administradores pueden desincorporar bienes
        if (! auth()->user()->canDeleteData()) {
            return response()->json(['message' => 'No tienes permisos para desincorporar bienes.'], 403);
        }

        // Log para depuración del ID de usuario
        $userId = auth()->id();
        logger()->info('Valor de auth()->id() durante la desincorporación', ['userId' => $userId]);

        if (!is_int($userId)) {
            logger()->warning('ID de usuario inválido durante la desincorporación', ['userId' => $userId]);
            return response()->json(['message' => 'Error interno: ID de usuario inválido.'], 500);
        }

        // Registrar movimiento de desincorporación
        \App\Models\Movimiento::create([
            'bien_id' => $bien->id,
            'tipo' => 'desincorporación',
            'descripcion' => 'Bien desincorporado',
            'usuario_id' => $userId,
        ]);

        // Marcar el bien como extraviado (valor válido del enum EstadoBien)
        $bien->update(['estado' => \App\Enums\EstadoBien::EXTRAVIADO]);

        // Archivar el bien eliminado
        \App\Services\EliminadosService::archiveModel($bien, $userId);

        return redirect()
            ->route('bienes.index')
            ->with('success', 'Bien desincorporado correctamente.');
    }
        public function galeriaCompleta()
    {
        // Recuperamos todos los bienes que tienen una fotografía adjunta
        $bienesConFoto = Bien::whereNotNull('fotografia')
                             ->where('fotografia', '!=', '')
                             ->select('id', 'codigo', 'descripcion', 'fotografia') // Seleccionamos solo los campos necesarios
                             ->get();

        // Creamos una colección simple de objetos para la vista, incluyendo la URL completa.
        $imagenes = $bienesConFoto->map(function ($bien) {
            return (object) [
                'id'          => $bien->id,
                'codigo'      => $bien->codigo,
                'descripcion' => $bien->descripcion,
                'url'         => Storage::url($bien->fotografia), // Asegúrate de que Storage::url funciona con tu configuración
            ];
        });

        return view('bienes.galeria-completa', compact('imagenes'));
    }

}
