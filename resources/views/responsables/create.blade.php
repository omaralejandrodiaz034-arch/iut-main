@extends('layouts.base')

@section('title', 'Registrar Responsable')

@section('content')
<div class="max-w-xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        👤 Registrar Responsable
    </h1>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Mensajes de advertencia / error --}}
    @if(session('error'))
        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg shadow-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('responsables.buscar') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula</label>
            <input type="text" name="cedula" id="cedula"
                   placeholder="Ej: 15740816"
                   class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('responsables.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Buscar y Registrar
            </button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cedulaInput = document.getElementById('cedula');
    const datosBox = document.getElementById('datos-responsable');
    const alertSuccess = document.getElementById('alert-success');
    const alertError = document.getElementById('alert-error');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');

    let timeout = null;

    cedulaInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const cedula = this.value.trim();

        if (cedula.length < 6) {
            datosBox.classList.add('hidden');
            alertSuccess.classList.add('hidden');
            alertError.classList.add('hidden');
            return;
        }

        timeout = setTimeout(() => {
            fetch("{{ route('responsables.buscar') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ cedula })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    document.getElementById('nombre').textContent = data.data.nombre;
                    document.getElementById('cedula-show').textContent = data.data.cedula;
                    document.getElementById('tipo').textContent = data.data.tipo;

                    datosBox.classList.remove('hidden');
                    alertError.classList.add('hidden');
                    successMessage.textContent = data.message;
                    alertSuccess.classList.remove('hidden');
                } else {
                    datosBox.classList.add('hidden');
                    alertSuccess.classList.add('hidden');
                    errorMessage.textContent = data.error || 'No se encontró persona con esa cédula';
                    alertError.classList.remove('hidden');
                }
            })
            .catch(() => {
                datosBox.classList.add('hidden');
                alertSuccess.classList.add('hidden');
                errorMessage.textContent = 'Error de conexión con el servidor';
                alertError.classList.remove('hidden');
            });
        }, 500);
    });
});

document.getElementById('codigo').addEventListener('input', function (e) {
    const regex = /^[0-9\-]*$/;
    if (!regex.test(e.target.value)) {
        e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
    }
});
</script>

@endsection


