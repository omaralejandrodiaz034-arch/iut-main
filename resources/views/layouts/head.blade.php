<header class="sticky top-0 z-40 flex h-16 items-center justify-between bg-[#510817] px-4 text-white shadow-lg lg:hidden">
    <div class="flex items-center gap-2 font-bold tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m-2 4h2m-2 4h2m3-8h2m-2 4h2m-2 4h2"/>
        </svg>
        <span>BIENES NACIONALES</span>
    </div>
    <div class="flex items-center gap-2">
        <button id="mobile-menu-btn" type="button" aria-label="Abrir menú" aria-controls="app-sidebar" aria-expanded="false"
            class="rounded-lg p-2 text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-red-200">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</header>

<div id="sidebar-backdrop" class="fixed inset-0 z-50 hidden bg-slate-950/60 lg:hidden" aria-hidden="true"></div>

<aside id="app-sidebar" aria-label="Navegación principal"
    class="fixed inset-y-0 left-0 z-[60] flex w-72 max-w-[85vw] -translate-x-full flex-col border-r border-[#6D1426] bg-[#510817] text-white shadow-xl transition-transform duration-300 ease-in-out lg:w-64 lg:max-w-none lg:translate-x-0">
    <div class="flex h-16 flex-shrink-0 items-center justify-between border-b border-white/10 px-5">
        <a href="{{ url('/') }}" class="flex items-center gap-3 font-bold tracking-wider text-white no-underline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 flex-shrink-0 text-red-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m-2 4h2m-2 4h2m3-8h2m-2 4h2m-2 4h2"/>
            </svg>
            <span class="text-sm">BIENES NACIONALES</span>
        </a>
        <button id="sidebar-close-btn" type="button" aria-label="Cerrar menú"
            class="rounded-lg p-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-200 lg:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    @auth
        <div class="flex-shrink-0 border-b border-white/10 px-4 py-4">
            <label for="global-search-input" class="sr-only">Buscar bienes y usuarios</label>
            <div id="global-search-wrap" class="relative">
                <input id="global-search-input" type="text" placeholder="Buscar bienes, usuarios…"
                    autocomplete="off"
                    class="w-full rounded-lg border border-white/10 bg-slate-900/40 py-2 pl-9 pr-3 text-sm text-white placeholder-slate-400 focus:border-red-200 focus:outline-none focus:ring-1 focus:ring-red-200">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <div id="global-search-results"
                    class="absolute left-0 right-0 top-full z-50 mt-1 hidden max-h-80 overflow-y-auto rounded-xl border border-gray-100 bg-white text-gray-900 shadow-xl">
                </div>
            </div>
        </div>
    @endauth

    <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm font-medium">
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('dashboard') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('dashboard')]) @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                <span>Panel</span>
            </a>
        @endif

        @guest
            <a href="{{ url('/') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->is('/')]) @if(request()->is('/')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-7 9 7v8a2 2 0 01-2 2h-4a2 2 0 01-2-2v-7H9v7a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <span>Inicio</span>
            </a>
        @endguest

        <a href="{{ route('organismos.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('organismos.*')]) @if(request()->routeIs('organismos.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21V7l4-3 4 3v14M4 21h16M10 11h4m-4 4h4"/></svg>
            <span>Organismos</span>
        </a>
        <a href="{{ route('unidades.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('unidades.*')]) @if(request()->routeIs('unidades.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/></svg>
            <span>Unidades</span>
        </a>
        <a href="{{ route('dependencias.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('dependencias.*')]) @if(request()->routeIs('dependencias.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            <span>Dependencias</span>
        </a>
        <a href="{{ route('bienes.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('bienes.*')]) @if(request()->routeIs('bienes.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.5 8.5L12 4 3.5 8.5 12 13l8.5-4.5zM3.5 15.5L12 20l8.5-4.5M3.5 8.5V15.5M20.5 8.5V15.5"/></svg>
            <span>Bienes</span>
        </a>
        <a href="{{ route('movimientos.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('movimientos.*')]) @if(request()->routeIs('movimientos.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h-7m0 0l3-3m-3 3l3 3M7 16h7m0 0l-3 3m3-3l-3-3"/></svg>
            <span>Movimientos</span>
        </a>
        <a href="{{ route('responsables.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('responsables.*')]) @if(request()->routeIs('responsables.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Responsables</span>
        </a>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('usuarios.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('usuarios.*')]) @if(request()->routeIs('usuarios.*')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Usuarios</span>
            </a>
        @endif
        <a href="{{ route('reportes.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('reportes.*')]) @if(request()->routeIs('reportes.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h6l4 4v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm3 5v4m3-2v2m3-4v4"/></svg>
            <span>Reportes</span>
        </a>
        <a href="{{ route('actas.pendientes') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('actas.pendientes')]) @if(request()->routeIs('actas.pendientes')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Actas pendientes</span>
        </a>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('auditoria.index') }}" @class(['flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 hover:text-red-100', 'bg-white/15 text-white' => request()->routeIs('auditoria.*')]) @if(request()->routeIs('auditoria.*')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14a3 3 0 004 0l4-4a3 3 0 00-4-4l-1 1m1 3a3 3 0 00-4 0l-4 4a3 3 0 004 4l1-1"/></svg>
                <span>Auditoría</span>
            </a>
        @endif
    </nav>

    <div class="flex-shrink-0 border-t border-white/10 p-3">
        @auth
            <div class="mb-2 flex items-center gap-3 rounded-lg px-2 py-2">
                <div class="relative flex-shrink-0">
                    <x-campana-notificaciones />
                </div>
                @if(auth()->user()->foto_perfil)
                    <img src="{{ asset('storage/fotos_perfil/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil" class="h-9 w-9 rounded-full object-cover">
                @else
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-[#6D1426] text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</p>
                    <a href="{{ route('perfil.show') }}" class="text-xs text-red-200 hover:text-white">Mi perfil</a>
                </div>
                <div class="relative">
                    <button id="user-menu-btn" type="button" aria-label="Abrir opciones de usuario" aria-controls="user-menu-dropdown"
                        class="rounded-md p-1.5 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                    <div id="user-menu-dropdown" class="absolute bottom-full right-0 z-50 mb-2 hidden w-52 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 text-gray-700 shadow-xl">
                        <a href="{{ route('perfil.show') }}" class="block px-4 py-2.5 text-sm transition hover:bg-gray-50">Mi Perfil</a>
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm transition hover:bg-gray-50">Dashboard</a>
                        @endif
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 rounded-lg bg-white/10 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                Iniciar sesión
            </a>
        @endauth
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuButton = document.getElementById('mobile-menu-btn');
        const closeButton = document.getElementById('sidebar-close-btn');
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        if (!menuButton || !sidebar || !backdrop) return;

        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            menuButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        };

        menuButton.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            menuButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');
        });

        closeButton?.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);
        sidebar.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeSidebar));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeSidebar();
        });
    });
</script>
