@extends('layouts.base')

@section('title', 'Editar Organismo')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Organismos', 'url' => route('organismos.index')], ['label' => $organismo->nombre, 'url' => route('organismos.show', $organismo)], ['label' => 'Editar']]" />
@endpush
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

            {{-- Código --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                           value="{{ old('codigo', $organismo->codigo) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600 font-mono bg-blue-50/10">

                    {{-- Botón cambiado de Sugerir a Requerido --}}
                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación si se modifica --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50/50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ Este código es requerido:</span>
                    <button type="button" id="btnRecuperar"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar valor original ({{ $organismo->codigo }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p id="error-ceros" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ El código no puede ser solo ceros; debe tener un valor real.</p>
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
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras y espacios).</p>
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
    const valorOriginalBD = "{{ $organismo->codigo }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const errorCeros = document.getElementById('error-ceros');

        codigoInput.value = valorOriginalBD;
        recuperarContenedor.classList.add('hidden');
        errorCeros.classList.add('hidden');

        codigoInput.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
        setTimeout(() => {
            codigoInput.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('organismoForm');

        codigoInput.addEventListener('input', function (e) {
            let currentVal = e.target.value;
            let filteredValue = currentVal.replace(/[^0-9]/g, '');

            if (currentVal !== filteredValue) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            // Validar si son puros ceros
            const esTodoCeros = e.target.value.length > 0 && /^0+$/.test(e.target.value);
            if (esTodoCeros) {
                errorCeros.classList.remove('hidden');
            } else {
                errorCeros.classList.add('hidden');
            }

            // Mostrar aviso de requerido si es distinto al original
            if (e.target.value !== valorOriginalBD || e.target.value.length < 8) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        // Validación final al enviar
        form.addEventListener('submit', (e) => {
            const val = codigoInput.value;
            const esTodoCeros = /^0+$/.test(val);

            if (val.length < 8 || esTodoCeros) {
                e.preventDefault();
                errorCeros.classList.remove('hidden');
                codigoInput.focus();
                return;
            }

            const btnSubmit = document.getElementById('btnGuardar');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-80', 'cursor-wait');
            document.getElementById('iconCheck').classList.add('hidden');
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('textGuardar').innerText = 'Procesando...';
        });

        // Lógica para el Nombre
        nombreInput.addEventListener('input', function (e) {
            let val = e.target.value;
            let filtered = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (val !== filtered) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }
            e.target.value = filtered.slice(0, 40);
        });
    });
</script>
@endsection
