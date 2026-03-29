@extends('reportes.pdf')

@section('contenido')
@php $totalUnidades = 0; $totalDependencias = 0; @endphp

@foreach($organismos as $organismo)
    <div class="group-header">
        ORGANISMO: {{ strtoupper($organismo->nombre) }} ({{ $organismo->codigo }})
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">Código</th>
                <th style="width: 60%;">Nombre de la Unidad Administradora</th>
                <th style="width: 25%;" class="text-center">Nro. Dependencias</th>
            </tr>
        </thead>
        <tbody>
        @php $subtotalDep = 0; @endphp
        @forelse($organismo->unidadesAdministradoras as $unidad)
            @php 
                $totalUnidades++;
                $numDep = $unidad->dependencias->count();
                $subtotalDep += $numDep;
                $totalDependencias += $numDep;
            @endphp
            <tr>
                <td class="text-center"><strong>{{ $unidad->codigo }}</strong></td>
                <td>{{ $unidad->nombre }}</td>
                <td class="text-center">{{ $numDep }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">Sin unidades administradoras.</td>
            </tr>
        @endforelse
        @if($organismo->unidadesAdministradoras->isNotEmpty())
            <tr class="subtotal">
                <td colspan="2" class="text-right"><strong>SUBTOTAL:</strong></td>
                <td class="text-center"><strong>{{ $subtotalDep }} dependencias</strong></td>
            </tr>
        @endif
        </tbody>
    </table>
    
    <div style="margin-bottom: 20px;"></div>
@endforeach

@if($totalUnidades > 0)
    <table>
        <tr class="total">
            <td class="text-right" style="padding: 10px;">
                <strong>TOTAL GENERAL: {{ $totalUnidades }} unidades administradoras | {{ $totalDependencias }} dependencias</strong>
            </td>
        </tr>
    </table>
@endif
@endsection
