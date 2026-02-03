@extends('layouts.base')

@section('title', 'Editar Organismo')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
        
        {{-- ENCABEZADO CON GRADIENTE --}}
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-pencil-square class="w-5 h-5 text-slate-300" />
                Editar Organismo
            </h1>
            <p class="text-slate-300 text-xs mt-1 opacity-90">
                Actualice la identificación y nombre del organismo principal.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('organismos.update', $organismo) }}" method="POST" id="organismoForm" class="p-8 space-y-6" novalidate>
            @csrf
            @method('PATCH')

            {{-- Código con Botón Sugerir Integrado --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                           value="{{ old('codigo', $organismo->codigo) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600 font-mono bg-blue-50/10">

                    {{-- Botón Sugerir/Restaurar dentro del input --}}
                    <button type="button" onclick="restaurarSugerencia()" 
                            class="absolute right-3 top-3 text-[10px] bg-blue-100 text-blue-700 px-2 py-1.5 rounded hover:bg-blue-200 transition font-bold uppercase tracking-wider">
                        Sugerir
                    </button>
                </div>

                {{-- Aviso de recuperación si se modifica --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-blue-50/50 p-2 rounded-md border border-blue-100">
                    <span class="text-blue-800 text-[11px] font-medium">⚠️ El código es distinto al original:</span>
                    <button type="button" id="btnRecuperar" 
                            class="text-blue-600 text-[11px] font-bold hover:text-blue-800 underline flex items-center gap-1">
                        Restaurar ({{ $organismo->codigo }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten números.</p>
                <p class="text-gray-400 text-[11px] mt-2">Código único del organismo (8 números).</p>
            </div>

            {{-- Nombre --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre"
                       value="{{ old('nombre', $organismo->nombre) }}"
                       maxlength="40"
                       placeholder="Nombre del organismo"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten letras.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Límite: 40 caracteres (solo letras).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('organismos.index') }}"
                   class="flex items-center gap-2 text-slate-600 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition-all active:scale-95 w-60">
                    <span id="iconCheck">✓</span>
                    <span id="textGuardar">Guardar Cambios</span>
                    
                    <div id="spinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Valor original cargado
    const valorOriginalBD = "{{ $organismo->codigo }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        
        codigoInput.value = valorOriginalBD;
        recuperarContenedor.classList.add('hidden');
        
        // Animación de éxito
        codigoInput.classList.add('ring-2', 'ring-green-500', 'bg-green-50'); 
        setTimeout(() => {
            codigoInput.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('organismoForm');

        // 1. Lógica para el Código
        codigoInput.addEventListener('input', function (e) {
            let currentVal = e.target.value;
            let filteredValue = currentVal.replace(/[^0-9]/g, '');

            if (currentVal !== filteredValue) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            // Mostrar aviso si es distinto al original
            if (e.target.value !== valorOriginalBD) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        // 2. Lógica para el Nombre
        nombreInput.addEventListener('input', function (e) {
            let val = e.target.value;
            let filtered = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (val !== filtered) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }
            e.target.value = filtered.slice(0, 40);
        });

        // 3. Estado de carga al enviar
        form.addEventListener('submit', (e) => {
            const btnSubmit = document.getElementById('btnGuardar');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-80', 'cursor-wait');
            document.getElementById('iconCheck').classList.add('hidden');
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('textGuardar').innerText = 'Procesando...';
        });
    });
</script>
@endsection