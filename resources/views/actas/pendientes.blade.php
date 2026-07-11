@extends('layouts.base')

@section('title', 'Actas pendientes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Actas pendientes', 'url' => route('actas.pendientes')]]" />
@endpush
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Actas pendientes de firma</h1>
        <form method="GET" class="flex items-center gap-2">
            <select name="tipo" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos los tipos</option>
                <option value="TRASLADO" @if(request('tipo')==='TRASLADO') selected @endif>Traslado</option>
                <option value="DONACION" @if(request('tipo')==='DONACION') selected @endif>Donación</option>
                <option value="DESINCORPORACION" @if(request('tipo')==='DESINCORPORACION') selected @endif>Desincorporación</option>
            </select>
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Tipo</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Bien</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Emitido por</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Vence</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($actas as $movimiento)
                        @php
                            $bien = $movimiento->bien;
                            $vence = $movimiento->fecha_limite_acta;
                            $restante = $vence ? now()->diffInHours($vence, false) : null;
                            $esCritico = $vence && now()->gt($vence->subHours(4));
                            $esVencido = $vence && now()->gt($vence);
                        @endphp
                        <tr class="{{ $esVencido ? 'bg-red-50' : ($esCritico ? 'bg-amber-50' : '') }}">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($movimiento->tipo) {
                                        'TRASLADO' => 'bg-blue-100 text-blue-800',
                                        'DONACION' => 'bg-amber-100 text-amber-800',
                                        'DESINCORPORACION' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    } }}">
                                    {{ $movimiento->tipo }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('bienes.show', $bien) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    {{ $bien?->codigo ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $bien?->descripcion ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $movimiento->usuario?->nombre_completo ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="{{ $esVencido ? 'text-red-600 font-medium' : ($esCritico ? 'text-amber-600 font-medium' : 'text-gray-700') }}">
                                    {{ $vence?->format('d/m/Y H:i') ?? 'N/A' }}
                                </span>
                                @if($vence)
                                    <p class="text-[10px] text-gray-500">
                                        @if($esVencido)
                                            Vencido
                                        @else
                                            Vence en {{ $restante }}h
                                        @endif
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($movimiento->acta_estado === 'FIRMADA' && $movimiento->acta_firmada_path)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Firmada
                                    </span>
                                @elseif($movimiento->acta_estado === 'RECHAZADA')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Rechazada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($movimiento->acta_estado === 'RECHAZADA')
                                    <form action="{{ route('bienes.acta-firmada', $bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-1">
                                        @csrf
                                        <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-xs border border-gray-300 rounded-lg px-2 py-1.5" />
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                            Subir acta corregida
                                        </button>
                                    </form>
                                @elseif($movimiento->acta_estado === 'PENDIENTE_FIRMA')
                                    <form action="{{ route('bienes.acta-firmada', $bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-1">
                                        @csrf
                                        <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-xs border border-gray-300 rounded-lg px-2 py-1.5" />
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                                            Subir acta
                                        </button>
                                    </form>
                                @elseif($movimiento->acta_estado === 'FIRMADA' && $movimiento->acta_firmada_path)
                                    <a href="{{ asset('storage/'.$movimiento->acta_firmada_path) }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                                        Ver acta firmada
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-sm text-gray-500 text-center">No hay actas pendientes de firma.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $actas->links() }}
    </div>
</div>
@endsection
