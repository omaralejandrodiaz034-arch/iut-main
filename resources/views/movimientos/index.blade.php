@extends('layouts.base')

@section('title', 'Movimientos')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Movimientos']]" />
@endpush
<div class="space-y-6 md:space-y-8">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-3">
            <span class="text-3xl">📄</span>
            Registro de Movimientos
        </h1>
    </div>

    <!-- Mensaje éxito -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Panel de filtros -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Filtros de búsqueda</h2>
        </div>

        <form action="{{ route('movimientos.index') }}" method="GET" id="filtrosForm" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de movimiento</label>
                    <select name="tipo" id="tipo"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                        <option value="">Todos los tipos</option>
                        <option value="Registro"     {{ ($filters['tipo'] ?? '') === 'Registro'     ? 'selected' : '' }}>Registro</option>
                        <option value="Actualización" {{ ($filters['tipo'] ?? '') === 'Actualización' ? 'selected' : '' }}>Actualización</option>
                        <option value="Eliminación"  {{ ($filters['tipo'] ?? '') === 'Eliminación'  ? 'selected' : '' }}>Eliminación</option>
                    </select>
                </div>

                <!-- Usuario -->
                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1.5">Usuario</label>
                    <input type="text" name="usuario" id="usuario" value="{{ $filters['usuario'] ?? '' }}"
                           placeholder="Nombre o apellido..." maxlength="30"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto filtro-input">
                    <p id="error-usuario" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo letras y espacios (máx. 30 caracteres)
                    </p>
                </div>

                <!-- Entidad -->
                <div>
                    <label for="entidad" class="block text-sm font-medium text-gray-700 mb-1.5">Entidad afectada</label>
                    <input type="text" name="entidad" id="entidad" value="{{ $filters['entidad'] ?? '' }}"
                           placeholder="Bien, Usuario, Compra..." maxlength="30"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto filtro-input">
                    <p id="error-entidad" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo letras y espacios (máx. 30 caracteres)
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1.5">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                </div>
                <div>
                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1.5">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('movimientos.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-center">
                    Limpiar filtros
                </a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-2.5 rounded-lg hover:bg-indigo-700 transition font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Chips de filtros activos -->
    <div id="activeFiltersContainer" class="min-h-[2.5rem]">
        @php $active = collect($filters ?? [])->filter(fn($v) => filled($v)); @endphp
        @if($active->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2.5 text-sm">
            <span class="font-medium text-gray-600">Filtros activos:</span>
            @foreach($active as $key => $value)
                @php
                    $label = match($key) {
                        'tipo'        => 'Tipo',
                        'usuario'     => 'Usuario',
                        'entidad'     => 'Entidad',
                        'fecha_desde' => 'Desde',
                        'fecha_hasta' => 'Hasta',
                        default       => ucfirst($key),
                    };
                    $query = request()->query();
                    unset($query[$key]);
                @endphp
                <div class="inline-flex items-center px-3.5 py-1.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100 shadow-sm">
                    <span class="font-medium mr-1.5">{{ $label }}:</span>
                    <span>{{ $value }}</span>
                    <a href="{{ route('movimientos.index', $query) }}"
                       class="ml-2 text-indigo-400 hover:text-red-500 font-bold transition focus:outline-none focus:text-red-600"
                       aria-label="Quitar filtro {{ $label }}">×</a>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Contenedor de tablas -->
    <div class="grid grid-cols-1 {{ isset($eliminados) && $eliminados->isNotEmpty() ? 'lg:grid-cols-2' : '' }} gap-6">
        <!-- Movimientos normales -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Movimientos registrados</h2>
            </div>
            <div class="overflow-x-auto" id="tablaMovimientos">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Entidad</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Observaciones</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($movimientos as $mov)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                    {{ $mov->fecha?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ match($mov->tipo) {
                                        'Registro'     => 'bg-emerald-100 text-emerald-800',
                                        'Actualización' => 'bg-amber-100  text-amber-800',
                                        'Eliminación'  => 'bg-rose-100   text-rose-800',
                                        default        => 'bg-gray-100   text-gray-700',
                                    } }}">
                                        {{ $mov->tipo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <strong class="text-gray-800">{{ class_basename($mov->subject_type ?? '—') }}</strong>
                                    <br>
                                    <span class="text-gray-600">{{ $mov->subject?->nombre_completo ?? $mov->subject?->nombre ?? $mov->subject?->descripcion ?? $mov->subject?->codigo ?? 'ID '.$mov->subject_id }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $mov->usuario?->nombre_completo ?? $mov->usuario?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    {{ Str::limit($mov->observaciones ?? '', 70) }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('movimientos.show', $mov) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium hover:underline">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                    No se encontraron movimientos con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100" id="movimientosPagination">
                {{ $movimientos->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Tabla de eliminados (solo si existen) -->
        @if(isset($eliminados) && $eliminados->isNotEmpty())
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Registros eliminados (archivados)</h2>
            </div>
            <div class="overflow-x-auto" id="tablaEliminados">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Eliminado por</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($eliminados as $e)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ class_basename($e->model_type) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $e->model_id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $e->deleted_by_user ?? $e->deleted_by ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                    {{ $e->deleted_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                            class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded border border-emerald-200 transition restore-button"
                                            data-id="{{ $e->id }}"
                                            data-model="{{ class_basename($e->model_type) }}"
                                            data-model-id="{{ $e->model_id }}">
                                        Restaurar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100" id="eliminadosPagination">
                {{ $eliminados->links('pagination::tailwind', ['pageName' => 'eliminados_page']) }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// ────────────────────────────────────────────────
// Gestión de filtros y actualización AJAX para Movimientos
// ────────────────────────────────────────────────
let fetchTimeout;

function aplicarFiltros(url = null) {
    if (fetchTimeout) clearTimeout(fetchTimeout);

    fetchTimeout = setTimeout(async () => {
        const form = document.getElementById('filtrosForm');
        if (!form) return;

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Si viene de un link de paginación, respetamos page o eliminados_page
        if (url) {
            const urlObj = new URL(url);
            urlObj.searchParams.forEach((value, key) => {
                params.set(key, value);
            });
        }

        const fetchUrl = `${form.action}?${params.toString()}`;

        // Feedback visual: opacidad + cursor loading
        const containers = [
            document.getElementById('tablaMovimientos')?.closest('.overflow-x-auto'),
            document.getElementById('tablaEliminados')?.closest('.overflow-x-auto')
        ].filter(Boolean);

        containers.forEach(el => {
            if (el) {
                el.classList.add('opacity-60', 'transition-opacity');
                el.style.cursor = 'wait';
            }
        });

        try {
            // Actualizar URL en barra de direcciones (mejora UX)
            window.history.replaceState(null, '', fetchUrl);

            const response = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Secciones a actualizar
            const sections = [
                'tablaMovimientos',
                'movimientosPagination',
                'activeFiltersContainer',
                'tablaEliminados',
                'eliminadosPagination'
            ];

            sections.forEach(id => {
                const oldElement = document.getElementById(id);
                const newElement = doc.getElementById(id);
                if (oldElement && newElement) {
                    oldElement.innerHTML = newElement.innerHTML;
                }
            });

            // Re-asignar eventos de paginación después de actualizar
            attachPaginationListeners();
        } catch (error) {
            console.error('Error al actualizar movimientos:', error);
            // Opcional: mostrar notificación al usuario
            // alert('No se pudo actualizar la lista. Intenta de nuevo.');
        } finally {
            // Restaurar apariencia
            containers.forEach(el => {
                if (el) {
                    el.classList.remove('opacity-60');
                    el.style.cursor = '';
                }
            });
        }
    }, 320); // debounce ~320ms
}

// ────────────────────────────────────────────────
// Re-asignar eventos a links de paginación (normal y eliminados)
// ────────────────────────────────────────────────
function attachPaginationListeners() {
    const paginationContainers = [
        '#movimientosPagination a',
        '#eliminadosPagination a'
    ];

    document.querySelectorAll(paginationContainers.join(', ')).forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            aplicarFiltros(this.href);
        });
    });
}

// ────────────────────────────────────────────────
// Validación en tiempo real (solo letras y espacios)
// ────────────────────────────────────────────────
function restringirEntrada(inputElement, errorElementId) {
    if (!inputElement) return;

    const errorMsg = document.getElementById(errorElementId);
    if (!errorMsg) return;

    inputElement.addEventListener('input', function(e) {
        const original = e.target.value;
        const cleaned = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

        if (original !== cleaned) {
            e.target.value = cleaned;
            errorMsg.classList.remove('hidden');
            setTimeout(() => errorMsg.classList.add('hidden'), 2200);
        }
    });
}

// ────────────────────────────────────────────────
// Inicialización
// ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const usuarioInput = document.getElementById('usuario');
    const entidadInput = document.getElementById('entidad');

    restringirEntrada(usuarioInput, 'error-usuario');
    restringirEntrada(entidadInput, 'error-entidad');

    // Eventos de cambio automático
    document.querySelectorAll('.filtro-auto, .filtro-input').forEach(element => {
        if (element.tagName === 'SELECT' || element.type === 'date') {
            element.addEventListener('change', () => aplicarFiltros());
        } else if (element.type === 'text') {
            element.addEventListener('input', () => aplicarFiltros());
        }
    });

    // Submit del formulario
    const form = document.getElementById('filtrosForm');
    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            aplicarFiltros();
        });
    }

    // Links de paginación iniciales
    attachPaginationListeners();
});
</script>
@endpush

@endsection
