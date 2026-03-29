@extends('layouts.base')

@section('title', 'Crear Responsable - Sistema de Bienes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-[#510817]">Panel</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('responsables.index') }}" class="text-gray-500 hover:text-[#510817]">Responsables</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Crear</span>
    </nav>

    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">➕ Nuevo Responsable</h1>
        <p class="text-gray-600 mt-1">Busque por cédula para registrar un responsable desde el sistema externo, o ingrese los datos manualmente</p>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <!-- Formulario de Búsqueda por Cédula -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Buscar por Cédula</h2>
            <p class="text-sm text-gray-600 mb-4">Ingrese la cédula para buscar el responsable en el sistema externo</p>
            
            <form id="buscar-form" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" id="cedula-buscar" placeholder="Ingrese cédula (ej: 12345678)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817]">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Buscar
                </button>
            </form>

            <!-- Resultado de búsqueda -->
            <div id="resultado-busqueda" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-green-800" id="resultado-nombre"></p>
                        <p class="text-sm text-green-600">Cédula: <span id="resultado-cedula"></span></p>
                        <p class="text-sm text-green-600">Tipo: <span id="resultado-tipo"></span></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btn-usar-datos" class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                            Usar datos
                        </button>
                    </div>
                </div>
            </div>

            <div id="error-busqueda" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800" id="mensaje-error"></p>
            </div>
        </div>

        <!-- Formulario de Registro -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📝 Datos del Responsable</h2>
            
            <form method="POST" action="{{ route('responsables.store') }}" id="registro-form">
                @csrf

                <div class="space-y-4">
                    <!-- Cédula -->
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula de Identidad *</label>
                        <input type="text" name="cedula" id="cedula" value="{{ old('cedula') }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('cedula') border-red-500 @enderror"
                               required>
                        @error('cedula')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('nombre') border-red-500 @enderror"
                               required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Responsable -->
                    <div>
                        <label for="tipo_id" class="block text-sm font-medium text-gray-700">Tipo de Responsable *</label>
                        <select name="tipo_id" id="tipo_id" 
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('tipo_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Correo -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" value="{{ old('correo') }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('correo') border-red-500 @enderror"
                               placeholder="correo@ejemplo.com">
                        @error('correo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('telefono') border-red-500 @enderror"
                               placeholder="0412-1234567">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('responsables.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-[#510817] text-white rounded-lg hover:bg-[#6D1426] transition">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscarForm = document.getElementById('buscar-form');
    const cedulaInput = document.getElementById('cedula-buscar');
    const resultadoDiv = document.getElementById('resultado-busqueda');
    const errorDiv = document.getElementById('error-busqueda');
    const btnUsarDatos = document.getElementById('btn-usar-datos');
    
    let datosEncontrados = null;

    buscarForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cedula = cedulaInput.value.trim();
        if (!cedula) return;

        // Mostrar estado de carga
        const btn = buscarForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Buscando...';

        fetch("{{ route('responsables.buscar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ cedula: cedula })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.textContent = 'Buscar';
            
            if (data.status === 'ok') {
                datosEncontrados = data.data;
                document.getElementById('resultado-nombre').textContent = data.data.nombre;
                document.getElementById('resultado-cedula').textContent = data.data.cedula;
                document.getElementById('resultado-tipo').textContent = data.data.tipo;
                
                resultadoDiv.classList.remove('hidden');
                errorDiv.classList.add('hidden');
            } else {
                document.getElementById('mensaje-error').textContent = data.message || 'No se encontró persona con esa cédula';
                resultadoDiv.classList.add('hidden');
                errorDiv.classList.remove('hidden');
                datosEncontrados = null;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.textContent = 'Buscar';
            document.getElementById('mensaje-error').textContent = 'Error al buscar. Intente de nuevo.';
            resultadoDiv.classList.add('hidden');
            errorDiv.classList.remove('hidden');
            datosEncontrados = null;
        });
    });

    btnUsarDatos.addEventListener('click', function() {
        if (datosEncontrados) {
            document.getElementById('cedula').value = datosEncontrados.cedula;
            document.getElementById('nombre').value = datosEncontrados.nombre;
            
            // Buscar el tipo en el select
            const tipoSelect = document.getElementById('tipo_id');
            const opciones = tipoSelect.options;
            for (let i = 0; i < opciones.length; i++) {
                if (opciones[i].text.toLowerCase().includes(datosEncontrados.tipo.toLowerCase())) {
                    tipoSelect.selectedIndex = i;
                    break;
                }
            }
            
            resultadoDiv.classList.add('hidden');
        }
    });
});
</script>
@endsection
