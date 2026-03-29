@extends('layouts.base')

@section('title', 'Editar Responsable - Sistema de Bienes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-[#510817]">Panel</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('responsables.index') }}" class="text-gray-500 hover:text-[#510817]">Responsables</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-700">Editar</span>
    </nav>

    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">✏️ Editar Responsable</h1>
        <p class="text-gray-600 mt-1">Actualice los datos del responsable</p>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('responsables.update', $responsable) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <!-- Cédula -->
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula de Identidad *</label>
                        <input type="text" name="cedula" id="cedula" value="{{ old('cedula', $responsable->cedula) }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('cedula') border-red-500 @enderror"
                               required>
                        @error('cedula')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $responsable->nombre) }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('nombre') border-red-500 @enderror"
                               required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Responsable -->
                    <div>
                        <label for="tipo_id" class="block text-sm font-medium text-gray-700">Tipo de Responsable *</label>
                        <select name="tipo_id" id="tipo_id" 
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('tipo_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_id', $responsable->tipo_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Correo -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" value="{{ old('correo', $responsable->correo) }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('correo') border-red-500 @enderror"
                               placeholder="correo@ejemplo.com">
                        @error('correo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $responsable->telefono) }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#510817] @error('telefono') border-red-500 @enderror"
                               placeholder="0412-1234567">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('responsables.show', $responsable) }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-[#510817] text-white rounded-lg hover:bg-[#6D1426] transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
