{{--
  Componente: x-modal

  Props:
    id       (string, requerido) — ID único del modal
    title    (string, opcional)  — Título del encabezado
    size     (string, opcional)  — 'sm' | 'md' (default) | 'lg' | 'xl'

  Uso:
    <x-modal id="confirmar-eliminar" title="Confirmar acción">
        <p>¿Estás seguro de eliminar este elemento?</p>

        <x-slot name="footer">
            <button class="close-modal px-4 py-2 bg-gray-100 rounded-lg">Cancelar</button>
            <button class="px-4 py-2 bg-red-600 text-white rounded-lg">Eliminar</button>
        </x-slot>
    </x-modal>

    Abrir con:
      <button data-modal-target="confirmar-eliminar">Eliminar</button>
      o: window.openModal('confirmar-eliminar')
--}}
@props([
    'id',
    'title' => null,
    'size'  => 'md',
])

@php
$sizeClass = match($size) {
    'sm'    => 'max-w-sm',
    'lg'    => 'max-w-2xl',
    'xl'    => 'max-w-4xl',
    default => 'max-w-lg',
};
@endphp

<div id="{{ $id }}" class="modal-overlay hidden" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-content {{ $sizeClass }}">

        {{-- Encabezado --}}
        @if($title)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
            <button class="close-modal text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-lg hover:bg-gray-100" aria-label="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @else
        <div class="flex justify-end px-4 pt-4">
            <button class="close-modal text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-lg hover:bg-gray-100" aria-label="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @endif

        {{-- Cuerpo --}}
        <div class="px-6 py-4">
            {{ $slot }}
        </div>

        {{-- Pie (opcional) --}}
        @isset($footer)
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
        @endisset

    </div>
</div>
