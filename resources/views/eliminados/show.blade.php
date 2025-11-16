@extends('layouts.base')

@section('title', 'Detalle Eliminado')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Registro Eliminado #{{ $eliminado->id }}</h1>

        <dl class="grid grid-cols-1 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-600">Modelo</dt>
                <dd class="text-lg text-gray-800">{{ $eliminado->model_type }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Model ID</dt>
                <dd class="text-lg text-gray-800">{{ $eliminado->model_id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Eliminado Por</dt>
                <dd class="text-lg text-gray-800">{{ $eliminado->deleted_by ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Fecha</dt>
                <dd class="text-lg text-gray-800">{{ $eliminado->deleted_at?->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Datos (JSON)</dt>
                <dd class="text-sm text-gray-700"><pre class="whitespace-pre-wrap">{{ json_encode($eliminado->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></dd>
            </div>
        </dl>

        <div class="mt-4">
            <a href="{{ route('eliminados.index') }}" class="bg-gray-200 px-4 py-2 rounded">Volver</a>
            @if(auth()->user()->isAdmin())
                <form action="{{ route('eliminados.restore', $eliminado->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="ml-2 bg-green-600 text-white px-4 py-2 rounded">Restaurar</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
