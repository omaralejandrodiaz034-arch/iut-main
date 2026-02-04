@extends('layouts.base')

@section('title', 'Organismos')

@section('content')
{{-- resources/views/organismos/index.blade.php --}}

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        🏢 Organismos
    </h1>
    <div class="flex gap-4">
        <a href="{{ route('organismos.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nuevo Organismo
        </a>
    </div>
</div>

{{-- Mensajes de éxito --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Filtros --}}
{{-- Filtros con validación de caracteres --}}
<div class="mb-6 bg-white shadow rounded-lg p-4 space-y-4">
    <form action="{{ route('organismos.index') }}" method="GET" class="space-y-4" id="filtrosForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Filtro General (Búsqueda) --}}
            <div class="flex flex-col">
                <label for="buscar" class="text-sm font-medium text-gray-700 mb-1">Búsqueda general</label>
                <input type="text"
                       name="buscar"
                       id="buscar"
                       value="{{ $validated['buscar'] ?? '' }}"
                       maxlength="40"
                       placeholder="Nombre o código (máx. 40 caracteres)..."
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto filtro-input">
                <p id="error-msg-buscar" class="text-red-500 text-xs mt-1 hidden font-semibold">
                    ⚠️ Solo se permiten letras, números y espacios.
                </p>
            </div>

            {{-- Filtro por Código (Específico) --}}
            <div class="flex flex-col">
                <label for="codigo" class="text-sm font-medium text-gray-700 mb-1">Código exacto</label>
                <input type="text"
                       name="codigo"
                       id="codigo"
                       value="{{ $validated['codigo'] ?? '' }}"
                       placeholder="Ej: 001"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto filtro-input">
            </div>
        </div>

        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('organismos.index') }}"
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
{{-- Chips de Filtros Activos --}}
<div id="activeFiltersContainer">
    @php
        $activeFilters = collect(request()->only(['buscar', 'codigo', 'nombre']))->filter();
    @endphp
    @if($activeFilters->isNotEmpty())
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-gray-700">Filtros activos:</span>
            @foreach($activeFilters as $key => $value)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">
                    {{ ucfirst($key) }}: <span class="ml-1 font-medium">{{ $value }}</span>
                    <a href="{{ route('organismos.index', request()->except($key)) }}"
                       class="ml-2 text-indigo-500 hover:text-red-600 font-bold"> × </a>
                </span>
            @endforeach
        </div>
    @endif
</div>

{{-- Tabla --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden" id="tablaOrganismos">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($organismos as $organismo)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-blue-600 font-mono">{{ $organismo->codigo }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $organismo->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                @include('components.action-buttons', [
                                    'resource' => 'organismos',
                                    'model' => $organismo,
                                    'canDelete' => false
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500 italic">
                                No se encontraron organismos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-6" id="organismosPagination">
    @if($organismos->hasPages())
        {{ $organismos->links() }}
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

                document.getElementById('tablaOrganismos').innerHTML = doc.querySelector('#tablaOrganismos').innerHTML;
                document.getElementById('organismosPagination').innerHTML = doc.querySelector('#organismosPagination').innerHTML;
                document.getElementById('activeFiltersContainer').innerHTML = doc.querySelector('#activeFiltersContainer').innerHTML;

                attachPaginationListeners();
            })
            .catch(error => console.error('Error:', error));
        }, 300);
    }

    function attachPaginationListeners() {
        document.querySelectorAll('#organismosPagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                aplicarFiltros(this.href);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const buscarInput = document.getElementById('buscar');
        const errorMsg = document.getElementById('error-msg-buscar');

        // Validación de caracteres especiales en búsqueda
        if(buscarInput) {
            buscarInput.addEventListener('input', function(e) {
                const originalValue = e.target.value;
                const cleanValue = originalValue.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, '');

                if (originalValue !== cleanValue) {
                    errorMsg.classList.remove('hidden');
                    setTimeout(() => errorMsg.classList.add('hidden'), 2500);
                }

                e.target.value = cleanValue.slice(0, 40);
            });
        }

        document.querySelectorAll('.filtro-auto').forEach(el => {
            el.addEventListener('change', () => aplicarFiltros());
        });

        document.querySelectorAll('.filtro-input').forEach(el => {
            el.addEventListener('keyup', () => aplicarFiltros());
        });

        document.getElementById('filtrosForm').addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltros();
        });

        attachPaginationListeners();
    });
</script>
@endpush
@endsection
