<nav class="bg-[#510817] text-white shadow-lg border-b border-[#6D1426]">
    <div class="w-full px-1 sm:px-2 lg:px-4">
        <div class="flex items-center justify-between h-12 sm:h-14">

            {{-- Logo / Título --}}
            <div class="flex items-center gap-1 sm:gap-2 pr-1 sm:pr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-indigo-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m-2 4h2m-2 4h2m3-8h2m-2 4h2m-2 4h2"/>
                </svg>
                <span class="text-[10px] sm:text-xs font-bold tracking-wider text-white hidden lg:inline">BIENES NACIONALES</span>
                <span class="text-[10px] sm:text-xs font-bold tracking-wider text-white lg:hidden">BIENES</span>
            </div>

            @auth
            {{-- Búsqueda Global AJAX --}}
            <div class="relative hidden lg:block mx-2 flex-1 max-w-xs" id="global-search-wrap">
                <input id="global-search-input" type="text" placeholder="Buscar bienes, usuarios…"
                    autocomplete="off"
                    class="w-full pl-9 pr-3 py-1.5 rounded-lg bg-slate-700/60 text-white placeholder-slate-400 text-sm border border-slate-600 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <div id="global-search-results"
                    class="absolute top-full mt-1 left-0 right-0 bg-white shadow-xl rounded-xl border border-gray-100 z-50 hidden overflow-hidden max-h-80 overflow-y-auto">
                </div>
            </div>

            {{-- Enlaces de Navegación --}}
            <div class="hidden lg:flex items-center space-x-0.5 text-xs font-medium">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('dashboard')])>
                    <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span>Panel</span>
                </a>

                <a href="{{ url('/') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->is('/')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-7 9 7v8a2 2 0 01-2 2h-4a2 2 0 01-2-2V13H9v7a2 2 0 01-2 2H3z"/>
                    </svg>
                    <span>Inicio</span>
                </a>

                <a href="{{ route('organismos.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('organismos.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 21V7l4-3 4 3v14M4 21h16M10 11h4m-4 4h4"/>
                    </svg>
                    <span>Organismos</span>
                </a>

                <a href="{{ route('unidades.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('unidades.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                    </svg>
                    <span>Unidades</span>
                </a>

                <a href="{{ route('dependencias.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('dependencias.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                    <span>Dependencias</span>
                </a>

                <a href="{{ route('bienes.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('bienes.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.5 8.5L12 4 3.5 8.5 12 13l8.5-4.5zM3.5 15.5L12 20l8.5-4.5M3.5 8.5V15.5M20.5 8.5V15.5"/>
                    </svg>
                    <span>Bienes</span>
                </a>

                <a href="{{ route('movimientos.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('movimientos.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 8h-7m0 0l3-3m-3 3l3 3M7 16h7m0 0l-3 3m3-3l-3-3"/>
                    </svg>
                    <span>Movimientos</span>
                </a>

                <a href="{{ route('responsables.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('responsables.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Responsables</span>
                </a>

                <a href="{{ route('usuarios.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('usuarios.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Usuarios</span>
                </a>

                <a href="{{ route('reportes.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-slate-800 hover:text-indigo-300"
                   @class(['bg-slate-800 text-indigo-400 shadow-inner' => request()->routeIs('reportes.*')])>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h6l4 4v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm3 5v4m3-2v2m3-4v4"/>
                    </svg>
                    <span>Reportes</span>
                </a>
            </div>
            @endauth

            {{-- Sección de Usuario / Acciones --}}
            <div class="flex items-center gap-2 whitespace-nowrap">
                @auth
                    {{-- Botón menú móvil --}}
                    <button id="mobile-menu-btn" type="button"
                        class="lg:hidden p-1.5 sm:p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Menú perfil/auditoría --}}
                    <div class="relative" id="user-menu-wrap">
                        <button id="user-menu-btn"
                            class="hidden sm:flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-600">
                            @if(auth()->user()->foto_perfil)
                                <img src="{{ asset('storage/fotos_perfil/' . auth()->user()->foto_perfil) }}"
                                     alt="Foto"
                                     class="h-6 w-6 rounded-full object-cover">
                            @else
                                <div class="h-6 w-6 rounded-full bg-[#640B21] flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-slate-300 max-w-[140px] truncate">
                                {{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="user-menu-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden py-1">
                            <a href="{{ route('perfil.show') }}"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                👤 Mi Perfil
                            </a>
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                📊 Dashboard
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('auditoria.index') }}"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                🔍 Auditoría
                            </a>
                            @endif
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    🚪 Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Fallback logout para móvil --}}
                    <form method="POST" action="{{ route('logout') }}" class="sm:hidden m-0">
                        @csrf
                        <button type="submit"
                            class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                        Iniciar sesión
                    </a>
                @endauth
            </div>

        </div>
    </div>

    {{-- Menú móvil desplegable --}}
    <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-700 bg-slate-800">
        <div class="px-2 pt-2 pb-3 space-y-1">
            {{-- Búsqueda móvil --}}
            <div class="relative mb-3" id="global-search-wrap-mobile">
                <input id="global-search-input-mobile" type="text" placeholder="Buscar bienes, usuarios…"
                    autocomplete="off"
                    class="w-full pl-9 pr-3 py-2 rounded-lg bg-slate-700/60 text-white placeholder-slate-400 text-sm border border-slate-600 focus:outline-none focus:border-indigo-400">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <div id="global-search-results-mobile"
                    class="absolute top-full mt-1 left-0 right-0 bg-white shadow-xl rounded-xl border border-gray-100 z-50 hidden overflow-hidden max-h-80 overflow-y-auto">
                </div>
            </div>

            {{-- Enlaces de navegación móvil --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('dashboard')) bg-slate-700 text-white @endif">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                <span>Panel</span>
            </a>
            <a href="{{ url('/') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->is('/')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-7 9 7v8a2 2 0 01-2 2h-4a2 2 0 01-2-2V13H9v7a2 2 0 01-2 2H3z"/></svg>
                <span>Inicio</span>
            </a>
            <a href="{{ route('organismos.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('organismos.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 21V7l4-3 4 3v14M4 21h16M10 11h4m-4 4h4"/></svg>
                <span>Organismos</span>
            </a>
            <a href="{{ route('unidades.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('unidades.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/></svg>
                <span>Unidades</span>
            </a>
            <a href="{{ route('dependencias.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('dependencias.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                <span>Dependencias</span>
            </a>
            <a href="{{ route('bienes.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('bienes.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.5 8.5L12 4 3.5 8.5 12 13l8.5-4.5zM3.5 15.5L12 20l8.5-4.5M3.5 8.5V15.5M20.5 8.5V15.5"/></svg>
                <span>Bienes</span>
            </a>
            <a href="{{ route('movimientos.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('movimientos.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 8h-7m0 0l3-3m-3 3l3 3M7 16h7m0 0l-3 3m3-3l-3-3"/></svg>
                <span>Movimientos</span>
            </a>
            <a href="{{ route('usuarios.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('usuarios.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('reportes.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition @if(request()->routeIs('reportes.*')) bg-slate-700 text-white @endif">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h6l4 4v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm3 5v4m3-2v2m3-4v4"/></svg>
                <span>Reportes</span>
            </a>

            {{-- Sección de usuario móvil --}}
            <hr class="my-3 border-slate-600">
            @auth
            <div class="flex items-center gap-2 px-3 py-2 text-slate-400 text-sm">
                <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0v2a4 4 0 004 4h0a4 4 0 004-4v-2zM12 7a3 3 0 110-6 3 3 0 010 6z"/></svg>
                <span>{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</span>
            </div>
            <a href="{{ route('perfil.show') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <span>👤</span><span>Mi Perfil</span>
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('auditoria.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <span>🔍</span><span>Auditoría</span>
            </a>
            @endif
            @endauth
        </div>
    </div>
</nav>

<script>
    // Toggle menú móvil
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
