@extends('reportes.pdf')

@section('contenido')
@php $totalBienes = 0; @endphp

@foreach($responsables as $resp)
    <div class="group-header">
        RESPONSABLE: {{ strtoupper($resp->nombre_completo) }} (C.I.: {{ $resp->cedula }})
    </div>
    
    <div class="small mb-2" style="padding: 5px; background: #f9f9f9;">
        <strong>Tipo:</strong> {{ $resp->tipo->nombre ?? 'N/D' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Dependencia</th>
                <th style="width: 15%;" class="text-center">Código Bien</th>
                <th style="width: 45%;">Descripción</th>
                <th style="width: 15%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
        @php $contador = 0; @endphp
        @forelse($resp->dependencias as $dep)
            @foreach($dep->bienes as $bien)
                @php 
                    $contador++;
                    $totalBienes++;
                @endphp
                <tr>
                    <td class="small">{{ $dep->nombre }}</td>
                    <td class="text-center"><strong>{{ $bien->codigo }}</strong></td>
                    <td>{{ Str::limit($bien->descripcion, 60) }}</td>
                    <td class="text-center">{{ $bien->estado ?? '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="4" class="text-center">Sin bienes asignados.</td>
            </tr>
        @endforelse
        @if($contador > 0)
            <tr class="subtotal">
                <td colspan="4" class="text-center"><strong>Total de bienes bajo responsabilidad: {{ $contador }}</strong></td>
            </tr>
        @endif
        </tbody>
    </table>
    
    <div style="margin-bottom: 20px;"></div>
@endforeach

@if($totalBienes > 0)
    <table>
        <tr class="total">
            <td class="text-center" style="padding: 10px;">
                <strong>TOTAL GENERAL: {{ $totalBienes }} bienes asignados a {{ $responsables->count() }} responsables</strong>
            </td>
        </tr>
    </table>
@endif
@endsection
