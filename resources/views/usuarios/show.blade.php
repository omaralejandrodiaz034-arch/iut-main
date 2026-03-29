@extends('layouts.base')

@section('title', 'Detalles del Usuario')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Usuarios', 'url' => route('usuarios.index')], ['label' => $usuario->nombre.' '.$usuario->apellido]]" />
@endpush
<div class="max-w-2xl mx-auto">
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <div class="flex items-center">
                {{-- Icono SVG en lugar de componente Blade --}}
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <div>
            <a href="{{ route('usuarios.index') }}"
               class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded hover:bg-gray-200 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Regresar
            </a>
        </div>

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-3xl font-bold text-gray-800 leading-tight">{{ $usuario->nombre_completo }}</h1>
            <div class="flex flex-wrap gap-2 md:justify-end">
                @include('components.show-actions', ['resource' => 'usuarios', 'model' => $usuario])
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información Personal</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nombre</p>
                        <p class="text-base font-medium text-gray-800">{{ $usuario->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Apellido</p>
                        <p class="text-base font-medium text-gray-800">{{ $usuario->apellido }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Cédula</p>
                        <p class="text-base font-medium text-gray-800">{{ $usuario->cedula }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Correo</p>
                        <p class="text-base font-medium text-gray-800 break-all">{{ $usuario->correo }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información del Sistema</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Rol</p>
                        <div class="mt-1">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $usuario->rol->nombre ?? 'Sin asignar' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estado</p>
                        <div class="mt-1">
                            <span class="inline-block {{ $usuario->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-3 py-1 rounded-full text-sm font-medium">
                                {{ $usuario->activo ? '✓ Activo' : '✗ Inactivo' }}
                            </span>
                        </div>
                    </div>

                    {{-- Solución al error format() on null --}}
                    <div>
                        <p class="text-sm text-gray-600">Fecha de Creación</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'No registrada' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Última Actualización</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'No registrada' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
