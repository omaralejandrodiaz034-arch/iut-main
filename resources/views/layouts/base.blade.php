<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Inventario')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        head {
            background-color: #640B21;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.head')

    <main class="max-w-screen-2xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    {{-- Script para filtros automáticos --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filtrosForm');
            if (!form) return;

            const filtros = form.querySelectorAll('input, select');

            filtros.forEach(filtro => {
                filtro.addEventListener('change', () => {
                    form.submit();
                });

                if (filtro.type === 'text') {
                    let timeout;
                    filtro.addEventListener('input', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => form.submit(), 400);
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

