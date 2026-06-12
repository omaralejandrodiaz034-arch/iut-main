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
                <label class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <div class="relative">
                    <div class="flex items-center gap-1">
                        <input type="text" id="prefijo_dependencia" value="" readonly
                            class="w-28 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                        <span class="text-gray-400 font-bold">-</span>
                        <input type="text" name="codigo_dependencia" id="codigo_dependencia"
                            value="" maxlength="3" inputmode="numeric" pattern="\d{3}"
                            placeholder="000"
                            class="w-20 px-3 py-3 border border-gray-300 rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition text-center" required>
                        <span class="text-gray-400 font-bold">-</span>
                        <input type="text" id="sufijo_dependencia" value="00" readonly
                            class="w-16 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                    </div>

                    <input type="hidden" name="codigo" id="codigo_completo" value="">

                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50/50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ Este código es requerido:</span>
                    <button type="button" id="btnRecuperar"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar valor requerido (<span id="sugerencia-original"></span>)
                    </button>
                </div>

                @error('codigo_dependencia')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p id="error-ceros" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ El código no puede ser solo ceros.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Solo edite los 3 dígitos de la dependencia (posiciones 6-8).</p>
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
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras, números y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (letras, números y espacios).</p>
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
    const unidadesData = @json($sugerenciasPorUnidad ?? []);

    function restaurarSugerencia() {
        const codigoCompletoInput = document.getElementById('codigo_completo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const errorCeros = document.getElementById('error-ceros');

        actualizarCamposDesdeCompleto(sugerenciaInicial);
        recuperarContenedor.classList.add('hidden');
        errorCeros.classList.add('hidden');

        codigoCompletoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
        setTimeout(() => {
            codigoCompletoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
        }, 800);
    }

    function actualizarCamposDesdeCompleto(codigo) {
        const prefijoInput = document.getElementById('prefijo_dependencia');
        const depInput = document.getElementById('codigo_dependencia');
        const sufijoInput = document.getElementById('sufijo_dependencia');
        const codigoCompletoInput = document.getElementById('codigo_completo');

        prefijoInput.value = codigo.substring(0, 5);
        depInput.value = codigo.substring(5, 8);
        sufijoInput.value = codigo.substring(8);
        codigoCompletoInput.value = codigo;
    }

    function actualizarCodigoCompleto() {
        const prefijo = document.getElementById('prefijo_dependencia').value || '';
        const dep = document.getElementById('codigo_dependencia').value || '';
        const sufijo = document.getElementById('sufijo_dependencia').value || '';
        document.getElementById('codigo_completo').value = prefijo + dep + sufijo;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const prefijoInput = document.getElementById('prefijo_dependencia');
        const depInput = document.getElementById('codigo_dependencia');
        const sufijoInput = document.getElementById('sufijo_dependencia');
        const codigoCompletoInput = document.getElementById('codigo_completo');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const sugerenciaOriginalSpan = document.getElementById('sugerencia-original');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const unidadSelect = document.getElementById('unidad_administradora_id');
        const responsableSelect = document.getElementById('responsable_id');
        const form = document.getElementById('dependenciaForm');

        sugerenciaOriginalSpan.textContent = sugerenciaInicial;

        unidadSelect.addEventListener('change', function() {
            const unidadId = this.value;
            const sugerencia = unidadesData[unidadId] ?? null;
            const nuevoCodigo = sugerencia ? sugerencia.codigo : sugerenciaInicial;
            actualizarCamposDesdeCompleto(nuevoCodigo);
            recuperarContenedor.classList.add('hidden');
        });

        depInput.addEventListener('input', function(e) {
            let val = e.target.value.replace(/[^0-9]/g, '');

            if (e.target.value !== val) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = val.slice(0, 3);
            actualizarCodigoCompleto();

            const codigoCompleto = codigoCompletoInput.value;
            const esTodoCeros = codigoCompleto.length > 0 && /^0+$/.test(codigoCompleto);
            if (esTodoCeros) {
                errorCeros.classList.remove('hidden');
            } else {
                errorCeros.classList.add('hidden');
            }

            if (codigoCompleto !== sugerenciaInicial || codigoCompleto.length < 10) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        depInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && e.target.value.length < 3) {
                e.target.value = e.target.value.padStart(3, '0');
                actualizarCodigoCompleto();
            }
        });

        nombreInput.addEventListener('input', function(e) {
            let val = e.target.value;
            let filtered = val.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (val !== filtered) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }
            e.target.value = filtered.slice(0, 40);
        });

        form.addEventListener('submit', function(e) {
            const codVal = codigoCompletoInput.value;
            const esTodoCeros = /^0+$/.test(codVal);
            const nombreVal = nombreInput.value.trim();
            const unidadVal = unidadSelect.value;

            if (codVal.length < 10 || esTodoCeros || nombreVal === "" || unidadVal === "") {
                e.preventDefault();

                if (esTodoCeros) {
                    errorCeros.classList.remove('hidden');
                    depInput.focus();
                }

                if(nombreVal === "") nombreInput.focus();

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
