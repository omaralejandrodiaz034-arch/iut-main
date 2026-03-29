@extends('reportes.pdf')

@section('contenido')
<table>
    <thead>
        <tr>
            @if($dimension === 'estado')
                <th>Estado</th>
            @elseif($dimension === 'dependencia')
                <th>Dependencia</th>
            @elseif($dimension === 'tipo_bien')
                <th>Tipo de bien</th>
            @else
                <th>Clave</th>
            @endif
            <th class="text-center">Cantidad de bienes</th>
            <th class="text-right">Total estimado (Bs.)</th>
        </tr>
    </thead>
    <tbody>
    @php
        $totalCantidad = 0;
        $totalMonto = 0;
    @endphp
    @forelse($resumen as $fila)
        @php
            $totalCantidad += (int) $fila->cantidad;
            $totalMonto += (float)($fila->total ?? 0);
        @endphp
        <tr>
            <td>
                @if($dimension === 'estado')
                    {{ $fila->estado ?? 'N/D' }}
                @elseif($dimension === 'dependencia')
                    {{ $fila->dependencia->nombre ?? ('ID '.$fila->dependencia_id) }}
                @elseif($dimension === 'tipo_bien')
                    {{ $fila->tipo_bien ?? 'N/D' }}
                @else
                    {{ $fila->clave ?? 'N/D' }}
                @endif
            </td>
            <td class="text-center">{{ number_format((int) $fila->cantidad, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format((float)($fila->total ?? 0), 2, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">No hay datos para mostrar.</td>
        </tr>
    @endforelse
    @if($resumen->isNotEmpty())
        <tr class="total">
            <td><strong>TOTAL GENERAL</strong></td>
            <td class="text-center"><strong>{{ number_format($totalCantidad, 0, ',', '.') }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalMonto, 2, ',', '.') }}</strong></td>
        </tr>
    @endif
    </tbody>
</table>
@endsection
