@extends('layouts.base')

@section('title', 'Editar Bien')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Bien</h1>

            <form action="{{ route('bienes.update', ['bien' => $bien->getKey()]) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

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

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Responsable</label>
                    <div id="responsable_display" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-gray-700">
                        {{ $bien->dependencia->responsable->nombre ?? 'No hay responsable asignado' }}
                    </div>
                </div>

                <div>
                    <label for="codigo" class="block text-sm font-semibold text-gray-700 mb-2">Código</label>
                    <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $bien->codigo) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Ej: BN001" maxlength="10">

                    <div id="sugerencia-container" class="mt-1 hidden">
                        <button type="button" id="btn-sugerencia"
                            class="text-[10px] text-blue-600 hover:underline font-bold italic">
                            💡 ¿Restaurar código original: <span id="span-sugerencia"></span>?
                        </button>
                    </div>

                    @error('codigo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="descripcion" class="block text-sm font-semibold text-gray-700">Descripción</label>
                        <span id="char-count" class="text-[10px] font-bold text-gray-400">0 / 50</span>
                    </div>
                    <textarea name="descripcion" id="descripcion" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Describe el bien..."
                        maxlength="50">{{ old('descripcion', $bien->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CAMPO PRECIO CON MÁSCARA --}}
                <div>
                    <label for="precio_display" class="block text-sm font-semibold text-gray-700 mb-2">Precio (Bs.)</label>
                    <input type="text" id="precio_display"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-right font-mono"
                        placeholder="0,00">

                    {{-- Campo oculto que Laravel procesará --}}
                    <input type="hidden" name="precio" id="precio_hidden" value="{{ old('precio', $bien->precio) }}">

                    @error('precio')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="fotografia" class="block text-sm font-semibold text-gray-700">Fotografía</label>
                    @if($bien->fotografia)
                        <div class="flex items-center gap-4">
                            <div class="w-32 h-32 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ str_starts_with($bien->fotografia, 'http') ? $bien->fotografia : asset('storage/' . $bien->fotografia) }}"
                                    alt="Fotografía actual" class="w-full h-full object-cover">
                            </div>
                            <p class="text-sm text-gray-500">Puedes subir una nueva imagen para reemplazarla.</p>
                        </div>
                    @endif
                    <input type="file" name="fotografia" id="fotografia" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                    <p class="text-gray-500 text-xs">Formatos admitidos: JPG, PNG, WEBP. Máx 2MB.</p>
                    @error('fotografia')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ubicacion" class="block text-sm font-semibold text-gray-700 mb-2">Ubicación</label>
                    <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $bien->ubicacion) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Oficina 101" maxlength="50">
                    @error('ubicacion')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

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

                <div>
                    <label for="fecha_registro" class="block text-sm font-semibold text-gray-700 mb-2">📅 Fecha de
                        Registro</label>
                    <div class="relative">
                        <input type="date" name="fecha_registro" id="fecha_registro"
                            value="{{ old('fecha_registro', optional($bien->fecha_registro)->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            style="font-size: 16px;">
                    </div>
                </div>

                <div>
                    <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Bien</label>
                    <select name="tipo" id="tipo"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Seleccione...</option>
                        <option value="inmueble" {{ old('tipo', $bien->tipo) == 'inmueble' ? 'selected' : '' }}>Inmueble
                        </option>
                        <option value="electrónico" {{ old('tipo', $bien->tipo) == 'electrónico' ? 'selected' : '' }}>
                            Electrónico</option>
                        <option value="mueble" {{ old('tipo', $bien->tipo) == 'mueble' ? 'selected' : '' }}>Mueble</option>
                        <option value="otro" {{ old('tipo', $bien->tipo) == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div id="campos-tipo-bien" class="space-y-6"></div>

                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('bienes.index') }}"
                        class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition">✗
                        Cancelar</a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">✓
                        Guardar Bien</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // --- RESPONSABLE ---
        const dependenciaSelect = document.getElementById('dependencia_id');
        const responsableDisplay = document.getElementById('responsable_display');
        const dependencias = {
            @foreach($dependencias as $d)
                '{{ $d->id }}': { responsable: {!! json_encode($d->responsable ? $d->responsable->nombre : null) !!} },
            @endforeach
            };

        function actualizarResponsable() {
            const id = dependenciaSelect.value;
            const dep = dependencias[id];
            responsableDisplay.textContent = dep?.responsable ?? 'No hay responsable asignado';
        }
        dependenciaSelect.addEventListener('change', actualizarResponsable);

        // --- CÓDIGO SUGERENCIA ---
        const codigoInput = document.getElementById('codigo');
        const sugerenciaContainer = document.getElementById('sugerencia-container');
        const spanSugerencia = document.getElementById('span-sugerencia');
        const btnSugerencia = document.getElementById('btn-sugerencia');
        const codigoOriginal = "{{ $bien->codigo }}";

        codigoInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^A-Za-z0-9]/g, '');
            if (e.target.value !== codigoOriginal) {
                spanSugerencia.textContent = codigoOriginal;
                sugerenciaContainer.classList.remove('hidden');
            } else {
                sugerenciaContainer.classList.add('hidden');
            }
        });
        btnSugerencia.addEventListener('click', () => {
            codigoInput.value = codigoOriginal;
            sugerenciaContainer.classList.add('hidden');
        });

        // --- DESCRIPCIÓN 50 CHARS ---
        const descTextarea = document.getElementById('descripcion');
        const charCount = document.getElementById('char-count');

        function actualizarContador() {
            const len = descTextarea.value.length;
            charCount.textContent = `${len} / 50`;
            charCount.classList.toggle('text-red-500', len >= 50);
        }
        descTextarea.addEventListener('input', function (e) {
            const regex = /[^A-Za-z0-9.,;:()áéíóúÁÉÍÓÚñÑ\s\-]/g;
            e.target.value = e.target.value.replace(regex, '');
            actualizarContador();
        });

        // --- PRECIO: MÁSCARA CONTABLE (ENTRADA POR DERECHA) ---
        const precioDisplay = document.getElementById('precio_display');
        const precioHidden = document.getElementById('precio_hidden');

        precioDisplay.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value === "") value = "0";

            let numericValue = (parseInt(value) / 100).toFixed(2);
            let displayValue = numericValue.replace('.', ',')
                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            this.value = displayValue;
            precioHidden.value = numericValue;
        });

        precioDisplay.addEventListener('click', function () {
            this.setSelectionRange(this.value.length, this.value.length);
        });

        // --- CAMPOS DINÁMICOS ---
        document.getElementById('tipo').addEventListener('change', function () {
            const tipo = this.value;
            const container = document.getElementById('campos-tipo-bien');
            container.innerHTML = '';
            if (tipo === 'electrónico') {
                container.innerHTML = `<div><label class="block text-sm font-semibold text-gray-700 mb-2">Serial</label><input type="text" name="serial" value="{{ old('serial', $bien->serial) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"></div>`;
            }
        });

        // --- INICIALIZACIÓN ---
        window.addEventListener('load', () => {
            actualizarResponsable();
            actualizarContador();

            // Inicializar precio si existe
            if (precioHidden.value) {
                let initialValue = Math.round(parseFloat(precioHidden.value) * 100).toString();
                precioDisplay.value = initialValue;
                precioDisplay.dispatchEvent(new Event('input'));
            }

            document.getElementById('tipo').dispatchEvent(new Event('change'));
        });
    </script>
@endpush