@extends('layouts.base')

@section('title', 'Editar Dependencia')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Dependencias', 'url' => route('dependencias.index')], ['label' => $dependencia->nombre, 'url' => route('dependencias.show', $dependencia)], ['label' => 'Editar']]" />
@endpush
<div class="max-w-2xl mx-auto mt-10">
    {{-- Contenedor Principal --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">

        {{-- ENCABEZADO CON GRADIENTE --}}
        <div class="bg-gradient-to-r from-slate-700 to-slate-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-pencil-square class="w-5 h-5 text-slate-300" />
                Editar Dependencia
            </h1>
            <p class="text-slate-300 text-xs mt-1 opacity-90">
                Modifique los datos de la dependencia y actualice su responsable.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form action="{{ route('dependencias.update', $dependencia) }}" method="POST" id="editDependenciaForm" class="p-8 space-y-6" novalidate>
            @csrf
            @method('PATCH')

            {{-- Unidad Administradora --}}
            <div class="px-2">
                <label for="unidad_administradora_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Unidad Administradora
                </label>
                <select name="unidad_administradora_id" id="unidad_administradora_id"
                        class="w-full px-4 py-3 border @error('unidad_administradora_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white cursor-pointer" required>
                    <option value="">Seleccione la unidad...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_administradora_id', $dependencia->unidad_administradora_id) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('unidad_administradora_id')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Código de Dependencia (solo editable el dígito de dependencia, posición 6) --}}
            <div class="px-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <div class="flex items-center gap-1">
                    <input type="text" value="{{ substr($dependencia->codigo, 0, 5) }}" readonly
                        class="w-28 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="text" name="codigo_dependencia" id="codigo_dependencia"
                        value="{{ old('codigo_dependencia', substr($dependencia->codigo, 5, 1)) }}"
                        maxlength="1" inputmode="numeric" pattern="\d{1}"
                        placeholder="0"
                        class="w-16 px-3 py-3 border @error('codigo_dependencia') border-red-500 @else border-gray-300 @enderror rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition text-center">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="text" value="{{ substr($dependencia->codigo, 6) }}" readonly
                        class="w-16 px-3 py-3 border border-gray-200 rounded-lg bg-gray-100 text-gray-500 font-mono text-center cursor-not-allowed">
                </div>

                <input type="hidden" name="codigo" id="codigo_completo" value="{{ $dependencia->codigo }}">

                @error('codigo_dependencia')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror

                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p id="error-ceros" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ El código no puede ser solo ceros.</p>

                <p class="text-gray-400 text-[11px] mt-2">Solo edite el dígito de la dependencia (posición 6). Formato: <span class="font-mono">{{ substr($dependencia->codigo, 0, 5) }}.{{ substr($dependencia->codigo, 5, 1) }}.{{ substr($dependencia->codigo, 6) }}</span></p>
            </div>

            {{-- Nombre de la Dependencia --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Dependencia</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dependencia->nombre) }}"
                      placeholder="Ej: Dirección de Finanzas"
                      maxlength="40" autocomplete="off"
                      class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-gray-600" required>
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras, números y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (letras, números y espacios).</p>
            </div>

            {{-- Responsable --}}
            <div class="px-2">
                <label for="responsable_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Responsable (opcional)
                </label>
                <select name="responsable_id" id="responsable_id"
                        class="w-full px-4 py-3 border @error('responsable_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white cursor-pointer">
                    <option value="">-- Ninguno --</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ old('responsable_id', $dependencia->responsable_id) == $resp->id ? 'selected' : '' }}>
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
                   class="flex items-center gap-2 text-slate-600 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Actualizar Dependencia</span>

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
    const prefijoDep = "{{ substr($dependencia->codigo, 0, 5) }}";
    const sufijoDep = "{{ substr($dependencia->codigo, 6) }}";
    const codigoOriginal = "{{ $dependencia->codigo }}";

    document.addEventListener('DOMContentLoaded', function() {
        const codigoDepInput = document.getElementById('codigo_dependencia');
        const codigoCompletoInput = document.getElementById('codigo_completo');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const form = document.getElementById('editDependenciaForm');

        // 1. Lógica de entrada de Código de Dependencia (solo dígito editable)
        codigoDepInput.addEventListener('input', function(e) {
            let val = e.target.value.replace(/[^0-9]/g, '');

            if (e.target.value !== val) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = val.slice(0, 1);

            const nuevoCodigo = prefijoDep + e.target.value + sufijoDep;
            codigoCompletoInput.value = nuevoCodigo;

            const esTodoCeros = nuevoCodigo.length > 0 && /^0+$/.test(nuevoCodigo);
            if (esTodoCeros) {
                errorCeros.classList.remove('hidden');
            } else {
                errorCeros.classList.add('hidden');
            }
        });

        codigoDepInput.addEventListener('blur', function() {
            if (this.value && this.value.length > 0 && this.value.length < 1) {
                this.value = this.value.padStart(1, '0');
                codigoCompletoInput.value = prefijoDep + this.value + sufijoDep;
            }
        });

        // 2. VALIDACIÓN FINAL AL ENVIAR (Bloqueo de ceros y vacío)
        form.addEventListener('submit', function(e) {
            const val = codigoCompletoInput.value.trim();
            const esTodoCeros = /^0+$/.test(val);

            if (val === "" || esTodoCeros || val.length < 8) {
                e.preventDefault();
                errorCeros.classList.remove('hidden');
                codigoDepInput.focus();
                return;
            }

            if (!nombreInput.value.trim()) {
                e.preventDefault();
                nombreInput.focus();
                return;
            }

            // Efecto de Carga Estilo Organismo
            const btn = document.getElementById('btnGuardar');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.innerText = 'Procesando...';
        });

        // Lógica para el Nombre (permitir números)
        nombreInput.addEventListener('input', function(e) {
            let val = e.target.value;
            let filtered = val.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (val !== filtered) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2000);
            }
            e.target.value = filtered.slice(0, 40);
        });
    });
</script>
@endsection
