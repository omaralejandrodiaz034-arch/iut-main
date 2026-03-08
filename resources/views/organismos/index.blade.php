@extends('layouts.base')

@section('title', 'Organismos')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Organismos']]" />
@endpush
<div class="space-y-6 md:space-y-8">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
            <span class="text-4xl drop-shadow-sm">🏢</span>
            Organismos
        </h1>

        <a href="{{ route('organismos.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-sm transition-all hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Organismo
        </a>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm animate-fade-in">
        {{ session('success') }}
    </div>
    @endif

    <!-- Panel de filtros -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Filtros de búsqueda</h2>
        </div>

        <form action="{{ route('organismos.index') }}" method="GET" id="filtrosForm" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
                <!-- Búsqueda general -->
                <div>
                    <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1.5">Búsqueda general</label>
                    <input type="text" name="buscar" id="buscar"
                           value="{{ request('buscar') ?? '' }}" maxlength="40"
                           placeholder="Nombre o código..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto filtro-input">
                    <p id="error-msg-buscar" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo letras, números y espacios permitidos
                    </p>
                </div>

                <!-- Código exacto -->
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1.5">Código exacto</label>
                    <input type="text" name="codigo" id="codigo" inputmode="numeric"
                           value="{{ request('codigo') ?? '' }}" maxlength="8"
                           placeholder="Solo números (máx. 8 dígitos)"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto filtro-input">
                    <p id="error-codigo-msg" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                        Solo números permitidos (máx. 8 dígitos)
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('organismos.index') }}"
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
            $params = request()->only(['buscar', 'codigo']);
            $active = collect($params)->filter(fn($v) => filled($v))->toArray();
        @endphp

        @if(!empty($active))
        <div class="flex flex-wrap items-center gap-2.5 text-sm">
            <span class="font-medium text-gray-600">Filtros activos:</span>
            @foreach($active as $key => $value)
                <div class="inline-flex items-center gap-1.5 pl-3.5 pr-2 py-1.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100 shadow-sm">
                    <span class="font-medium">{{ ucfirst($key) }}:</span>
                    <span>{{ $value }}</span>
                    <a href="{{ route('organismos.index', request()->except($key)) }}"
                       class="ml-1 p-1 rounded-full hover:bg-indigo-100 transition text-indigo-500 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
                       aria-label="Quitar filtro {{ ucfirst($key) }}">
                        ×
                    </a>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Tabla de organismos -->
    <div id="tablaOrganismos" class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre del organismo</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($organismos as $organismo)
                        <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                            <td class="px-6 py-5 text-sm font-mono font-semibold text-indigo-700 tracking-tight">
                                {{ $organismo->codigo }}
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-gray-900">
                                {{ $organismo->nombre }}
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                @include('components.action-buttons', [
                                    'resource' => 'organismos',
                                    'model' => $organismo,
                                    'canDelete' => false
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center text-gray-500 italic text-base">
                                No se encontraron organismos con los criterios seleccionados.
                                <p class="mt-2 text-sm text-gray-400">
                                    Prueba ajustando los filtros o crea uno nuevo.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div id="organismosPagination" class="mt-6 flex justify-center">
        @if($organismos->hasPages())
            {{ $organismos->links('pagination::tailwind') }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// ────────────────────────────────────────────────
// Scripts para la vista de Organismos (versión profesional)
// ────────────────────────────────────────────────
let fetchTimeout;

function validarYLimpiar(input, regex, errorElement) {
    if (!input || !errorElement) return;

    const pos = input.selectionStart;
    const val = input.value;
    const clean = val.replace(regex, '');

    if (val !== clean) {
        errorElement.classList.remove('hidden');
        setTimeout(() => errorElement.classList.add('hidden'), 2200);
        input.value = clean;
        input.setSelectionRange(pos - (val.length - clean.length), pos - (val.length - clean.length));
    }
}

async function aplicarFiltros(url = null) {
    clearTimeout(fetchTimeout);
    fetchTimeout = setTimeout(async () => {
        const table = document.getElementById('tablaOrganismos');
        if (table) table.classList.add('opacity-60', 'transition-opacity');

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

            ['tablaOrganismos', 'organismosPagination', 'activeFiltersContainer'].forEach(id => {
                const old = document.getElementById(id);
                const neu = doc.getElementById(id);
                if (old && neu) old.innerHTML = neu.innerHTML;
            });

            if (table) table.classList.remove('opacity-60');
            attachPagination();
        } catch (err) {
            console.error('Error al actualizar organismos:', err);
            if (table) table.classList.remove('opacity-60');
        }
    }, 320);
}

function attachPagination() {
    document.querySelectorAll('#organismosPagination a').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            aplicarFiltros(a.href);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const buscar = document.getElementById('buscar');
    const codigo = document.getElementById('codigo');

    if (buscar) {
        buscar.addEventListener('input', () => {
            validarYLimpiar(buscar, /[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, document.getElementById('error-msg-buscar'));
            aplicarFiltros();
        });
    }

    if (codigo) {
        codigo.addEventListener('input', () => {
            validarYLimpiar(codigo, /[^0-9]/g, document.getElementById('error-codigo-msg'));
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
