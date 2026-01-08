<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina de Bienes Nacionales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar opcional --}}
    @include('layouts.head')

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-[#640B21] to-[#7a1a33] text-gray-100 py-20 px-6">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-6">Oficina de Bienes Nacionales</h1>
            <p class="max-w-3xl mx-auto text-lg leading-relaxed">
                La Oficina de Bienes Nacionales es una unidad esencial en la estructura de la universidad, ya que
                actúa como el custodio y administrador principal de todo el patrimonio institucional. Su rol es
                asegurar el control completo y eficiente de todos los recursos muebles e inmuebles, apoyando la
                planificación administrativa y salvaguardando el uso exclusivo de estos bienes para los fines de
                la institución.
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto py-16 px-6">
        <!-- Cards Grid usando Flexbox -->
        <div class="flex flex-wrap gap-8 justify-center">

            <!-- Misión -->
            <div class="flex-1 min-w-80 max-w-md bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-8">
                <h2 class="text-2xl font-bold text-blue-700 mb-4">Misión</h2>
                <p class="text-gray-600 leading-relaxed">
                    Nuestra misión es administrar y gestionar los bienes muebles e inmuebles propiedad de la universidad
                    con un enfoque de eficiencia, transparencia y responsabilidad. Esto implica un control integral que
                    abarca desde el registro, la asignación y el uso hasta la desincorporación y disposición final de los
                    activos, todo en estricto cumplimiento del marco legal y normativo vigente.
                </p>
            </div>

            <!-- Visión -->
            <div class="flex-1 min-w-80 max-w-md bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-8">
                <h2 class="text-2xl font-bold text-blue-700 mb-4">Visión</h2>
                <p class="text-gray-600 leading-relaxed">
                    Aspiramos a ser la unidad líder y centralizada en la gestión patrimonial de la universidad,
                    reconocida por la excelencia y la modernización de sus procesos. Visualizamos la implementación
                    de sistemas de información automatizados que nos permitan mantener un registro digital confiable
                    y alcanzar la trazabilidad total de cada activo.
                </p>
            </div>

            <!-- Funciones Clave -->
            <div class="flex-1 min-w-80 max-w-md bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-8">
                <h2 class="text-2xl font-bold text-blue-700 mb-4">Funciones Clave</h2>
                <p class="text-gray-600 leading-relaxed">
                    La Oficina de Bienes Nacionales ejecuta funciones esenciales como el mantenimiento y actualización
                    constante del inventario institucional, incluyendo el registro de las altas (adquisiciones),
                    transferencias y bajas (desincorporaciones) de los bienes.
                </p>
            </div>

            <!-- Gestión y Control -->
            <div class="flex-1 min-w-80 max-w-md bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-8">
                <h2 class="text-2xl font-bold text-blue-700 mb-4">Gestión y Control</h2>
                <p class="text-gray-600 leading-relaxed">
                    Realiza inspecciones y recuentos físicos periódicos para verificar la existencia, ubicación y estado
                    de conservación de los activos. Además, genera reportes y estadísticas para auditorías y coordina
                    políticas de mantenimiento preventivo y correctivo que aseguren la durabilidad y buen uso de los bienes.
                </p>
            </div>

        </div>
    </main>

</body>
</html>





