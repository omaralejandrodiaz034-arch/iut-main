@extends('reportes.pdf')

@section('contenido')
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
    @forelse($movimientos as $mov)
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
    </tbody>
</table>

@if($movimientos->isNotEmpty())
    <div class="mt-2 small text-right">
        <strong>Total de movimientos:</strong> {{ $movimientos->count() }}
    </div>
@endif
@endsection
