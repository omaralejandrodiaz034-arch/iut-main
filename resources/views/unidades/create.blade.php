@extends('layouts.base')

@section('title', 'Crear Unidad')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Unidades Administradoras', 'url' => route('unidades.index')], ['label' => 'Nueva Unidad']]" />
@endpush
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white dark:bg-slate-900 shadow-xl dark:shadow-slate-800 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">

        {{-- ENCABEZADO CON ESTILO (Igual a Organismo) --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5 text-blue-200" />
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

                    {{-- Botón Requerido --}}
                    <button type="button"
                            class="absolute right-3 top-3 text-[10px] bg-red-100 text-red-700 px-2 py-1.5 rounded transition font-bold uppercase tracking-wider border border-red-200 cursor-default">
                        Requerido
                    </button>
                </div>

                {{-- Aviso de recuperación (Igual a Organismo) --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-red-50/50 p-2 rounded-md border border-red-100">
                    <span class="text-red-800 text-[11px] font-medium">⚠️ Este código es requerido:</span>
                    <button type="button" id="btnRecuperar"
                            class="text-red-600 text-[11px] font-bold hover:text-red-800 underline flex items-center gap-1">
                        Restaurar valor requerido ({{ $siguienteCodigo ?? '' }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror

                {{-- Mensajes de error dinámicos --}}
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten números.</p>
                <p id="error-ceros" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ El código no puede ser solo ceros; debe tener un valor real.</p>

                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Campo obligatorio de 8 dígitos numéricos.</p>
            </div>

            {{-- Nombre de la Unidad --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Unidad</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       maxlength="40" autocomplete="off"
                       placeholder="Ej: Recursos Humanos"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras y espacios).</p>
            </div>

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('unidades.index') }}"
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
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
    const sugerenciaInicial = "{{ $siguienteCodigo ?? '' }}";
    const organismosData = @json($sugerenciasPorOrganismo ?? []);

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
        const codigoInput = document.getElementById('codigo');
        const errorCodigo = document.getElementById('error-codigo');
        const errorCeros = document.getElementById('error-ceros');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        const btnRecuperar = document.getElementById('btnRecuperar');
        const nombreInput = document.getElementById('nombre');
        const errorNombre = document.getElementById('error-nombre');
        const organismoSelect = document.getElementById('organismo_id');
        const form = document.getElementById('unidadForm');

        // Actualizar código sugerido al cambiar organismo
        organismoSelect.addEventListener('change', function() {
            const organismoId = this.value;
            const nuevoCodigo = organismosData[organismoId] || sugerenciaInicial;
            codigoInput.value = nuevoCodigo;
            recuperarContenedor.classList.add('hidden');
        });

        // ... resto del script sin cambios ...
@endsection
