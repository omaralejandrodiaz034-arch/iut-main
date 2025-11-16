@extends('layouts.base')

@section('title', 'Detalle del Movimiento')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Movimiento #{{ $movimiento->id }}</h1>

        <dl class="grid grid-cols-1 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-600">Fecha</dt>
                <dd class="text-lg text-gray-800">
                    {{ optional($movimiento->fecha)->format('Y-m-d') ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-600">Tipo</dt>
                <dd class="text-lg text-gray-800">{{ $movimiento->tipo }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-600">Entidad relacionada</dt>
                <dd class="text-lg text-gray-800">
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

                    @if($s)
                        <strong>{{ class_basename($movimiento->subject_type) }}</strong> - {{ $label }}
                    @elseif($movimiento->bien)
                        <strong>Bien</strong> - {{ $label }}
                    @else
                        <span class="text-gray-500">Sin entidad asociada</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-600">Usuario</dt>
                <dd class="text-lg text-gray-800">
                    {{ $movimiento->usuario->nombre_completo ?? $movimiento->usuario->nombre ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-600">Observaciones</dt>
                <dd class="text-lg text-gray-800">{{ $movimiento->observaciones }}</dd>
            </div>
        </dl>

        @if($movimiento->tipo === 'Actualización' && $movimiento->historialMovimientos && $movimiento->historialMovimientos->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-lg font-semibold mb-2">Historial de cambios</h2>
                <ul class="list-disc pl-6 text-gray-700">
                    @foreach($movimiento->historialMovimientos as $h)
                        <li>
                            <span class="font-medium">{{ optional($h->fecha)->format('Y-m-d H:i') ?? '-' }}</span>:
                            {{ $h->detalle }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('movimientos.index') }}" class="bg-gray-200 px-4 py-2 rounded">Volver</a>
        </div>
    </div>
</div>
@endsection


