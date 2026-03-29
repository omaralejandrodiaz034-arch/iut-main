@extends('reportes.pdf')

@section('contenido')
@php $totalDependencias = 0; $totalBienes = 0; @endphp

@foreach($unidades as $unidad)
    <div class="group-header">
        UNIDAD ADMINISTRADORA: {{ strtoupper($unidad->nombre) }} ({{ $unidad->codigo }})
    </div>
    
    <div class="small mb-2" style="padding: 5px; background: #f9f9f9;">
        <strong>Organismo:</strong> {{ $unidad->organismo->nombre ?? 'N/A' }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 12%;" class="text-center">Código</th>
                <th style="width: 45%;">Nombre de la Dependencia</th>
                <th style="width: 28%;">Responsable</th>
                <th style="width: 15%;" class="text-center">Nro. Bienes</th>
            </tr>
        </thead>
        <tbody>
        @php $subtotalBienes = 0; @endphp
        @forelse($unidad->dependencias as $dependencia)
            @php 
                $totalDependencias++;
                $numBienes = $dependencia->bienes->count();
                $subtotalBienes += $numBienes;
                $totalBienes += $numBienes;
            @endphp
            <tr>
                <td class="text-center"><strong>{{ $dependencia->codigo }}</strong></td>
                <td>{{ $dependencia->nombre }}</td>
                <td class="small">
                    @if($dependencia->responsable)
                        {{ $dependencia->responsable->nombre_completo }}<br>
                        <span style="color: #666;">C.I.: {{ $dependencia->responsable->cedula }}</span>
                    @else
                        <span style="color: #999;">Sin responsable</span>
                    @endif
                </td>
                <td class="text-center">{{ $numBienes }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Sin dependencias asociadas.</td>
            </tr>
        @endforelse
        @if($unidad->dependencias->isNotEmpty())
            <tr class="subtotal">
                <td colspan="3" class="text-right"><strong>SUBTOTAL ({{ $unidad->dependencias->count() }} dependencias):</strong></td>
                <td class="text-center"><strong>{{ $subtotalBienes }} bienes</strong></td>
            </tr>
        @endif
        </tbody>
    </table>
    
    <div style="margin-bottom: 20px;"></div>
@endforeach

@if($totalDependencias > 0)
    <table>
        <tr class="total">
            <td class="text-right" style="padding: 10px;">
                <strong>TOTAL GENERAL: {{ $totalDependencias }} dependencias | {{ $totalBienes }} bienes</strong>
            </td>
        </tr>
    </table>
@endif
@endsection
