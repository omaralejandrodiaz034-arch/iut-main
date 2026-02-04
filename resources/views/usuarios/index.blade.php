@extends('layouts.base')

@section('title', 'Usuarios')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">👥 Usuarios</h1>
    <a href="{{ route('usuarios.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Nuevo Usuario
    </a>
</div>

{{-- Filtros Avanzados --}}
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtrar Usuarios</h2>
    <form action="{{ route('usuarios.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="buscar" class="block text-sm font-semibold text-gray-700 mb-2">Búsqueda General</label>
            <input type="text" name="buscar" id="buscar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Nombre, cédula..." value="{{ $validated['buscar'] ?? '' }}">
        </div>
        <div>
            <label for="cedula" class="block text-sm font-semibold text-gray-700 mb-2">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="V-XX.XXX.XXX" value="{{ $validated['cedula'] ?? '' }}">
        </div>
        <div>
            <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo</label>
            <input type="email" name="correo" id="correo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="correo@ejemplo.com" value="{{ $validated['correo'] ?? '' }}">
        </div>
        <div>
            <label for="rol_id" class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
            <select name="rol_id" id="rol_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los roles</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" {{ isset($validated['rol_id']) && $validated['rol_id'] == $rol->id ? 'selected' : '' }}>
                        {{ $rol->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="activo" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
            <select name="activo" id="activo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="1" {{ isset($validated['activo']) && $validated['activo'] === true ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ isset($validated['activo']) && $validated['activo'] === false ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">
                🔎 Buscar
            </button>
            <a href="{{ route('usuarios.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400">
                ✕ Limpiar
            </a>
        </div>
    </form>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cédula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre y Apellido</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($usuarios as $usuario)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-6 py-4 text-sm font-semibold text-blue-600 font-mono">{{ $usuario->cedula }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $usuario->nombre_completo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $usuario->correo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $usuario->rol->nombre ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($usuario->is_admin)
                            <span class="px-2.5 py-1 text-xs font-bold text-purple-800 bg-purple-100 rounded-full">Administrador</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-full">Usuario</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($usuario->activo)
                            <span class="px-2.5 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full">Activo</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold text-red-800 bg-red-100 rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right space-x-2">
                        @include('components.action-buttons', [
                            'resource' => 'usuarios',
                            'model' => $usuario,
                            'canDelete' => auth()->user()->canDeleteUser($usuario),
                            'confirm' => '¿Estás seguro? No podrás deshacer esta acción.',
                            'label' => $usuario->nombre_completo
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 italic">No hay usuarios registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($usuarios->hasPages())
    <div class="mt-6">
        {{ $usuarios->links() }}
    </div>
@endif
@endsection
