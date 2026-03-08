@extends('reportes.pdf')

@section('contenido')
<table>
    <thead>
        <tr>
            <th style="width: 18%;" class="text-center">Fecha eliminación</th>
            <th style="width: 20%;">Tipo de registro</th>
            <th style="width: 12%;" class="text-center">ID</th>
            <th style="width: 25%;">Eliminado por</th>
            <th style="width: 25%;">Detalles</th>
        </tr>
    </thead>
    <tbody>
    @php $totalPorTipo = []; @endphp
    @forelse($eliminados as $item)
        @php
            $tipo = class_basename($item->model_type);
            $totalPorTipo[$tipo] = ($totalPorTipo[$tipo] ?? 0) + 1;
        @endphp
        <tr>
            <td class="text-center small">{{ optional($item->deleted_at)->format('d/m/Y H:i') }}</td>
            <td>
                <span style="background: #ffe6e6; padding: 2px 6px; border-radius: 3px; font-size: 9px; color: #c00;">
                    {{ $tipo }}
                </span>
            </td>
            <td class="text-center"><strong>{{ $item->model_id }}</strong></td>
            <td class="small">
                {{ $item->deleted_by ?? ($item->data['_archived_by'] ?? 'Sistema') }}
            </td>
            <td class="small">
                @if(isset($item->data['codigo']))
                    <strong>Código:</strong> {{ $item->data['codigo'] }}
                @endif
                @if(isset($item->data['nombre']))
                    <br><strong>Nombre:</strong> {{ Str::limit($item->data['nombre'], 30) }}
                @endif
                @if(isset($item->data['descripcion']))
                    <br><strong>Desc:</strong> {{ Str::limit($item->data['descripcion'], 30) }}
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No hay registros eliminados para mostrar.</td>
        </tr>
    @endforelse
    </tbody>
</table>

@if($eliminados->isNotEmpty())
    <div class="mt-2">
        <div class="group-header" style="font-size: 10px;">
            RESUMEN POR TIPO DE REGISTRO
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tipo de registro</th>
                    <th class="text-center">Cantidad eliminada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($totalPorTipo as $tipo => $cantidad)
                    <tr>
                        <td>{{ $tipo }}</td>
                        <td class="text-center"><strong>{{ $cantidad }}</strong></td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-center"><strong>{{ $eliminados->count() }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
@endsection
