@extends('layouts.base')

@section('title', 'Ruta del acta firmada')

@section('content')
@push('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Notificaciones', 'url' => route('notificaciones.index')], ['label' => 'Ruta del acta firmada']]" />
@endpush
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Ruta del acta firmada</h1>

    <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
        <div>
            <p class="text-sm text-gray-600 mb-1">Ruta en storage:</p>
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded p-2">
                <code class="flex-1 text-sm text-gray-800 break-all">{{ $ruta }}</code>
                <button onclick="navigator.clipboard.writeText(this.previousElementSibling.innerText).then(() => this.innerText='Copiado').catch(() => alert('No se pudo copiar'))" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">Copiar</button>
            </div>
        </div>

        @php
            $storageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($ruta);
        @endphp
        <div>
            <p class="text-sm text-gray-600 mb-1">URL pública:</p>
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded p-2">
                <code class="flex-1 text-sm text-gray-800 break-all">{{ $storageUrl }}</code>
                <a href="{{ $storageUrl }}" target="_blank" class="text-xs bg-gray-800 text-white px-3 py-1.5 rounded hover:bg-gray-900">Abrir</a>
            </div>
        </div>
    </div>
</div>
@endsection
