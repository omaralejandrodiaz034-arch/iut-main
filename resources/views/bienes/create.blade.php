@extends('layouts.base')

@section('title', 'Crear Bien')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Registrar Nuevo Bien</h1>

            <form action="{{ route('bienes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Dependencia -->
                <div>
                    <label for="dependencia_id" class="block text-sm font-semibold text-gray-700 mb-2">Dependencia</label>
                    <select name="dependencia_id" id="dependencia_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Seleccione...</option>
                        @foreach($dependencias as $dep)
                            <option value="{{ $dep->id }}" {{ old('dependencia_id') == $dep->id ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('dependencia_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Responsable: se asigna a nivel de Dependencia -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Responsable</label>
                    <div id="responsable_display" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-700">Seleccione una dependencia para ver el responsable asignado</div>
                </div>

                <!-- Código -->
                <div>
                    <x-form-input name="codigo" label="Código" :value="old('codigo')" placeholder="Ej: BN-001" help="Código interno del bien" />
                </div>

                <!-- Descripción -->
                <div>
                    <x-form-input name="descripcion" label="Descripción" type="textarea" :value="old('descripcion')" placeholder="Describe el bien..." help="Información relevante sobre el bien (uso, estado, detalles)" />
                </div>

                <!-- Precio -->
                <div>
                    <x-form-input name="precio" label="Precio (Bs.)" type="number" :value="old('precio', '0.00')" placeholder="0.00" step="0.01" min="0" help="Precio aproximado del bien" />
                </div>

                <!-- Fotografía -->
                <div>
                    <label for="fotografia" class="block text-sm font-semibold text-gray-700 mb-2">Fotografía</label>
                    <input type="file" name="fotografia" id="fotografia"
                           accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                    <p class="text-gray-500 text-xs mt-2">Formatos admitidos: JPG, PNG, WEBP. Máx 2MB.</p>
                    @error('fotografia')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ubicación -->
                <div>
                    <x-form-input name="ubicacion" label="Ubicación" :value="old('ubicacion')" placeholder="Oficina 101" help="Lugar físico donde se encuentra el bien" />
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                    <select name="estado" id="estado"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Seleccione...</option>
                        @foreach(\App\Enums\EstadoBien::cases() as $estado)
                            <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
                                {{ ucfirst($estado->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de registro -->
                <div>
                    <x-form-input name="fecha_registro" label="📅 Fecha de Registro" type="date" :value="old('fecha_registro', now()->format('Y-m-d'))" help="Selecciona la fecha en la que se registró el bien" />
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('bienes.index') }}"
                       class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition duration-200">
                        ✗ Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition duration-200">
                        ✓ Guardar Bien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar responsable según la dependencia seleccionada (cargadas en el servidor con dependencia.responsable)
    const dependenciaSelect = document.getElementById('dependencia_id');
    const responsableDisplay = document.getElementById('responsable_display');

    const dependencias = {
        @foreach($dependencias as $d)
            '{{ $d->id }}': {
                nombre: `{{ addslashes($d->nombre) }}`,
                responsable: {!! json_encode($d->responsable ? $d->responsable->nombre : null) !!}
            },
        @endforeach
    };

    function actualizarResponsable() {
        const id = dependenciaSelect.value;
        if (!id) {
            responsableDisplay.textContent = 'Seleccione una dependencia para ver el responsable asignado';
            return;
        }
        const dep = dependencias[id];
        if (dep && dep.responsable) {
            responsableDisplay.textContent = dep.responsable;
        } else {
            responsableDisplay.textContent = 'No hay responsable asignado para esta dependencia';
        }
    }

    dependenciaSelect.addEventListener('change', actualizarResponsable);
    // Inicializar
    actualizarResponsable();
</script>
@endpush

