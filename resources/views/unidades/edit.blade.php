@extends('layouts.base')

@section('title', 'Editar Unidad')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Unidades Administradoras', 'url' => route('unidades.index')], ['label' => $unidadAdministradora->nombre, 'url' => route('unidades.show', $unidadAdministradora)], ['label' => 'Editar']]" />
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

            {{-- Código de Unidad (solo editable los 4 dígitos de unidad) --}}
            <div class="px-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Código de Unidad</label>
                <div class="flex items-center gap-1">
                    <input type="text" value="{{ substr($unidadAdministradora->codigo, 0, 1) }}" readonly
                        class="w-12 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="text" name="codigo_unidad" id="codigo_unidad"
                        value="{{ old('codigo_unidad', substr($unidadAdministradora->codigo, 1, 4)) }}"
                        maxlength="4" inputmode="numeric" pattern="\d{4}"
                        placeholder="0000"
                        class="w-24 px-3 py-3 border @error('codigo_unidad') border-red-500 @else border-gray-300 @enderror rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition text-center">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="text" value="{{ substr($unidadAdministradora->codigo, 5) }}" readonly
                        class="w-20 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                </div>

                <input type="hidden" name="codigo" id="codigo_completo" value="{{ $unidadAdministradora->codigo }}">

                @error('codigo_unidad')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic"></p>
                <p class="text-gray-400 text-[11px] mt-2">Solo edite los 4 dígitos de la unidad (posiciones 2-5). Formato: <span class="font-mono">{{ substr($unidadAdministradora->codigo, 0, 1) }}.{{ substr($unidadAdministradora->codigo, 1, 4) }}.{{ substr($unidadAdministradora->codigo, 5) }}</span></p>
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
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras, números y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (letras, números y espacios).</p>
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
        const prefijoUnidad = "{{ substr($unidadAdministradora->codigo, 0, 1) }}";
        const sufijoUnidad = "{{ substr($unidadAdministradora->codigo, 5) }}";
        const codigoUnidadInput = document.getElementById('codigo_unidad');
        const codigoCompletoInput = document.getElementById('codigo_completo');
        const errorCodigo = document.getElementById('error-codigo');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('editUnidadForm');

        // 1. LÓGICA DE CÓDIGO (solo dígitos de unidad editables)
        codigoUnidadInput.addEventListener('input', function(e) {
            let currentVal = e.target.value;
            let filteredValue = currentVal.replace(/[^0-9]/g, '');

            if (currentVal !== filteredValue) {
                errorCodigo.innerText = "⚠️ Solo se permiten números.";
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 4);

            const nuevoCodigo = prefijoUnidad + e.target.value + sufijoUnidad;
            codigoCompletoInput.value = nuevoCodigo;

            const esTodoCeros = nuevoCodigo.length > 0 && /^0+$/.test(nuevoCodigo);
            const estaVacio = nuevoCodigo.length === 0;

            if (esTodoCeros || estaVacio) {
                errorCodigo.innerText = estaVacio ? "⚠️ El código es requerido." : "⚠️ El código no puede ser solo ceros.";
                errorCodigo.classList.remove('hidden');
            } else {
                errorCodigo.classList.add('hidden');
            }
        });

        codigoUnidadInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && !/^0+$/.test(e.target.value)) {
                e.target.value = e.target.value.padStart(4, '0');
                codigoCompletoInput.value = prefijoUnidad + e.target.value + sufijoUnidad;
            }
        });

        // 2. LÓGICA DE NOMBRE (permitir números)
        nombreInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (originalValue !== filteredValue) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }
            e.target.value = filteredValue.slice(0, 40);
        });

        // 3. ENVÍO Y VALIDACIÓN FINAL
        form.addEventListener('submit', function(e) {
            const val = codigoCompletoInput.value;
            const esTodoCeros = /^0+$/.test(val);
            const estaVacio = val.trim() === "";

            if (estaVacio || esTodoCeros || val.length < 10 || nombreInput.value.trim() === "") {
                e.preventDefault();
                if (estaVacio || esTodoCeros || val.length < 10) {
                    errorCodigo.innerText = "⚠️ Ingrese un código válido de 10 dígitos.";
                    errorCodigo.classList.remove('hidden');
                    codigoUnidadInput.focus();
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
