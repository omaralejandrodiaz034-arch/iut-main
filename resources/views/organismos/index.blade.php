@extends('layouts.base')

@section('title', 'Organismos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">🏢 Organismos</h1>
    <a href="{{ route('organismos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Nuevo
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

{{-- Filtros --}}
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtrar Organismos</h2>
    <form action="{{ route('organismos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="buscar" class="block text-sm font-semibold text-gray-700 mb-2">Búsqueda General</label>
            <input type="text" name="buscar" id="buscar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   placeholder="Código o nombre..." value="{{ $validated['buscar'] ?? '' }}">
        </div>
        <div>
            <label for="codigo" class="block text-sm font-semibold text-gray-700 mb-2">Código</label>
            <input type="text" name="codigo" id="codigo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   placeholder="Código..." value="{{ $validated['codigo'] ?? '' }}">
        </div>
        <div>
            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                   placeholder="Nombre..." value="{{ $validated['nombre'] ?? '' }}">
        </div>
        <div class="md:col-span-3 flex gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                🔎 Buscar
            </button>
            <a href="{{ route('organismos.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400">
                ✕ Limpiar
            </a>
        </div>
    </form>
</div>

{{-- Tabla de organismos --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($organismos as $organismo)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $organismo->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $organismo->codigo }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $organismo->nombre }}</td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    @include('components.action-buttons', [
                                        'resource' => 'organismos',
                                        'model' => $organismo,
                                        'confirm' => '¿Estás seguro?',
                                        'label' => $organismo->nombre
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay organismos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

@if($organismos->hasPages())
    <div class="mt-6">
        {{ $organismos->links() }}
    </div>
@endif
@endsection


