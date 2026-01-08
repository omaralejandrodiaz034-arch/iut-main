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

        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
          <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula</label>
          <input type="text" name="cedula" id="cedula" value="{{ old('cedula', $usuario->cedula) }}"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              maxlength="20" @if(!auth()->user()->isAdmin()) readonly @endif>
                @error('cedula')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p id="cedula-error" class="text-sm text-red-600 mt-1" style="display:none;"></p>
                @if(!auth()->user()->isAdmin())
                    <p class="text-sm text-gray-500 mt-1">Solo los administradores pueden modificar la cédula.</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('nombre')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                    <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $usuario->apellido) }}"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('apellido')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                <input type="email" name="correo" id="correo" value="{{ old('correo', $usuario->correo) }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('correo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(auth()->user()->isAdmin())
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Rol del Usuario</h3>
                    <select name="rol_id" id="rol_id" class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('rol_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="hash_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="hash_password" id="hash_password"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="••••••••">
                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres si deseas cambiar la contraseña</p>
                @error('hash_password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Activo</label>
                <div class="flex items-center">
                    <input type="checkbox" name="activo" id="activo" value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           {{ $usuario->activo ? 'checked' : '' }}>
                    <label for="activo" class="ml-2 block text-sm text-gray-700">
                        Usuario activo
                    </label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" id="guardar-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Guardar Cambios
                </button>
                <a href="{{ route('usuarios.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Resultado -->
<div id="resultado-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Encabezado -->
        <div id="modal-header" class="px-6 py-4 border-b">
            <h2 id="modal-title" class="text-xl font-bold"></h2>
        </div>

        <!-- Contenido -->
        <div class="px-6 py-4">
            <div class="flex items-start gap-4">
                <div id="modal-icon" class="text-4xl flex-shrink-0"></div>
                <div>
                    <p id="modal-message" class="text-gray-700 text-sm"></p>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="px-6 py-4 border-t flex gap-3 justify-end">
            <button id="modal-redirect-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" onclick="redirigirADetalle()">
                Ver Detalles
            </button>
        </div>
    </div>
</div>

@endsection

<script>
    document.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();

        try {
            const formData = new FormData(this);
            const response = await fetch('{{ route("usuarios.update", $usuario->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                mostrarModal('success', '¡Éxito!', 'El usuario ha sido actualizado correctamente.');
                document.getElementById('guardar-btn').disabled = true;
            } else if (response.status === 422) {
                const errors = await response.json();
                const errorMsg = Object.values(errors.errors || {})[0]?.[0] || 'Error en los datos ingresados';
                mostrarModal('error', 'Error de Validación', errorMsg);
            } else {
                mostrarModal('error', 'Error', 'Ocurrió un error al actualizar el usuario.');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarModal('error', 'Error', 'Hubo un problema al conectarse con el servidor.');
        }
    });

    function mostrarModal(tipo, titulo, mensaje) {
        const modal = document.getElementById('resultado-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalIcon = document.getElementById('modal-icon');
        const modalMessage = document.getElementById('modal-message');
        const modalHeader = document.getElementById('modal-header');
        const redirectBtn = document.getElementById('modal-redirect-btn');

        modalTitle.textContent = titulo;
        modalMessage.textContent = mensaje;

        if (tipo === 'success') {
            modalIcon.innerHTML = '<x-heroicon-o-check class="w-6 h-6 text-green-500" />';
            modalHeader.className = 'px-6 py-4 border-b bg-green-50';
            modalTitle.className = 'text-xl font-bold text-green-700';
            redirectBtn.style.display = 'inline-block';
        } else if (tipo === 'error') {
            modalIcon.innerHTML = '<x-heroicon-o-x class="w-6 h-6 text-red-500" />';
            modalHeader.className = 'px-6 py-4 border-b bg-red-50';
            modalTitle.className = 'text-xl font-bold text-red-700';
            redirectBtn.textContent = 'Cerrar';
            redirectBtn.onclick = function() {
                document.getElementById('resultado-modal').style.display = 'none';
            };
        }

        modal.style.display = 'flex';
    }

    function redirigirADetalle() {
        window.location.href = '{{ route("usuarios.show", $usuario->id) }}';
    }

    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });
</script>
