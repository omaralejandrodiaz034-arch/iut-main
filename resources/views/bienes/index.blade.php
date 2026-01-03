@extends('layouts.base')

@section('title', 'Bienes')

@section('content')
{{-- resources/views/bienes/index.blade.php --}}

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        📦 Bienes
    </h1>
    <div class="flex gap-4">
        {{-- Botón para ir a la galería completa --}}
        <a href="{{ route('bienes.galeria') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
            🖼️ Ver Galería Completa
        </a>

        {{-- Botón existente para crear nuevo bien --}}
        <a href="{{ route('bienes.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nuevo
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
<div class="mb-6 bg-white shadow rounded-lg p-4 space-y-4">
    <form action="{{ route('bienes.index') }}" method="GET" class="space-y-4" id="filtrosForm">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="search" class="text-sm font-medium text-gray-700 mb-1">Búsqueda rápida</label>
                {{-- Agregado filtro-auto y filtro-input --}}
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Código, descripción o ubicación"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto filtro-input">
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="organismo_id" class="text-sm font-medium text-gray-700 mb-1">Organismo</label>
                {{-- Agregado filtro-auto --}}
                <select name="organismo_id" id="organismo_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos</option>
                    @foreach($organismos as $organismo)
                        <option value="{{ $organismo->id }}"
                            @selected(($filters['organismo_id'] ?? null) == $organismo->id)>
                            {{ $organismo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="unidad_id" class="text-sm font-medium text-gray-700 mb-1">Unidad Administradora</label>
                {{-- Agregado filtro-auto --}}
                <select name="unidad_id" id="unidad_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todas</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}"
                            @selected(($filters['unidad_id'] ?? null) == $unidad->id)>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="dependencias" class="text-sm font-medium text-gray-700 mb-1">Dependencias</label>
                {{-- Agregado filtro-auto --}}
                <select name="dependencias[]" id="dependencias" multiple size="5"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    @foreach($dependencias as $dependencia)
                        <option value="{{ $dependencia->id }}"
                            @selected(collect($filters['dependencias'] ?? [])->contains($dependencia->id))>
                            {{ $dependencia->nombre }} ({{ $dependencia->unidadAdministradora->nombre ?? '—' }})
                        </option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Ctrl/⌘ + click para seleccionar múltiples opciones.</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-700 mb-1">Estado del Bien</span>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($estados as $valor => $label)
                        <label class="inline-flex items-center text-sm text-gray-700">
                            {{-- Agregado filtro-auto --}}
                            <input type="checkbox" name="estado[]" value="{{ $valor }}"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 filtro-auto"
                                   @checked(collect($filters['estado'] ?? [])->contains($valor))>
                            <span class="ml-2">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col">
                <label for="fecha_desde" class="text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                {{-- Agregado filtro-auto --}}
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
            </div>
            <div class="flex flex-col">
                <label for="fecha_hasta" class="text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                {{-- Agregado filtro-auto --}}
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
            </div>
        </div>

        {{-- Filtro por Tipo de Bien --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="tipo_bien" class="text-sm font-medium text-gray-700 mb-1">Tipo de Bien</label>
                <select name="tipo_bien" id="tipo_bien"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 filtro-auto">
                    <option value="">Todos los Tipos</option>
                    @foreach(\App\Enums\TipoBien::cases() as $tipo)
                        <option value="{{ $tipo->value }}" {{ request('tipo_bien') == $tipo->value ? 'selected' : '' }}>{{ $tipo->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('bienes.index') }}"
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

@php
    $activeFilters = collect($filters ?? [])->filter(function ($value, $key) {
        if (is_array($value)) {
            return ! empty($value);
        }

        return filled($value);
    });
@endphp

{{-- ID para contenedor de filtros activos --}}
<div id="activeFiltersContainer">
@if($activeFilters->isNotEmpty())
    <div class="mb-4 flex flex-wrap items-center gap-2 text-sm">
        <span class="font-medium text-gray-700">Filtros activos:</span>
        @foreach($activeFilters as $key => $value)
            @php
                $label = match($key) {
                    'search' => 'Búsqueda',
                    'codigo' => 'Código',
                    'descripcion' => 'Descripción',
                    'organismo_id' => 'Organismo',
                    'unidad_id' => 'Unidad',
                    'dependencias' => 'Dependencias',
                    'estado' => 'Estado',
                    'fecha_desde' => 'Desde',
                    'fecha_hasta' => 'Hasta',
                    'tipo_bien' => 'Tipo de Bien',
                    default => ucfirst(str_replace('_', ' ', $key)),
                };

                $display = $value;

                if ($key === 'organismo_id') {
                    $display = optional($organismos->firstWhere('id', $value))->nombre ?? $value;
                } elseif ($key === 'unidad_id') {
                    $display = optional($unidades->firstWhere('id', $value))->nombre ?? $value;
                } elseif ($key === 'dependencias') {
                    $display = collect($value)->map(function ($id) use ($dependencias) {
                        return optional($dependencias->firstWhere('id', $id))->nombre ?? $id;
                    })->implode(', ');
                } elseif ($key === 'estado') {
                    $display = collect($value)->map(fn ($estado) => $estados[$estado] ?? $estado)->implode(', ');
                } elseif ($key === 'tipo_bien') {
                    $tipoBienEnum = \App\Enums\TipoBien::tryFrom($value);
                    $display = $tipoBienEnum?->label() ?? $value;
                }

                // Generar nuevo query sin este filtro
                $querySinFiltro = collect($filters)->forget($key)->toArray();
            @endphp

            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">
                {{ $label }}: <span class="ml-1 font-medium">{{ $display }}</span>
                <a href="{{ route('bienes.index', $querySinFiltro) }}"
                   class="ml-2 text-indigo-500 hover:text-red-600 font-bold" title="Quitar filtro">
                    ×
                </a>
            </span>
        @endforeach
    </div>
@endif
</div>


{{-- Tabla --}}
{{-- ID para contenedor de la tabla --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden" id="tablaBienes">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @php
    $currentSort = request('sort', 'fecha_registro');
    $currentDirection = request('direction', 'desc');

                function sortLink($column, $label) {
                    $direction = (request('sort') === $column && request('direction') === 'asc') ? 'desc' : 'asc';
                    return route('bienes.index', array_merge(request()->query(), ['sort' => $column, 'direction' => $direction]));
                }
            @endphp

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ sortLink('codigo', 'Código') }}" class="flex items-center gap-1">
                            Código
                            @if($currentSort === 'codigo')
                                <span>{{ $currentDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ sortLink('descripcion', 'Descripción') }}" class="flex items-center gap-1">
                            Descripción
                            @if($currentSort === 'descripcion')
                                <span>{{ $currentDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organismo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dependencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo de Bien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ sortLink('precio', 'Precio') }}" class="flex items-center gap-1">
                            Precio
                            @if($currentSort === 'precio')
                                <span>{{ $currentDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ sortLink('estado', 'Estado') }}" class="flex items-center gap-1">
                            Estado
                            @if($currentSort === 'estado')
                                <span>{{ $currentDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ sortLink('fecha_registro', 'Fecha Registro') }}" class="flex items-center gap-1">
                            Fecha Registro
                            @if($currentSort === 'fecha_registro')
                                <span>{{ $currentDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bienes as $bien)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $bien->codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $bien->descripcion }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bien->dependencia->unidadAdministradora->organismo->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bien->dependencia->unidadAdministradora->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bien->dependencia->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bien->dependencia->responsable->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $tipoBienLabel = $bien->tipo_bien?->label() ?? 'N/A';
                                $tipoBienColor = match($bien->tipo_bien?->value) {
                                    'ELECTRONICO' => 'bg-blue-100 text-blue-800',
                                    'INMUEBLE' => 'bg-amber-100 text-amber-800',
                                    'MOBILIARIO' => 'bg-purple-100 text-purple-800',
                                    'VEHICULO' => 'bg-red-100 text-red-800',
                                    'OTROS' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tipoBienColor }}">
                                {{ $tipoBienLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                            {{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                     @if($bien->fotografia && file_exists(public_path('storage/' . $bien->fotografia)))
                                <img src="{{ asset('storage/' . $bien->fotografia) }}"
                                     alt="Foto del bien"
                                     class="w-48 h-48 object-cover rounded-lg shadow">
                            @else
                                <span class="text-gray-500">Sin fotografía disponible</span>
                            @endif


                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $estadoValor = $bien->estado?->value;
                                $estadoLabel = $bien->estado?->label() ?? $estadoValor;
                                $estadoColor = match($estadoValor) {
                                    \App\Enums\EstadoBien::ACTIVO->value => 'bg-green-100 text-green-800',
                                    \App\Enums\EstadoBien::DANADO->value => 'bg-red-100 text-red-800',
                                    \App\Enums\EstadoBien::EN_REPARACION->value => 'bg-yellow-100 text-yellow-800',
                                    \App\Enums\EstadoBien::EN_CAMINO->value => 'bg-blue-100 text-blue-800',
                                    \App\Enums\EstadoBien::EXTRAVIADO->value => 'bg-gray-200 text-gray-900',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $estadoColor }}">
                                {{ $estadoLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $bien->ubicacion ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ optional($bien->fecha_registro)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right space-x-2">
                            @include('components.action-buttons', [
                                'resource' => 'bienes',
                                'model' => $bien,
                                'confirm' => "¿Seguro que deseas desincorporar este bien?",
                                'label' => $bien->codigo,
                                'buttonText' => 'Desincorporar'
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay bienes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
{{-- ID para contenedor de paginación --}}
<div class="mt-6" id="bienesPagination">
@if($bienes->hasPages())
        {{ $bienes->links() }}
@endif
</div>

@push('scripts')
<script>
    let fetchTimeout;

    /**
     * Realiza la petición AJAX para filtrar y actualiza solo los resultados.
     */
    function aplicarFiltros(url = null) {
        if (fetchTimeout) {
            clearTimeout(fetchTimeout);
        }

        // Definir un pequeño retraso (300ms) para inputs de texto
        fetchTimeout = setTimeout(() => {
            const form = document.getElementById('filtrosForm');
            const baseUrl = url || form.action;
            const formParams = new URLSearchParams(new FormData(form));
            const fetchUrl = baseUrl.split('?')[0] + '?' + formParams.toString();

            // Reemplazar la URL en el historial sin recargar
            window.history.pushState(null, '', fetchUrl);

            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en respuesta de red');
                return res.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Actualizar Tabla
                const nuevaTabla = doc.querySelector('#tablaBienes');
                const contenedorTabla = document.getElementById('tablaBienes');
                if (nuevaTabla && contenedorTabla) {
                    contenedorTabla.innerHTML = nuevaTabla.innerHTML;
                }

                // 2. Actualizar Paginación
                const nuevaPaginacion = doc.querySelector('#bienesPagination');
                const contenedorPaginacion = document.getElementById('bienesPagination');
                if (nuevaPaginacion && contenedorPaginacion) {
                    contenedorPaginacion.innerHTML = nuevaPaginacion.innerHTML;
                    attachPaginationListeners(); // Reasignar eventos a los nuevos links
                }

                // 3. Actualizar Filtros Activos (chips)
                const nuevosFiltros = doc.querySelector('#activeFiltersContainer');
                const contenedorFiltros = document.getElementById('activeFiltersContainer');
                if (contenedorFiltros) {
                    contenedorFiltros.innerHTML = nuevosFiltros ? nuevosFiltros.innerHTML : '';
                }
            })
            .catch(error => console.error('Error al filtrar:', error));

        }, 300);
    }

    /**
     * Intercepta los clicks en la paginación para usar AJAX
     */
    function attachPaginationListeners() {
        document.querySelectorAll('#bienesPagination a').forEach(link => {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });

        // También interceptar clicks en el ordenamiento de la tabla si es necesario
        document.querySelectorAll('#tablaBienes thead a').forEach(link => {
             link.addEventListener('click', function(e) {
                 e.preventDefault();
                 aplicarFiltros(this.href);
             });
        });
    }

    function handlePaginationClick(e) {
        e.preventDefault();
        aplicarFiltros(this.href);
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', () => {
        // Inputs/Selects que disparan filtro automático
        document.querySelectorAll('.filtro-auto').forEach(el => {
            el.addEventListener('change', () => aplicarFiltros());
        });

        // Inputs de texto que disparan al escribir (con delay)
        document.querySelectorAll('.filtro-input').forEach(el => {
            el.addEventListener('keyup', () => aplicarFiltros());
        });

        // Prevenir submit normal
        document.getElementById('filtrosForm').addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltros();
        });

        attachPaginationListeners();
    });

    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });
</script>
@endpush
@endsection




