@extends('layouts.base')

@section('title', 'Registrar Bien')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => 'Nuevo Bien']]" />
@endpush
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-slate-900 shadow-xl dark:shadow-slate-800 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">
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

                {{-- Resumen de errores de validación --}}
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 text-red-700 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <span class="font-bold">Por favor corrige los siguientes errores:</span>
                        </div>
                        <ul class="text-sm list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Sección 1: Ubicación Administrativa --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 border-b dark:border-slate-700 pb-2 flex items-center gap-2">
                        <x-heroicon-o-home-modern class="w-5 h-5 text-blue-600 dark:text-blue-400" /> Asignación Administrativa
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="dependencia_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dependencia <span class="text-red-500">*</span></label>
                            <select name="dependencia_id" id="dependencia_id" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
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
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Responsable de la Dependencia</label>
                            <div id="responsable_display"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 italic text-sm flex items-center h-[50px]">
                                Seleccione una dependencia...
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección 2: Identificación del Bien --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 border-b dark:border-slate-700 pb-2 flex items-center gap-2">
                        <x-heroicon-o-identification class="w-5 h-5 text-blue-600 dark:text-blue-400" /> Identificación Técnica
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Código del Bien con Sugerencia --}}
                        <div>
                            <label for="codigo" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Código del Bien</label>
                            <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}"
                                maxlength="8" inputmode="numeric" placeholder="Ej: 00000001"
                                class="w-full px-4 py-3 border @error('codigo') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition uppercase bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                required pattern="\d{8}" title="El código debe contener exactamente 8 dígitos numéricos">

                            @error('codigo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            {{-- Contenedor para la sugerencia --}}
                            <div id="sugerencia-container" class="mt-1 hidden">
                                <button type="button" id="btn-sugerencia"
                                    class="text-[10px] text-blue-600 dark:text-blue-400 hover:underline font-bold italic">
                                    💡 ¿Usar código sugerido: <span id="span-sugerencia"></span>?
                                </button>
                            </div>
                        </div>

                        {{-- Tipo de Bien --}}
                        <div>
                            <label for="tipo_bien" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo de Bien</label>
                            <select name="tipo_bien" id="tipo_bien" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <option value="">Seleccione tipo...</option>
                                @foreach($tiposBien as $value => $label)
                                    <option value="{{ $value }}" {{ old('tipo_bien') == $value ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label for="estado" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Estado Físico</label>
                            <select name="estado" id="estado" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <option value="">Seleccione estado...</option>
                                @foreach(\App\Enums\EstadoBien::cases() as $estado)
                                    <option value="{{ $estado->value }}" {{ old('estado') == $estado->value ? 'selected' : '' }}>
                                        {{ $estado->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Descripción con Límite de 255 --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="descripcion" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Descripción
                                General</label>
                            <span id="char-count" class="text-[10px] font-bold text-gray-400 dark:text-gray-500">0 / 255</span>
                        </div>
                        <textarea name="descripcion" id="descripcion" rows="2" required maxlength="255"
                            placeholder="Indique nombre, marca, modelo..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                {{-- Sección 3: Valores y Archivos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Precio (Bs.)</label>
                        <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio', '0.00') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Fecha de Adquisición</label>
                        <input type="date" name="fecha_registro" id="fecha_registro"
                            min="2000-01-01" max="{{ now()->format('Y-m-d') }}"
                            value="{{ old('fecha_registro', now()->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Fotografía</label>
                        <input type="file" name="fotografia" accept="image/*"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-blue-300">
                    </div>
                </div>

                <div id="campos-tipo-bien" class="transition-all duration-300"></div>

                <div class="flex justify-end gap-4 pt-8 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('bienes.index') }}"
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition">Cancelar</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            /* 1. Lógica de Responsable */
            const dependenciasData = {
                @foreach($dependencias as $d)
                    '{{ $d->id }}': '{{ $d->responsable ? addslashes($d->responsable->nombre) : "Sin responsable asignado" }}',
                @endforeach
            };

            const depSelect = document.getElementById('dependencia_id');
            const respDisplay = document.getElementById('responsable_display');

            if (depSelect && respDisplay) {
                // Disparar evento inicial si hay valor preseleccionado
                if (depSelect.value) {
                    respDisplay.textContent = dependenciasData[depSelect.value] || 'Seleccione una dependencia...';
                    respDisplay.classList.toggle('text-gray-900', !!depSelect.value);
                    respDisplay.classList.toggle('font-bold', !!depSelect.value);
                }

                depSelect.addEventListener('change', function () {
                    const responsable = dependenciasData[this.value];
                    respDisplay.textContent = responsable || 'Seleccione una dependencia...';
                    if (responsable && responsable !== 'Sin responsable asignado') {
                        respDisplay.classList.add('text-gray-900', 'font-bold');
                        respDisplay.classList.remove('text-gray-500', 'italic');
                    } else {
                        respDisplay.classList.remove('text-gray-900', 'font-bold');
                        respDisplay.classList.add('text-gray-500', 'italic');
                    }
                });
            }

            /* 2. Lógica de Código con Sugerencia por Dependencia */
            const codigoInput = document.getElementById('codigo');
            const sugerenciaContainer = document.getElementById('sugerencia-container');
            const spanSugerencia = document.getElementById('span-sugerencia');
            const btnSugerencia = document.getElementById('btn-sugerencia');

            const baseUrl = "{{ url('bienes') }}";
            let codigoSugeridoDependencia = null;

            function actualizarSugerencia(codigo) {
                codigoSugeridoDependencia = codigo;
                spanSugerencia.textContent = codigo;
                sugerenciaContainer.classList.remove('hidden');
            }

            function ocultarSugerencia() {
                codigoSugeridoDependencia = null;
                sugerenciaContainer.classList.add('hidden');
            }

            function obtenerSugerencia(dependenciaId) {
                if (!dependenciaId) {
                    ocultarSugerencia();
                    return;
                }

                fetch(`${baseUrl}/${dependenciaId}/recomendar-codigo`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                if (data.success === false && data.error === 'rango_exhausto') {
                                    alert(data.mensaje);
                                    ocultarSugerencia();
                                } else {
                                    throw new Error('Error al obtener sugerencia');
                                }
                            }).catch(() => { throw new Error('Error de red'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.success) {
                            actualizarSugerencia(data.codigo);
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        ocultarSugerencia();
                    });
            }

            // Cambio de dependencia: limpiar código y cargar nueva sugerencia
            depSelect.addEventListener('change', function () {
                const depId = this.value;
                codigoInput.value = '';
                ocultarSugerencia();
                if (depId) obtenerSugerencia(depId);
            });

            // Input: sanitización y control de sugerencia
            if (codigoInput) {
                codigoInput.addEventListener('input', function (e) {
                    let original = e.target.value;
                    let cleaned = original.replace(/\D/g, '');

                    // Limitar a 8 dígitos
                    if (cleaned.length > 8) {
                        cleaned = cleaned.slice(0, 8);
                    }

                    if (original !== cleaned) {
                        e.target.value = cleaned;
                    }

                    if (codigoSugeridoDependencia && cleaned !== codigoSugeridoDependencia) {
                        spanSugerencia.textContent = codigoSugeridoDependencia;
                        sugerenciaContainer.classList.remove('hidden');
                    } else if (sugerenciaContainer) {
                        sugerenciaContainer.classList.add('hidden');
                    }
                });

                // Formatear a 8 dígitos al perder el foco
                codigoInput.addEventListener('blur', function () {
                    if (this.value && this.value.length > 0) {
                        this.value = this.value.padStart(8, '0');
                    }
                });
            }

            // Botón: aplicar sugerencia
            if (btnSugerencia && sugerenciaContainer) {
                btnSugerencia.addEventListener('click', function () {
                    if (codigoSugeridoDependencia) {
                        codigoInput.value = codigoSugeridoDependencia;
                        sugerenciaContainer.classList.add('hidden');
                    }
                });
            }

            /* 3. Límite de Caracteres en Descripción */
            const descTextarea = document.getElementById('descripcion');
            const charCount = document.getElementById('char-count');

            if (descTextarea && charCount) {
                function updateCharCount() {
                    const len = descTextarea.value.length;
                    charCount.textContent = `${len} / 255`;
                    if (len >= 255) {
                        charCount.classList.add('text-red-500');
                        charCount.classList.remove('text-gray-400');
                    } else {
                        charCount.classList.remove('text-red-500');
                        charCount.classList.add('text-gray-400');
                    }
                }
                
                descTextarea.addEventListener('input', updateCharCount);
                updateCharCount(); // Inicializar
            }

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

            if (tipoBienSelect && container) {
                function loadDynamicFields() {
                    const tipo = tipoBienSelect.value;
                    container.innerHTML = '';
                    
                    if (!tipo || !camposPorTipo[tipo]) return;

                    const config = camposPorTipo[tipo];
                    let html = `<div class="bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-6 rounded-xl space-y-4">
                                    <h3 class="text-blue-800 dark:text-blue-300 font-bold text-sm uppercase flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Detalles Técnicos del ${tipo}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

                    config.fields.forEach(campo => {
                        if (campo.type === 'select') {
                            html += `<div>
                                        <label class="block text-xs font-bold text-blue-700 dark:text-blue-400 mb-1">${campo.label} ${campo.required ? '<span class="text-red-500">*</span>' : ''}</label>
                                        <select name="${campo.name}" id="subtipo_selector" ${campo.required ? 'required' : ''} class="w-full px-4 py-2 border border-blue-200 dark:border-blue-700 rounded-lg outline-none bg-white dark:bg-gray-800">
                                            <option value="">Seleccione...</option>
                                            ${campo.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                        </select>
                                    </div>`;
                        } else if (campo.type === 'textarea') {
                            html += `<div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-blue-700 dark:text-blue-400 mb-1">${campo.label}</label>
                                        <textarea name="${campo.name}" data-field="${campo.name}" class="dynamic-field w-full px-4 py-2 border border-blue-200 dark:border-blue-700 rounded-lg bg-white dark:bg-gray-800" rows="2"></textarea>
                                    </div>`;
                        } else {
                            const isReadonly = config.isParent ? 'readonly' : '';
                            const bgClass = config.isParent ? 'bg-gray-100 dark:bg-gray-700' : 'bg-white dark:bg-gray-800';
                            const defaultValue = config.isParent ? 'S/N' : '';

                            html += `<div>
                                        <label class="block text-xs font-bold text-blue-700 dark:text-blue-400 mb-1">${campo.label}</label>
                                        <input type="text" name="${campo.name}" data-field="${campo.name}"
                                            class="dynamic-field w-full px-4 py-2 border border-blue-200 dark:border-blue-700 rounded-lg ${bgClass}"
                                            ${isReadonly} value="${defaultValue}">
                                    </div>`;
                        }
                    });

                    html += `</div></div>`;
                    container.innerHTML = html;

                    if (config.isParent) {
                        const selector = document.getElementById('subtipo_selector');
                        if (selector) {
                            selector.addEventListener('change', function() {
                                const st = this.value;
                                const camposVisibles = config.subtipos[st] || [];

                                document.querySelectorAll('.dynamic-field').forEach(input => {
                                    const fieldName = input.getAttribute('data-field');
                                    if (camposVisibles.includes(fieldName)) {
                                        input.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                                        input.classList.add('bg-white', 'dark:bg-gray-800');
                                        input.removeAttribute('readonly');
                                        if(input.value === 'S/N') input.value = '';
                                    } else {
                                        input.classList.add('bg-gray-100', 'dark:bg-gray-700');
                                        input.classList.remove('bg-white', 'dark:bg-gray-800');
                                        input.setAttribute('readonly', true);
                                        input.value = 'S/N';
                                    }
                                });
                            });
                        }
                    }
                }

                tipoBienSelect.addEventListener('change', loadDynamicFields);
                
                // Cargar campos si ya hay un valor seleccionado (ej: después de error de validación)
                if (tipoBienSelect.value) {
                    loadDynamicFields();
                }
            }

            /* 5. Validación antes de enviar */
            const form = document.querySelector('form[action*="bienes"]');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const codigo = document.getElementById('codigo');
                    const codigoValue = codigo ? codigo.value.trim() : '';
                    const descripcion = document.getElementById('descripcion');
                    const descripcionValue = descripcion ? descripcion.value.trim() : '';
                    const tipo = tipoBienSelect ? tipoBienSelect.value : '';
                    const estado = document.getElementById('estado');
                    const estadoValue = estado ? estado.value : '';
                    const dependenciaSel = depSelect ? depSelect.value : '';

                    // Validación de código
                    if (!codigoValue || codigoValue.length !== 8 || !/^\d{8}$/.test(codigoValue)) {
                        e.preventDefault();
                        alert('El código debe contener exactamente 8 dígitos numéricos.');
                        if (codigo) codigo.focus();
                        return;
                    }

                    // Validación de descripción
                    if (!descripcionValue) {
                        e.preventDefault();
                        alert('La descripción es obligatoria.');
                        if (descripcion) descripcion.focus();
                        return;
                    }

                    // Validación de dependencia
                    if (!dependenciaSel) {
                        e.preventDefault();
                        alert('Debe asignar una dependencia antes de guardar el bien.');
                        if (depSelect) depSelect.focus();
                        return;
                    }

                    // Validación de tipo
                    if (!tipo) {
                        e.preventDefault();
                        alert('Debe seleccionar el tipo de bien.');
                        if (tipoBienSelect) tipoBienSelect.focus();
                        return;
                    }

                    // Validación de estado
                    if (!estadoValue) {
                        e.preventDefault();
                        alert('Debe seleccionar el estado del bien.');
                        if (estado) estado.focus();
                        return;
                    }

                    // Validación de Fecha
                    const fechaInput = document.getElementById('fecha_registro');
                    if (fechaInput && fechaInput.value) {
                        const fechaSeleccionada = new Date(fechaInput.value);
                        const fechaMinima = new Date('2000-01-01');
                        const fechaMaxima = new Date();

                        if (fechaSeleccionada < fechaMinima) {
                            e.preventDefault();
                            alert('La fecha de adquisición no puede ser anterior al año 2000.');
                            fechaInput.focus();
                            return;
                        }
                        
                        if (fechaSeleccionada > fechaMaxima) {
                            e.preventDefault();
                            alert('La fecha de adquisición no puede ser futura.');
                            fechaInput.focus();
                            return;
                        }
                    }
                });
            }
        });
    </script>
@endpush