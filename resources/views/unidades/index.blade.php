@extends('layouts.base')

@section('title', 'Unidades Administradoras')

@section('content')
{{-- resources/views/unidades/index.blade.php --}}

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        📚 Unidades Administradoras
    </h1>
    <a href="{{ route('unidades.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Nueva Unidad
    </a>
</div>

{{-- Mensajes de éxito --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Panel de Filtros --}}
<div class="mb-6 bg-white shadow rounded-lg p-4 space-y-4">
    <form action="{{ route('unidades.index') }}" method="GET" class="space-y-4" id="filtrosForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Filtro General con validación --}}
            <div class="flex flex-col">
                <label for="search" class="text-sm font-medium text-gray-700 mb-1">Búsqueda rápida</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       maxlength="40"
                       placeholder="Código o nombre (máx. 40 caracteres)..."
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto filtro-input">
                <p id="error-msg" class="text-red-500 text-xs mt-1 hidden font-semibold">
                    ⚠️ Solo se permiten letras, números y espacios.
                </p>
            </div>

            {{-- Filtro por Organismo --}}
            <div class="flex flex-col">
                <label for="organismo_id" class="text-sm font-medium text-gray-700 mb-1">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos los organismos</option>
                    @foreach($organismos as $organismo)
                        <option value="{{ $organismo->id }}" @selected(request('organismo_id') == $organismo->id)>
                            {{ $organismo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('unidades.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                Limpiar
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Aplicar filtros
            </button>
        </div>
    </form>
</div>

{{-- Contenedor de Filtros Activos (Chips) --}}
<div id="activeFiltersContainer">
    @php
        $activeFilters = collect(request()->only(['search', 'organismo_id']))->filter();
    @endphp
    @if($activeFilters->isNotEmpty())
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-gray-700">Filtros activos:</span>
            @foreach($activeFilters as $key => $value)
                @php
                    $displayValue = ($key === 'organismo_id')
                        ? ($organismos->firstWhere('id', $value)->nombre ?? $value)
                        : $value;
                    $label = $key === 'search' ? 'Búsqueda' : 'Organismo';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">
                    {{ $label }}: <span class="ml-1 font-medium">{{ $displayValue }}</span>
                    <a href="{{ route('unidades.index', request()->except($key)) }}"
                       class="ml-2 text-indigo-500 hover:text-red-600 font-bold"> × </a>
                </span>
            @endforeach
        </div>
    @endif
</div>

{{-- Tabla de Unidades --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden" id="tablaUnidades">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organismo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dependencias</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($unidades as $unidad)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-blue-600 font-mono">{{ $unidad->codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $unidad->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $unidad->organismo->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($unidad->dependencias->count())
                                <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                    {{ $unidad->dependencias->count() }} dependencias
                                </span>
                            @else
                                <span class="text-gray-400 italic">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            @include('components.action-buttons', [
                                'resource' => 'unidades',
                                'model' => $unidad,
                                'canDelete' => false
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 italic">
                            No hay unidades que coincidan con la búsqueda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-6" id="unidadesPagination">
    @if($unidades->hasPages())
        {{ $unidades->links() }}
    @endif
</div>

@push('scripts')
<script>
    let fetchTimeout;

    function aplicarFiltros(url = null) {
        if (fetchTimeout) clearTimeout(fetchTimeout);

        fetchTimeout = setTimeout(() => {
            const form = document.getElementById('filtrosForm');
            const baseUrl = url || form.action;
            const formParams = new URLSearchParams(new FormData(form));
            const fetchUrl = baseUrl.split('?')[0] + '?' + formParams.toString();

            window.history.pushState(null, '', fetchUrl);

            fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                document.getElementById('tablaUnidades').innerHTML = doc.querySelector('#tablaUnidades').innerHTML;
                document.getElementById('unidadesPagination').innerHTML = doc.querySelector('#unidadesPagination').innerHTML;
                document.getElementById('activeFiltersContainer').innerHTML = doc.querySelector('#activeFiltersContainer').innerHTML;

                attachPaginationListeners();
            })
            .catch(error => console.error('Error al filtrar:', error));
        }, 300);
    }

    function attachPaginationListeners() {
        document.querySelectorAll('#unidadesPagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                aplicarFiltros(this.href);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search');
        const errorMsg = document.getElementById('error-msg');

        // Validación de caracteres y longitud
        searchInput.addEventListener('input', function(e) {
            const originalValue = e.target.value;
            const cleanValue = originalValue.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, '');

            if (originalValue !== cleanValue) {
                errorMsg.classList.remove('hidden');
                setTimeout(() => errorMsg.classList.add('hidden'), 2500);
            }

            e.target.value = cleanValue.slice(0, 40);
        });

        // Disparadores automáticos
        searchInput.addEventListener('keyup', () => aplicarFiltros());
        
        document.querySelectorAll('.filtro-auto').forEach(el => {
            if(el.tagName === 'SELECT') el.addEventListener('change', () => aplicarFiltros());
        });

        document.getElementById('filtrosForm').addEventListener('submit', (e) => {
            e.preventDefault();
            aplicarFiltros();
        });

        attachPaginationListeners();
    });
</script>
@endpush
@endsection
