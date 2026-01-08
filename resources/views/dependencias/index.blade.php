@extends('layouts.base')

@section('title', 'Dependencias')

@section('content')
<div class="flex justify-between items-center mb-6">
    <!-- Reemplazo de emojis por Heroicons -->
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        <x-heroicon-o-link class="w-6 h-6 text-gray-500" /> Dependencias
    </h1>
    <div class="flex gap-2">
        <a href="{{ route('dependencias.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nueva
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

{{-- Buscador --}}
<div class="mb-4">
    <form action="{{ route('dependencias.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Buscar por código o nombre..."
               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Buscar
        </button>
    </form>
</div>

{{-- Tabla --}}
<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-24 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                <th class="w-64 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="w-64 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad Administradora</th>
                <th class="w-72 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                <th class="w-32 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bienes</th>
                <th class="w-40 px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($dependencias as $dep)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-4 text-sm text-gray-900 font-mono">{{ $dep->codigo }}</td>

                    <td class="px-4 py-4 text-sm text-gray-900 truncate" title="{{ $dep->nombre }}">
                        {{ $dep->nombre }}
                    </td>

                    <td class="px-4 py-4 text-sm text-gray-600 truncate" title="{{ $dep->unidadAdministradora->nombre ?? '-' }}">
                        {{ $dep->unidadAdministradora->nombre ?? '-' }}
                    </td>

                    <td class="px-4 py-4 text-sm text-gray-600 truncate" title="{{ $dep->responsable?->nombre }}">
                        @if($dep->responsable)
                            {{ $dep->responsable->nombre }}
                            @if($dep->responsable->cedula)
                                ({{ $dep->responsable->cedula }})
                            @endif
                            - {{ $dep->responsable->tipo->nombre ?? '' }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-4 text-sm text-gray-600">
                        @if($dep->bienes->count())
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                                {{ $dep->bienes->count() }} bienes
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-4 text-sm text-right space-x-2">
                        @include('components.action-buttons', [
                            'resource' => 'dependencias',
                            'model' => $dep,
                            'confirm' => "¿Seguro que deseas eliminar esta dependencia?",
                            'label' => $dep->nombre
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                        No hay dependencias registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
@if($dependencias->hasPages())
    <div class="mt-6">
        {{ $dependencias->links() }}
    </div>
@endif
@endsection






