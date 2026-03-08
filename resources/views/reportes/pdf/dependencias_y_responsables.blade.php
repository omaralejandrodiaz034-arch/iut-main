@extends('reportes.pdf')

@section('contenido')
<table>
    <thead>
        <tr>
            <th style="width: 20%;">Organismo</th>
            <th style="width: 20%;">Unidad Adm.</th>
            <th style="width: 10%;" class="text-center">Código</th>
            <th style="width: 25%;">Dependencia</th>
            <th style="width: 25%;">Responsable</th>
        </tr>
    </thead>
    <tbody>
    @php $currentOrganismo = null; @endphp
    @forelse($dependencias as $dep)
        @php
            $organismoNombre = $dep->unidadAdministradora->organismo->nombre ?? 'N/D';
            $showOrganismo = ($organismoNombre !== $currentOrganismo);
            $currentOrganismo = $organismoNombre;
        @endphp
        <tr>
            <td class="small">
                @if($showOrganismo)
                    <strong>{{ Str::limit($organismoNombre, 30) }}</strong>
                @endif
            </td>
            <td class="small">{{ Str::limit($dep->unidadAdministradora->nombre ?? 'N/D', 30) }}</td>
            <td class="text-center"><strong>{{ $dep->codigo }}</strong></td>
            <td>{{ $dep->nombre }}</td>
            <td class="small">
                @if($dep->responsable)
                    <strong>{{ $dep->responsable->nombre_completo }}</strong><br>
                    <span style="color: #666;">C.I.: {{ $dep->responsable->cedula }}</span>
                @else
                    <span style="color: #999;">Sin responsable asignado</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No hay dependencias para mostrar.</td>
        </tr>
    @endforelse
    </tbody>
</table>

@if($dependencias->isNotEmpty())
    <div class="mt-2 small text-right">
        <strong>Total de dependencias:</strong> {{ $dependencias->count() }}
    </div>
@endif
@endsection
