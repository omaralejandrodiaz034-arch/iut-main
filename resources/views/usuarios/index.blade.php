@extends('layouts.base')

@section('title', 'Usuarios')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Usuarios']]" />
@endpush
<div class="space-y-6 md:space-y-8">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
            <span class="text-4xl drop-shadow-sm">👥</span>
            Usuarios del Sistema
        </h1>

        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-sm transition-all hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Usuario
        </a>
    </div>

    <!-- Mensaje de éxito (si existiera) -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Panel de filtros avanzados -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Filtros de búsqueda</h2>
        </div>

        <form action="{{ route('usuarios.index') }}" method="GET" id="filterForm" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Búsqueda general -->
            <div>
                <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1.5">Búsqueda general</label>
                <input type="text" name="buscar" id="buscar"
                       value="{{ request('buscar') ?? '' }}"
                       placeholder="Nombre, apellido, cédula..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
            </div>

            <!-- Cédula -->
            <div>
                <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Cédula</label>
                <input type="text" name="cedula" id="cedula"
                       value="{{ request('cedula') ?? '' }}"
                       placeholder="V-12.345.678 o E-..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all font-mono filtro-auto">
                <p id="error-cedula" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                    Solo números y formato V-/E- permitido
                </p>
            </div>

            <!-- Correo -->
            <div>
                <label for="correo" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                <input type="email" name="correo" id="correo" maxlength="40"
                       value="{{ request('correo') ?? '' }}"
                       placeholder="correo@ejemplo.com"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                <p id="error-correo" class="mt-1.5 text-xs text-red-600 font-medium hidden">
                    Formato de correo inválido
                </p>
            </div>

            <!-- Rol -->
            <div>
                <label for="rol_id" class="block text-sm font-medium text-gray-700 mb-1.5">Rol</label>
                <select name="rol_id" id="rol_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ request('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div class="md:col-span-2 lg:col-span-1">
                <label for="activo" class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                <select name="activo" id="activo"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 transition-all filtro-auto">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <!-- Botones de acción -->
            <div class="md:col-span-2 lg:col-span-4 flex flex-col sm:flex-row sm:justify-end gap-3 pt-2">
                <button type="submit"
                        class="px-8 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                    Buscar
                </button>
                <a href="{{ route('usuarios.index') }}"
                   class="px-8 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium text-center">
                    Limpiar filtros
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cédula</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre y Apellido</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Correo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                            <td class="px-6 py-5 text-sm font-mono font-semibold text-indigo-700">
                                {{ $usuario->cedula }}
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-gray-900">
                                {{ $usuario->nombre_completo }}
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-700 truncate max-w-xs">
                                {{ $usuario->correo }}
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-700">
                                {{ $usuario->rol?->nombre ?? 'Sin rol' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($usuario->is_admin)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                        Administrador
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        Usuario
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($usuario->activo)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap space-x-2">
                                @include('components.action-buttons', [
                                    'resource' => 'usuarios',
                                    'model' => $usuario,
                                    'canDelete' => auth()->user()->canDeleteUser($usuario),
                                    'confirm' => '¿Estás seguro de eliminar a ' . $usuario->nombre_completo . '?',
                                    'label' => $usuario->nombre_completo
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500 italic text-base">
                                No se encontraron usuarios con los criterios seleccionados.
                                <p class="mt-2 text-sm text-gray-400">
                                    Prueba ajustando los filtros o registra un nuevo usuario.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación (si la tienes implementada) -->
    <div class="mt-6 flex justify-center">
        {{ $usuarios->links('pagination::tailwind') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
// Validación y formateo de cédula (V- o E- + puntos)
document.addEventListener('DOMContentLoaded', () => {
    const cedulaInput = document.getElementById('cedula');
    const errorCedula = document.getElementById('error-cedula');
    const correoInput = document.getElementById('correo');
    const errorCorreo = document.getElementById('error-correo');
    const form = document.getElementById('filterForm');

    if (cedulaInput) {
        cedulaInput.addEventListener('input', function(e) {
            let pos = this.selectionStart;
            let val = this.value.toUpperCase().replace(/[^VE0-9-]/gi, '');

            // Forzar prefijo V- o E-
            if (val && !val.match(/^[VE]-/)) {
                val = 'V-' + val.replace(/[^0-9]/g, '');
            }

            let digits = val.replace(/[^0-9]/g, '').slice(0, 8);
            let formatted = digits ? (val.startsWith('E-') ? 'E-' : 'V-') : '';

            if (digits.length > 0) {
                if (digits.length <= 2) {
                    formatted += digits;
                } else if (digits.length <= 5) {
                    formatted += digits.slice(0,2) + '.' + digits.slice(2);
                } else {
                    formatted += digits.slice(0,2) + '.' + digits.slice(2,5) + '.' + digits.slice(5);
                }
            }

            this.value = formatted;

            // Restaurar posición del cursor
            let diff = val.length - formatted.length;
            this.setSelectionRange(pos - diff, pos - diff);

            // Feedback de error si intenta letras inválidas
            if (e.inputType === 'insertText' && /[a-zA-Z]/.test(e.data) && !['V','E','-'].includes(e.data.toUpperCase())) {
                if (errorCedula) {
                    errorCedula.classList.remove('hidden');
                    setTimeout(() => errorCedula.classList.add('hidden'), 2200);
                }
            }
        });
    }

    if (correoInput) {
        const validarCorreo = () => {
            const val = correoInput.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (val && !regex.test(val)) {
                errorCorreo?.classList.remove('hidden');
                correoInput.classList.add('border-red-500', 'focus:border-red-500');
            } else {
                errorCorreo?.classList.add('hidden');
                correoInput.classList.remove('border-red-500');
            }
        };

        correoInput.addEventListener('input', validarCorreo);
        correoInput.addEventListener('blur', validarCorreo);
    }

    if (form) {
        form.addEventListener('submit', e => {
            // Validar correo antes de enviar
            if (correoInput?.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correoInput.value.trim())) {
                e.preventDefault();
                errorCorreo?.classList.remove('hidden');
                correoInput?.focus();
            }
        });
    }
});
</script>
@endpush
