@extends('layouts.base')

@section('title', 'Nueva Dependencia')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Dependencias', 'url' => route('dependencias.index')], ['label' => 'Nueva Dependencia']]" />
@endpush
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">

        {{-- ENCABEZADO CON ESTILO (Igual a Organismo) --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5 text-blue-200" />
                Registrar Nueva Dependencia
            </h1>
            <p class="text-blue-100 text-xs mt-1 opacity-90">
                Gestione las áreas administrativas y asigne sus respectivos responsables.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('dependencias.store') }}" method="POST" id="dependenciaForm" class="p-8 space-y-6" novalidate>
            @csrf

            {{-- Unidad Administradora --}}
            <div class="px-2">
                <label for="unidad_administradora_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Unidad Administradora
                </label>
                <select name="unidad_administradora_id" id="unidad_administradora_id"
                        class="w-full px-4 py-3 border @error('unidad_administradora_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white cursor-pointer">
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
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo"
                        value="{{ old('codigo', $proximoCodigo ?? '') }}"
                        maxlength="8" inputmode="numeric" autocomplete="off"
                        placeholder="00000001"
                        class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/20">

                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación (Estilo Organismo) --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50/50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ Este código es requerido:</span>
                    <button type="button" id="btnRecuperar"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar valor requerido ({{ $proximoCodigo ?? '' }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p id="error-ceros" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ El código no puede ser solo ceros.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Campo obligatorio de 8 dígitos numéricos.</p>
            </div>

            {{-- Nombre --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Dependencia</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       placeholder="Ej: Dirección de Servicios"
                       maxlength="40" autocomplete="off"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras y espacios).</p>
            </div>

            {{-- Responsable --}}
            <div class="px-2">
                <label for="responsable_id" class="block text-sm font-bold text-slate-700 mb-2">Responsable (Opcional)</label>
                <select name="responsable_id" id="responsable_id"
                        class="w-full px-4 py-3 border @error('responsable_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white cursor-pointer">
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

            {{-- Botones de Acción (Estilo Organismo) --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('dependencias.index') }}"
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-lg hover:bg-blue-700 transition-all active:scale-95">
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
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const errorCeros = document.getElementById('error-ceros');

        codigoInput.value = sugerenciaInicial;
        recuperarContenedor.classList.add('hidden');
        errorCeros.classList.add('hidden');

        codigoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
        setTimeout(() => {
            codigoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
        }, 800);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nombreInput = document.getElementById('nombre');
        const codigoInput = document.getElementById('codigo');
        const errorNombre = document.getElementById('error-nombre');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const form = document.getElementById('dependenciaForm');

        // Validación de Nombre
        nombreInput.addEventListener('input', function(e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            if (original !== filtrado) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }
            e.target.value = filtrado.slice(0, 40);
        });

        // Validación de Código
        codigoInput.addEventListener('input', function(e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^0-9]/g, '');

            if (original !== filtrado) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filtrado.slice(0, 8);

            // Validar ceros
            const esTodoCeros = e.target.value.length > 0 && /^0+$/.test(e.target.value);
            if (esTodoCeros) {
                errorCeros.classList.remove('hidden');
            } else {
                errorCeros.classList.add('hidden');
            }

            // Mostrar aviso de restauración
            if (e.target.value !== sugerenciaInicial || e.target.value.length < 8) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && e.target.value.length < 8) {
                e.target.value = e.target.value.padStart(8, '0');
                if (e.target.value === sugerenciaInicial) {
                    recuperarContenedor.classList.add('hidden');
                }
            }
        });

        // Estado de carga y validación final
        form.addEventListener('submit', function(e) {
            const val = codigoInput.value;
            const esTodoCeros = /^0+$/.test(val);

            if (val.length < 8 || esTodoCeros) {
                e.preventDefault();
                errorCeros.classList.remove('hidden');
                codigoInput.focus();
                return;
            }

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
