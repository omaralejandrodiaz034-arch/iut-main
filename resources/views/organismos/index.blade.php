@extends('layouts.base')

@section('title', 'Organismos')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">🏢 Organismos</h1>
        <a href="{{ route('organismos.create') }}"
            class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm hover:bg-blue-700 transition-all active:scale-95 text-sm">
            <span>+</span>
            <span>Nuevo Organismo</span>
        </a>
    </div>

    @if(session('success'))
<<<<<<< HEAD
        <div
            class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm flex items-center gap-3 text-base">
=======
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm flex items-center gap-3 text-base">
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
            <span class="font-bold">✓</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

<<<<<<< HEAD
    {{-- Panel de Filtros --}}
=======
    {{-- Panel de Filtros - Punto Medio --}}
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
    <div class="bg-white shadow-sm rounded-xl p-6 mb-6 border border-gray-100">
        <h2 class="text-xl font-bold text-slate-800 mb-5 flex items-center gap-2">
            <span>🔍</span> Filtrar Organismos
        </h2>
        <form action="{{ route('organismos.index') }}" method="GET" id="filterForm"
            class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div>
                <label for="buscar" class="block text-sm font-bold text-slate-700 mb-2">Búsqueda General</label>
                <input type="text" name="buscar" id="buscar" maxlength="30"
                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm"
<<<<<<< HEAD
                    placeholder="Solo letras y números..." value="{{ $validated['buscar'] ?? '' }}">
                <p id="error-buscar" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">No se permiten caracteres especiales.</p>
                <p class="text-gray-400 text-[11px] mt-1 italic">Máximo 30 caracteres (sin símbolos).</p>
=======
                    placeholder="Buscar..." value="{{ $validated['buscar'] ?? '' }}">
                <p class="text-gray-400 text-[11px] mt-1 italic">Máximo 30 caracteres.</p>
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
            </div>

            <div>
                <label for="codigo_filtro" class="block text-sm font-bold text-slate-700 mb-2">Código</label>
                <input type="text" name="codigo" id="codigo_filtro" maxlength="8" inputmode="numeric"
                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono shadow-sm"
                    placeholder="00000000" value="{{ $validated['codigo'] ?? '' }}">
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo números.</p>
                <p class="text-blue-500 text-[10px] mt-1 italic">Máximo 8 números.</p>
            </div>

            <div>
                <label for="nombre_filtro" class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre_filtro" maxlength="30"
                    class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm"
                    placeholder="Nombre..." value="{{ $validated['nombre'] ?? '' }}">
<<<<<<< HEAD
                <p id="error-nombre" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">Solo letras.</p>
                <p class="text-gray-400 text-[11px] mt-1 italic">Máximo 30 caracteres (solo letras).</p>
=======
                <p class="text-gray-400 text-[11px] mt-1 italic">Máximo 30 caracteres.</p>
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
            </div>

            <div class="md:col-span-3 flex items-center gap-6 pt-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold shadow-md hover:bg-blue-700 transition-all active:scale-95 text-base">
                    🔎 Aplicar Filtros
                </button>
                <a href="{{ route('organismos.index') }}"
                    class="flex items-center gap-2 text-slate-500 font-bold transition-all hover:text-black text-sm">
                    <span class="text-lg">✕</span>
                    <span>Limpiar</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla - Punto Medio --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($organismos as $organismo)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-400 font-mono italic">{{ $organismo->id }}</td>
                            <td class="px-6 py-4 text-base font-bold text-slate-700 font-mono tracking-tight">{{ $organismo->codigo }}</td>
                            <td class="px-6 py-4 text-base text-slate-600 font-medium">{{ $organismo->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end">
                                    @include('components.action-buttons', [
<<<<<<< HEAD
                                        'resource' => 'organismos',
                                        'model' => $organismo,
                                        'confirm' => '¿Eliminar?',
=======
                                        'resource' => 'organismos', 
                                        'model' => $organismo, 
                                        'confirm' => '¿Eliminar?', 
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
                                        'label' => $organismo->nombre
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">No hay resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buscarInput = document.getElementById('buscar');
            const errorMsgBuscar = document.getElementById('error-buscar');
            const codigoInput = document.getElementById('codigo_filtro');
            const errorMsgCodigo = document.getElementById('error-codigo');
            const nombreInput = document.getElementById('nombre_filtro');
            const errorMsgNombre = document.getElementById('error-nombre');

<<<<<<< HEAD
            // 1. Lógica para BÚSQUEDA GENERAL (Solo letras, números y espacios)
            buscarInput?.addEventListener('input', function (e) {
                const originalValue = e.target.value;
                // Remueve caracteres que no sean letras, números o espacios
                const cleanValue = originalValue.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, '');
                
=======
            codigoInput?.addEventListener('input', function (e) {
                const originalValue = e.target.value;
                const cleanValue = originalValue.replace(/[^0-9]/g, '');

>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
                if (originalValue !== cleanValue) {
                    errorMsgBuscar?.classList.remove('hidden');
                    setTimeout(() => errorMsgBuscar?.classList.add('hidden'), 2000);
                }
                e.target.value = cleanValue.slice(0, 30);
            });

            // 2. Lógica para CÓDIGO (Solo permite números)
            codigoInput?.addEventListener('input', function (e) {
                const originalValue = e.target.value;
                const cleanValue = originalValue.replace(/[^0-9]/g, '');
                if (originalValue !== cleanValue) {
                    errorMsgCodigo?.classList.remove('hidden');
                    setTimeout(() => errorMsgCodigo?.classList.add('hidden'), 2000);
                }
                e.target.value = cleanValue.slice(0, 8);
            });

<<<<<<< HEAD
            // 3. Lógica para NOMBRE (Solo letras y espacios, prohibe números y símbolos)
            nombreInput?.addEventListener('input', function (e) {
                const originalValue = e.target.value;
                // Solo letras, tildes y espacios
                const cleanValue = originalValue.replace(/[^a-zA-Z\sáéíóúÁÉÍÓÚñÑ]/g, '');
                
                if (originalValue !== cleanValue) {
                    errorMsgNombre?.classList.remove('hidden');
                    setTimeout(() => errorMsgNombre?.classList.add('hidden'), 2000);
                }
                e.target.value = cleanValue.slice(0, 30);
=======
            const inputs30 = [document.getElementById('buscar'), document.getElementById('nombre_filtro')];
            inputs30.forEach(input => {
                input?.addEventListener('input', e => {
                    if (e.target.value.length > 30) e.target.value = e.target.value.slice(0, 30);
                });
>>>>>>> 31838aec7962599342adf5f0477eb157d3c8bcc8
            });
        });
    </script>
@endsection