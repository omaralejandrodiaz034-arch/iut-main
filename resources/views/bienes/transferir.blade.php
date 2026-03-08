@extends('layouts.base')

@section('title', 'Transferir Bien')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => $bien->codigo, 'url' => route('bienes.show', $bien)], ['label' => 'Transferir']]" />
@endpush

<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-8 py-5">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                🔄 Transferir Bien: <span class="font-mono text-indigo-200">{{ $bien->codigo }}</span>
            </h1>
            <p class="text-indigo-100 text-xs mt-1 opacity-90">Traslado de bien a otra dependencia</p>
        </div>

        {{-- Info del bien --}}
        <div class="bg-indigo-50/50 px-8 py-4 border-b border-gray-100">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase">Descripción</p>
                    <p class="font-medium text-gray-800">{{ $bien->descripcion }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase">Dependencia Actual</p>
                    <p class="font-medium text-gray-800">{{ optional($bien->dependencia)->nombre ?? 'Sin dependencia' }}</p>
                    <p class="text-xs text-gray-400">{{ optional($bien->dependencia?->unidadAdministradora)->nombre ?? '' }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('bienes.transferir', $bien) }}" class="px-8 py-6 space-y-5">
            @csrf
            @method('PATCH')

            @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                ⚠️ {{ session('error') }}
            </div>
            @endif

            <div>
                <label for="dependencia_id" class="block text-sm font-bold text-gray-700 mb-2">
                    Dependencia de Destino <span class="text-red-500">*</span>
                </label>
                <select name="dependencia_id" id="dependencia_id" required
                    class="w-full px-4 py-3 border-2 @error('dependencia_id') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                    <option value="">-- Seleccionar dependencia --</option>
                    @foreach($dependencias as $dep)
                    <option value="{{ $dep->id }}" @selected(old('dependencia_id') == $dep->id)>
                        {{ $dep->nombre }}
                        @if($dep->unidadAdministradora) — {{ $dep->unidadAdministradora->nombre }} @endif
                        @if($dep->unidadAdministradora?->organismo) — {{ $dep->unidadAdministradora->organismo->nombre }} @endif
                    </option>
                    @endforeach
                </select>
                @error('dependencia_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="motivo" class="block text-sm font-bold text-gray-700 mb-2">
                    Motivo del Traslado <span class="text-red-500">*</span>
                </label>
                <textarea name="motivo" id="motivo" rows="4" required
                    class="w-full px-4 py-3 border-2 @error('motivo') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm resize-none"
                    placeholder="Describa el motivo del traslado...">{{ old('motivo') }}</textarea>
                @error('motivo')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-2">
                <a href="{{ route('bienes.show', $bien) }}"
                    class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition">
                    ← Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Confirmar transferencia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
