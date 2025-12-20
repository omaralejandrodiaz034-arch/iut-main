@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Bienes Desincorporados</h1>

    @if($bienes->isEmpty())
        <p class="text-gray-600">No hay bienes desincorporados.</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Código</th>
                    <th class="border border-gray-300 px-4 py-2">Descripción</th>
                    <th class="border border-gray-300 px-4 py-2">Fecha de Desincorporación</th>
                    <th class="border border-gray-300 px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bienes as $bien)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $bien->codigo }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $bien->descripcion }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $bien->movimiento->created_at->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form action="{{ route('movimientos.reintegrar', $bien->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Reintegrar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
