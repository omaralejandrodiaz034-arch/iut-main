@extends('layouts.base')

@section('title', 'Detalles del Organismo')

@section('content')
@php
use Illuminate\Support\Str;
@endphp
<div class="max-w-3xl mx-auto">
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <div class="flex items-center">
                <x-heroicon-o-check class="w-6 h-6 text-green-500 mr-3" />
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <div>
            <a
                href="{{ route('organismos.index') }}"
                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1"
            >
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Regresar
            </a>
        </div>

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-3xl font-bold text-gray-800 leading-tight">Organismo: {{ $organismo->codigo }} — {{ $organismo->nombre }}</h1>
            <div class="flex flex-wrap gap-2 md:justify-end">
                <a
                    href="{{ route('organismos.pdf', $organismo) }}"
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-100 rounded hover:bg-indigo-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                >
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                    Descargar PDF
                </a>
                @include('components.action-buttons', [
                    'resource' => 'organismos',
                    'model' => $organismo,
                    'canDelete' => auth()->user()->canDeleteData(),
                    'confirm' => '¿Estás seguro?',
                    'label' => $organismo->nombre
                ])
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Código</p>
                        <p class="text-base font-medium text-gray-800">{{ $organismo->codigo }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Nombre</p>
                        <p class="text-base font-medium text-gray-800">{{ $organismo->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Fecha de Creación</p>
                        <p class="text-base font-medium text-gray-800">{{ $organismo->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Última Actualización</p>
                        <p class="text-base font-medium text-gray-800">{{ $organismo->updated_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Unidades Administradoras</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Total de Unidades</p>
                        <p class="text-base font-medium text-gray-800">{{ $organismo->unidadesAdministradoras->count() }}</p>
                    </div>

                    @if($organismo->unidadesAdministradoras->isNotEmpty())
                        <div>
                            <p class="text-sm text-gray-600">Listado (ejemplos)</p>
                            <ul class="list-disc list-inside text-sm text-gray-800">
                                @foreach($organismo->unidadesAdministradoras->take(5) as $u)
                                    <li>{{ $u->codigo }} — {{ Str::limit($u->nombre, 60) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>
@endsection
