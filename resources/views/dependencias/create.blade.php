@extends('layouts.base')

@section('title', 'Nueva Dependencia')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    {{-- El contenedor principal DEBE tener overflow-hidden para que el degradado del encabezado no se salga de las esquinas redondeadas --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-home-modern class="w-5 h-5 text-slate-300" />
                Registrar Nueva Dependencia
            </h1>
            <p class="text-slate-300 text-xs mt-1 opacity-90">
                Gestione las áreas administrativas y asigne sus respectivos responsables.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('dependencias.store') }}" method="POST" id="dependenciaForm" class="p-8 space-y-6" novalidate>
            @csrf

            {{-- Unidad Administradora --}}
            <div>
                <label for="unidad_administradora_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Unidad Administradora
                </label>
                <select name="unidad_administradora_id" id="unidad_administradora_id"
                        class="w-full px-4 py-3 border @error('unidad_administradora_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white cursor-pointer">
                    <option value="">Seleccione la unidad...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_administradora_id') == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('unidad_administradora_id')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Código de Dependencia --}}
            <div>
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                           value="{{ old('codigo', $proximoCodigo ?? '') }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           placeholder="00000001"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition font-mono bg-blue-50/20">

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

            {{-- Nombre --}}
            <div>
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Dependencia</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       placeholder="Ej: Dirección de Servicios"
                       maxlength="40" autocomplete="off"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
            </div>

            {{-- Responsable --}}
            <div>
                <label for="responsable_id" class="block text-sm font-bold text-slate-700 mb-2">Responsable (Opcional)</label>
                <select name="responsable_id" id="responsable_id"
                        class="w-full px-4 py-3 border @error('responsable_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white cursor-pointer">
                    <option value="">-- Ninguno --</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ old('responsable_id') == $resp->id ? 'selected' : '' }}>
                            {{ $resp->nombre }} {{ $resp->cedula ? "($resp->cedula)" : '' }}
                        </option>
                    @endforeach
                </select>
                @error('responsable_id')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-end items-center gap-4 border-t border-gray-50">
                <a href="{{ route('dependencias.index') }}"
                   class="px-6 py-3 text-slate-500 font-bold hover:text-slate-800 transition">
                    Cancelar
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Dependencia</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const sugerenciaInicial = "{{ $proximoCodigo ?? '' }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        codigoInput.value = sugerenciaInicial;
        codigoInput.classList.add('ring-2', 'ring-blue-400');
        setTimeout(() => codigoInput.classList.remove('ring-2', 'ring-blue-400'), 800);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nombreInput = document.getElementById('nombre');
        const codigoInput = document.getElementById('codigo');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('dependenciaForm');

        nombreInput.addEventListener('input', function(e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            if (original !== filtrado) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }
            e.target.value = filtrado.slice(0, 40);
        });

        codigoInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 8);
        });

        codigoInput.addEventListener('blur', function(e) {
            let valor = e.target.value;
            if (valor.length > 0 && valor.length < 8) {
                e.target.value = valor.padStart(8, '0');
            }
        });

        form.addEventListener('submit', function() {
            if(!form.checkValidity()) return;
            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            text.innerText = 'Procesando...';
        });
    });
</script>
@endsection
