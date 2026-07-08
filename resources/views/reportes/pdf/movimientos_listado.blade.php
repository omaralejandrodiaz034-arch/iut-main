@extends('reportes.pdf')

@section('contenido')
@php
    $groupedMovements = [];
    $groupLabel = null;
    $totalGeneral = 0;

    if (! empty($agruparPorTipo)) {
        foreach ($movimientos as $mov) {
            $groupedMovements[$mov->tipo ?? 'Sin tipo'][] = $mov;
        }
        $groupLabel = 'Tipo';
    } elseif (! empty($agruparPorUsuario)) {
        foreach ($movimientos as $mov) {
            $usuarioNombre = $mov->usuario->nombre_completo ?? 'Sin usuario';
            $groupedMovements[$usuarioNombre][] = $mov;
        }
        $groupLabel = 'Usuario';
    }
@endphp

<table>
    <thead>
        <tr>
            <th class="text-center">Fecha</th>
            <th>Tipo</th>
            <th>Usuario</th>
            <th>Bien / Sujeto</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($groupedMovements))
            @foreach($groupedMovements as $groupName => $groupMovements)
                <tr class="group-header">
                    <td colspan="5">{{ strtoupper($groupLabel) }}: {{ strtoupper($groupName) }} — {{ count($groupMovements) }} movimientos</td>
                </tr>
                @foreach($groupMovements as $mov)
                    @php $totalGeneral++; @endphp
                    <tr>
                        <td class="text-center">{{ optional($mov->fecha)->format('d/m/Y H:i') }}</td>
                        <td>{{ $mov->tipo }}</td>
                        <td>{{ $mov->usuario->nombre_completo ?? 'N/D' }}</td>
                        <td>
                            @if($mov->bien)
                                <strong>{{ $mov->bien->codigo }}</strong> — {{ Str::limit($mov->bien->descripcion, 40) }}
                            @elseif($mov->subject)
                                {{ class_basename($mov->subject_type) }} #{{ $mov->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="small">{{ Str::limit($mov->observaciones, 50) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal">
                    <td colspan="4" class="text-right"><strong>Subtotal {{ strtolower($groupLabel) }} "{{ $groupName }}":</strong></td>
                    <td class="text-right"><strong>{{ count($groupMovements) }} movimientos</strong></td>
                </tr>
            @endforeach
        @else
            @forelse($movimientos as $mov)
                @php $totalGeneral++; @endphp
                <tr>
                    <td class="text-center">{{ optional($mov->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->tipo }}</td>
                    <td>{{ $mov->usuario->nombre_completo ?? 'N/D' }}</td>
                    <td>
                        @if($mov->bien)
                            <strong>{{ $mov->bien->codigo }}</strong> — {{ Str::limit($mov->bien->descripcion, 40) }}
                        @elseif($mov->subject)
                            {{ class_basename($mov->subject_type) }} #{{ $mov->subject_id }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="small">{{ Str::limit($mov->observaciones, 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay movimientos para mostrar.</td>
                </tr>
            @endforelse
        @endif
    </tbody>
</table>

@if($totalGeneral > 0)
    <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
        <tr class="total">
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;" colspan="4">
                <strong>Total general de movimientos:</strong>
            </td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                <strong>{{ $totalGeneral }}</strong>
            </td>
        </tr>
    </table>
@endif
@endsection
