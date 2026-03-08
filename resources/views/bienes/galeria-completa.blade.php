{{-- resources/views/bienes/galeria-completa.blade.php --}}
@extends('layouts.base')

@section('title', 'Galería Completa de Bienes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => 'Galería']]" />
@endpush
<div class="mb-8 flex items-center justify-between">
    <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
    <!-- Heroicon: Photo -->
    <svg xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
         class="h-8 w-8 text-indigo-600">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 7.5h2.25l1.5-2.25h10.5l1.5 2.25H21v12.75H3V7.5z" />
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
    </svg>
    Galería de Bienes
</h1>

    <a href="{{ route('bienes.index') }}"
       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 transition font-medium">
        <!-- Heroicon: Arrow Left -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7" />
        </svg>
        Volver al listado
    </a>
</div>

{{-- Filtro por Tipo de Bien --}}
<div class="mb-4">
    <form method="GET" action="{{ route('bienes.galeria') }}" class="flex items-center gap-4">
        <select name="tipo" id="tipo" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            <option value="">Todos los Tipos</option>
            <option value="inmueble" {{ request('tipo') == 'inmueble' ? 'selected' : '' }}>Inmueble</option>
            <option value="electrónico" {{ request('tipo') == 'electrónico' ? 'selected' : '' }}>Electrónico</option>
            <option value="mueble" {{ request('tipo') == 'mueble' ? 'selected' : '' }}>Mueble</option>
            <option value="otro" {{ request('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>
    </form>
</div>

{{-- Contenedor de la galería --}}
<div class="gallery-container grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
    @foreach($imagenes as $imagen)
        <a href="{{ route('bienes.show', $imagen->id) }}"
           class="group relative rounded-lg overflow-hidden shadow hover:shadow-lg transition block">

            {{-- Imagen --}}
            <img src="{{ $imagen->url }}"
                 alt="{{ $imagen->descripcion }}"
                 class="w-full h-48 object-cover transform group-hover:scale-105 transition duration-300">

            {{-- Overlay con código y descripción --}}
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3 text-white">
                <p class="text-sm font-semibold truncate flex items-center gap-1">
                    <!-- Heroicon: Tag -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h10v10H7z" />
                    </svg>
                    {{ $imagen->codigo }}
                </p>
                <p class="text-xs opacity-80 truncate">{{ $imagen->descripcion }}</p>
            </div>
        </a>
    @endforeach
</div>


{{-- Lightbox Modal --}}
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-5xl w-full px-4">
        {{-- Botón de cerrar --}}
        <button id="close-lightbox"
                class="absolute top-4 right-4 text-white text-4xl font-bold cursor-pointer hover:text-red-400 transition flex items-center gap-1">
            <!-- Heroicon: X -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Imagen principal --}}
        <img id="lightbox-img" src="" alt="Imagen ampliada"
             class="w-full max-h-[90vh] object-contain rounded-lg shadow-lg border border-gray-700">
    </div>
</div>
@endsection
