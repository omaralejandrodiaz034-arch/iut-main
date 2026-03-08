<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- Título dinámico con fallback --}}
    <title>@yield('title', 'Oficina de Bienes Nacionales | UPTOS')</title>

    {{-- NO usar CDN de Tailwind, ya tienes Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos globales del landing --}}
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
    </style>
</head>

<body class="bg-white text-slate-900 antialiased font-sans overflow-x-hidden">

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
</body>
</html>
