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

                <!-- Tipo de Bien -->
                <div>
                    <label for="tipo_bien" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Bien</label>
                    <select name="tipo_bien" id="tipo_bien" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Seleccione...</option>
                        @foreach($tiposBien as $value => $label)
                            <option value="{{ $value }}" {{ old('tipo_bien') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_bien')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campos dinámicos según el tipo de bien -->
                <div id="campos-tipo-bien" class="space-y-6">
                    <!-- Estos campos se mostrarán dinámicamente según el tipo seleccionado -->
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

<script>
    // Mapeo de campos específicos por tipo de bien
    const camposPorTipo = {
        'ELECTRONICO': [
            { name: 'procesador', label: 'Procesador', type: 'text' },
            { name: 'memoria', label: 'Memoria (GB)', type: 'text' },
            { name: 'almacenamiento', label: 'Almacenamiento (GB)', type: 'text' },
            { name: 'pantalla', label: 'Tamaño de pantalla', type: 'text' },
            { name: 'serial', label: 'Número de serie', type: 'text' },
            { name: 'garantia', label: 'Garantía hasta', type: 'date' }
        ],
        'INMUEBLE': [
            { name: 'dimensiones', label: 'Dimensiones (largo x ancho x alto)', type: 'text' },
            { name: 'material', label: 'Material principal', type: 'text' },
            { name: 'area', label: 'Área (m²)', type: 'number' },
            { name: 'pisos', label: 'Número de pisos', type: 'number' },
            { name: 'construccion', label: 'Año de construcción', type: 'text' },
            { name: 'direccion', label: 'Dirección exacta', type: 'text' }
        ],
        'MOBILIARIO': [
            { name: 'material', label: 'Material', type: 'text' },
            { name: 'dimensiones', label: 'Dimensiones', type: 'text' },
            { name: 'color', label: 'Color', type: 'text' },
            { name: 'capacidad', label: 'Capacidad de personas/carga', type: 'text' },
            { name: 'cantidad_piezas', label: 'Cantidad de piezas', type: 'number' },
            { name: 'acabado', label: 'Tipo de acabado', type: 'text' }
        ],
        'VEHICULO': [
            { name: 'marca', label: 'Marca', type: 'text' },
            { name: 'modelo', label: 'Modelo', type: 'text' },
            { name: 'anio', label: 'Año', type: 'text' },
            { name: 'placa', label: 'Placa o número de registro', type: 'text' },
            { name: 'motor', label: 'Número de motor', type: 'text' },
            { name: 'chasis', label: 'Número de chasis', type: 'text' },
            { name: 'combustible', label: 'Tipo de combustible', type: 'text' },
            { name: 'kilometraje', label: 'Kilometraje actual', type: 'text' }
        ],
        'OTROS': [
            { name: 'especificaciones', label: 'Especificaciones adicionales', type: 'textarea' },
            { name: 'cantidad', label: 'Cantidad de unidades', type: 'number' },
            { name: 'presentacion', label: 'Presentación/Formato', type: 'text' }
        ]
    };

    document.getElementById('tipo_bien').addEventListener('change', function () {
        const tipo = this.value;
        const container = document.getElementById('campos-tipo-bien');
        container.innerHTML = '';

        if (!tipo || !camposPorTipo[tipo]) {
            return;
        }

        const campos = camposPorTipo[tipo];
        let html = `<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded">
            <p class="text-blue-800 font-semibold">Información específica para ${tipo.toLowerCase()}</p>
        </div>`;

        campos.forEach(campo => {
            if (campo.type === 'textarea') {
                html += `
                    <div>
                        <label for="${campo.name}" class="block text-sm font-semibold text-gray-700 mb-2">${campo.label}</label>
                        <textarea name="${campo.name}" id="${campo.name}" rows="4" placeholder="Ingrese ${campo.label.toLowerCase()}"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                    </div>
                `;
            } else {
                html += `
                    <div>
                        <label for="${campo.name}" class="block text-sm font-semibold text-gray-700 mb-2">${campo.label}</label>
                        <input type="${campo.type}" name="${campo.name}" id="${campo.name}" placeholder="Ingrese ${campo.label.toLowerCase()}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                `;
            }
        });

        container.innerHTML = html;
    });

    // Trigger change event to load fields on page load
    document.getElementById('tipo_bien').dispatchEvent(new Event('change'));
</script>

<script>
    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });
</script>
@endpush

