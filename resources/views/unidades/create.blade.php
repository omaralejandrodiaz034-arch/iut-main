@extends('layouts.base')

@section('title', 'Crear Unidad')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Unidades Administradoras', 'url' => route('unidades.index')], ['label' => 'Nueva Unidad']]" />
@endpush
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">

        {{-- ENCABEZADO CON ESTILO --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Crear Nueva Unidad Administradora
            </h1>
            <p class="text-blue-100 text-xs mt-1 opacity-90">
                Registre una nueva unidad dependiente de un organismo en el sistema.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('unidades.store') }}" method="POST" id="unidadForm" class="p-8 space-y-6" novalidate>
            @csrf

            {{-- Selección de Organismo --}}
            <div class="px-2">
                <label for="organismo_id" class="block text-sm font-bold text-gray-700 mb-2">Organismo</label>
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Código de Unidad</label>
                <div class="relative">
                    <div class="flex items-center gap-1">
                        <input type="text" id="prefijo_unidad" value="" readonly
                            class="w-12 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                        <span class="text-gray-400 font-bold">-</span>
                        <input type="text" name="codigo_unidad" id="codigo_unidad"
                            value="" maxlength="4" inputmode="numeric" pattern="\d{4}"
                            placeholder="0000"
                            class="w-24 px-3 py-3 border border-gray-300 rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition text-center" required>
                        <span class="text-gray-400 font-bold">-</span>
                        <input type="text" id="sufijo_unidad" value="00000" readonly
                            class="w-16 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                    </div>

                    <input type="hidden" name="codigo" id="codigo_completo" value="">

                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ Este código es requerido:</span>
                    <button type="button" id="btnRecuperar"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar valor requerido (<span id="sugerencia-original"></span>)
                    </button>
                </div>

                @error('codigo_unidad')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic"></p>
                <p class="text-gray-400 text-[11px] mt-2">Solo edite los 4 dígitos de la unidad (posiciones 2-5).</p>
            </div>

            {{-- Nombre de la Unidad --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-gray-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       maxlength="40" autocomplete="off"
                       placeholder="Ej: Recursos Humanos"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-white">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras, números y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (letras, números y espacios).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-100">
                <a href="{{ route('unidades.index') }}"
                   class="flex items-center gap-2 text-gray-700 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Guardar Unidad</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function actualizarCamposDesdeCompleto(codigo) {
        document.getElementById('prefijo_unidad').value = codigo.substring(0, 1);
        document.getElementById('codigo_unidad').value = codigo.substring(1, 5);
        document.getElementById('sufijo_unidad').value = codigo.substring(5);
        document.getElementById('codigo_completo').value = codigo;
    }

    function actualizarCodigoCompleto() {
        const prefijo = document.getElementById('prefijo_unidad').value || '';
        const unidad = document.getElementById('codigo_unidad').value || '';
        const sufijo = document.getElementById('sufijo_unidad').value || '';
        document.getElementById('codigo_completo').value = prefijo + unidad + sufijo;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sugerenciaInicial = "{{ $siguienteCodigo ?? '' }}";
        const organismosData = @json($sugerenciasPorOrganismo ?? []);

        const prefijoInput = document.getElementById('prefijo_unidad');
        const unidadInput = document.getElementById('codigo_unidad');
        const sufijoInput = document.getElementById('sufijo_unidad');
        const codigoCompletoInput = document.getElementById('codigo_completo');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const sugerenciaOriginalSpan = document.getElementById('sugerencia-original');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const organismoSelect = document.getElementById('organismo_id');
        const form = document.getElementById('unidadForm');

        sugerenciaOriginalSpan.textContent = sugerenciaInicial;

        // Actualizar código sugerido al cambiar organismo
        if (organismoSelect) {
            organismoSelect.addEventListener('change', function() {
                const organismoId = this.value;
                const sugerencia = organismosData[organismoId] ?? null;
                const nuevoCodigo = sugerencia ? sugerencia.codigo : sugerenciaInicial;
                actualizarCamposDesdeCompleto(nuevoCodigo);
                recuperarContenedor.classList.add('hidden');
                errorCeros.classList.add('hidden');
            });
        }

        // Validación de código (solo números en la parte de unidad)
        if (unidadInput) {
            unidadInput.addEventListener('input', function(e) {
                let original = e.target.value;
                let cleaned = original.replace(/\D/g, '');

                if (cleaned.length > 4) {
                    cleaned = cleaned.slice(0, 4);
                }

                if (original !== cleaned) {
                    e.target.value = cleaned;
                }

                actualizarCodigoCompleto();

                const codigoCompleto = codigoCompletoInput.value;
                const esTodoCeros = codigoCompleto.length > 0 && /^0+$/.test(codigoCompleto);
                const estaVacio = codigoCompleto.length === 0;

                if (esTodoCeros || estaVacio) {
                    errorCodigo.innerText = estaVacio ? "⚠️ El código es requerido." : "⚠️ El código no puede ser solo ceros.";
                    errorCodigo.classList.remove('hidden');
                } else {
                    errorCodigo.classList.add('hidden');
                }

                if (codigoCompleto !== sugerenciaInicial || estaVacio || esTodoCeros) {
                    recuperarContenedor.classList.remove('hidden');
                } else {
                    recuperarContenedor.classList.add('hidden');
                }
            });

            unidadInput.addEventListener('blur', function() {
                if (this.value && this.value.length > 0 && this.value.length < 4) {
                    this.value = this.value.padStart(4, '0');
                    actualizarCodigoCompleto();
                }
            });
        }

        // Botón recuperar
        if (btnRecuperar) {
            btnRecuperar.addEventListener('click', function() {
                actualizarCamposDesdeCompleto(sugerenciaInicial);
                recuperarContenedor.classList.add('hidden');
                errorCodigo.classList.add('hidden');

                codigoCompletoInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
                setTimeout(() => {
                    codigoCompletoInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
                }, 800);
            });
        }

        // Validación de nombre (letras, números y espacios)
        if (nombreInput) {
            nombreInput.addEventListener('input', function(e) {
                const value = e.target.value;
                const regex = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]*$/;

                if (!regex.test(value)) {
                    errorNombre.classList.remove('hidden');
                    this.value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');
                } else {
                    errorNombre.classList.add('hidden');
                }
            });
        }

        // Validación antes de enviar
        if (form) {
            form.addEventListener('submit', function(e) {
                const codigo = codigoCompletoInput.value.trim();
                const nombre = nombreInput.value.trim();
                const organismo = organismoSelect.value;
                let hasError = false;

                // Validar organismo
                if (!organismo) {
                    e.preventDefault();
                    alert('Por favor, seleccione un organismo.');
                    organismoSelect.focus();
                    return;
                }

                // Validar código
                if (!codigo || codigo.length !== 10 || !/^\d{10}$/.test(codigo)) {
                    e.preventDefault();
                    alert('El código debe contener exactamente 10 dígitos numéricos.');
                    unidadInput.focus();
                    return;
                }

                if (/^0+$/.test(codigo)) {
                    e.preventDefault();
                    alert('El código no puede ser solo ceros.');
                    unidadInput.focus();
                    return;
                }

                // Validar nombre
                if (!nombre) {
                    e.preventDefault();
                    alert('El nombre de la unidad es obligatorio.');
                    nombreInput.focus();
                    return;
                }

                if (nombre.length > 40) {
                    e.preventDefault();
                    alert('El nombre no puede exceder los 40 caracteres.');
                    nombreInput.focus();
                    return;
                }

                const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
                if (!nombreRegex.test(nombre)) {
                    e.preventDefault();
                    alert('El nombre solo puede contener letras y espacios.');
                    nombreInput.focus();
                    return;
                }
            });
        }
    });
</script>
@endsection
