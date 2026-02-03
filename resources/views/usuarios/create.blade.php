@extends('layouts.base')

@section('title', 'Crear Usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear Nuevo Usuario</h1>

            @if(!auth()->user()->isAdmin())
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-lg">
                    <p class="text-sm font-semibold">ℹ Solo puedes crear usuarios con privilegios normales. Para perfiles
                        administrativos, contacta al administrador principal.</p>
                </div>
            @endif

            <form action="{{ route('usuarios.store') }}" method="POST" id="usuarioForm" class="space-y-6">
                @csrf

                {{-- Campo Cédula --}}
                <div>
                    <x-form-input name="cedula" id="cedula" label="Cédula (Formato: V-XX.XXX.XXX)" :value="old('cedula')"
                        placeholder="V-12.345.678" maxlength="15" required />
                    @error('cedula')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <p id="error-cedula" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten
                        números.</p>
                </div>

                {{-- Campos Nombre y Apellido --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-form-input name="nombre" id="nombre" label="Nombre" :value="old('nombre')" placeholder="Ej: Juan"
                            maxlength="30" required />
                        @error('nombre')
                            <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                        @enderror
                        <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se
                            permiten letras.</p>
                    </div>

                    <div>
                        <x-form-input name="apellido" id="apellido" label="Apellido" :value="old('apellido')"
                            placeholder="Ej: Pérez" maxlength="30" required />
                        @error('apellido')
                            <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                        @enderror
                        <p id="error-apellido" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se
                            permiten letras.</p>
                    </div>
                </div>

                {{-- Campo Correo --}}
                <div>
                    <x-form-input name="correo" id="correo" label="Correo Electrónico" type="email" :value="old('correo')"
                        placeholder="usuario@ejemplo.com" maxlength="40" required />
                    @error('correo')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <p id="error-correo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Formato de correo
                        inválido.</p>
                </div>

                {{-- Campo Contraseña con Icono Ajustado --}}
                <div class="relative">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña (mínimo 8
                        caracteres)</label>
                    <div class="relative">
                        <input name="hash_password" type="password" id="password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="••••••••" maxlength="30" required>

                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-blue-600 transition-colors">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Máximo 30 caracteres.</p>
                    @error('hash_password')
                        <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Roles y Estado --}}
                @if(auth()->user()->isAdmin())
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label for="rol_id_select" class="block text-sm font-semibold text-gray-700 mb-2">Rol del
                            Usuario</label>
                        <select name="rol_id" id="rol_id_select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}" {{ (old('rol_id') == $rol->id) ? 'selected' : '' }}>{{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="is_admin" id="is_admin" value="0">
                @else
                    <input type="hidden" name="rol_id"
                        value="{{ old('rol_id') ?: \App\Models\Rol::where('nombre', 'Usuario Normal')->value('id') }}">
                @endif

                <div class="flex items-center p-2 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <input type="checkbox" name="activo" id="activo" value="1" class="w-5 h-5 rounded text-blue-600" {{ old('activo', true) ? 'checked' : '' }}>
                    <label for="activo" class="ml-3 block text-sm font-semibold text-gray-700">Habilitar acceso al
                        sistema</label>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('usuarios.index') }}"
                        class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400">✗
                        Cancelar</a>
                    <button type="submit" id="guardar-btn"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg">✓
                        Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('usuarioForm');

            // 1. VISIBILIDAD DE CONTRASEÑA (Icono y Lógica)
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            const eyeOpenPath = "M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z";
            const eyeClosedPath = "M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88";

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.querySelector('path').setAttribute('d', isPassword ? eyeClosedPath : eyeOpenPath);
                eyeIcon.classList.toggle('text-blue-600', isPassword);
            });

            // 2. MÁSCARA DE CÉDULA
            const cedulaInput = document.getElementById('cedula');
            cedulaInput.addEventListener('input', function (e) {
                let digits = e.target.value.replace(/[^0-9]/g, '').slice(0, 8);
                let formatted = digits.length > 0 ? 'V-' : '';
                if (digits.length <= 2) formatted += digits;
                else if (digits.length <= 5) formatted += digits.substring(0, 2) + '.' + digits.substring(2);
                else formatted += digits.substring(0, 2) + '.' + digits.substring(2, 5) + '.' + digits.substring(5);
                e.target.value = formatted;
            });

            // 3. RESTRICCIÓN LETRAS (NOMBRE/APELLIDO)
            const validateLetters = (id, errorId) => {
                const input = document.getElementById(id);
                const error = document.getElementById(errorId);
                input.addEventListener('input', function (e) {
                    let filtered = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').slice(0, 30);
                    if (e.target.value !== filtered) {
                        error.classList.remove('hidden');
                        setTimeout(() => error.classList.add('hidden'), 2000);
                    }
                    e.target.value = filtered;
                });
            };
            validateLetters('nombre', 'error-nombre');
            validateLetters('apellido', 'error-apellido');

            // 4. VALIDACIÓN CORREO
            const correoInput = document.getElementById('correo');
            correoInput.addEventListener('blur', function () {
                const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
                document.getElementById('error-correo').classList.toggle('hidden', valid || this.value === "");
            });

            // 5. SINCRONIZAR IS_ADMIN
            const rolSelect = document.getElementById('rol_id_select');
            if (rolSelect) {
                rolSelect.addEventListener('change', function () {
                    document.getElementById('is_admin').value = (this.options[this.selectedIndex].text.trim() === 'Administrador') ? '1' : '0';
                });
                rolSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush