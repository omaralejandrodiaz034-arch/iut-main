@extends('reportes.pdf')

@section('contenido')
@php
    $totalGeneral = 0;
    $cantidadGeneral = 0;
@endphp

@foreach($organismos as $organismo)
    <div class="group-header">
        ORGANISMO: {{ strtoupper($organismo->nombre) }} ({{ $organismo->codigo }})
    </div>

    @foreach($organismo->unidadesAdministradoras as $unidad)
        <div style="margin-top: 15px;">
            <div style="background: #d9d9d9; padding: 6px; font-weight: bold; font-size: 10px;">
                Unidad Administradora: {{ $unidad->nombre }} ({{ $unidad->codigo }})
            </div>

            @foreach($unidad->dependencias as $dependencia)
                <div style="margin-left: 15px; margin-top: 8px;">
                    <div style="background: #efefef; padding: 5px; font-size: 9px; font-weight: bold;">
                        Dependencia: {{ $dependencia->nombre }} ({{ $dependencia->codigo }})
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 12%;">Código</th>
                                <th style="width: 50%;">Descripción</th>
                                <th style="width: 13%;">Estado</th>
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
                                <td>{{ Str::limit($bien->descripcion, 80) }}</td>
                                <td class="text-center">{{ $bien->estado ?? '-' }}</td>
                                <td class="text-right">{{ number_format((float)($bien->precio ?? 0), 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center small">Sin bienes en esta dependencia.</td>
                            </tr>
                        @endforelse
                        @if($cantidad > 0)
                            <tr style="background: #f8f8f8;">
                                <td colspan="3" class="text-right small"><strong>Subtotal:</strong></td>
                                <td class="text-right"><strong>{{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach
    
    <div style="margin-bottom: 25px;"></div>
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
