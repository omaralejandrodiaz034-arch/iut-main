@extends('layouts.base')

@section('title', 'Registrar Bien')

@section('content')
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
                            <label for="dependencia_id" class="block text-sm font-bold text-gray-700 mb-2">Dependencia
                                (Opcional)</label>
                            <select name="dependencia_id" id="dependencia_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                                <option value="">Sin asignar (Almacén Central)</option>
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep->id }}" {{ old('dependencia_id') == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
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
                        {{-- Código del Bien --}}
                        <div>
                            <label for="codigo" class="block text-sm font-bold text-gray-700 mb-2">Código del Bien</label>
                            <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $codigoSugerido ?? '') }}"
                                maxlength="8" inputmode="numeric" placeholder="00000000"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono focus:ring-2 focus:ring-blue-500 outline-none transition">

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
                            <span id="char-count" class="text-[10px] font-bold text-gray-400">0 / 50</span>
                        </div>
                        <textarea name="descripcion" id="descripcion" rows="2" required maxlength="50"
                            placeholder="Nombre, marca, modelo..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                {{-- Sección 3: Valores y Archivos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- PRECIO CON MÁSCARA CONTABLE --}}
                    <div>
                        <label for="precio_display" class="block text-sm font-bold text-gray-700 mb-2">Precio (Bs.)</label>
                        <input type="text" id="precio_display"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-right font-mono"
                            placeholder="0,00">
                        {{-- Campo oculto para el envío de datos --}}
                        <input type="hidden" name="precio" id="precio_hidden" value="{{ old('precio', '0.00') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Adquisición</label>
                        <input type="date" name="fecha_registro" value="{{ old('fecha_registro', now()->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fotografía</label>
                        <input type="file" name="fotografia" accept="image/*"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
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

        /* 2. Código con Sugerencia */
        const codigoInput = document.getElementById('codigo');
        const sugerenciaContainer = document.getElementById('sugerencia-container');
        const spanSugerencia = document.getElementById('span-sugerencia');
        const btnSugerencia = document.getElementById('btn-sugerencia');
        const codigoOriginalSugerido = "{{ $codigoSugerido ?? '' }}";

        codigoInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '');
            if (codigoOriginalSugerido && e.target.value !== codigoOriginalSugerido) {
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

        /* 3. Límite de Caracteres en Descripción */
        const descTextarea = document.getElementById('descripcion');
        const charCount = document.getElementById('char-count');

        descTextarea.addEventListener('input', function () {
            const len = this.value.length;
            charCount.textContent = `${len} / 50`;
            charCount.classList.toggle('text-red-500', len >= 50);
        });

        /* 4. PRECIO: MÁSCARA CONTABLE (DERECHA A IZQUIERDA) */
        const precioDisplay = document.getElementById('precio_display');
        const precioHidden = document.getElementById('precio_hidden');

        precioDisplay.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value === "") value = "0";

            let numericValue = (parseInt(value) / 100).toFixed(2);
            let displayValue = numericValue.replace('.', ',');

            // Separador de miles opcional
            displayValue = displayValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            this.value = displayValue;
            precioHidden.value = numericValue;
        });

        // Inicializar valor si existe old()
        if (precioHidden.value) {
            let initial = (parseFloat(precioHidden.value) * 100).toString();
            precioDisplay.value = initial;
            precioDisplay.dispatchEvent(new Event('input'));
        }

        /* 5. Campos Dinámicos */
        const camposPorTipo = {
            'ELECTRONICO': [
                { name: 'serial', label: 'Número de Serie', type: 'text' },
                { name: 'modelo', label: 'Modelo/Versión', type: 'text' },
                { name: 'procesador', label: 'Procesador', type: 'text' }
            ],
            'VEHICULO': [
                { name: 'placa', label: 'Número de Placa', type: 'text' },
                { name: 'marca', label: 'Marca', type: 'text' }
            ],
            'MOBILIARIO': [
                { name: 'material', label: 'Material', type: 'text' },
                { name: 'dimensiones', label: 'Dimensiones', type: 'text' }
            ],
            'OTROS': [
                { name: 'especificaciones', label: 'Especificaciones Extra', type: 'textarea' }
            ]
        };

        const tipoBienSelect = document.getElementById('tipo_bien');
        const container = document.getElementById('campos-tipo-bien');

        tipoBienSelect.addEventListener('change', function () {
            const tipo = this.value;
            container.innerHTML = '';
            if (!tipo || !camposPorTipo[tipo]) return;

            let html = `
                    <div class="bg-blue-50/50 border border-blue-100 p-6 rounded-xl space-y-4 animate-fade-in">
                        <h3 class="text-blue-800 font-bold text-sm uppercase tracking-wider flex items-center gap-2">Detalles Técnicos del ${tipo}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

            camposPorTipo[tipo].forEach(campo => {
                html += `
                        <div class="${campo.type === 'textarea' ? 'md:col-span-2' : ''}">
                            <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label}</label>
                            <input type="${campo.type}" name="${campo.name}" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        </div>`;
            });
            html += `</div></div>`;
            container.innerHTML = html;
        });

        if (tipoBienSelect.value) tipoBienSelect.dispatchEvent(new Event('change'));
    </script>
@endpush