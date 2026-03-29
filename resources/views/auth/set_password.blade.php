<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer Contraseña - Inventario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 shadow-md" style="background-color: #800020 !important;">
            <span class="text-2xl text-white">🔐</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Completar Registro</h1>
        <p class="text-gray-600 mt-2 italic">Cree sus credenciales de acceso</p>
    </div>

    <div class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-6 text-center" style="background-color: #800020 !important;">
            <h2 class="text-lg font-bold text-white">
                {{ $persona['fullname'] ?? ($persona['firstnames'] ?? 'Usuario') }} 
                <span class="block text-sm font-normal opacity-80 mt-1">Cédula: {{ $persona['pin_str'] ?? '' }}</span>
            </h2>
        </div>

        <div class="px-6 py-8">
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-600 text-red-700 rounded shadow-sm">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-600 text-blue-700 rounded shadow-sm">
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-600 text-red-700 rounded shadow-sm">
                    <ul class="text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.set_password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="cedula" value="{{ $persona['pin'] ?? '' }}">

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-800 transition shadow-sm" 
                           placeholder="Mínimo 8 caracteres" required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-800 transition shadow-sm" 
                           placeholder="Repita su contraseña" required>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full text-white font-bold py-3 rounded-lg shadow-lg hover:brightness-110 active:scale-95 transition duration-200 mt-6" style="background-color: #800020 !important;">
                    Guardar y Continuar
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-500 font-medium">© 2026 Sistema de Inventario de Bienes</p>
        </div>
    </div>
</div>
</body>
</html>