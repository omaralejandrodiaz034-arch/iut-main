@extends('layouts.base')

@section('title', 'Bienes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes']]" />
@endpush
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        <x-heroicon-o-cube class="w-8 h-8 text-blue-600" /> Bienes
    </h1>

    <div class="flex gap-3">
        <a href="{{ route('bienes.galeria') }}"
           class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Galería</span>
        </a>

        <a href="{{ route('bienes.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Bien</span>
        </a>

        <a href="{{ route('bienes.reporte', request()->query()) }}"
           class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span>PDF</span>
        </a>

        <a href="{{ route('graficas', request()->query()) }}"
           title="Ver gráficas basadas en los filtros actuales"
           class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M6 10h10M6 6h.01M6 14h.01M6 18h.01" />
            </svg>
            <span>Gráficas</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Panel de Filtros -->
<div class="mb-6 bg-white border border-slate-200 shadow-sm rounded-lg p-4 space-y-4">
    <form action="{{ route('bienes.index') }}" method="GET" class="space-y-4" id="filtrosForm">

        <!-- Primera fila -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Búsqueda rápida -->
            <div class="flex flex-col">
                <label for="search" class="text-sm font-medium text-gray-700 mb-1">Búsqueda rápida</label>
                <input type="text" name="search" id="search"
                       value="{{ old('search', request('search')) }}"
                       maxlength="40"
                       pattern="[a-zA-Z0-9\s\-.]*"
                       title="Solo letras, números, espacios, guiones y puntos"
                       placeholder="Código o descripción..."
                       class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                <p id="error-msg-search" class="text-red-600 text-xs mt-1 hidden font-medium">
                    ⚠️ Solo se permiten letras, números, espacios, guiones y puntos.
                </p>
            </div>

            <!-- Tipo de Bien -->
            <div class="flex flex-col">
                <label for="tipo_bien" class="text-sm font-medium text-gray-700 mb-1">Tipo de Bien</label>
                <select name="tipo_bien" id="tipo_bien"
                        class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos los tipos</option>
                    @foreach($tiposBien as $valor => $label)
                        <option value="{{ $valor }}" {{ old('tipo_bien', request('tipo_bien')) == $valor ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Organismo -->
            <div class="flex flex-col">
                <label for="organismo_id" class="text-sm font-medium text-gray-700 mb-1">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos los Organismos</option>
                    @foreach($organismos as $organismo)
                        <option value="{{ $organismo->id }}" {{ old('organismo_id', request('organismo_id')) == $organismo->id ? 'selected' : '' }}>
                            {{ $organismo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Unidad Administradora -->
            <div class="flex flex-col">
                <label for="unidad_id" class="text-sm font-medium text-gray-700 mb-1">Unidad Administradora</label>
                <select name="unidad_id" id="unidad_id"
                        class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todas las unidades</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_id', request('unidad_id')) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Segunda fila -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="fecha_desde" class="text-sm font-medium text-gray-700 mb-1">Fecha registro desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde"
                       value="{{ old('fecha_desde', request('fecha_desde')) }}"
                       max="{{ date('Y-m-d') }}"
                       class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
            </div>

            <div class="flex flex-col">
                <label for="fecha_hasta" class="text-sm font-medium text-gray-700 mb-1">Fecha registro hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                       value="{{ old('fecha_hasta', request('fecha_hasta')) }}"
                       max="{{ date('Y-m-d') }}"
                       class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                <p id="error-msg-fechas" class="text-red-600 text-xs mt-1 hidden font-medium">
                    La fecha "hasta" debe ser igual o posterior a "desde"
                </p>
            </div>

            <div class="flex flex-col">
                <label for="dependencias" class="text-sm font-medium text-gray-700 mb-1">Dependencia (Múltiple)</label>
                <select name="dependencias[]" id="dependencias" multiple
                        class="border border-gray-300 bg-white text-gray-900 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto text-sm h-24">
                    @foreach($dependencias as $dependencia)
                        <option value="{{ $dependencia->id }}"
                            {{ collect(old('dependencias', request('dependencias', [])))->contains($dependencia->id) ? 'selected' : '' }}>
                            {{ $dependencia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Estados -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-200">
            <div class="flex flex-wrap gap-4">
                <span class="text-sm font-medium text-gray-700 self-center">Estado:</span>
                @foreach($estados as $valor => $label)
                    <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer group">
                        <input type="checkbox" name="estado[]" value="{{ $valor }}"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 filtro-auto"
                               {{ collect(old('estado', request('estado', [])))->contains($valor) ? 'checked' : '' }}>
                        <span class="ml-2 group-hover:text-blue-600 transition">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('bienes.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                    Limpiar
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-bold">
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Chips de filtros activos -->
<div id="activeFiltersContainer" class="mb-5">
    @php
        $params = request()->only(['search', 'tipo_bien', 'organismo_id', 'unidad_id', 'fecha_desde', 'fecha_hasta', 'estado', 'dependencias']);
        $activeFilters = collect($params)->filter(fn($v) => filled($v) && $v !== [] && $v !== '');
    @endphp

    @if($activeFilters->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-gray-600 dark:text-gray-400">Filtros activos:</span>
            @foreach($activeFilters as $key => $value)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    <span class="font-semibold mr-1.5">
                        @switch($key)
                            @case('search') Búsqueda @break
                            @case('tipo_bien') Tipo @break
                            @case('organismo_id') Organismo @break
                            @case('unidad_id') Unidad @break
                            @case('fecha_desde') Desde @break
                            @case('fecha_hasta') Hasta @break
                            @case('estado') Estados @break
                            @case('dependencias') Dependencias @break
                            @default {{ ucfirst($key) }}
                        @endswitch:
                    </span>
                    @php
                        $display = is_array($value) ? (count($value) . ' seleccionadas') : $value;
                        if ($key === 'tipo_bien' && isset($tiposBien[$value])) $display = $tiposBien[$value];
                        if ($key === 'organismo_id') $display = $organismos->firstWhere('id', $value)?->nombre ?? $value;
                        if ($key === 'unidad_id') $display = $unidades->firstWhere('id', $value)?->nombre ?? $value;
                    @endphp
                    {{ $display }}
                    <a href="{{ route('bienes.index', request()->except($key)) }}"
                       class="ml-2 text-red-500 hover:text-red-700 font-bold">×</a>
                </span>
            @endforeach
        </div>
    @endif
</div>

<!-- Tabla -->
<div id="tablaBienesContainer" class="transition-opacity duration-300">
    @include('bienes.partials.table', ['bienes' => $bienes])
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filtrosForm');
    const container = document.getElementById('tablaBienesContainer');
    const searchInput = document.getElementById('search');
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');
    const errorSearch = document.getElementById('error-msg-search');
    const errorFechas = document.getElementById('error-msg-fechas');

    // Validación búsqueda en tiempo real
    searchInput.addEventListener('input', function () {
        const valor = this.value;
        const regex = /^[a-zA-Z0-9\s\-.]*$/;
        if (!regex.test(valor)) {
            this.value = valor.slice(0, -1);
            errorSearch.classList.remove('hidden');
        } else {
            errorSearch.classList.add('hidden');
        }
    });

    // Validación rango fechas
    function validarFechas() {
        if (fechaDesde.value && fechaHasta.value) {
            if (new Date(fechaDesde.value) > new Date(fechaHasta.value)) {
                errorFechas.classList.remove('hidden');
                fechaHasta.classList.add('border-red-500');
            } else {
                errorFechas.classList.add('hidden');
                fechaHasta.classList.remove('border-red-500');
            }
        } else {
            errorFechas.classList.add('hidden');
            fechaHasta.classList.remove('border-red-500');
        }
    }

    fechaDesde.addEventListener('change', validarFechas);
    fechaHasta.addEventListener('change', validarFechas);

    // AJAX para aplicar filtros y paginación
    const aplicarFiltros = () => {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.delete('page'); // reset página al filtrar

        const url = `${window.location.pathname}?${params}`;
        container.style.opacity = '0.4';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1';
                window.history.pushState({}, '', url);
            })
            .catch(err => {
                console.error(err);
                container.style.opacity = '1';
            });
    };

    // Cambio automático en selects, checkboxes, fechas
    document.querySelectorAll('.filtro-auto').forEach(el => {
        el.addEventListener('change', aplicarFiltros);
    });

    // Submit manual
    form.addEventListener('submit', e => {
        e.preventDefault();
        if (fechaDesde.value && fechaHasta.value && new Date(fechaDesde.value) > new Date(fechaHasta.value)) {
            alert('La fecha "hasta" debe ser igual o posterior a "desde".');
            return;
        }
        aplicarFiltros();
    });

    // Paginación con event delegation
    container.addEventListener('click', e => {
        const link = e.target.closest('.pagination a');
        if (!link) return;
        e.preventDefault();

        const url = new URL(link.href);
        const page = url.searchParams.get('page');

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        if (page) params.set('page', page);

        const finalUrl = `${window.location.pathname}?${params}`;
        aplicarFiltros(); // mejor: cargar con la nueva url
        // Para ser exactos:
        fetch(finalUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                container.innerHTML = html;
                window.history.pushState({}, '', finalUrl);
            });
    });
});
</script>
@endpush
@endsection
