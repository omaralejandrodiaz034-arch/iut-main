@extends('layouts.base')

@section('title', 'Notificaciones')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Notificaciones', 'url' => route('notificaciones.index')]]" />
@endpush
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Notificaciones</h1>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        @forelse ($notificaciones as $n)
            <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 @if(is_null($n->read_at)) bg-indigo-50/50 @endif">
                <a href="{{ route('notificaciones.ir', $n) }}"
                   class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800">{{ $n->data['titulo'] ?? 'Notificación' }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $n->data['mensaje'] ?? '' }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </a>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if(is_null($n->read_at))
                        <span class="mt-1 inline-flex items-center justify-center h-2.5 w-2.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                    @endif
                    <form action="{{ route('notificaciones.eliminar', $n) }}" method="POST" onsubmit="return confirm('¿Eliminar esta notificación? Serás redirigido a la sección relacionada.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="px-4 py-8 text-sm text-gray-500 text-center">No tienes notificaciones.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notificaciones->links() }}
    </div>
</div>
@endsection
