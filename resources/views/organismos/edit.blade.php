@extends('layouts.base')

@section('title', 'Editar Organismo')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-sm rounded-xl p-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-8 px-2">Editar Organismo</h1>

        <form action="{{ route('organismos.update', $organismo) }}" method="POST" id="organismoForm" class="space-y-6" novalidate>
            @csrf
            @method('PATCH')

            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código</label>
                <input type="text" name="codigo" id="codigo"
                       value="{{ old('codigo', $organismo->codigo) }}"
                       maxlength="8"
                       class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600 font-mono">

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-[11px] mt-2">Código único del organismo (8 números secuenciales).</p>
            </div>

            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre"
                       value="{{ old('nombre', $organismo->nombre) }}"
                       maxlength="30"
                       placeholder="Nombre del organismo"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-600">

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-[11px] mt-2">Nombre completo (Máximo 30 caracteres).</p>
            </div>

            <div class="pt-6 flex justify-center items-center gap-8">
                
                <a href="{{ route('organismos.index') }}"
                   class="flex items-center gap-2 text-black font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" id="btnGuardar"
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold shadow-sm hover:bg-blue-700 transition-all active:scale-95 w-56">
                    <span id="iconCheck">✓</span>
                    <span id="textGuardar">Guardar Cambios</span>
                    
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
    const codigoInput = document.getElementById('codigo');
    const nombreInput = document.getElementById('nombre'); // Nuevo
    const form = document.getElementById('organismoForm');
    const btn = document.getElementById('btnGuardar');
    const text = document.getElementById('textGuardar');
    const icon = document.getElementById('iconCheck');
    const spinner = document.getElementById('spinner');

    // 1. Restricción Código: Solo números y máximo 8
    codigoInput.addEventListener('input', function (e) {
        let val = e.target.value.replace(/[^0-9]/g, '');
        if (val.length > 8) val = val.slice(0, 8);
        e.target.value = val;
    });

    codigoInput.addEventListener('blur', function (e) {
        if (e.target.value.length > 0) {
            e.target.value = e.target.value.padStart(8, '0');
        }
    });

    // 2. Restricción Nombre: Máximo 30 caracteres (seguridad extra)
    nombreInput.addEventListener('input', function (e) {
        if (e.target.value.length > 30) {
            e.target.value = e.target.value.slice(0, 30);
        }
    });

    // 3. Estado de carga
    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-wait');
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        text.innerText = 'Guardando...';
    });
</script>
@endsection