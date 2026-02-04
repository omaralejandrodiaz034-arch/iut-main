@extends('layouts.base')

@section('title', 'Editar Unidad')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
        <h1 class="text-2xl font-bold text-slate-800 mb-8 px-2">Editar Unidad Administradora</h1>

        <form action="{{ route('unidades.update', ['unidadAdministradora' => $unidadAdministradora->getKey()]) }}" 
              method="POST" id="editUnidadForm" class="space-y-6" novalidate>
            @csrf
            @method('PATCH')

            <div class="px-2">
                <label for="organismo_id" class="block text-sm font-bold text-slate-700 mb-2">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="w-full px-4 py-3 border @error('organismo_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white cursor-pointer">
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

            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Unidad</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $unidadAdministradora->codigo) }}"
                       maxlength="8" inputmode="numeric" autocomplete="off"
                       class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono">
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Solo 8 números. Se completará con ceros automáticamente.</p>
            </div>

            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $unidadAdministradora->nombre) }}" 
                       maxlength="30" autocomplete="off"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                {{-- Alerta visual para el usuario --}}
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 30 caracteres (solo letras y espacios).</p>
            </div>

            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('unidades.index') }}" 
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>
                
                <button type="submit" id="btnGuardar" 
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('codigo');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('editUnidadForm');

        // 1. BLOQUEO TOTAL DE LETRAS Y GUIONES (SOLO NÚMEROS)
        codigoInput.addEventListener('input', function(e) {
            let val = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = val.slice(0, 8);
        });

        // AUTORELLENO CON CEROS AL SALIR DEL CAMPO (BLUR)
        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0) {
                e.target.value = e.target.value.padStart(8, '0');
            }
        });

        // 2. RESTRICCIÓN ESTRICTA DE NÚMEROS Y CARACTERES ESPECIALES
        nombreInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            // Expresión: Solo permite letras de la A a la Z (incluye tildes y ñ) y espacios
            let filteredValue = originalValue.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (originalValue !== filteredValue) {
                // Muestra el error si se intenta ingresar algo prohibido
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }

            e.target.value = filteredValue.slice(0, 30);
        });

        // 3. ESTADO DE CARGA AL ENVIAR
        form.addEventListener('submit', function() {
            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            text.innerText = 'Actualizando...';
        });
    });
</script>
@endsection