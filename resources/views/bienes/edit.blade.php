@extends('layouts.base')

@section('title', 'Editar Bien')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Bien</h1>

        <form action="{{ route('bienes.update', ['bien' => $bien->getKey()]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Dependencia -->
            <div>
                <label for="dependencia_id" class="block text-sm font-semibold text-gray-700 mb-2">Dependencia</label>
                <select name="dependencia_id" id="dependencia_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Seleccione...</option>
                    @foreach($dependencias as $dep)
                        <option value="{{ $dep->id }}" {{ old('dependencia_id', $bien->dependencia_id) == $dep->id ? 'selected' : '' }}>
                            {{ $dep->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('dependencia_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Responsable -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Responsable</label>
                <div id="responsable_display" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-700">
                    {{ $bien->dependencia->responsable->nombre ?? 'No hay responsable asignado' }}
                </div>
            </div>

            <!-- Código -->
            <div>
                <label for="codigo" class="block text-sm font-semibold text-gray-700 mb-2">Código</label>
                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $bien->codigo) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="Ej: BN001"
                       maxlength="10"
                       pattern="[A-Za-z0-9]+"
                       title="Solo letras y números, máximo 10 caracteres">
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                          placeholder="Describe el bien..."
                          maxlength="255"
                          pattern="[A-Za-z0-9.,;:()áéíóúÁÉÍÓÚñÑ\s\-]+"
                          title="Letras, números, espacios y signos básicos permitidos, máximo 255 caracteres">{{ old('descripcion', $bien->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio -->
            <div>
                <label for="precio" class="block text-sm font-semibold text-gray-700 mb-2">Precio (Bs.)</label>
                <input type="number" name="precio" id="precio"
                       value="{{ old('precio', $bien->precio) }}"
                       step="0.01" min="0"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                @error('precio')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fotografía -->
            <div class="space-y-2">
                <label for="fotografia" class="block text-sm font-semibold text-gray-700">Fotografía</label>
                @if($bien->fotografia)
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-32 rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ str_starts_with($bien->fotografia, 'http') ? $bien->fotografia : asset('storage/'.$bien->fotografia) }}"
                                 alt="Fotografía actual"
                                 class="w-full h-full object-cover">
                        </div>
                        <p class="text-sm text-gray-500">Puedes subir una nueva imagen para reemplazarla.</p>
                    </div>
                @endif
                <input type="file" name="fotografia" id="fotografia"
                       accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                <p class="text-gray-500 text-xs">Formatos admitidos: JPG, PNG, WEBP. Máx 2MB.</p>
                @error('fotografia')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-semibold text-gray-700 mb-2">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $bien->ubicacion) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="Oficina 101"
                       maxlength="50"
                       pattern="[A-Za-z0-9\s]+"
                       title="Solo letras, números y espacios, máximo 50 caracteres">
                @error('ubicacion')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                <select name="estado" id="estado"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Seleccione...</option>
                    @foreach(\App\Enums\EstadoBien::cases() as $estado)
                        <option value="{{ $estado->value }}" {{ old('estado', $bien->estado?->value) == $estado->value ? 'selected' : '' }}>
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
                <label for="fecha_registro" class="block text-sm font-semibold text-gray-700 mb-2">📅 Fecha de Registro</label>
                <div class="relative">
                    <input type="date" name="fecha_registro" id="fecha_registro" value="{{ old('fecha_registro', optional($bien->fecha_registro)->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           style="font-size: 16px;">
                    <span class="absolute right-3 top-3 text-gray-400 pointer-events-none">📆</span>
                </div>
                @error('fecha_registro')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-2">Selecciona la fecha en la que se registró el bien</p>
            </div>

            <!-- Tipo de Bien -->
            <div>
                <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Bien</label>
                <select name="tipo" id="tipo" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Seleccione...</option>
                    <option value="inmueble" {{ old('tipo', $bien->tipo) == 'inmueble' ? 'selected' : '' }}>Inmueble</option>
                    <option value="electrónico" {{ old('tipo', $bien->tipo) == 'electrónico' ? 'selected' : '' }}>Electrónico</option>
                    <option value="mueble" {{ old('tipo', $bien->tipo) == 'mueble' ? 'selected' : '' }}>Mueble</option>
                    <option value="otro" {{ old('tipo', $bien->tipo) == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <!-- Campos dinámicos según el tipo de bien -->
            <div id="campos-tipo-bien" class="space-y-6">
                <!-- Estos campos se mostrarán dinámicamente según el tipo seleccionado -->
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('bienes.index') }}"
                   class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition duration-200">✗ Cancelar</a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition duration-200">✓ Guardar Bien</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar responsable según la dependencia seleccionada
    const dependenciaSelect = document.getElementById('dependencia_id');
    const responsableDisplay = document.getElementById('responsable_display');

    const dependencias = {
        @foreach($dependencias as $d)
            '{{ $d->id }}': {
                nombre: {{ addslashes($d->nombre) }},
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
        responsableDisplay.textContent = dep?.responsable ?? 'No hay responsable asignado para esta dependencia';
    }

    dependenciaSelect.addEventListener('change', actualizarResponsable);
    actualizarResponsable();

    // Validación en tiempo real
    function soloAlfanumerico(event) {
        const regex = /^[A-Za-z0-9]*$/;
        if (!regex.test(event.target.value)) {
            event.target.value = event.target.value.replace(/[^A-Za-z0-9]/g, '');
        }
    }

    function descripcionValida(event) {
        const regex = /[^A-Za-z0-9.,;:()áéíóúÁÉÍÓÚñÑ\s\-]/g;
        event.target.value = event.target.value.replace(regex, '');
    }

    function soloLetrasNumerosEspacios(event) {
        const regex = /[^A-Za-z0-9\s]/g;
        event.target.value = event.target.value.replace(regex, '');
    }

    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });

    document.getElementById('codigo').addEventListener('input', soloAlfanumerico);
    document.getElementById('descripcion').addEventListener('input', descripcionValida);
    document.getElementById('ubicacion').addEventListener('input', soloLetrasNumerosEspacios);

    // Campos dinámicos para tipo de bien
    document.getElementById('tipo').addEventListener('change', function () {
        console.log('Tipo seleccionado:', this.value); // Depuración
        const tipo = this.value;
        const container = document.getElementById('campos-tipo-bien');
        container.innerHTML = '';

        if (tipo === 'electrónico') {
            container.innerHTML = `
                <div>
                    <label for="serial" class="block text-sm font-semibold text-gray-700 mb-2">Serial</label>
                    <input type="text" name="serial" id="serial" value="{{ old('serial', $bien->serial) }}" placeholder="Ingrese el serial del bien"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="procesador" class="block text-sm font-semibold text-gray-700 mb-2">Procesador</label>
                    <input type="text" name="procesador" id="procesador" value="{{ old('procesador', $bien->procesador) }}" placeholder="Ej: Intel i7"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="caracteristicas" class="block text-sm font-semibold text-gray-700 mb-2">Características</label>
                    <textarea name="caracteristicas" id="caracteristicas" rows="3" placeholder="Detalles técnicos del bien"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('caracteristicas', $bien->caracteristicas) }}</textarea>
                </div>
            `;
        } else if (tipo === 'inmueble') {
            container.innerHTML = `
                <div>
                    <label for="direccion" class="block text-sm font-semibold text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $bien->direccion) }}" placeholder="Ubicación del inmueble"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="area" class="block text-sm font-semibold text-gray-700 mb-2">Área (m²)</label>
                    <input type="number" name="area" id="area" value="{{ old('area', $bien->area) }}" placeholder="Ej: 120"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="uso" class="block text-sm font-semibold text-gray-700 mb-2">Uso</label>
                    <input type="text" name="uso" id="uso" value="{{ old('uso', $bien->uso) }}" placeholder="Ej: Oficina, Almacén"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            `;
        } else if (tipo === 'mueble') {
            container.innerHTML = `
                <div>
                    <label for="material" class="block text-sm font-semibold text-gray-700 mb-2">Material</label>
                    <input type="text" name="material" id="material" value="{{ old('material', $bien->material) }}" placeholder="Ej: Madera, Metal"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label for="dimensiones" class="block text-sm font-semibold text-gray-700 mb-2">Dimensiones</label>
                    <input type="text" name="dimensiones" id="dimensiones" value="{{ old('dimensiones', $bien->dimensiones) }}" placeholder="Ej: 2x1x1.5 m"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            `;
        }
    });

    // Trigger change event to load fields on page load
    document.getElementById('tipo').dispatchEvent(new Event('change'));
</script>
@endpush
