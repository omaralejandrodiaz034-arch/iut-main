<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    {{-- Título dinámico con fallback --}}
    <title>@yield('title', 'Oficina de Bienes Nacionales | UPTOS')</title>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js para dropdowns interactivos --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Estilos globales del landing y animaciones --}}
    <style>
        /* Reveal animation */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        .phrase-container { height: 1.2em; overflow: hidden; display: inline-block; vertical-align: bottom; line-height: 1.2em; }
        .phrase-inner { display: flex; flex-direction: column; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .phrase-inner span { height: 1.2em; display: block; white-space: nowrap; }

        .float-card:hover { transform: translateY(-10px) scale(1.02); }

        .step-line { position: relative; }
        .step-line::after {
            content: ''; position: absolute; top: 50%; left: 100%; width: 0; height: 2px;
            background: #640B21; transition: width 0.5s ease; z-index: 0;
        }
        .group:hover .step-line::after { width: 1.5rem; }
    </style>
</head>

<body class="bg-[#f3f4f6] text-slate-900 antialiased font-sans overflow-x-hidden lg:pl-72">

    {{-- Sidebar y Menú Móvil (Incluye layouts.head corregido) --}}
    @include('layouts.head')

    {{-- Cabecera Institucional Superior --}}
    <header class="relative z-30 w-full border-b border-slate-200 bg-white shadow-sm lg:sticky lg:top-0">
        <div class="mx-auto max-w-[1500px] px-4 py-3 lg:px-8">
            <div class="grid items-center gap-4 border border-slate-200/80 bg-white px-3 py-2 shadow-sm sm:grid-cols-[1fr_auto_1fr]">
                <div class="flex items-center gap-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-700">
                    <span class="inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-[#510817] text-white shadow-sm">🏛</span>
                    <span class="line-clamp-2">Ministerio del Poder Popular para la Educación</span>
                </div>
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/baner.jpeg') }}"
                         class="h-auto max-h-12 w-full max-w-xl object-contain"
                         alt="Banner Institucional">
                </div>
                <div class="flex items-center justify-end gap-3 text-right text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-600">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1">UPTOS</span>
                    <span>Bienes Nacionales</span>

                    @auth
                        <div class="relative ml-2 flex items-center gap-2">
                            <div class="relative">
                                <x-campana-notificaciones />
                            </div>

                            <div class="flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-2 py-1.5 shadow-sm">
                                <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-[#510817] text-[10px] font-bold text-white ring-2 ring-white">
                                    @if(auth()->user()->foto_perfil)
                                        <img src="{{ asset('storage/fotos_perfil/' . auth()->user()->foto_perfil) }}" alt="Foto de perfil" class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido, 0, 1)) }}
                                    @endif
                                </div>

                                <div class="hidden min-w-0 text-left sm:block">
                                    <p class="truncate text-[10px] font-bold leading-none text-slate-800">{{ auth()->user()->nombre_completo ?? auth()->user()->nombre }}</p>
                                    <span class="mt-1 block text-[9px] uppercase tracking-[0.12em] text-slate-500">{{ auth()->user()->isAdmin() ? 'Administrador' : 'Usuario' }}</span>
                                </div>

                                <div class="relative">
                                    <button id="header-user-menu-btn" type="button" aria-label="Abrir menú de usuario" aria-controls="header-user-menu-dropdown" aria-expanded="false"
                                        class="rounded-full p-1.5 text-slate-600 transition hover:bg-slate-200 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#510817]/20">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>

                                    <div id="header-user-menu-dropdown" class="absolute right-0 top-full z-50 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 text-left text-sm text-slate-700 shadow-xl shadow-slate-200/70">
                                        <a href="{{ route('perfil.show') }}" class="block px-4 py-2.5 transition hover:bg-slate-50">Mi Perfil</a>
                                        @if(auth()->user()?->isAdmin())
                                            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 transition hover:bg-slate-50">Dashboard</a>
                                            <a href="{{ route('usuarios.index') }}" class="block px-4 py-2.5 transition hover:bg-slate-50">Usuarios</a>
                                            <a href="{{ route('auditoria.index') }}" class="block px-4 py-2.5 transition hover:bg-slate-50">Logs</a>
                                        @endif
                                        <a href="{{ route('reportes.index') }}" class="block px-4 py-2.5 transition hover:bg-slate-50">Reportes</a>
                                        <hr class="my-1 border-slate-100">
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="w-full px-4 py-2.5 text-left text-red-600 transition hover:bg-red-50">Cerrar sesión</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-[#510817] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-white shadow-sm transition hover:bg-[#6a1026]">Iniciar sesión</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Contenido Principal --}}
    <main class="w-full px-3 py-4 sm:px-5 md:px-7 lg:px-8">
        <div class="mx-auto max-w-[1500px]">
            @stack('breadcrumbs')
            @yield('content')
        </div>
    </main>

    {{-- Script para animaciones Reveal con mejor rendimiento --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const revealElements = document.querySelectorAll(".reveal");

            const checkReveal = () => {
                revealElements.forEach(el => {
                    const top = el.getBoundingClientRect().top;
                    if (top < window.innerHeight - 100) {
                        el.classList.add("active");
                    }
                });
            };

            window.addEventListener("scroll", checkReveal);
            checkReveal(); // Comprobar elementos visibles al cargar
        });
    </script>

    {{-- Script de filtros automáticos (para vistas con buscador/filtros) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filtrosForm');
            if (!form) return;

            const filtros = form.querySelectorAll('input, select');
            filtros.forEach(filtro => {
                filtro.addEventListener('change', () => form.submit());

                if (filtro.type === 'text') {
                    let timeout;
                    filtro.addEventListener('input', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => form.submit(), 500);
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const headerUserMenuBtn = document.getElementById('header-user-menu-btn');
            const headerUserMenuDropdown = document.getElementById('header-user-menu-dropdown');

            if (headerUserMenuBtn && headerUserMenuDropdown) {
                headerUserMenuBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const shouldShow = headerUserMenuDropdown.classList.contains('hidden');
                    headerUserMenuDropdown.classList.toggle('hidden', !shouldShow);
                    headerUserMenuBtn.setAttribute('aria-expanded', shouldShow ? 'true' : 'false');
                });

                document.addEventListener('click', (event) => {
                    if (!headerUserMenuBtn.contains(event.target) && !headerUserMenuDropdown.contains(event.target)) {
                        headerUserMenuDropdown.classList.add('hidden');
                        headerUserMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        headerUserMenuDropdown.classList.add('hidden');
                        headerUserMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
