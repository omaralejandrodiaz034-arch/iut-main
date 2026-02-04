@extends('layouts.base')

@section('title', 'Editar Dependencia')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
        <h1 class="text-2xl font-bold text-slate-800 mb-8">Editar Dependencia</h1>

        <form action="{{ route('dependencias.update', $dependencia) }}" method="POST" class="space-y-6" novalidate>
            @csrf
            @method('PATCH')

            <div>
                <label for="unidad_administradora_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Unidad Administradora
                </label>
                <select name="unidad_administradora_id" id="unidad_administradora_id"
                        class="w-full px-4 py-3 border @error('unidad_administradora_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white" required>
                    <option value="">Seleccione...</option>
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

            <div>
                <label for="nombre" class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Dependencia</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dependencia->nombre) }}"
                       placeholder="Ej: Dirección de Finanzas"
                       maxlength="40"
                       class="w-full px-4 py-3 border @error('nombre') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">⚠️ Solo se permiten letras y espacios.</p>
                <p class="text-gray-400 text-[11px] mt-2 italic font-medium">Máximo 40 caracteres (solo letras y espacios).</p>
            </div>

            <div>
                <label for="codigo" class="block text-sm font-bold text-slate-700 mb-2">Código de Dependencia</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $dependencia->codigo) }}"
                       placeholder="00000001"
                       maxlength="8"
                       inputmode="numeric"
                       class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition font-mono" required>
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-[11px] mt-2 font-medium italic">El sistema completará automáticamente con ceros a la izquierda (ej: 100 → 00000100).</p>
            </div>

            <div>
                <label for="responsable_id" class="block text-sm font-bold text-slate-700 mb-2">
                    Responsable (opcional)
                </label>
                <select name="responsable_id" id="responsable_id"
                        class="w-full px-4 py-3 border @error('responsable_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                    <option value="">-- Ninguno --</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ old('responsable_id', $dependencia->responsable_id) == $resp->id ? 'selected' : '' }}>
                            {{ $resp->nombre }}
                            @if($resp->cedula) ({{ $resp->cedula }}) @endif
                            - {{ $resp->tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('responsable_id')
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('dependencias.index') }}" 
                   class="px-6 py-3 text-slate-600 font-bold hover:bg-slate-50 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-sm hover:bg-blue-700 transition active:scale-95">
                    Actualizar Dependencia
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nombreInput = document.getElementById('nombre');
        const codigoInput = document.getElementById('codigo');
        const errorNombre = document.getElementById('error-nombre');

        // 1. Validación de Nombre: Solo letras, tildes y espacios
        nombreInput.addEventListener('input', function(e) {
            let original = e.target.value;
            let filtrado = original.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (original !== filtrado) {
                errorNombre.classList.remove('hidden');
                setTimeout(() => errorNombre.classList.add('hidden'), 2500);
            }
            e.target.value = filtrado.slice(0, 40);
        });

        // 2. Validación de Código: Solo números
        codigoInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // 3. Autorelleno de ceros al salir del campo (Blur)
        codigoInput.addEventListener('blur', function(e) {
            let valor = e.target.value;
            if (valor.length > 0 && valor.length < 8) {
                e.target.value = valor.padStart(8, '0');
            }
        });
    });
</script>
@endsection