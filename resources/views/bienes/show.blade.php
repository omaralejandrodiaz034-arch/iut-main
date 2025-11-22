@extends('layouts.base')

@section('title', 'Detalles del Bien')

@section('content')
@php
use Illuminate\Support\Str;
@endphp
<div class="max-w-3xl mx-auto">
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <div class="flex items-center">
                <span class="text-2xl mr-3">✓</span>
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <!-- Botón regresar -->
        <div>
            <a
                href="{{ route('bienes.index') }}"
                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1"
            >
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Regresar
            </a>
        </div>

        <!-- Encabezado -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-3xl font-bold text-gray-800 leading-tight">
                Bien: {{ $bien->codigo }} — {{ Str::limit($bien->descripcion, 80) }}
            </h1>
            <div class="flex flex-wrap gap-2 md:justify-end">
                <a
                    href="{{ route('bienes.pdf', $bien) }}"
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-100 rounded hover:bg-indigo-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                >
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                    Descargar PDF
                </a>
                @include('components.action-buttons', [
                    'resource' => 'bienes',
                    'model' => $bien,
                    'canDelete' => auth()->user()->canDeleteData(),
                    'confirm' => "¿Seguro que deseas eliminar este bien?",
                    'label' => $bien->codigo
                ])
            </div>
        </div>

        <!-- Información principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información del Bien</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Código</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->codigo }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Descripción</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->descripcion }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Precio estimado</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Ubicación</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->ubicacion ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Estado</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->estado?->name ?? (string)$bien->estado }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Fecha de Registro</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->fecha_registro?->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Dependencia y Responsable</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Dependencia</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->dependencia->nombre ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Responsable (dependencia)</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->dependencia->responsable->nombre_completo ?? 'Sin asignar' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Movimientos registrados</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->movimientos->count() }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Creado en el sistema</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Última Actualización</p>
                        <p class="text-base font-medium text-gray-800">{{ $bien->updated_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fotografía -->
        <div class="grid grid-cols-1 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Fotografía</h2>
                @if($bien->fotografia)
                    <div class="rounded-lg overflow-hidden border border-gray-100 shadow-sm max-w-md">
                        <img
                            src="{{ str_starts_with($bien->fotografia, 'http') ? $bien->fotografia : asset('storage/'.$bien->fotografia) }}"
                            alt="Fotografía del bien {{ $bien->codigo }}"
                            class="w-full h-64 object-cover"
                        >
                    </div>
                @else
                    <p class="text-sm text-gray-600">No se ha registrado una fotografía para este bien.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection



