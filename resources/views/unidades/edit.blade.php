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

            {{-- Selección de Organismo --}}
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

            {{-- Código de Unidad --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Unidad</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $unidadAdministradora->codigo) }}"
                       maxlength="8" inputmode="numeric" autocomplete="off"
                       class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono">
                
                {{-- NUEVO: Recomendación de recuperación --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-blue-50/50 p-2 rounded-md border border-blue-100">
                    <span class="text-blue-800 text-[11px] font-medium">⚠️ El código es distinto al guardado:</span>
                    <button type="button" id="btnRecuperar" 
                            data-original="{{ $unidadAdministradora->codigo }}"
                            class="text-blue-600 text-[11px] font-bold hover:text-blue-800 underline flex items-center gap-1">
                        Restaurar código: {{ $unidadAdministradora->codigo }}
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten números.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Solo 8 números. Se completará con ceros automáticamente.</p>
            </div>

            {{-- Nombre de la Unidad --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $unidadAdministradora->nombre) }}" 
                       maxlength="40" autocomplete="off"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten letras.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras).</p>
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
        const errorCodigo = document.getElementById('error-codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const valorOriginalBD = btnRecuperar.getAttribute('data-original');

        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('editUnidadForm');

        // 1. LÓGICA DE CÓDIGO CON RECOMENDACIÓN
        codigoInput.addEventListener('input', function(e) {
            let currentVal = e.target.value;
            let filteredValue = currentVal.replace(/[^0-9]/g, '');

            // Aviso si escribe algo no numérico
            if (currentVal !== filteredValue) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            // Mostrar recomendación si el valor actual difiere del original
            if (e.target.value !== valorOriginalBD) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        // Restaurar código original al hacer clic
        btnRecuperar.addEventListener('click', function() {
            codigoInput.value = valorOriginalBD;
            recuperarContenedor.classList.add('hidden');
            
            // Efecto visual de restauración
            codigoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
            setTimeout(() => {
                codigoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
            }, 800);
        });

        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0) {
                e.target.value = e.target.value.padStart(8, '0');
                // Re-verificar tras el padStart
                if (e.target.value === valorOriginalBD) {
                    recuperarContenedor.classList.add('hidden');
                }
            }
        });

        // 2. RESTRICCIÓN DE NOMBRE (Límite 40 caracteres)
        nombreInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (originalValue !== filteredValue) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }

            // Aplicamos el límite de 40
            e.target.value = filteredValue.slice(0, 40);
        });

        // 3. ESTADO DE CARGA AL ENVIAR
        form.addEventListener('submit', function(e) {
            if (nombreInput.value.trim() === "" || codigoInput.value.trim() === "") {
                return;
            }

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