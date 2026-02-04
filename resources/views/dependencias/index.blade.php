@extends('layouts.base')

@section('title', 'Dependencias')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        🏛️ Dependencias
    </h1>
    <div class="flex gap-2">
        <a href="{{ route('dependencias.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nueva Dependencia
        </a>
        <a href="{{ route('responsables.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
            + Nuevo Responsable
        </a>
    </div>
</div>

{{-- Mensajes de éxito --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Panel de Filtros Avanzado --}}
<div class="mb-6 bg-white shadow rounded-lg p-4 space-y-4">
    <form action="{{ route('dependencias.index') }}" method="GET" class="space-y-4" id="filtrosForm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Filtro General con validación --}}
            <div class="flex flex-col">
                <label for="search" class="text-sm font-medium text-gray-700 mb-1">Búsqueda rápida</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       maxlength="40"
                       placeholder="Código o nombre (máx. 40 caracteres)..."
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                <p id="error-msg" class="text-red-500 text-xs mt-1 hidden font-semibold">
                    ⚠️ Solo se permiten letras, números y espacios.
                </p>
            </div>

            {{-- Filtro por Unidad Administradora --}}
            <div class="flex flex-col">
                <label for="unidad_id" class="text-sm font-medium text-gray-700 mb-1">Unidad Administradora</label>
                <select name="unidad_id" id="unidad_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todas las unidades</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" @selected(request('unidad_id') == $unidad->id)>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Responsable --}}
            <div class="flex flex-col">
                <label for="responsable_id" class="text-sm font-medium text-gray-700 mb-1">Responsable</label>
                <select name="responsable_id" id="responsable_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos los responsables</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" @selected(request('responsable_id') == $resp->id)>
                            {{ $resp->nombre }} ({{ $resp->cedula }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('dependencias.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                Limpiar Filtros
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-bold">
                Aplicar
            </button>
        </div>
    </form>
</div>

{{-- Chips de Filtros Activos --}}
<div id="activeFiltersContainer">
    @php
        $params = request()->only(['search', 'unidad_id', 'responsable_id']);
        $activeFilters = collect($params)->filter();
    @endphp
    @if($activeFilters->isNotEmpty())
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-gray-600">Filtrado por:</span>
            @foreach($activeFilters as $key => $value)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    <span class="font-bold mr-1">
                        {{ $key == 'search' ? 'Búsqueda' : ($key == 'unidad_id' ? 'Unidad' : 'Responsable') }}:
                    </span>
                    @php
                        $display = $value;
                        if($key == 'unidad_id') $display = $unidades->firstWhere('id', $value)->nombre ?? $value;
                        if($key == 'responsable_id') $display = $responsables->firstWhere('id', $value)->nombre ?? $value;
                    @endphp
                    {{ $display }}
                    <a href="{{ route('dependencias.index', request()->except($key)) }}" class="ml-2 hover:text-red-500">×</a>
                </span>
            @endforeach
        </div>
    @endif
</div>

{{-- Tabla de Dependencias --}}
<div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100" id="tablaDependencias">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad Adm.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bienes</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dependencias as $dep)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-blue-600 font-mono">{{ $dep->codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $dep->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $dep->unidadAdministradora->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($dep->responsable)
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900">{{ $dep->responsable->nombre }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $dep->responsable->cedula }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic">No asignado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($dep->bienes_count > 0)
                                <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                    {{ $dep->bienes_count }} items
                                </span>
                            @else
                                <span class="text-gray-400 italic">0 bienes</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @include('components.action-buttons', [
                                'resource' => 'dependencias',
                                'model' => $dep,
                                'canDelete' => false
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 italic">
                            No se encontraron dependencias con los criterios seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-6" id="paginationLinks">
    {{ $dependencias->links() }}
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const errorMsg = document.getElementById('error-msg');
        const form = document.getElementById('filtrosForm');

        // 1. Validación de Caracteres Especiales y Límite de 40
        searchInput.addEventListener('input', function(e) {
            const originalValue = e.target.value;
            const cleanValue = originalValue.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, '');

            if (originalValue !== cleanValue) {
                errorMsg.classList.remove('hidden');
                setTimeout(() => errorMsg.classList.add('hidden'), 2500);
            }

            e.target.value = cleanValue.slice(0, 40);
        });

        // 2. Disparadores para filtros
        searchInput.addEventListener('keyup', updateTable);
        
        document.querySelectorAll('.filtro-auto').forEach(el => {
            if(el.tagName === 'SELECT') el.addEventListener('change', updateTable);
        });

        // 3. Función AJAX para actualización fluida
        function updateTable(url = null) {
            const formData = new URLSearchParams(new FormData(form));
            const fetchUrl = url || `${form.action}?${formData.toString()}`;

            window.history.pushState(null, '', fetchUrl);

            fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    document.getElementById('tablaDependencias').innerHTML = doc.getElementById('tablaDependencias').innerHTML;
                    document.getElementById('paginationLinks').innerHTML = doc.getElementById('paginationLinks').innerHTML;
                    document.getElementById('activeFiltersContainer').innerHTML = doc.getElementById('activeFiltersContainer').innerHTML;

                    attachPagination();
                });
        }

        function attachPagination() {
            document.querySelectorAll('#paginationLinks a').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    updateTable(link.href);
                });
            });
        }

        attachPagination();
    });
</script>
@endpush
@endsection
