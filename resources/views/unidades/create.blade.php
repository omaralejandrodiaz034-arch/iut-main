@extends('layouts.base')

@section('title', 'Crear Unidad')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
        <h1 class="text-2xl font-bold text-slate-800 mb-8 px-2">Crear Nueva Unidad Administradora</h1>

        <form action="{{ route('unidades.store') }}" method="POST" id="unidadForm" class="space-y-6" novalidate>
            @csrf

            {{-- Selección de Organismo --}}
            <div class="px-2">
                <label for="organismo_id" class="block text-sm font-bold text-slate-700 mb-2">Organismo</label>
                <select name="organismo_id" id="organismo_id"
                        class="w-full px-4 py-3 border @error('organismo_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white cursor-pointer">
                    <option value="">Seleccione el organismo...</option>
                    @foreach($organismos as $org)
                        <option value="{{ $org->id }}" {{ old('organismo_id') == $org->id ? 'selected' : '' }}>
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
                    <input type="text" name="codigo" id="codigo" 
                           value="{{ old('codigo', $siguienteCodigo ?? '') }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           placeholder="00000000"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/20">
                    
                    {{-- Botón original "Recomendar" a la derecha --}}
                    <button type="button" onclick="restaurarSugerencia()" 
                            class="absolute right-3 top-3 text-[10px] bg-blue-100 text-blue-700 px-2 py-1.5 rounded hover:bg-blue-200 transition font-bold uppercase tracking-wider">
                        Sugerir
                    </button>
                </div>

                {{-- NUEVO: Aviso de recuperación abajo si se modifica el código sugerido --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-blue-50/50 p-2 rounded-md border border-blue-100">
                    <span class="text-blue-800 text-[11px] font-medium">⚠️ El código no coincide con la sugerencia:</span>
                    <button type="button" id="btnRecuperar" 
                            class="text-blue-600 text-[11px] font-bold hover:text-blue-800 underline flex items-center gap-1">
                        Restaurar sugerencia ({{ $siguienteCodigo ?? '' }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten números.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Sugerencia secuencial activa.</p>
            </div>

            {{-- Nombre de la Unidad (Límite 40) --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                       maxlength="40"
                       autocomplete="off"
                       placeholder="Ej: Recursos Humanos"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten letras.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Límite: 40 caracteres (sin números).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('unidades.index') }}" 
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>
                
                <button type="submit" id="btnGuardar" 
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Unidad</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Valor sugerido por el controlador
    const sugerenciaInicial = "{{ $siguienteCodigo ?? '' }}";

    // Función que se dispara desde el botón lateral o el aviso de abajo
    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        
        codigoInput.value = sugerenciaInicial;
        recuperarContenedor.classList.add('hidden');
        
        // Efecto visual de éxito
        codigoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
        setTimeout(() => {
            codigoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
        }, 800);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('unidadForm');

        // 1. LÓGICA DE CÓDIGO CON RECOMENDACIÓN PROACTIVA
        codigoInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^0-9]/g, '');

            // Si intenta escribir letras
            if (originalValue !== filteredValue) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            // Si el valor actual es diferente a la sugerencia, mostramos el aviso abajo
            if (e.target.value !== sugerenciaInicial) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        // Evento para el botón de restaurar abajo
        btnRecuperar.addEventListener('click', restaurarSugerencia);

        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && e.target.value.length < 8) {
                e.target.value = e.target.value.padStart(8, '0');
                // Volver a chequear tras el padStart
                if (e.target.value === sugerenciaInicial) {
                    recuperarContenedor.classList.add('hidden');
                }
            }
        });

        // 2. RESTRICCIÓN DE NOMBRE (40 caracteres)
        nombreInput.addEventListener('input', function(e) {
            let val = e.target.value;
            let filtered = val.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (val !== filtered) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }

            e.target.value = filtered.slice(0, 40);
        });

        // 3. EFECTO DE CARGA
        form.addEventListener('submit', function(e) {
            if (nombreInput.value.trim() === "" || codigoInput.value.trim() === "") {
                return; 
            }

            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            text.innerText = 'Procesando...';
        });
    });
</script>
@endsection