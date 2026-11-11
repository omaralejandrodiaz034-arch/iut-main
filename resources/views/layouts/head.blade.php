@auth
<style>
    .nav-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-radius: 0.875rem;
        padding: 0.75rem 0.875rem;
        color: rgba(255, 255, 255, 0.82);
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        transform: translateX(2px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08));
        color: #fff;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    }

    .nav-link.active::before {
        content: "";
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 3px;
        border-radius: 9999px;
        background: linear-gradient(180deg, #f9c7d0 0%, #f3a7b5 100%);
    }

    .brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 0.8rem;
        background: rgba(255, 255, 255, 0.09);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #f7d8dd;
    }
</style>

<!-- Header Móvil con Campanita integrada en la derecha -->
<header class="sticky top-0 z-40 flex h-16 items-center justify-between bg-[#510817] px-4 text-white shadow-lg shadow-[#510817]/20 lg:hidden">
    <div class="flex items-center gap-3 font-bold tracking-[0.12em] text-sm uppercase">
        <span class="brand-mark">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m-2 4h2m-2 4h2m3-8h2m-2 4h2m-2 4h2"/>
            </svg>
        </span>
        <span>Bienes Nacionales</span>
    </div>

    <div class="flex items-center gap-2">
        @auth
            <div class="relative flex items-center text-slate-800">
                <x-campana-notificaciones />
            </div>
        @endauth
        <button id="mobile-menu-btn" type="button" aria-label="Abrir menú" aria-controls="app-sidebar" aria-expanded="false"
            class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-red-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</header>

<!-- Backdrop para móviles -->
<div id="sidebar-backdrop" class="fixed inset-0 z-50 hidden bg-slate-950/60 lg:hidden" aria-hidden="true"></div>

