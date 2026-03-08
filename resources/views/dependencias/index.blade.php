@extends('layouts.base')

@section('title', 'Dependencias')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Dependencias']]" />
@endpush
<div class="space-y-6 md:space-y-8">
    <!-- Encabezado con título y botones de acción -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
            <span class="text-4xl md:text-5xl drop-shadow-sm">🏛️</span>
            Dependencias
        </h1>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dependencias.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition-all hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Dependencia
            </a>

            <a href="{{ route('responsables.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-sm transition-all hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Nuevo Responsable
            </a>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <!-- Panel de filtros -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6">
        <form action="{{ route('dependencias.index') }}" method="GET" id="filtrosForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                <!-- Búsqueda -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Búsqueda</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Código o nombre..." maxlength="40"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto"
                           aria-describedby="search-help">
                    <p id="error-msg" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo letras, números, espacios y acentos permitidos
                    </p>
                </div>

                <!-- Unidad -->
                <div>
                    <label for="unidad_id" class="block text-sm font-medium text-gray-700 mb-1.5">Unidad Administradora</label>
                    <select name="unidad_id" id="unidad_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                        <option value="">Todas las unidades</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsable_id" class="block text-sm font-medium text-gray-700 mb-1.5">Responsable</label>
                    <select name="responsable_id" id="responsable_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                        <option value="">Todos los responsables</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>
                                {{ $resp->nombre }} ({{ $resp->cedula }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('dependencias.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition text-sm font-medium text-center min-w-[140px]">
                    Limpiar filtros
                </a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-700 transition font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 min-w-[140px]">
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Chips de filtros activos -->
    <div id="activeFiltersContainer" class="min-h-[2.5rem]">
        @php
            $params = request()->only(['search', 'unidad_id', 'responsable_id']);
            $active = collect($params)->filter(fn($v) => filled($v))->toArray();
        @endphp

        @if (!empty($active))
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="text-sm font-medium text-gray-600">Filtros activos:</span>
                @foreach ($active as $key => $value)
                    @php
                        $label = match($key) {
                            'search' => 'Búsqueda',
                            'unidad_id' => 'Unidad',
                            'responsable_id' => 'Responsable',
                            default => ucfirst($key),
                        };
                        $display = $value;
                        if ($key === 'unidad_id') $display = $unidades->firstWhere('id', $value)?->nombre ?? $value;
                        if ($key === 'responsable_id') $display = $responsables->firstWhere('id', $value)?->nombre ?? $value;
                    @endphp
                    <div class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100 text-sm font-medium shadow-sm">
                        <span>{{ $label }}: <strong>{{ Str::limit($display, 28) }}</strong></span>
                        <a href="{{ route('dependencias.index', request()->except($key)) }}"
                           class="ml-1 p-1 rounded-full hover:bg-indigo-100 transition text-indigo-500 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
                           aria-label="Eliminar filtro {{ $label }}">
                            ×
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tabla principal -->
    <div id="tablaDependencias" class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unidad</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Responsable</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bienes</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($dependencias as $dep)
                        <tr class="hover:bg-indigo-50/40 transition-colors duration-150">
                            <td class="px-6 py-5 text-sm font-mono font-semibold text-indigo-700">{{ $dep->codigo }}</td>
                            <td class="px-6 py-5 text-sm font-medium text-gray-900">{{ $dep->nombre }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">{{ $dep->unidadAdministradora?->nombre ?? '—' }}</td>
                            <td class="px-6 py-5 text-sm">
                                @if ($dep->responsable)
                                    <div class="space-y-0.5">
                                        <div class="font-medium text-gray-900">{{ $dep->responsable->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $dep->responsable->cedula }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-sm">Sin responsable</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if ($dep->bienes_count > 0)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $dep->bienes_count }} bienes
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                        0 bienes
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                @include('components.action-buttons', [
                                    'resource' => 'dependencias',
                                    'model' => $dep,
                                    'canDelete' => false
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-500">
                                <div class="text-lg font-medium">No se encontraron dependencias</div>
                                <p class="mt-2 text-sm text-gray-400">
                                    Ajusta los filtros o crea una nueva dependencia con el botón superior.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div id="paginationLinks" class="mt-6 flex justify-center">
        @if ($dependencias->hasPages())
            {{ $dependencias->links('pagination::tailwind') }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// ────────────────────────────────────────────────
// Mejoras: mejor debounce, manejo de errores visible, cursor restore mejorado
// ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const form        = document.getElementById('filtrosForm');
    const searchInput = document.getElementById('search');
    const errorMsg    = document.getElementById('error-msg');
    const table       = document.getElementById('tablaDependencias');
    let debounceTimer;

    if (!form || !table) return;

    // Validación en tiempo real del campo búsqueda
    if (searchInput && errorMsg) {
        searchInput.addEventListener('input', function () {
            const pos = this.selectionStart;
            const val = this.value;
            const clean = val.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑüÜ]/g, '');

            if (val !== clean) {
                errorMsg.classList.remove('hidden');
                setTimeout(() => errorMsg.classList.add('hidden'), 2800);
                this.value = clean;
                // Mejor restauración de cursor
                const diff = val.length - clean.length;
                this.setSelectionRange(pos - diff, pos - diff);
            }
        });
    }

    // Actualización principal vía AJAX
    async function refreshContent(url = null) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            table.classList.add('opacity-60');

            const formData = new URLSearchParams(new FormData(form));

            if (url) {
                const urlObj = new URL(url);
                formData.set('page', urlObj.searchParams.get('page') || '1');
            } else {
                formData.delete('page');
            }

            const fetchUrl = `${form.action}?${formData}`;

            try {
                history.replaceState({}, '', fetchUrl);

                const res = await fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Actualizar solo las partes dinámicas
                ['tablaDependencias', 'paginationLinks', 'activeFiltersContainer'].forEach(id => {
                    const oldEl = document.getElementById(id);
                    const newEl = doc.getElementById(id);
                    if (oldEl && newEl) oldEl.innerHTML = newEl.innerHTML;
                });

                table.classList.remove('opacity-60');
                attachPagination();
            } catch (err) {
                console.error('Error al refrescar tabla:', err);
                table.classList.remove('opacity-60');
                // Opcional: mostrar mensaje de error al usuario
                // alert('No se pudo actualizar la tabla. Intenta de nuevo.');
            }
        }, 320);
    }

    function attachPagination() {
        document.querySelectorAll('#paginationLinks a').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                refreshContent(a.href);
            });
        });
    }

    // Eventos
    if (searchInput) searchInput.addEventListener('input', () => refreshContent());
    document.querySelectorAll('.filtro-auto').forEach(el => el.addEventListener('change', () => refreshContent()));

    form.addEventListener('submit', e => {
        e.preventDefault();
        refreshContent();
    });

    attachPagination();
});
</script>
@endpush
