@extends('layouts.base')

@section('title', 'Crear Usuario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear Nuevo Usuario</h1>

        {{-- Aviso para no-administradores --}}
        @if(!auth()->user()->isAdmin())
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-lg">
                <p class="text-sm font-semibold">ℹ Solo puedes crear usuarios con privilegios normales. Para perfiles administrativos, contacta al administrador principal.</p>
            </div>
        @endif

        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <x-form-input name="cedula" id="cedula" label="Cédula (Formato: V-XX.XXX.XXX)"
                    :value="old('cedula')" placeholder="V-12.345.678" maxlength="20" required
                    help="Debe comenzar con V-, seguido de números separados por puntos" />
                @error('cedula')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-form-input name="nombre" label="Nombre" :value="old('nombre')" placeholder="Ej: Juan" required />
                    @error('nombre')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-form-input name="apellido" label="Apellido" :value="old('apellido')" placeholder="Ej: Pérez" required />
                    @error('apellido')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <x-form-input name="correo" label="Correo Electrónico" type="email" :value="old('correo')" placeholder="usuario@ejemplo.com" required />
                @error('correo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-form-input name="hash_password" label="Contraseña (mínimo 8 caracteres)" type="password" id="password" placeholder="••••••••" required />
                @error('hash_password')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            @if(auth()->user()->isAdmin())
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label for="rol_id_select" class="block text-sm font-semibold text-gray-700 mb-2">Rol del Usuario</label>
                    <select name="rol_id" id="rol_id_select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ (old('rol_id') == $rol->id) ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                    @error('rol_id')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Campo oculto para manejar is_admin lógicamente --}}
                <input type="hidden" name="is_admin" id="is_admin" value="0">
            @else
                <input type="hidden" name="rol_id" value="{{ old('rol_id') ?: \App\Models\Rol::where('nombre', 'Usuario Normal')->value('id') }}">
            @endif

            <div class="flex items-center p-2 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <input type="checkbox" name="activo" id="activo" value="1"
                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer"
                       {{ old('activo', true) ? 'checked' : '' }}>
                <label for="activo" class="ml-3 block text-sm font-semibold text-gray-700 cursor-pointer">
                    Habilitar acceso al sistema para este usuario
                </label>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('usuarios.index') }}"
                   class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition duration-200">
                    ✗ Cancelar
                </a>
                <button type="submit" id="guardar-btn"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition duration-200">
                    ✓ Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lógica de Máscara de Cédula (V-XX.XXX.XXX)
    const cedulaInput = document.getElementById('cedula');

    cedulaInput.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        value = value.replace(/[^V0-9\-]/g, '');

        if (value.startsWith('V')) {
            const numbers = value.substring(1).replace(/[^0-9]/g, '');
            if (numbers.length === 0) value = 'V-';
            else if (numbers.length <= 2) value = 'V-' + numbers;
            else if (numbers.length <= 5) value = 'V-' + numbers.substring(0, 2) + '.' + numbers.substring(2);
            else if (numbers.length <= 8) value = 'V-' + numbers.substring(0, 2) + '.' + numbers.substring(2, 5) + '.' + numbers.substring(5);
            else value = 'V-' + numbers.substring(0, 2) + '.' + numbers.substring(2, 5) + '.' + numbers.substring(5, 8);
        } else {
            value = 'V-';
        }
        e.target.value = value;
    });

    // Sincronizar campo oculto is_admin según el rol
    const rolSelect = document.getElementById('rol_id_select');
    if(rolSelect) {
        rolSelect.addEventListener('change', function() {
            const isAdminInput = document.getElementById('is_admin');
            const selectedText = this.options[this.selectedIndex].text;
            isAdminInput.value = (selectedText === 'Administrador') ? '1' : '0';
        });
        // Disparar una vez al cargar para asegurar consistencia
        rolSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
