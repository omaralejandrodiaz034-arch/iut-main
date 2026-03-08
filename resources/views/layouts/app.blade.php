<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventario</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    @include('layouts.head')
    <main class="max-w-7xl mx-auto px-4 py-8">
        @stack('breadcrumbs')
        {{ $slot ?? '' }}
    </main>
    @stack('scripts')

</body>
</html>
