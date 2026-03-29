@extends('reportes.pdf')

@section('contenido')
@php
    $totalGeneral = 0;
    $cantidadGeneral = 0;
@endphp

@foreach($unidades as $unidad)
    <div class="group-header">
        UNIDAD ADMINISTRADORA: {{ strtoupper($unidad->nombre) }} ({{ $unidad->codigo }})
    </div>
    
    <div class="small mb-2" style="padding: 5px; background: #f9f9f9;">
        <strong>Organismo:</strong> {{ $unidad->organismo->nombre ?? 'N/A' }}
    </div>

    @foreach($unidad->dependencias as $dependencia)
        <div style="margin-left: 10px; margin-top: 10px;">
            <div style="background: #e6e6e6; padding: 6px; font-weight: bold; font-size: 10px;">
                Dependencia: {{ $dependencia->nombre }} ({{ $dependencia->codigo }})
                @if($dependencia->responsable)
                    — Responsable: {{ $dependencia->responsable->nombre_completo }}
                @endif
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 45%;">Descripción</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 15%;" class="text-right">Precio (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                @php $subtotal = 0; $cantidad = 0; @endphp
                @forelse($dependencia->bienes as $bien)
                    @php
                        $subtotal += (float)($bien->precio ?? 0);
                        $cantidad++;
                        $totalGeneral += (float)($bien->precio ?? 0);
                        $cantidadGeneral++;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $bien->codigo }}</td>
                        <td>{{ Str::limit($bien->descripcion, 70) }}</td>
                        <td class="text-center">{{ $bien->estado ?? '-' }}</td>
                        <td class="text-right">{{ number_format((float)($bien->precio ?? 0), 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center small">Sin bienes en esta dependencia.</td>
                    </tr>
                @endforelse
                @if($cantidad > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="3" class="text-right"><strong>Subtotal ({{ $cantidad }} bienes):</strong></td>
                        <td class="text-right"><strong>{{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    @endforeach
    
    <div style="margin-bottom: 20px;"></div>
@endforeach

@if($cantidadGeneral > 0)
    <table>
        <tr class="total">
            <td colspan="3" class="text-right" style="padding: 10px;"><strong>TOTAL GENERAL ({{ $cantidadGeneral }} bienes):</strong></td>
            <td class="text-right" style="padding: 10px;"><strong>{{ number_format($totalGeneral, 2, ',', '.') }}</strong></td>
        </tr>
    </table>
@endif
@endsection
