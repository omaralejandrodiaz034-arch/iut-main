@extends('layouts.base')

@section('title', 'Reincorporar Bien')

@section('content')
@if(auth()->user()?->isAdmin())
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => $bien->codigo, 'url' => route('bienes.show', $bien)], ['label' => 'Reincorporar']]" />
@endpush
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Información -->
    <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl">
        <div class="flex">
            <x-heroicon-o-check-circle class="h-6 w-6 text-green-600 mr-4 flex-shrink-0 mt-1" />
            <div>
                <h3 class="text-lg font-medium text-green-800">Reincorporación</h3>
                <p class="mt-2 text-sm text-green-700">
                    Al confirmar, el bien pasará a estado "Activo" y quedará nuevamente disponible en las listas institucionales.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <x-heroicon-o-document-text class="h-7 w-7 text-green-100" />
                Reincorporar Bien
            </h1>
            <p class="mt-2 text-green-100 text-sm opacity-90">
                Código: <span class="font-mono font-semibold">{{ $bien->codigo }}</span> — {{ Str::limit($bien->descripcion, 60) }}
            </p>
        </div>

        <form action="{{ route('bienes.reincorporar', $bien->id) }}" method="POST" class="p-8 space-y-8" id="formReincorporar" enctype="multipart/form-data">

            @csrf
            @method('POST')

            <div>
                <label for="motivo" class="block text-base font-semibold text-gray-800 mb-3">
                    Motivo de la reincorporación <span class="text-red-600">*</span>
                </label>
                <textarea name="motivo" id="motivo" rows="5" required placeholder="Explique el motivo por el cual se reincorpora el bien" class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition resize-y min-h-[140px] @error('motivo') border-red-500 @enderror">{{ old('motivo') }}</textarea>

                @error('motivo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="acta_reincorporacion" class="block text-base font-semibold text-gray-800 mb-3">Acta de reincorporación (opcional, PDF)</label>
                <input type="file" name="acta_reincorporacion" id="acta_reincorporacion" accept="application/pdf" />
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t border-gray-200">
                <a href="{{ route('bienes.show', $bien->id) }}" class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-left class="h-5 w-5" />
                    Cancelar
                </a>

                <button type="submit" id="btnConfirmar" class="px-10 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-green-300 transition flex items-center justify-center gap-2 disabled:opacity-60">
                    Confirmar reincorporación
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formReincorporar');
    const btn = document.getElementById('btnConfirmar');
    const motivoInput = document.getElementById('motivo');

    const checkBtn = () => { btn.disabled = motivoInput.value.trim().length < 10; };
    motivoInput.addEventListener('input', checkBtn);
    checkBtn();

    form.addEventListener('submit', function() {
        btn.disabled = true;
        btn.innerHTML = 'Procesando...';
    });
});
</script>
@endpush

@endif
@endsection
