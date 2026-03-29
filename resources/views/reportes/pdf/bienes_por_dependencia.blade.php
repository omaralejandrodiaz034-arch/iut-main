@extends('reportes.pdf')

@section('contenido')
@php
    $totalGeneral = 0;
    $cantidadGeneral = 0;
@endphp

@foreach($dependencias as $dependencia)
    <div class="group-header">
        DEPENDENCIA: {{ strtoupper($dependencia->nombre) }} ({{ $dependencia->codigo }})
    </div>
    
    <div class="small mb-2" style="padding: 5px; background: #f9f9f9;">
        <strong>Unidad Administradora:</strong> {{ $dependencia->unidadAdministradora->nombre ?? 'N/A' }} |
        <strong>Organismo:</strong> {{ $dependencia->unidadAdministradora->organismo->nombre ?? 'N/A' }}
        @if($dependencia->responsable)
            | <strong>Responsable:</strong> {{ $dependencia->responsable->nombre_completo }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 40%;">Descripción</th>
                <th style="width: 15%;">Tipo</th>
                <th style="width: 15%;">Estado</th>
                <th style="width: 15%;" class="text-right">Precio (Bs.)</th>
            </tr>
        </thead>
        <tbody>
        @php
            $subtotal = 0;
            $cantidad = 0;
        @endphp
        @forelse($dependencia->bienes as $index => $bien)
            @php
                $subtotal += (float)($bien->precio ?? 0);
                $cantidad++;
                $totalGeneral += (float)($bien->precio ?? 0);
                $cantidadGeneral++;
            @endphp
            <tr>
                <td class="text-center">{{ $bien->codigo }}</td>
                <td>{{ Str::limit($bien->descripcion, 60) }}</td>
                <td class="text-center">{{ $bien->tipo_bien ?? '-' }}</td>
                <td class="text-center">{{ $bien->estado ?? '-' }}</td>
                <td class="text-right">{{ number_format((float)($bien->precio ?? 0), 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Sin bienes registrados en esta dependencia.</td>
            </tr>
        @endforelse
        @if($cantidad > 0)
            <tr class="subtotal">
                <td colspan="4" class="text-right"><strong>SUBTOTAL ({{ $cantidad }} bienes):</strong></td>
                <td class="text-right"><strong>{{ number_format($subtotal, 2, ',', '.') }}</strong></td>
            </tr>
        @endif
        </tbody>
    </table>
    
    <div style="margin-bottom: 20px;"></div>
@endforeach

@if($cantidadGeneral > 0)
    <table>
        <tr class="total">
            <td colspan="4" class="text-right" style="padding: 10px;"><strong>TOTAL GENERAL ({{ $cantidadGeneral }} bienes):</strong></td>
            <td class="text-right" style="padding: 10px;"><strong>{{ number_format($totalGeneral, 2, ',', '.') }}</strong></td>
        </tr>
    </table>
@endif
@endsection
