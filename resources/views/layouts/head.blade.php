<nav class="bg-blue-600 text-white shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 text-xl font-bold">
            🏢 SISTEMA DE GESTION
            </div>

            <!-- Links -->
            <div class="hidden md:flex space-x-1 items-center">
                <a href="/" class="px-3 py-2 rounded hover:bg-blue-500">Inicio</a>
                <a href="{{ route('organismos.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Organismos</a>
                <a href="{{ route('unidades.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Unidades</a>
                <a href="{{ route('dependencias.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Dependencias</a>
                <a href="{{ route('bienes.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Bienes</a>
                <a href="{{ route('movimientos.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Movimientos</a>
                <a href="{{ route('usuarios.index') }}" class="px-3 py-2 rounded hover:bg-blue-500">Usuarios</a>
                {{-- Eliminados ahora se muestran dentro de Movimientos para administradores --}}
            </div>

            <!-- User and Logout -->
            <div class="flex items-center gap-3">
                @auth
                    <div class="text-sm text-blue-100">
                        <span class="font-semibold">{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </div>
</nav>


