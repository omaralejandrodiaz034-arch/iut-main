@extends('layouts.base')

@section('title', 'Bienes')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        📦 Bienes
    </h1>
    <a href="{{ route('bienes.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Nuevo
    </a>
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
                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Código, descripción o ubicación"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="organismo_id" class="text-sm font-medium text-gray-700 mb-1">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <select name="unidad_id" id="unidad_id"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <select name="dependencias[]" id="dependencias" multiple size="5"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            <input type="checkbox" name="estado[]" value="{{ $valor }}"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                   @checked(collect($filters['estado'] ?? [])->contains($valor))>
                            <span class="ml-2">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col">
                <label for="fecha_desde" class="text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex flex-col">
                <label for="fecha_hasta" class="text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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


{{-- Tabla --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organismo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dependencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Registro</th>
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
                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                            {{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($bien->fotografia)
                                <img
                                    src="{{ str_starts_with($bien->fotografia, 'http') ? $bien->fotografia : asset('storage/'.$bien->fotografia) }}"
                                    alt="Foto {{ $bien->codigo }}"
                                    class="w-14 h-14 object-cover rounded-md border border-gray-200"
                                >
                            @else
                                <span class="text-xs text-gray-400">Sin foto</span>
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
                                'confirm' => "¿Seguro que deseas eliminar este bien?",
                                'label' => $bien->codigo
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay bienes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
@if($bienes->hasPages())
    <div class="mt-6">
        {{ $bienes->links() }}
    </div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filtrosForm');
        const filtros = form.querySelectorAll('input, select');

        filtros.forEach(filtro => {
            filtro.addEventListener('change', () => {
                form.submit();
            });

            if (filtro.type === 'text') {
                let timeout;
                filtro.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => form.submit(), 400);
                });
            }
        });
    });
</script>
@endsection





