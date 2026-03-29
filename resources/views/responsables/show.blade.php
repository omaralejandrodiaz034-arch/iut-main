@extends('layouts.base')

@section('title', 'Ver Responsable - Sistema de Bienes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-[#510817]">Panel</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('responsables.index') }}" class="text-gray-500 hover:text-[#510817]">Responsables</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Ver Detalle</span>
    </nav>

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">👤 {{ $responsable->nombre }}</h1>
            <p class="text-gray-600 mt-1">Cédula: {{ $responsable->cedula }}</p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="{{ route('responsables.edit', $responsable) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Editar
            </a>
            <a href="{{ route('responsables.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos del Responsable -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#510817]" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Datos del Responsable
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Cédula</p>
                        <p class="font-medium text-gray-900">{{ $responsable->cedula }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p class="font-medium text-gray-900">{{ $responsable->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipo de Responsable</p>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $responsable->tipo->nombre ?? 'Sin tipo' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Correo</p>
                        <p class="font-medium text-gray-900">{{ $responsable->correo ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="font-medium text-gray-900">{{ $responsable->telefono ?? 'No registrado' }}</p>
                    </div>
                </div>
            </div>

            <!-- Dependencias Asignadas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#510817]" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                    Dependencias Asignadas
                    <span class="text-sm font-normal text-gray-500">({{ $responsable->dependencias->count() }})</span>
                </h2>
                
                @if($responsable->dependencias->count() > 0)
                    <div class="space-y-3">
                        @foreach($responsable->dependencias as $dependencia)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $dependencia->nombre }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $dependencia->unidadAdministradora->nombre ?? 'Sin unidad' }} - 
                                            {{ $dependencia->unidadAdministradora->organismo->nombre ?? 'Sin organismo' }}
                                        </p>
                                    </div>
                                    <a href="{{ route('dependencias.show', $dependencia) }}" 
                                       class="text-[#510817] hover:text-[#6D1426] text-sm font-medium">
                                        Ver →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No hay dependencias asignadas a este responsable.</p>
                @endif
            </div>

            <!-- Bienes a Cargo -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#510817]" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    Bienes a Cargo
                    <span class="text-sm font-normal text-gray-500">({{ $responsable->bienes->count() }})</span>
                </h2>
                
                @if($responsable->bienes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left">Código</th>
                                    <th class="px-3 py-2 text-left">Descripción</th>
                                    <th class="px-3 py-2 text-left">Estado</th>
                                    <th class="px-3 py-2 text-left">Dependencia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($responsable->bienes->take(10) as $bien)
                                    <tr>
                                        <td class="px-3 py-2 font-mono text-gray-600">{{ $bien->codigo }}</td>
                                        <td class="px-3 py-2">{{ $bien->descripcion }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-0.5 rounded text-xs
                                                @switch($bien->estado)
                                                    @case('ACTIVO') bg-green-100 text-green-800 @break
                                                    @case('DANADO') bg-red-100 text-red-800 @break
                                                    @case('EN_MANTENIMIENTO') bg-yellow-100 text-yellow-800 @break
                                                    @case('EXTRAVIADO') bg-gray-100 text-gray-800 @break
                                                    @case('DESINCORPORADO') bg-gray-200 text-gray-600 @break
                                                @endswitch">
                                                {{ $bien->estado }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-gray-600">{{ $bien->dependencia->nombre ?? 'Sin dependencia' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($responsable->bienes->count() > 10)
                        <p class="text-center text-gray-500 text-sm mt-2">
                            Y {{ $responsable->bienes->count() - 10 }} bienes más...
                        </p>
                    @endif
                @else
                    <p class="text-gray-500 text-center py-4">No hay bienes a cargo de este responsable.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold text-gray-800 mb-4">Acciones</h3>
                <div class="space-y-2">
                    <a href="{{ route('responsables.edit', $responsable) }}" 
                       class="w-full flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Editar Datos
                    </a>
                    @if(auth()->user()->canDeleteData())
                    <form action="{{ route('responsables.destroy', $responsable) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition"
                                onclick="return confirm('¿Está seguro de eliminar este responsable?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Eliminar
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold text-gray-800 mb-4">Resumen</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dependencias</span>
                        <span class="font-bold text-gray-900">{{ $responsable->dependencias->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bienes a cargo</span>
                        <span class="font-bold text-gray-900">{{ $responsable->bienes->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
