@extends('layouts.base')

@section('title', 'Editar Unidad')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Unidades Administradoras', 'url' => route('unidades.index')], ['label' => $unidad->nombre, 'url' => route('unidades.show', $unidad)], ['label' => 'Editar']]" />
@endpush
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white dark:bg-slate-900 shadow-xl dark:shadow-slate-800 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">

        {{-- ENCABEZADO CON GRADIENTE --}}
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-pencil-square class="w-5 h-5 text-slate-300" />
                Editar Unidad Administradora
            </h1>
            <p class="text-slate-300 text-xs mt-1 opacity-90">
                Actualice la vinculación, identificación y nombre de la unidad.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('unidades.update', ['unidadAdministradora' => $unidadAdministradora->getKey()]) }}"
              method="POST" id="editUnidadForm" class="p-8 space-y-6" novalidate>
            @csrf
            @method('PATCH')

            {{-- Selección de Organismo --}}
            <div class="px-2">
                <label for="organismo_id" class="block text-sm font-bold text-slate-700 mb-2">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="w-full px-4 py-3 border @error('organismo_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white cursor-pointer text-gray-600">
                    <option value="">Seleccione el organismo...</option>
                    @foreach($organismos as $org)
                        <option value="{{ $org->id }}" {{ old('organismo_id', $unidadAdministradora->organismo_id) == $org->id ? 'selected' : '' }}>
                            {{ $org->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('organismo_id')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Código de Unidad --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Unidad</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $unidadAdministradora->codigo) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/10 text-gray-600">

                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50/50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ El código es requerido o distinto:</span>
                    <button type="button" id="btnRecuperar"
                            data-original="{{ $unidadAdministradora->codigo }}"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar código: {{ $unidadAdministradora->codigo }}
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic"></p>
                <p class="text-gray-400 text-[11px] mt-2">Solo 8 números. Se completará con ceros automáticamente.</p>
            </div>

            {{-- Nombre de la Unidad --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $unidadAdministradora->nombre) }}"
                       maxlength="40" autocomplete="off" placeholder="Nombre de la unidad"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras y espacios).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('unidades.index') }}"
                   class="flex items-center gap-2 text-slate-600 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Cambios</span>

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
    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const valorOriginalBD = btnRecuperar.getAttribute('data-original');

        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('editUnidadForm');

        // 1. LÓGICA DE CÓDIGO (Unificada con Organismo)
        codigoInput.addEventListener('input', function(e) {
            let currentVal = e.target.value;
            let filteredValue = currentVal.replace(/[^0-9]/g, '');

            if (currentVal !== filteredValue) {
                errorCodigo.innerText = "⚠️ Solo se permiten números.";
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            const esTodoCeros = e.target.value.length > 0 && /^0+$/.test(e.target.value);
            const estaVacio = e.target.value.length === 0;

            if (esTodoCeros || estaVacio) {
                errorCodigo.innerText = estaVacio ? "⚠️ El código es requerido." : "⚠️ El código no puede ser solo ceros.";
                errorCodigo.classList.remove('hidden');
            } else {
                errorCodigo.classList.add('hidden');
            }

            if (e.target.value !== valorOriginalBD || estaVacio || esTodoCeros) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', function() {
            codigoInput.value = valorOriginalBD;
            recuperarContenedor.classList.add('hidden');
            errorCodigo.classList.add('hidden');

            codigoInput.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
            setTimeout(() => {
                codigoInput.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
            }, 1000);
        });

        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && !/^0+$/.test(e.target.value)) {
                e.target.value = e.target.value.padStart(8, '0');
                if (e.target.value === valorOriginalBD) {
                    recuperarContenedor.classList.add('hidden');
                }
            }
        });

        // 2. LÓGICA DE NOMBRE
        nombreInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (originalValue !== filteredValue) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }
            e.target.value = filteredValue.slice(0, 40);
        });

        // 3. ENVÍO Y VALIDACIÓN FINAL
        form.addEventListener('submit', function(e) {
            const val = codigoInput.value;
            const esTodoCeros = /^0+$/.test(val);
            const estaVacio = val.trim() === "";

            if (estaVacio || esTodoCeros || val.length < 8 || nombreInput.value.trim() === "") {
                e.preventDefault();
                if (estaVacio || esTodoCeros || val.length < 8) {
                    errorCodigo.innerText = "⚠️ Ingrese un código válido de 8 dígitos.";
                    errorCodigo.classList.remove('hidden');
                    codigoInput.focus();
                }
                return;
            }

            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.innerText = 'Actualizando...';
        });
    });
</script>
@endsection
