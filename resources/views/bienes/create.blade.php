@extends('layouts.base')

@section('title', 'Crear Bien')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800">Registrar Nuevo Bien</h1>
        <p class="text-sm text-gray-600 mt-2">Completa la información básica. Puedes registrar el bien sin asignarlo a una dependencia y hacerlo luego.</p>

        <form action="{{ route('bienes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="dependencia_id" class="block text-sm font-semibold text-gray-700 mb-2">Dependencia (opcional)</label>
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

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Responsable</label>
                <div id="responsable_display" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-700 bg-gray-50">
                    Seleccione una dependencia para ver el responsable
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form-input name="codigo" label="Código" :value="old('codigo')" placeholder="Ej: 5684" :required="true" />
                <x-form-input name="precio" label="Precio (Bs.)" type="number" :value="old('precio', '0.00')" step="0.01" :required="true" />
            </div>

            <x-form-input name="descripcion" label="Descripción" type="textarea" :value="old('descripcion')" :required="true" />

            <div>
                <label for="fotografia" class="block text-sm font-semibold text-gray-700 mb-2">Fotografía</label>
                <input type="file" name="fotografia" id="fotografia" accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
                @error('fotografia')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <x-form-input name="ubicacion" label="Ubicación" :value="old('ubicacion')" placeholder="Oficina 101" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                    <select name="estado" id="estado" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Seleccione...</option>
                        @foreach(\App\Enums\EstadoBien::cases() as $estado)
                            <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
                                {{ $estado->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-form-input name="fecha_registro" label="Fecha de Registro" type="date" :value="old('fecha_registro', now()->format('Y-m-d'))" :required="true" />

                <div>
                    <label for="tipo_bien" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Bien</label>
                    <select name="tipo_bien" id="tipo_bien" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione...</option>
                        @foreach($tiposBien as $value => $label)
                            <option value="{{ $value }}" {{ old('tipo_bien') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="campos-tipo-bien" class="space-y-6"></div>

            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('bienes.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg">✗ Cancelar</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">✓ Guardar Bien</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lógica de Responsable
    const dependenciaSelect = document.getElementById('dependencia_id');
    const responsableDisplay = document.getElementById('responsable_display');
    const dependencias = {
        @foreach($dependencias as $d)
            '{{ $d->id }}': { responsable: {!! json_encode($d->responsable ? $d->responsable->nombre : null) !!} },
        @endforeach
    };

    function actualizarResponsable() {
        const dep = dependencias[dependenciaSelect.value];
        responsableDisplay.textContent = dep?.responsable || (dependenciaSelect.value ? 'Sin responsable asignado' : 'Seleccione una dependencia');
    }
    dependenciaSelect.addEventListener('change', actualizarResponsable);

    // Lógica de Campos por Tipo (Sin Subtipos)
    const camposPorTipo = {
        'ELECTRONICO': [
            { name: 'procesador', label: 'Procesador', type: 'text' },
            { name: 'memoria', label: 'Memoria (GB)', type: 'text' },
            { name: 'almacenamiento', label: 'Almacenamiento (GB)', type: 'text' },
            { name: 'pantalla', label: 'Tamaño de pantalla', type: 'text' },
            { name: 'serial', label: 'Número de serie', type: 'text' },
            { name: 'garantia', label: 'Garantía hasta', type: 'date' }
        ],
        'MOBILIARIO': [
            { name: 'material', label: 'Material', type: 'text' },
            { name: 'dimensiones', label: 'Dimensiones', type: 'text' },
            { name: 'color', label: 'Color', type: 'text' },
            { name: 'acabado', label: 'Tipo de acabado', type: 'text' }
        ],
        'VEHICULO': [
            { name: 'marca', label: 'Marca', type: 'text' },
            { name: 'modelo', label: 'Modelo', type: 'text' },
            { name: 'placa', label: 'Placa', type: 'text' },
            { name: 'motor', label: 'Número de motor', type: 'text' },
            { name: 'chasis', label: 'Número de chasis', type: 'text' }
        ],
        'OTROS': [
            { name: 'especificaciones', label: 'Especificaciones adicionales', type: 'textarea' },
            { name: 'cantidad', label: 'Cantidad', type: 'number' }
        ]
    };

    const oldValues = @json(old());

    document.getElementById('tipo_bien').addEventListener('change', function () {
        const tipo = this.value;
        const container = document.getElementById('campos-tipo-bien');
        container.innerHTML = '';

        if (!tipo || !camposPorTipo[tipo]) return;

        let html = `<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded">
            <p class="text-blue-800 font-semibold">Detalles específicos para este tipo de bien</p>
        </div><div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

        camposPorTipo[tipo].forEach(campo => {
            const val = oldValues[campo.name] || '';
            const isFullWidth = campo.type === 'textarea' ? 'md:col-span-2' : '';
            
            html += `
                <div class="${isFullWidth}">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">${campo.label}</label>
                    ${campo.type === 'textarea' 
                        ? `<textarea name="${campo.name}" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">${val}</textarea>`
                        : `<input type="${campo.type}" name="${campo.name}" value="${val}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">`
                    }
                </div>`;
        });

        html += `</div>`;
        container.innerHTML = html;
    });

    // Cargar campos si hay error de validación al volver
    document.getElementById('tipo_bien').dispatchEvent(new Event('change'));
</script>
@endpush

