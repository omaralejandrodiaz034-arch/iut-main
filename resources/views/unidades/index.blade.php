@extends('layouts.base')

@section('title', 'Unidades Administradoras')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Unidades Administradoras']]" />
@endpush
<div class="space-y-6 md:space-y-8">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
            <span class="text-4xl drop-shadow-sm">📚</span>
            Unidades Administradoras
        </h1>

        <a href="{{ route('unidades.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-sm transition-all hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Unidad
        </a>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm animate-fade-in">
        {{ session('success') }}
    </div>
    @endif

    <!-- Panel de filtros -->
    <div class="bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Filtros de búsqueda</h2>
        </div>

        <form action="{{ route('unidades.index') }}" method="GET" id="filtrosForm" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
                <!-- Búsqueda rápida -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Búsqueda rápida</label>
                    <input type="text" name="search" id="search" value="{{ request('search') ?? '' }}"
                           maxlength="40" placeholder="Código o nombre..."
                           class="w-full border border-gray-300 bg-white text-gray-900 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto filtro-input">
                    <p id="error-msg" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo letras, números y espacios permitidos
                    </p>
                </div>

                <!-- Organismo -->
                <div>
                    <label for="organismo_id" class="block text-sm font-medium text-gray-700 mb-1.5">Organismo</label>
                    <select name="organismo_id" id="organismo_id"
                            class="w-full border border-gray-300 bg-white text-gray-900 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                        <option value="">Todos los organismos</option>
                        @foreach($organismos as $organismo)
                            <option value="{{ $organismo->id }}" {{ request('organismo_id') == $organismo->id ? 'selected' : '' }}>
                                {{ $organismo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('unidades.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center min-w-[140px]">
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
            $params = request()->only(['search', 'organismo_id']);
            $active = collect($params)->filter(fn($v) => filled($v))->toArray();
        @endphp

        @if(!empty($active))
        <div class="flex flex-wrap items-center gap-2.5 text-sm">
            <span class="font-medium text-gray-600">Filtros activos:</span>
            @foreach($active as $key => $value)
                @php
                    $label = $key === 'search' ? 'Búsqueda' : 'Organismo';
                    $display = ($key === 'organismo_id')
                        ? ($organismos->firstWhere('id', $value)?->nombre ?? $value)
                        : $value;
                @endphp
                <div class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100 shadow-sm">
                    <span class="font-medium">{{ $label }}:</span>
                    <span>{{ Str::limit($display, 28) }}</span>
                    <a href="{{ route('unidades.index', request()->except($key)) }}"
                       class="ml-1 p-1 rounded-full hover:bg-indigo-100 transition text-indigo-500 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
                       aria-label="Quitar filtro {{ $label }}">
                        ×
                    </a>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Tabla de unidades -->
    <div id="tablaUnidades" class="bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Organismo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Dependencias</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($unidades as $unidad)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-5 text-sm font-mono font-semibold text-indigo-700 tracking-tight">
                                {{ $unidad->codigo }}
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-slate-900">
                                {{ $unidad->nombre }}
                            </td>
                            <td class="px-6 py-5 text-sm text-slate-700">
                                {{ $unidad->organismo?->nombre ?? '—' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($unidad->dependencias_count ?? $unidad->dependencias?->count() > 0)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $unidad->dependencias_count ?? $unidad->dependencias?->count() }} dependencias
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                @include('components.action-buttons', [
                                    'resource' => 'unidades',
                                    'model' => $unidad,
                                    'canDelete' => false
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-500 italic text-base">
                                No se encontraron unidades administradoras.
                                <p class="mt-2 text-sm text-slate-400">
                                    Ajusta los filtros o registra una nueva unidad.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div id="unidadesPagination" class="mt-6 flex justify-center">
        @if($unidades->hasPages())
            {{ $unidades->links('pagination::tailwind') }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// ────────────────────────────────────────────────
// Scripts para Unidades Administradoras (versión mejorada)
// ────────────────────────────────────────────────
let fetchTimeout;

function validarYLimpiar(input, regex, errorEl) {
    if (!input || !errorEl) return;

    const pos = input.selectionStart;
    const val = input.value;
    const clean = val.replace(regex, '');

    if (val !== clean) {
        errorEl.classList.remove('hidden');
        setTimeout(() => errorEl.classList.add('hidden'), 2200);
        input.value = clean;
        input.setSelectionRange(pos - (val.length - clean.length), pos - (val.length - clean.length));
    }
}

async function aplicarFiltros(url = null) {
    clearTimeout(fetchTimeout);
    fetchTimeout = setTimeout(async () => {
        const table = document.getElementById('tablaUnidades');
        if (table) table.classList.add('opacity-60');

        const form = document.getElementById('filtrosForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        if (url) {
            const urlObj = new URL(url);
            if (urlObj.searchParams.has('page')) {
                params.set('page', urlObj.searchParams.get('page'));
            }
        } else {
            params.delete('page');
        }

        const fetchUrl = `${form.action}?${params}`;

        try {
            history.replaceState({}, '', fetchUrl);

            const res = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            ['tablaUnidades', 'unidadesPagination', 'activeFiltersContainer'].forEach(id => {
                const old = document.getElementById(id);
                const neu = doc.getElementById(id);
                if (old && neu) old.innerHTML = neu.innerHTML;
            });

            if (table) table.classList.remove('opacity-60');
            attachPagination();
        } catch (err) {
            console.error('Error al actualizar unidades:', err);
            if (table) table.classList.remove('opacity-60');
        }
    }, 320);
}

function attachPagination() {
    document.querySelectorAll('#unidadesPagination a').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            aplicarFiltros(a.href);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search');

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            validarYLimpiar(
                searchInput,
                /[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g,
                document.getElementById('error-msg')
            );
            aplicarFiltros();
        });
    }

    document.querySelectorAll('.filtro-auto').forEach(el => {
        el.addEventListener('change', () => aplicarFiltros());
    });

    const form = document.getElementById('filtrosForm');
    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            aplicarFiltros();
        });
    }

    attachPagination();
});
</script>
@endpush
