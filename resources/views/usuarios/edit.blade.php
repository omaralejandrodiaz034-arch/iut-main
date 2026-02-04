@extends('layouts.base')

@section('title', 'Editar Usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Usuario</h1>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded">
                    <ul class="text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="space-y-6" id="edit-user-form">
                @csrf
                @method('PUT')

                {{-- Cédula --}}
                <div>
                    <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula</label>
                    <input type="text" name="cedula" id="cedula" value="{{ old('cedula', $usuario->cedula) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 {{ !auth()->user()->isAdmin() ? 'bg-gray-100' : '' }}"
                        maxlength="15" @if(!auth()->user()->isAdmin()) readonly @endif>
                    <p id="error-cedula" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten
                        números.</p>
                    @if(!auth()->user()->isAdmin())
                        <p class="text-xs text-gray-500 mt-1 italic">Solo los administradores pueden modificar la cédula.</p>
                    @endif
                </div>

                {{-- Nombre y Apellido --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                            maxlength="30" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2">
                        <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo letras.
                        </p>
                    </div>

                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $usuario->apellido) }}"
                            maxlength="30" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2">
                        <p id="error-apellido" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo letras.
                        </p>
                    </div>
                </div>

                {{-- Correo --}}
                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                    <input type="email" name="correo" id="correo" value="{{ old('correo', $usuario->correo) }}"
                        maxlength="40" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2">
                    <p id="error-correo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Formato de correo
                        inválido.</p>
                </div>

                {{-- Contraseña con Visibilidad --}}
                <div class="relative">
                    <label for="hash_password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña (dejar
                        en blanco para no cambiar)</label>
                    <div class="relative">
                        <input type="password" name="hash_password" id="password"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 pr-12 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="••••••••" maxlength="30">

                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-blue-600 transition-colors">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Roles (Solo Admin) --}}
                @if(auth()->user()->isAdmin())
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Rol del Usuario</h3>
                        <select name="rol_id" id="rol_id" class="block w-full rounded-md border border-gray-300 px-3 py-2">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <div class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" value="1"
                            class="rounded border-gray-300 text-blue-600 shadow-sm" {{ $usuario->activo ? 'checked' : '' }}>
                        <label for="activo" class="ml-2 block text-sm text-gray-700">Usuario activo</label>
                    </div>
                </div>

                <div class="flex gap-4 border-t pt-6">
                    <button type="submit" id="guardar-btn"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('usuarios.index') }}"
                        class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Resultado --}}
    <div id="resultado-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
            <div id="modal-header" class="px-6 py-4 border-b">
                <h2 id="modal-title" class="text-xl font-bold"></h2>
            </div>
            <div class="px-6 py-6">
                <div class="flex items-center gap-4">
                    <div id="modal-icon" class="flex-shrink-0"></div>
                    <p id="modal-message" class="text-gray-700"></p>
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end">
                <button id="modal-btn" class="px-4 py-2 rounded-lg text-white font-medium"></button>
            </div>
        </div>
    </div>
@endsection

{{-- AQUI ESTÁ EL CAMBIO CLAVE: Abrir el push scripts --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('edit-user-form');
            const cedulaInput = document.getElementById('cedula');
            const nombreInput = document.getElementById('nombre');
            const apellidoInput = document.getElementById('apellido');
            const correoInput = document.getElementById('correo');
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            // VISIBILIDAD DE CONTRASEÑA
            const eyeOpenPath = "M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z";
            const eyeClosedPath = "M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88";

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.querySelector('path').setAttribute('d', isPassword ? eyeClosedPath : eyeOpenPath);
                eyeIcon.classList.toggle('text-blue-600', isPassword);
            });

            // MÁSCARA CÉDULA
            if (!cedulaInput.readOnly) {
                cedulaInput.addEventListener('input', function (e) {
                    let digits = e.target.value.replace(/[^0-9]/g, '').slice(0, 8);
                    let formatted = digits.length > 0 ? 'V-' : '';
                    if (digits.length <= 2) formatted += digits;
                    else if (digits.length <= 5) formatted += digits.substring(0, 2) + '.' + digits.substring(2);
                    else formatted += digits.substring(0, 2) + '.' + digits.substring(2, 5) + '.' + digits.substring(5);
                    e.target.value = formatted;
                });
            }

            // RESTRICCIONES
            const restrict = (el, regex, errorId) => {
                const err = document.getElementById(errorId);
                el.addEventListener('input', (e) => {
                    let val = e.target.value.replace(regex, '');
                    if (e.target.value !== val) {
                        err.classList.remove('hidden');
                        setTimeout(() => err.classList.add('hidden'), 2000);
                    }
                    e.target.value = val;
                });
            };
            restrict(nombreInput, /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, 'error-nombre');
            restrict(apellidoInput, /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, 'error-apellido');

            // ENVÍO AJAX
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const guardarBtn = document.getElementById('guardar-btn');
                guardarBtn.disabled = true;

                try {
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (response.ok) {
                        mostrarModal('success', '¡Éxito!', 'Usuario actualizado correctamente.');
                    } else {
                        const data = await response.json();
                        mostrarModal('error', 'Error', data.message || 'Error al procesar.');
                        guardarBtn.disabled = false;
                    }
                } catch (err) {
                    mostrarModal('error', 'Error', 'Error de conexión.');
                    guardarBtn.disabled = false;
                }
            });

            function mostrarModal(tipo, titulo, mensaje) {
                const modal = document.getElementById('resultado-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalIcon = document.getElementById('modal-icon');
                const modalMessage = document.getElementById('modal-message');
                const modalHeader = document.getElementById('modal-header');
                const modalBtn = document.getElementById('modal-btn');

                modalTitle.textContent = titulo;
                modalMessage.textContent = mensaje;

                if (tipo === 'success') {
                    modalIcon.innerHTML = '✅';
                    modalHeader.className = 'px-6 py-4 border-b bg-green-50';
                    modalBtn.textContent = 'Ver Detalles';
                    modalBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg';
                    modalBtn.onclick = () => window.location.href = '{{ route("usuarios.show", $usuario->id) }}';
                } else {
                    modalIcon.innerHTML = '❌';
                    modalHeader.className = 'px-6 py-4 border-b bg-red-50';
                    modalBtn.textContent = 'Cerrar';
                    modalBtn.className = 'px-4 py-2 bg-gray-600 text-white rounded-lg';
                    modalBtn.onclick = () => modal.style.display = 'none';
                }
                modal.style.display = 'flex';
            }
        });
    </script>
@endpush