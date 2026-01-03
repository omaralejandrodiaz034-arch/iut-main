@extends('layouts.base')

@section('title', 'Editar Dependencia')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Dependencia</h1>

        <form action="{{ route('dependencias.update', $dependencia) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Unidad Administradora -->
            <div>
                <label for="unidad_administradora_id" class="block text-sm font-medium text-gray-700">
                    Unidad Administradora
                </label>
                <select name="unidad_administradora_id" id="unidad_administradora_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="">Seleccione...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_administradora_id', $dependencia->unidad_administradora_id) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('unidad_administradora_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dependencia->nombre) }}"
                       placeholder="Ej: Dirección de Finanzas"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Código -->
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $dependencia->codigo) }}"
                       placeholder="Ej: DEP-001"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                              focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono" required>
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Responsable -->
            <div>
                <label for="responsable_id" class="block text-sm font-medium text-gray-700">
                    Responsable (opcional)
                </label>
                <select name="responsable_id" id="responsable_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('dependencias.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });
</script>
@endsection

