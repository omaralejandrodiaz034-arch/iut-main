@extends('layouts.base')

@section('title', 'Desincorporar Bien')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => $bien->codigo, 'url' => route('bienes.show', $bien)], ['label' => 'Desincorporar']]" />
@endpush
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <!-- Advertencia clara -->
    <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-xl">
        <div class="flex">
            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 mr-4 flex-shrink-0 mt-1" />
            <div>
                <h3 class="text-lg font-medium text-red-800">Acción irreversible</h3>
                <p class="mt-2 text-sm text-red-700">
                    Al confirmar, el bien pasará a estado "Desincorporado" y se descargará automáticamente
                    el acta de desincorporación generada por el sistema.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">

        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <x-heroicon-o-archive-box-x-mark class="h-7 w-7 text-red-100" />
                Desincorporar Bien
            </h1>
            <p class="mt-2 text-red-100 text-sm opacity-90">
                Código: <span class="font-mono font-semibold">{{ $bien->codigo }}</span> —
                {{ Str::limit($bien->descripcion, 60) }}
            </p>
        </div>

        <!-- Formulario simplificado (solo motivo) -->
        <form action="{{ route('bienes.desincorporar', $bien->id) }}"
              method="POST"
              class="p-8 space-y-8"
              id="formDesincorporar">

            @csrf
            @method('POST')

            <!-- Motivo (único campo requerido ahora) -->
            <div>
                <label for="motivo" class="block text-base font-semibold text-gray-800 mb-3">
                    Motivo de la desincorporación <span class="text-red-600">*</span>
                </label>
                <textarea
                    name="motivo"
                    id="motivo"
                    rows="5"
                    required
                    placeholder="Explique detalladamente el motivo (daño irreparable, obsolescencia, extravío, donación, venta, destrucción autorizada, etc.)"
                    class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition resize-y min-h-[140px] @error('motivo') border-red-500 @enderror"
                >{{ old('motivo') }}</textarea>

                @error('motivo')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t border-gray-200">
                <a href="{{ route('bienes.show', $bien->id) }}"
                   class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-left class="h-5 w-5" />
                    Cancelar
                </a>

                <button type="submit"
                        id="btnConfirmar"
                        class="px-10 py-3 bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 hover:shadow-red-300 transition flex items-center justify-center gap-2 disabled:opacity-60">
                    Confirmar y descargar acta
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formDesincorporar');
    const btn = document.getElementById('btnConfirmar');
    const motivoInput = document.getElementById('motivo');

    // Habilitar botón solo si hay motivo razonable
    const checkBtn = () => {
        btn.disabled = motivoInput.value.trim().length < 10;
    };

    motivoInput.addEventListener('input', checkBtn);
    checkBtn(); // estado inicial

    // Indicador de carga al enviar
    form.addEventListener('submit', function() {
        btn.disabled = true;
        btn.innerHTML = 'Procesando... <svg class="animate-spin ml-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    });
});
</script>
@endpush

@endsection
