@extends('layouts.base')

@section('title', 'Mi Perfil')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Mi Perfil']]" />
@endpush

<div class="max-w-2xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-gray-900">Mi Perfil</h1>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- ── Foto de perfil ─────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#800020] to-[#5a0016] px-6 py-4">
            <h2 class="text-white font-bold text-base">Foto de Perfil</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-6">
                {{-- Foto actual --}}
                <div class="flex-shrink-0">
                    @if($usuario->foto_perfil)
                        <img src="{{ asset('storage/fotos_perfil/' . $usuario->foto_perfil) }}" 
                             alt="Foto de perfil" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#800020] to-[#5a0016] flex items-center justify-center border-4 border-gray-200 shadow-lg">
                            <span class="text-3xl font-bold text-white">
                                {{ strtoupper(substr($usuario->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->apellido, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Formulario de subida --}}
                <div class="flex-1">
                    <form method="POST" action="{{ route('perfil.foto.update') }}" 
                          enctype="multipart/form-data" 
                          class="space-y-3">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Subir nueva foto</label>
                            <input type="file" 
                                   name="foto_perfil" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif"
                                   class="w-full px-4 py-2.5 border-2 @error('foto_perfil') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition text-sm">
                            @error('foto_perfil')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Formatos: JPEG, PNG, JPG, GIF. Máximo 2MB</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="px-4 py-2 bg-[#800020] text-white rounded-xl font-bold text-sm hover:bg-[#5a0016] transition">
                                Subir Foto
                            </button>
                            
                            @if($usuario->foto_perfil)
                                <button type="button"
                                    onclick="document.getElementById('eliminar-foto-modal').classList.remove('hidden')"
                                    class="px-4 py-2 bg-red-100 text-red-600 rounded-xl font-bold text-sm hover:bg-red-200 transition">
                                    Eliminar
                                </button>
                            @endif
                        </div>
                    </form>

                    {{-- Modal de confirmación para eliminar --}}
                    @if($usuario->foto_perfil)
                        <div id="eliminar-foto-modal" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <p class="text-sm text-red-700 mb-3">¿Estás seguro de que quieres eliminar tu foto de perfil?</p>
                            <form method="POST" action="{{ route('perfil.foto.delete') }}" class="flex gap-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition">
                                    Sí, eliminar
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('eliminar-foto-modal').classList.add('hidden')"
                                    class="px-4 py-2 bg-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-300 transition">
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Información del perfil ──────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-[#800020] to-[#5a0016] px-6 py-4">
            <h2 class="text-white font-bold text-base">Información Personal</h2>
        </div>
        <form method="POST" action="{{ route('perfil.update') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                        class="w-full px-4 py-2.5 border-2 @error('nombre') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                        required>
                    @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Apellido</label>
                    <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido) }}"
                        class="w-full px-4 py-2.5 border-2 @error('apellido') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                        required>
                    @error('apellido')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" name="correo" value="{{ old('correo', $usuario->correo) }}"
                    class="w-full px-4 py-2.5 border-2 @error('correo') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                    required>
                @error('correo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 pt-1 text-sm text-gray-500">
                <div><span class="font-semibold text-gray-700">Cédula:</span> {{ $usuario->cedula }}</div>
                <div><span class="font-semibold text-gray-700">Rol:</span> {{ optional($usuario->rol)->nombre ?? 'N/A' }}</div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-[#800020] text-white rounded-xl font-bold text-sm hover:bg-[#5a0016] transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    {{-- ── Cambiar contraseña ──────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gray-800 px-6 py-4">
            <h2 class="text-white font-bold text-base">Cambiar Contraseña</h2>
        </div>
        <form method="POST" action="{{ route('perfil.password') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Contraseña Actual</label>
                <input type="password" name="password_actual"
                    class="w-full px-4 py-2.5 border-2 @error('password_actual') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                    placeholder="••••••••" required>
                @error('password_actual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 border-2 @error('password') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                        placeholder="Mínimo 8 caracteres" required minlength="8">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800020] transition"
                        placeholder="Repite la contraseña" required minlength="8">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-gray-800 text-white rounded-xl font-bold text-sm hover:bg-gray-900 transition">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
