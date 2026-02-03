@extends('layouts.base')

@section('title', 'Crear Organismo')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
        
        {{-- ENCABEZADO CON ESTILO --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5 text-blue-200" />
                Crear Nuevo Organismo
            </h1>
            <p class="text-blue-100 text-xs mt-1 opacity-90">
                Registre un nuevo organismo principal en el sistema.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('organismos.store') }}" method="POST" id="organismoForm" class="p-8 space-y-6" novalidate>
            @csrf

            {{-- Código del Organismo --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código del Organismo</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                           value="{{ old('codigo', $codigoSugerido) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           placeholder="00000000"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/20">

                    {{-- Botón Sugerir Integrado --}}
                    <button type="button" onclick="restaurarSugerencia()"
                            class="absolute right-3 top-3 text-[10px] bg-blue-100 text-blue-700 px-2 py-1.5 rounded hover:bg-blue-200 transition font-bold uppercase tracking-wider border border-blue-200">
                        Sugerir
                    </button>
                </div>

                {{-- Aviso de recuperación si el usuario cambia la sugerencia --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-blue-50/50 p-2 rounded-md border border-blue-100">
                    <span class="text-blue-800 text-[11px] font-medium">⚠️ El código no coincide con la sugerencia:</span>
                    <button type="button" id="btnRecuperar" 
                            class="text-blue-600 text-[11px] font-bold hover:text-blue-800 underline flex items-center gap-1">
                        Restaurar sugerencia ({{ $codigoSugerido }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Sugerencia secuencial activa (8 dígitos).</p>
            </div>

            {{-- Nombre del Organismo --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre del Organismo</label>
                <input type="text" name="nombre" id="nombre"
                       value="{{ old('nombre') }}"
                       maxlength="40"
                       placeholder="Ej: Ministerio de Educación"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Límite: 40 caracteres (sin números ni símbolos).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('organismos.index') }}"
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Organismo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Valor sugerido desde el controlador
    const sugerenciaInicial = "{{ $codigoSugerido }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        
        codigoInput.value = sugerenciaInicial;
        recuperarContenedor.classList.add('hidden');
        
        // Feedback visual
        codigoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
        setTimeout(() => {
            codigoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
        }, 800);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('organismoForm');

        // 1. Lógica de Código
        codigoInput.addEventListener('input', function (e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^0-9]/g, '');

            if (original !== filtrado) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filtrado.slice(0, 8);

            // Mostrar/ocultar aviso de restauración
            if (e.target.value !== sugerenciaInicial) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        codigoInput.addEventListener('blur', function (e) {
            if (e.target.value.length > 0 && e.target.value.length < 8) {
                e.target.value = e.target.value.padStart(8, '0');
                if (e.target.value === sugerenciaInicial) {
                    recuperarContenedor.classList.add('hidden');
                }
            }
        });

        // 2. Lógica de Nombre
        nombreInput.addEventListener('input', function (e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (original !== filtrado) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }
            e.target.value = filtrado.slice(0, 40);
        });

        // 3. Estado de carga
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            text.innerText = 'Guardando...';
        });
    });
</script>
@endsection