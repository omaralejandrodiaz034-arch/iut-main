@extends('layouts.base')

@section('title', 'Organismos')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">🏢 Organismos</h1>
        <a href="{{ route('organismos.create') }}"
            class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm hover:bg-blue-700 transition-all active:scale-95">
            <span>+</span>
            <span>Nuevo Organismo</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded shadow-sm flex items-center gap-3">
            <span class="font-bold">✓</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Panel de Filtros --}}
    <div class="bg-white shadow-sm rounded-xl p-8 mb-8 border border-gray-100">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <span>🔍</span> Filtrar Organismos
        </h2>
        <form action="{{ route('organismos.index') }}" method="GET" id="filterForm"
            class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <label for="buscar" class="block text-sm font-bold text-slate-700 mb-2">Búsqueda General</label>
                <input type="text" name="buscar" id="buscar" maxlength="30"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    placeholder="Buscar..." value="{{ $validated['buscar'] ?? '' }}">
                <p class="text-gray-400 text-[10px] mt-1 italic">Máximo 30 caracteres.</p>
            </div>

            <div>
                <label for="codigo_filtro" class="block text-sm font-bold text-slate-700 mb-2">Código</label>
                <input type="text" name="codigo" id="codigo_filtro" maxlength="8" inputmode="numeric"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-mono"
                    placeholder="00000000" value="{{ $validated['codigo'] ?? '' }}">
                <p id="error-codigo" class="text-red-500 text-[10px] mt-1 hidden font-bold italic">¡Solo se permiten
                    números!</p>
                <p class="text-blue-500 text-[10px] mt-1 italic">Solo 8 números.</p>
            </div>

            <div>
                <label for="nombre_filtro" class="block text-sm font-bold text-slate-700 mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre_filtro" maxlength="30"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    placeholder="Nombre..." value="{{ $validated['nombre'] ?? '' }}">
                <p class="text-gray-400 text-[10px] mt-1 italic">Máximo 30 caracteres.</p>
            </div>

            <div class="md:col-span-3 flex items-center gap-6 pt-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-2.5 rounded-lg font-bold shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                    🔎 Buscar
                </button>
                <a href="{{ route('organismos.index') }}"
                    class="flex items-center gap-2 text-black font-bold transition-opacity hover:opacity-70">
                    <span class="text-lg">✕</span>
                    <span>Limpiar Filtros</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Nombre</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($organismos as $organismo)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-400 font-mono italic">{{ $organismo->id }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-700 font-mono">{{ $organismo->codigo }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 font-medium">{{ $organismo->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end">
                                    @include('components.action-buttons', ['resource' => 'organismos', 'model' => $organismo, 'confirm' => '¿Eliminar?', 'label' => $organismo->nombre])
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
            const codigoInput = document.getElementById('codigo_filtro');
            const errorMsg = document.getElementById('error-codigo');

            // BLOQUEO REAL: Si no es número, se borra al instante
            codigoInput.addEventListener('input', function (e) {
                const originalValue = e.target.value;
                // Eliminar todo lo que no sea número
                const cleanValue = originalValue.replace(/[^0-9]/g, '');

                if (originalValue !== cleanValue) {
                    errorMsg.classList.remove('hidden');
                    setTimeout(() => errorMsg.classList.add('hidden'), 2000);
                }

                e.target.value = cleanValue.slice(0, 8);
            });

            // Bloqueo de 30 caracteres para los otros campos
            const inputs30 = [document.getElementById('buscar'), document.getElementById('nombre_filtro')];
            inputs30.forEach(input => {
                input.addEventListener('input', e => {
                    if (e.target.value.length > 30) e.target.value = e.target.value.slice(0, 30);
                });
            });
        });
    </script>
@endsection