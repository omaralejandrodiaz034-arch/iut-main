@extends('layouts.base')

@section('title', 'Editar Dependencia')

@section('content')
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

            {{-- Código de Dependencia (Botón sugerir integrado) --}}
            <div class="px-2">
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <div class="relative">
                    <input type="text" name="codigo" id="codigo" 
                           value="{{ old('codigo', $dependencia->codigo) }}"
                           maxlength="8" inputmode="numeric" autocomplete="off"
                           placeholder="00000000"
                           class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono bg-blue-50/20">
                    
                    {{-- Botón Sugerir a la derecha --}}
                    <button type="button" onclick="restaurarSugerencia()" 
                            class="absolute right-3 top-3 text-[10px] bg-blue-100 text-blue-700 px-2 py-1.5 rounded hover:bg-blue-200 transition font-bold uppercase tracking-wider">
                        Sugerir
                    </button>
                </div>

                {{-- Aviso de recuperación si se modifica --}}
                <div id="recuperar-contenedor" class="hidden mt-2 flex items-center gap-2 bg-blue-50/50 p-2 rounded-md border border-blue-100">
                    <span class="text-blue-800 text-[11px] font-medium">⚠️ El código no coincide con el original:</span>
                    <button type="button" id="btnRecuperar" 
                            class="text-blue-600 text-[11px] font-bold hover:text-blue-800 underline flex items-center gap-1">
                        Restaurar código ({{ $dependencia->codigo }})
                    </button>
                </div>

                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten números.</p>
                <p class="text-blue-500 text-[11px] mt-2 italic font-medium">Sugerencia secuencial activa.</p>
            </div>

            {{-- Nombre de la Dependencia --}}
            <div class="px-2">
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Dependencia</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dependencia->nombre) }}"
                       placeholder="Ej: Dirección de Finanzas"
                       maxlength="40" autocomplete="off"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo se permiten letras.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Límite: 40 caracteres (sin números).</p>
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

            {{-- Botones de Acción --}}
            <div class="pt-6 flex justify-center items-center gap-8 border-t border-gray-50">
                <a href="{{ route('dependencias.index') }}" 
                   class="flex items-center gap-2 text-slate-900 font-bold transition-opacity hover:opacity-70">
                    <span class="text-xl">✕</span>
                    <span>Cancelar</span>
                </a>
                
                <button type="submit" id="btnGuardar" 
                        class="flex items-center justify-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg font-bold w-64 shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                    <span id="btnIcon">✓</span>
                    <span id="btnText">Actualizar Dependencia</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Valor original de la dependencia
    const codigoOriginal = "{{ $dependencia->codigo }}";

    function restaurarSugerencia() {
        const codigoInput = document.getElementById('codigo');
        const recuperarContenedor = document.getElementById('recuperar-contenedor');
        
        codigoInput.value = codigoOriginal;
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
        const form = document.getElementById('editDependenciaForm');

        // 1. LÓGICA DE CÓDIGO CON RECOMENDACIÓN PROACTIVA
        codigoInput.addEventListener('input', function(e) {
            let originalValue = e.target.value;
            let filteredValue = originalValue.replace(/[^0-9]/g, '');

            if (originalValue !== filteredValue) {
                errorCodigo.classList.remove('hidden');
                setTimeout(() => errorCodigo.classList.add('hidden'), 2000);
            }

            e.target.value = filteredValue.slice(0, 8);

            // Si el valor cambia del original, mostramos el aviso
            if (e.target.value !== codigoOriginal) {
                recuperarContenedor.classList.remove('hidden');
            } else {
                recuperarContenedor.classList.add('hidden');
            }
        });

        btnRecuperar.addEventListener('click', restaurarSugerencia);

        codigoInput.addEventListener('blur', function(e) {
            if (e.target.value.length > 0 && e.target.value.length < 8) {
                e.target.value = e.target.value.padStart(8, '0');
                if (e.target.value === codigoOriginal) {
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