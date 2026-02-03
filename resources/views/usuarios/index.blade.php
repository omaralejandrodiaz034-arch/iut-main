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
    <form action="{{ route('usuarios.index') }}" method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="buscar" class="block text-sm font-semibold text-gray-700 mb-2">Búsqueda General</label>
            <input type="text" name="buscar" id="buscar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Nombre, cédula..." value="{{ $validated['buscar'] ?? '' }}">
        </div>
        <div>
            <label for="cedula" class="block text-sm font-semibold text-gray-700 mb-2">Cédula</label>
            <input type="text" name="cedula" id="cedula" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                   placeholder="V-25.123.123" value="{{ $validated['cedula'] ?? '' }}">
            <p id="error-cedula" class="text-red-500 text-[10px] mt-1 hidden font-bold">Solo se permiten números.</p>
        </div>
        <div>
            <label for="correo" class="block text-sm font-semibold text-gray-700 mb-2">Correo</label>
            <input type="email" name="correo" id="correo" 
                   maxlength="40"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="correo@ejemplo.com" value="{{ $validated['correo'] ?? '' }}">
            <p id="error-correo" class="text-red-500 text-[10px] mt-1 hidden font-bold">Formato de correo inválido.</p>
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

{{-- Tabla de resultados --}}
<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Cédula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre y Apellido</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Correo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Rol</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $usuario->cedula }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $usuario->nombre_completo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $usuario->correo }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $usuario->rol->nombre ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($usuario->activo)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Activo</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right space-x-2">
                        @include('components.action-buttons', [
                            'resource' => 'usuarios',
                            'model' => $usuario,
                            'canDelete' => auth()->user()->canDeleteUser($usuario),
                            'confirm' => '¿Estás seguro?',
                            'label' => $usuario->nombre_completo
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay usuarios registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cedulaInput = document.getElementById('cedula');
        const errorCedula = document.getElementById('error-cedula');
        const correoInput = document.getElementById('correo');
        const errorCorreo = document.getElementById('error-correo');
        const filterForm = document.getElementById('filterForm');

        // Lógica de Máscara de Cédula V-00.000.000
        cedulaInput.addEventListener('input', function (e) {
            let value = e.target.value;
            
            // Extraer solo los dígitos
            let digits = value.replace(/\D/g, '');
            
            // Mostrar error si el usuario intentó meter letras
            if (value.replace(/[Vv\-\.]/g, '').match(/\D/)) {
                errorCedula.classList.remove('hidden');
                setTimeout(() => errorCedula.classList.add('hidden'), 2000);
            }

            // Limitar a 8 dígitos de cédula
            digits = digits.slice(0, 8);

            // Construir el formato
            let formatted = "";
            if (digits.length > 0) {
                formatted = "V-";
                if (digits.length <= 2) {
                    formatted += digits;
                } else if (digits.length <= 5) {
                    formatted += digits.slice(0, 2) + "." + digits.slice(2);
                } else {
                    formatted += digits.slice(0, 2) + "." + digits.slice(2, 5) + "." + digits.slice(5);
                }
            }
            
            e.target.value = formatted;
        });

        // Correo: Validación y Máximo 40
        correoInput.addEventListener('blur', function (e) {
            const emailValue = e.target.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailValue !== "" && !emailRegex.test(emailValue)) {
                errorCorreo.classList.remove('hidden');
                correoInput.classList.add('border-red-500');
            } else {
                errorCorreo.classList.add('hidden');
                correoInput.classList.remove('border-red-500');
            }
        });

        // Prevenir envío con errores
        filterForm.addEventListener('submit', function (e) {
            const emailValue = correoInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailValue !== "" && !emailRegex.test(emailValue)) {
                e.preventDefault();
                errorCorreo.classList.remove('hidden');
            }
        });
    });
</script>
@endsection