@extends('layouts.base')

@section('title', 'Detalle del Movimiento')

@section('content')
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
                href="{{ route('movimientos.index') }}"
                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1"
            >
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Regresar
            </a>
        </div>

        <!-- Encabezado -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-800 leading-tight">
        Movimiento #{{ $movimiento->id }} — {{ $movimiento->tipo }}
    </h1>
    <div class="flex flex-wrap gap-2 md:justify-end">
        <a
            href="{{ route('movimientos.pdf', $movimiento) }}"
            class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-100 rounded hover:bg-indigo-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
        >
            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
            Descargar PDF
        </a>
    </div>
</div>


        <!-- Información principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información del Movimiento</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Fecha</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ optional($movimiento->fecha)->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Tipo</p>
                        <p class="text-base font-medium text-gray-800">{{ $movimiento->tipo }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Usuario</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ $movimiento->usuario->nombre_completo ?? $movimiento->usuario->nombre ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Observaciones</p>
                        <p class="text-base font-medium text-gray-800">{{ $movimiento->observaciones ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Entidad Relacionada</h2>

                <div class="space-y-3">
                    @php
                        $s = $movimiento->subject;
                        $label = $s?->nombre_completo
                            ?? $s?->nombre
                            ?? $s?->descripcion
                            ?? $s?->codigo
                            ?? ($movimiento->bien?->codigo
                                ? $movimiento->bien->codigo.' - '.$movimiento->bien->descripcion
                                : 'ID '.$movimiento->subject_id);
                    @endphp

                    <div>
                        <p class="text-sm text-gray-600">Entidad</p>
                        <p class="text-base font-medium text-gray-800">
                            @if($s)
                                <strong>{{ class_basename($movimiento->subject_type) }}</strong> — {{ $label }}
                            @elseif($movimiento->bien)
                                <strong>Bien</strong> — {{ $label }}
                            @else
                                <span class="text-gray-500">Sin entidad asociada</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de cambios -->
        @if($movimiento->tipo === 'Actualización' && $movimiento->historialMovimientos && $movimiento->historialMovimientos->isNotEmpty())
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Historial de cambios</h2>
                <div class="space-y-2">
                    @foreach($movimiento->historialMovimientos as $h)
                        <div class="flex items-start space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ optional($h->fecha)->format('d/m/Y H:i') ?? '—' }}
                            </span>
                            <span class="text-gray-700">{{ $h->detalle }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection




