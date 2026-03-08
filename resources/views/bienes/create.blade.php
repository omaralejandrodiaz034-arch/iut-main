@extends('layouts.base')

@section('title', 'Registrar Bien')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => 'Nuevo Bien']]" />
@endpush
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
            {{-- Encabezado --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-5">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-blue-100" />
                    Registrar Nuevo Bien
                </h1>
                <p class="text-blue-100 text-xs mt-1 opacity-90">
                    Complete la información técnica y administrativa del activo patrimonial.
                </p>
            </div>

            <form action="{{ route('bienes.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf

                {{-- Sección 1: Ubicación Administrativa --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
                        <x-heroicon-o-home-modern class="w-5 h-5 text-blue-600" /> Asignación Administrativa
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="dependencia_id" class="block text-sm font-bold text-gray-700 mb-2">Dependencia <span class="text-red-500">*</span></label>
                            <select name="dependencia_id" id="dependencia_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                                <option value="" disabled {{ old('dependencia_id') ? '' : 'selected' }}>Seleccione dependencia...</option>
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep->id }}" {{ old('dependencia_id') == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('dependencia_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Responsable de la Dependencia</label>
                            <div id="responsable_display"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 italic text-sm flex items-center h-[50px]">
                                Seleccione una dependencia...
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección 2: Identificación del Bien --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center gap-2">
                        <x-heroicon-o-identification class="w-5 h-5 text-blue-600" /> Identificación Técnica
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Código del Bien con Sugerencia --}}
                        <div>
                            <label for="codigo" class="block text-sm font-bold text-gray-700 mb-2">Código del Bien</label>
                            <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $codigoSugerido ?? '') }}"
                                maxlength="8" inputmode="numeric" placeholder="Ej: 00000001"
                                class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 @enderror rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition uppercase"
                                required pattern="\d{8}" title="El código debe contener exactamente 8 dígitos numéricos">

                            @error('codigo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            {{-- Contenedor para la sugerencia --}}
                            <div id="sugerencia-container" class="mt-1 hidden">
                                <button type="button" id="btn-sugerencia"
                                    class="text-[10px] text-blue-600 hover:underline font-bold italic">
                                    💡 ¿Usar código sugerido: <span id="span-sugerencia"></span>?
                                </button>
                            </div>
                        </div>

                        {{-- Tipo de Bien --}}
                        <div>
                            <label for="tipo_bien" class="block text-sm font-bold text-gray-700 mb-2">Tipo de Bien</label>
                            <select name="tipo_bien" id="tipo_bien" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                                <option value="">Seleccione tipo...</option>
                                @foreach($tiposBien as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipo_bien') == $value ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label for="estado" class="block text-sm font-bold text-gray-700 mb-2">Estado Físico</label>
                            <select name="estado" id="estado" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                                <option value="">Seleccione estado...</option>
                                @foreach(\App\Enums\EstadoBien::cases() as $estado)
                                    <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
                                        {{ $estado->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Descripción con Límite de 50 --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="descripcion" class="block text-sm font-bold text-gray-700">Descripción
                                General</label>
                            <span id="char-count" class="text-[10px] font-bold text-gray-400">0 / 255</span>
                        </div>
                        <textarea name="descripcion" id="descripcion" rows="2" required maxlength="255"
                            placeholder="Indique nombre, marca, modelo..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                {{-- Sección 3: Valores y Archivos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Precio (Bs.)</label>
                        <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio', '0.00') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Adquisición</label>
                        <input type="date" name="fecha_registro" id="fecha_registro"
                            min="2000-01-01" max="{{ now()->format('Y-m-d') }}"
                            value="{{ old('fecha_registro', now()->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fotografía</label>
                        <input type="file" name="fotografia" accept="image/*"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div id="campos-tipo-bien" class="transition-all duration-300"></div>

                <div class="flex justify-end gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('bienes.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Cancelar</a>
                    <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition">Guardar
                        Activo</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* 1. Lógica de Responsable */
        const dependenciasData = {
            @foreach($dependencias as $d)
                '{{ $d->id }}': '{{ $d->responsable ? $d->responsable->nombre : "Sin responsable asignado" }}',
            @endforeach
        };

        const depSelect = document.getElementById('dependencia_id');
        const respDisplay = document.getElementById('responsable_display');

        depSelect.addEventListener('change', function () {
            respDisplay.textContent = dependenciasData[this.value] || 'Seleccione una dependencia...';
            respDisplay.classList.toggle('text-gray-900', !!this.value);
            respDisplay.classList.toggle('font-bold', !!this.value);
        });

        /* 2. Lógica de Código con Sugerencia */
        const codigoInput = document.getElementById('codigo');
        const codigoError = document.getElementById('codigo-error');
        const sugerenciaContainer = document.getElementById('sugerencia-container');
        const spanSugerencia = document.getElementById('span-sugerencia');
        const btnSugerencia = document.getElementById('btn-sugerencia');

        // El código que vino del servidor originalmente
        const codigoOriginalSugerido = "{{ $codigoSugerido ?? '' }}";

        codigoInput.addEventListener('input', function (e) {
            const original = e.target.value;
            const cleaned = original.replace(/\D/g, '');

            if (original !== cleaned) {
                // Si codigoError no existe en el HTML original, esta línea no hará nada o dará error si no se maneja
                if(codigoError) {
                    codigoError.classList.remove('hidden');
                    setTimeout(() => codigoError.classList.add('hidden'), 2000);
                }
            }
            e.target.value = cleaned;

            if (codigoOriginalSugerido && cleaned !== codigoOriginalSugerido) {
                spanSugerencia.textContent = codigoOriginalSugerido;
                sugerenciaContainer.classList.remove('hidden');
            } else {
                sugerenciaContainer.classList.add('hidden');
            }
        });

        btnSugerencia.addEventListener('click', function () {
            codigoInput.value = codigoOriginalSugerido;
            sugerenciaContainer.classList.add('hidden');
        });

        codigoInput.addEventListener('blur', function () {
            if (this.value && this.value.length > 0) {
                this.value = this.value.padStart(8, '0');
            }
        });

        /* 3. Límite de Caracteres en Descripción */
        const descTextarea = document.getElementById('descripcion');
        const charCount = document.getElementById('char-count');

        descTextarea.addEventListener('input', function () {
            const len = this.value.length;
            charCount.textContent = `${len} / 255`;
            charCount.classList.toggle('text-red-500', len >= 255);
        });

        if (descTextarea) descTextarea.dispatchEvent(new Event('input'));

        /* 4. Campos Dinámicos */
        const camposPorTipo = {
            'ELECTRONICO': {
                isParent: true,
                subtipos: {
                    'MONITOR': ['serial', 'pantalla'],
                    'PC': ['serial', 'procesador', 'memoria', 'almacenamiento'],
                    'IMPRESORA': ['serial', 'modelo'],
                    'TELEVISOR': ['serial', 'pantalla', 'modelo']
                },
                fields: [
                    { name: 'subtipo', label: 'Subtipo', type: 'select', options: ['MONITOR', 'PC', 'IMPRESORA', 'TELEVISOR'], required: true },
                    { name: 'serial', label: 'Número de Serie', type: 'text' },
                    { name: 'modelo', label: 'Modelo', type: 'text' },
                    { name: 'procesador', label: 'Procesador', type: 'text' },
                    { name: 'memoria', label: 'RAM/Memoria', type: 'text' },
                    { name: 'almacenamiento', label: 'Almacenamiento', type: 'text' },
                    { name: 'pantalla', label: 'Pulgadas de Pantalla', type: 'text' }
                ]
            },
            'VEHICULO': {
                fields: [
                    { name: 'placa', label: 'Número de Placa', type: 'text' },
                    { name: 'marca', label: 'Marca', type: 'text' },
                    { name: 'motor', label: 'Serial de Motor', type: 'text' },
                    { name: 'chasis', label: 'Serial de Carrocería', type: 'text' }
                ]
            },
            'MOBILIARIO': {
                fields: [
                    { name: 'material', label: 'Material', type: 'text' },
                    { name: 'color', label: 'Color', type: 'text' },
                    { name: 'dimensiones', label: 'Dimensiones', type: 'text' }
                ]
            },
            'OTROS': {
                fields: [
                    { name: 'especificaciones', label: 'Especificaciones Extra', type: 'textarea' }
                ]
            }
        };

        const tipoBienSelect = document.getElementById('tipo_bien');
        const container = document.getElementById('campos-tipo-bien');

        tipoBienSelect.addEventListener('change', function () {
            const tipo = this.value;
            container.innerHTML = '';
            if (!tipo || !camposPorTipo[tipo]) return;

            const config = camposPorTipo[tipo];
            let html = `<div class="bg-blue-50/50 border border-blue-100 p-6 rounded-xl space-y-4 animate-fade-in">
                            <h3 class="text-blue-800 font-bold text-sm uppercase flex items-center gap-2">
                                <x-heroicon-o-information-circle class="w-5 h-5" /> Detalles Técnicos del ${tipo}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

            config.fields.forEach(campo => {
                if (campo.type === 'select') {
                    html += `<div>
                                <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label}</label>
                                <select name="${campo.name}" id="subtipo_selector" required class="w-full px-4 py-2 border border-blue-200 rounded-lg outline-none bg-white">
                                    <option value="">Seleccione...</option>
                                    ${campo.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                </select>
                            </div>`;
                } else if (campo.type === 'textarea') {
                    html += `<div class="md:col-span-2">
                                <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label}</label>
                                <textarea name="${campo.name}" data-field="${campo.name}" class="dynamic-field w-full px-4 py-2 border border-blue-200 rounded-lg bg-white uppercase"></textarea>
                            </div>`;
                } else {
                    const isReadonly = config.isParent ? 'readonly' : '';
                    const bgClass = config.isParent ? 'bg-gray-100' : 'bg-white';
                    const defaultValue = config.isParent ? 'S/N' : '';

                    html += `<div>
                                <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label}</label>
                                <input type="text" name="${campo.name}" data-field="${campo.name}"
                                    class="dynamic-field w-full px-4 py-2 border border-blue-200 rounded-lg ${bgClass} uppercase"
                                    ${isReadonly} value="${defaultValue}">
                            </div>`;
                }
            });

            html += `</div></div>`;
            container.innerHTML = html;

            if (config.isParent) {
                const selector = document.getElementById('subtipo_selector');
                selector.addEventListener('change', function() {
                    const st = this.value;
                    const camposVisibles = config.subtipos[st] || [];

                    document.querySelectorAll('.dynamic-field').forEach(input => {
                        const fieldName = input.getAttribute('data-field');
                        if (camposVisibles.includes(fieldName)) {
                            input.classList.remove('bg-gray-100');
                            input.classList.add('bg-white');
                            input.removeAttribute('readonly');
                            if(input.value === 'S/N') input.value = '';
                        } else {
                            input.classList.add('bg-gray-100');
                            input.classList.remove('bg-white');
                            input.setAttribute('readonly', true);
                            input.value = 'S/N';
                        }
                    });
                });
            }
        });

        if (tipoBienSelect.value) tipoBienSelect.dispatchEvent(new Event('change'));

        /* 5. Validación antes de enviar */
        const form = document.querySelector('form[action*="bienes"]');
        if (form) {
            form.addEventListener('submit', function (e) {
                const codigo = document.getElementById('codigo').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const tipo = tipoBienSelect.value;
                const estado = document.getElementById('estado')?.value || '';
                const dependenciaSel = depSelect.value;

                // Validación de Fecha (A partir del año 2000)
                const fechaInput = document.getElementById('fecha_registro');
                const fechaSeleccionada = new Date(fechaInput.value);
                const fechaMinima = new Date('2000-01-01');

                if (fechaInput.value && fechaSeleccionada < fechaMinima) {
                    e.preventDefault();
                    alert('La fecha de adquisición no puede ser anterior al año 2000.');
                    return;
                }

                if (!dependenciaSel) {
                    e.preventDefault();
                    alert('Debe asignar una dependencia antes de guardar el bien.');
                    return;
                }

                if (!codigo || codigo.length !== 8) {
                    e.preventDefault();
                    alert('El código debe contener exactamente 8 dígitos.');
                    return;
                }
                if (!descripcion) {
                    e.preventDefault();
                    alert('La descripción es obligatoria.');
                    return;
                }
                if (!tipo) {
                    e.preventDefault();
                    alert('Debe seleccionar el tipo de bien.');
                    return;
                }
                if (!estado) {
                    e.preventDefault();
                    alert('Debe seleccionar el estado del bien.');
                    return;
                }
            });
        }
    </script>
@endpush
