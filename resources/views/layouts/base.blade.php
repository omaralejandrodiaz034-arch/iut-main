<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- Título dinámico con fallback --}}
    <title>@yield('title', 'Oficina de Bienes Nacionales | UPTOS')</title>

    {{-- NO usar CDN de Tailwind, ya tienes Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos globales del landing + Dark Mode CSS --}}
    <style>
        /* Reveal animation */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        .phrase-container { height: 1.2em; overflow: hidden; display: inline-block; vertical-align: bottom; }
        .phrase-inner { display: flex; flex-direction: column; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        .float-card:hover { transform: translateY(-10px) scale(1.02); }

        .step-line { position: relative; }
        .step-line::after {
            content: ''; position: absolute; top: 50%; left: 100%; width: 0; height: 2px;
            background: #640B21; transition: width 0.5s ease; z-index: 0;
        }
        .group:hover .step-line::after { width: 1.5rem; }

        .phrase-container { height: 1.2em; line-height: 1.2em; }
        .phrase-inner span { height: 1.2em; display: block; white-space: nowrap; }

        /* Dark Mode CSS - Aplicar solo cuando html tenga clase .dark */
        html.dark body { background-color: #0f172a; color: #e2e8f0; }

        /* Fondos en modo oscuro - solo elementos específicos */
        html.dark .bg-white { background-color: #1e293b; }
        html.dark .bg-white\/80 { background-color: #1e293b; }

        /* Textos en modo oscuro */
        html.dark .text-slate-900 { color: #e2e8f0; }
        html.dark .text-gray-900 { color: #f1f5f9; }
        html.dark .text-gray-800 { color: #e2e8f0; }
        html.dark .text-gray-700 { color: #cbd5e1; }

        /* Bordes en modo oscuro */
        html.dark .border-gray-200 { border-color: #334155; }
        html.dark .border-gray-100 { border-color: #1e293b; }

        /* Sombras en modo oscuro */
        html.dark .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5); }
        html.dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.4); }
        html.dark .shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4); }

        /* Formularios en modo oscuro - solo inputs sin clases explícitas */
        html.dark input:not([class*="bg-"]),
        html.dark select:not([class*="bg-"]),
        html.dark textarea:not([class*="bg-"]) {
            background-color: #1e293b;
            color: #f1f5f9;
            border-color: #334155;
        }

        /* Enlaces en modo oscuro */
        html.dark a:not([class*="text-"]) { color: #94a3b8; }

        /* Header/Navbar en modo oscuro */
        html.dark header.bg-white\/80 { background-color: rgba(15, 23, 42, 0.95); }

        /* Scrollbar oscuro */
        html.dark ::-webkit-scrollbar { width: 8px; }
        html.dark ::-webkit-scrollbar-track { background: #1e293b; }
        html.dark ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        html.dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Transición suave */
        body, body * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body class="bg-[#fcfcfc] dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased font-sans overflow-x-hidden transition-colors">

    {{-- Banner Institucional --}}
    <header class="w-full bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <img src="https://www.uptos.edu.ve/wp-content/uploads/2026/02/cropped-cropped-WhatsApp-Image-2026-02-11-at-10.17.38-PM-1.jpeg"
                 class="h-10 md:h-14 w-auto object-contain hover:scale-105 transition-transform" alt="Logo UPTOS">
            <div class="hidden md:block text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Venezuela</p>
                <p class="text-[10px] font-bold text-[#640B21] uppercase">M.P.P. Educación Universitaria</p>
            </div>
        </div>

        {{-- Navbar del sistema --}}
        @include('layouts.head')
    </header>

    {{-- Contenido Principal --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @stack('breadcrumbs')
        @yield('content')
    </main>

    {{-- Script reveal --}}
    <script>
        document.addEventListener("scroll", () => {
            document.querySelectorAll(".reveal").forEach(el => {
                const top = el.getBoundingClientRect().top;
                if (top < window.innerHeight - 100) {
                    el.classList.add("active");
                }
            });
        });
    </script>

    {{-- Script de filtros automáticos --}}
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

    @stack('scripts')

    {{-- Global Search, Dark Mode & User Menu Scripts --}}
    <script>
    (function() {
        'use strict';

        // --- Dark Mode Toggle ---
        const darkBtn = document.getElementById('dark-mode-btn');
        const html = document.documentElement; // Cambiar a elemento <html>
        const body = document.body;
        const darkIcon = document.getElementById('dark-icon');
        const DARK_KEY = 'dark_mode';

        function applyDarkMode(isDark) {
            if (isDark) {
                // Añadir clase 'dark' al elemento <html> para Tailwind y CSS personalizado
                html.classList.add('dark');
                darkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
                localStorage.setItem(DARK_KEY, '1');
            } else {
                // Quitar clase 'dark' del elemento <html>
                html.classList.remove('dark');
                darkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
                localStorage.setItem(DARK_KEY, '0');
            }
        }

        // Init dark mode from localStorage
        const savedPreference = localStorage.getItem(DARK_KEY);

        // Initialize icon based on current state (before applying any mode)
        function updateIcon(isDark) {
            if (isDark) {
                darkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
            } else {
                darkIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
            }
        }

        // Only apply system preference on first visit (when no preference is saved)
        if (savedPreference === '1') {
            applyDarkMode(true);
        } else if (savedPreference === null) {
            // First visit: check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                applyDarkMode(true);
            } else {
                updateIcon(false);
            }
        } else {
            // savedPreference is '0' - ensure icon is correct for light mode
            updateIcon(false);
        }

        if (darkBtn) {
            darkBtn.addEventListener('click', () => {
                applyDarkMode(!html.classList.contains('dark'));
            });
        }

        // --- User Menu Dropdown ---
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                userMenuDropdown.classList.add('hidden');
            });

            userMenuDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // --- Global Search AJAX ---
        const searchInput = document.getElementById('global-search-input');
        const searchResults = document.getElementById('global-search-results');

        if (searchInput && searchResults) {
            let searchTimeout;

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                const query = searchInput.value.trim();

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    searchResults.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('buscar.global') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Sin resultados</div>';
                        } else {
                            let html = '';
                            data.forEach(item => {
                                const icon = item.type === 'bien' ? '📦' : (item.type === 'usuario' ? '👤' : '🏢');
                                const url = item.url || '#';
                                html += `<a href="${url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                    <span class="text-lg">${icon}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">${item.title}</p>
                                        <p class="text-xs text-gray-500 truncate">${item.subtitle || ''}</p>
                                    </div>
                                </a>`;
                            });
                            searchResults.innerHTML = html;
                        }
                        searchResults.classList.remove('hidden');
                    })
                    .catch(() => {
                        searchResults.classList.add('hidden');
                    });
                }, 300);
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!document.getElementById('global-search-wrap').contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Close on Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchResults.classList.add('hidden');
                    searchInput.blur();
                }
            });
        }
    })();
    </script>
</body>
</html>