<!-- Sidebar Principal -->
<aside id="app-sidebar" aria-label="Navegación principal"
    class="fixed inset-y-0 left-0 z-[60] flex w-72 max-w-[85vw] -translate-x-full flex-col border-r border-[#7d1d33] bg-[#510817] bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.12),_transparent_35%)] text-white shadow-2xl shadow-[#26050e]/30 transition-transform duration-300 ease-in-out lg:w-64 lg:max-w-none lg:translate-x-0">

    <!-- Cabecera del Sidebar -->
    <div class="flex h-16 flex-shrink-0 items-center justify-between border-b border-white/10 px-4">
        <a href="{{ url('/') }}" class="flex items-center gap-3 font-bold tracking-[0.14em] text-white no-underline uppercase">
            <span class="brand-mark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M6 21V5a2 2 0 012-2h8a2 2 0 012 2v16M9 8h2m-2 4h2m-2 4h2m3-8h2m-2 4h2m-2 4h2"/>
                </svg>
            </span>
            <span class="text-xs">Bienes Nacionales</span>
        </a>
        <button id="sidebar-close-btn" type="button" aria-label="Cerrar menú"
            class="rounded-lg p-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-200 lg:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Buscador Global (Solo usuarios autenticados) -->
    @auth
        <div class="flex-shrink-0 border-b border-white/10 px-3 py-4">
            <label for="global-search-input" class="sr-only">Buscar bienes y usuarios</label>
            <div id="global-search-wrap" class="relative">
                <input id="global-search-input" type="text" placeholder="Buscar bienes, usuarios…"
                    autocomplete="off"
                    class="w-full rounded-xl border border-white/10 bg-[#2d0814]/70 py-2.5 pl-9 pr-3 text-sm text-white placeholder-slate-300 shadow-inner shadow-black/10 focus:border-red-200 focus:outline-none focus:ring-2 focus:ring-red-300/60">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <div id="global-search-results"
                    class="absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-80 overflow-y-auto rounded-xl border border-gray-200 bg-white text-gray-900 shadow-xl">
                </div>
            </div>
        </div>
    @endauth

    <!-- Enlaces de Navegación -->
    <nav class="min-h-0 flex-1 space-y-1.5 overflow-y-auto px-3 py-4 text-sm font-medium" aria-label="Menú principal">
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                <span>Panel</span>
            </a>
        @endif

        @guest
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}" @if(request()->is('/')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-7 9 7v8a2 2 0 01-2 2h-4a2 2 0 01-2-2v-7H9v7a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <span>Inicio</span>
            </a>
        @endguest

        <a href="{{ route('organismos.index') }}" class="nav-link {{ request()->routeIs('organismos.*') ? 'active' : '' }}" @if(request()->routeIs('organismos.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21V7l4-3 4 3v14M4 21h16M10 11h4m-4 4h4"/></svg>
            <span>Organismos</span>
        </a>

        <a href="{{ route('unidades.index') }}" class="nav-link {{ request()->routeIs('unidades.*') ? 'active' : '' }}" @if(request()->routeIs('unidades.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/></svg>
            <span>Unidades</span>
        </a>

        <a href="{{ route('dependencias.index') }}" class="nav-link {{ request()->routeIs('dependencias.*') ? 'active' : '' }}" @if(request()->routeIs('dependencias.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            <span>Dependencias</span>
        </a>

        <a href="{{ route('bienes.index') }}" class="nav-link {{ request()->routeIs('bienes.*') ? 'active' : '' }}" @if(request()->routeIs('bienes.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.5 8.5L12 4 3.5 8.5 12 13l8.5-4.5zM3.5 15.5L12 20l8.5-4.5M3.5 8.5V15.5M20.5 8.5V15.5"/></svg>
            <span>Bienes</span>
        </a>

        <a href="{{ route('movimientos.index') }}" class="nav-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}" @if(request()->routeIs('movimientos.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h6l2-6 4 12 2-6h4"/></svg>
            <span>Movimientos</span>
        </a>

        <a href="{{ route('responsables.index') }}" class="nav-link {{ request()->routeIs('responsables.*') ? 'active' : '' }}" @if(request()->routeIs('responsables.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M9.5 11a3 3 0 100-6 3 3 0 000 6zm10.5 10v-2a4 4 0 00-3-3.87M16.5 3.13a4 4 0 010 7.75"/></svg>
            <span>Responsables</span>
        </a>

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" @if(request()->routeIs('usuarios.*')) aria-current="page" @endif>
                <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 20a6 6 0 00-12 0M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                <span>Usuarios</span>
            </a>
        @endif

        <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" @if(request()->routeIs('reportes.*')) aria-current="page" @endif>
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h6l4 4v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm3 5v4m3-2v2m3-4v4"/></svg>
            <span>Reportes</span>
        </a>

    </nav>

    <!-- Footer del Sidebar / Perfil de Usuario con Menú de 3 Puntos -->
    <div class="flex-shrink-0 border-t border-white/10 p-3">
        @auth
            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 shadow-inner shadow-black/10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-[#7d1d33] text-sm font-bold text-white ring-2 ring-white/10 flex-shrink-0">
                        @if(auth()->user()->foto_perfil)
                            <img src="{{ asset('storage/fotos_perfil/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</p>
                        <a href="{{ route('perfil.show') }}" class="text-xs text-red-100 hover:text-white">Mi perfil</a>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                Iniciar sesión
            </a>
        @endauth
    </div>
</aside>

<!-- Script para el comportamiento del menú móvil, menú de usuario y campanita -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuButton = document.getElementById('mobile-menu-btn');
        const closeButton = document.getElementById('sidebar-close-btn');
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        // Control del menú lateral móvil
        const closeSidebar = () => {
            if (!sidebar || !backdrop || !menuButton) return;
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            menuButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        };

        if (menuButton && sidebar && backdrop) {
            menuButton.addEventListener('click', () => {
                const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
                if (isOpen) {
                    closeSidebar();
                    return;
                }
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                menuButton.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            });

            closeButton?.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
            sidebar.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeSidebar));
        }

        // Control del menú desplegable de 3 puntos del usuario
        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = userMenuDropdown.classList.contains('hidden');
                userMenuDropdown.classList.toggle('hidden', !isHidden);
                userMenuBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            });

            // Cerrar el menú de usuario al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                    userMenuBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Cerrar con tecla Escape
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
                if (userMenuDropdown) {
                    userMenuDropdown.classList.add('hidden');
                    userMenuBtn?.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });
</script>
@endauth
