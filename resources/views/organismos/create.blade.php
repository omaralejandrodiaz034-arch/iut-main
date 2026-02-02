@extends('layouts.base')

@section('title', 'Crear Organismo')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
        <h1 class="text-2xl font-bold text-slate-800 mb-8 px-2">Crear Nuevo Organismo</h1>

        <form action="{{ route('organismos.store') }}" method="POST" id="organismoForm" class="space-y-6" novalidate>
            @csrf

            {{-- Código del Organismo (Secuencial Automático) --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código del Organismo</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                           value="{{ old('codigo', $codigoSugerido) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           placeholder="00000000"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/20">

                    <button type="button" onclick="restaurarSugerencia()"
                            class="absolute right-3 top-3 text-[10px] bg-blue-100 text-blue-700 px-2 py-1.5 rounded hover:bg-blue-200 transition font-bold uppercase tracking-wider">
                        Recomendar
                    </button>
                </div>
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Sugerencia secuencial activa (8 dígitos).</p>
            </div>

            {{-- Nombre del Organismo (Solo letras - Máx 40) --}}
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
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Organismo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Almacenamos la sugerencia del controlador
    const sugerenciaInicial = "{{ $codigoSugerido }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        codigoInput.value = sugerenciaInicial;
        codigoInput.classList.add('ring-2', 'ring-blue-400');
        setTimeout(() => codigoInput.classList.remove('ring-2', 'ring-blue-400'), 800);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const codigoInput = document.getElementById('codigo');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('organismoForm');

        // 1. Restricción Código: Solo números
        codigoInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 8);
        });

        // Autorelleno de ceros al perder el foco
        codigoInput.addEventListener('blur', function (e) {
            if (e.target.value.length > 0 && e.target.value.length < 8) {
                e.target.value = e.target.value.padStart(8, '0');
            }
        });

        // 2. Restricción Nombre: Solo letras y espacios (Máx 40)
        nombreInput.addEventListener('input', function (e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (originalValue !== filteredValue) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 40);
        });

        // 3. Estado de carga al enviar
        form.addEventListener('submit', function(e) {
            if(!form.checkValidity()) return;

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
