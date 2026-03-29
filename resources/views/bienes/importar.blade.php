@extends('layouts.app')

@section('title', 'Importar Bienes')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Bienes', 'url' => route('bienes.index')], ['label' => 'Importar']]" />
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Encabezado -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Importar Bienes desde Excel</h1>
        <p class="mt-2 text-gray-600">Carga un archivo Excel para importar múltiples bienes al inventario.</p>
    </div>

    <!-- Errores de importación previa -->
    @if(session('errores_importacion'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold">Errores en la importación</h3>
                <ul class="mt-2 text-red-700 text-sm list-disc list-inside max-h-40 overflow-y-auto">
                    @foreach(session('errores_importacion') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Mensaje de éxito -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Tarjeta de carga de archivo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('bienes.importar') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Área de carga de archivo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivo Excel</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors cursor-pointer" id="dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="archivo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Subir archivo</span>
                                    <input id="archivo" name="archivo" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                                </label>
                                <p class="pl-1">o arrastrar y soltar</p>
                            </div>
                            <p class="text-xs text-gray-500">Excel (.xlsx, .xls) hasta 10MB</p>
                            <p id="nombre-archivo" class="text-sm text-indigo-600 font-medium mt-2"></p>
                        </div>
                    </div>
                    @error('archivo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('bienes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </a>
                    <a href="{{ route('bienes.descargar-template') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Descargar Plantilla
                    </a>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Importar Bienes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="mt-8 bg-blue-50 rounded-xl border border-blue-200 p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">📋 Instrucciones</h3>
        <ol class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start">
                <span class="font-bold mr-2">1.</span>
                Descarga la plantilla de Excel haciendo clic en "Descargar Plantilla"
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">2.</span>
                Completa los datos siguiendo el formato de la plantilla
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">3.</span>
                Asegúrate de que los códigos de dependencia y Cédula de responsable existan en el sistema
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">4.</span>
                Guarda el archivo y súbelo usando el formulario
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">5.</span>
                Revisa los errores (si los hay) y corrígelos en el Excel
            </li>
        </ol>

        <h4 class="text-blue-800 font-semibold mt-6 mb-2">Columnas requeridas:</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
            <div class="bg-white rounded px-3 py-2 text-blue-700">código (obligatorio)</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">descripcion</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">precio</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">estado</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">codigo_dependencia</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">cedula_responsable</div>
            <div class="bg-white rounded px-3 py-2 text-blue-700">fecha_registro</div>
        </div>

        <h4 class="text-blue-800 font-semibold mt-4 mb-2">Estados válidos:</h4>
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Activo</span>
            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">Inactivo</span>
            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">En Mantenimiento</span>
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Dado de Baja</span>
            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">Extraviado</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar nombre del archivo seleccionado
    document.getElementById('archivo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('nombre-archivo').textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        }
    });

    // Arrastrar y soltar
    const dropzone = document.getElementById('dropzone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
    }

    function unhighlight() {
        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
    }

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('archivo').files = files;
        
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('nombre-archivo').textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        }
    }
</script>
@endpush
@endsection
