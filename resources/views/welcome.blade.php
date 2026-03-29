<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Inventario de Bienes | UPTOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="description" content="Sistema integral de gestión patrimonial para instituciones educativas venezolanas. Control total de bienes públicos con trazabilidad, auditoría y reportes oficiales.">

    <style>
        /* Animación de entrada suave */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* Rotación de frases */
        .phrase-container { 
            height: 1.5em; 
            overflow: hidden; 
            display: inline-block; 
            vertical-align: bottom;
            line-height: 1.5;
        }
        .phrase-inner { 
            display: flex; 
            flex-direction: column; 
            transition: transform 0.5s ease-in-out; 
        }
        .phrase-inner span {
            height: 1.5em;
            display: block;
            white-space: nowrap;
        }

        /* Efecto flotante para tarjetas */
        .float-card:hover { transform: translateY(-10px) scale(1.02); }

        /* Línea de tiempo animada */
        .step-line { position: relative; }
        .step-line::after {
            content: ''; position: absolute; top: 50%; left: 100%; width: 0; height: 2px;
            background: #640B21; transition: width 0.5s ease; z-index: 0;
        }
        .group:hover .step-line::after { width: 1.5rem; }

        .phrase-container {
            height: 1.2em;
            line-height: 1.2em;
        }
        .phrase-inner span {
            height: 1.2em;
            display: block;
            white-space: nowrap;
        }

        /* Animación de gradiente */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animated-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }
    </style>
</head>

<body class="bg-[#fcfcfc] text-slate-900 antialiased font-sans overflow-x-hidden">

    {{-- Banner Institucional --}}
    <header class="w-full bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        {{-- Imagen banner contenida --}}
        <div class="max-w-7xl mx-auto px-4 py-2">
            <img src="{{ asset('images/baner.jpeg') }}"
                 class="w-full max-w-4xl h-auto object-contain mx-auto"
                 alt="Banner Institucional">
        </div>
        @include('layouts.head')
    </header>

    {{-- Hero Section con Rotación de Frases --}}
    <section class="relative bg-slate-900 py-32 md:py-48 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#4a0819] via-[#640B21] to-[#800f2f] opacity-95"></div>

        {{-- Partículas decorativas animadas --}}
        <div class="absolute top-10 left-10 w-2 h-2 bg-white/20 rounded-full animate-ping"></div>
        <div class="absolute bottom-20 right-20 w-3 h-3 bg-white/10 rounded-full animate-bounce"></div>
        <div class="absolute top-1/3 right-1/4 w-2 h-2 bg-white/15 rounded-full animate-pulse"></div>

        <div class="max-w-5xl mx-auto px-6 text-center relative z-10 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-red-200 text-xs font-bold uppercase tracking-widest mb-8 backdrop-blur-xl">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                Sistema Oficial de Gestión Patrimonial
            </div>

            <h1 class="text-5xl md:text-8xl font-black text-white mb-8 tracking-tighter leading-none">
                Gestión de <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-200 via-white to-red-100">
                    Bienes Institucionales
                </span>
            </h1>

            <p class="max-w-2xl mx-auto text-xl text-red-100/70 leading-relaxed font-light mb-12">
                Plataforma integral para el registro, control y trazabilidad de bienes públicos en instituciones educativas venezolanas. Cumple con normativas de control patrimonial del Estado.
            </p>

            <div class="flex flex-wrap justify-center gap-4 mb-8">
                <a href="{{ route('login') }}" class="px-8 py-4 bg-[#640B21] hover:bg-[#4a0819] text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg shadow-red-900/30">
                    Iniciar Sesión
                </a>
                <span class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full transition-all border border-white/20 cursor-default">
                    ¿Necesitas ayuda?
                </span>
            </div>

            <div class="flex justify-center items-center gap-3">
                <span class="h-1 w-12 bg-red-500 rounded-full"></span>
                <span class="h-1 w-3 bg-red-500/50 rounded-full"></span>
                <span class="h-1 w-3 bg-red-500/20 rounded-full"></span>
            </div>
        </div>
    </section>

    {{-- Jerarquía del Sistema --}}
    <main class="max-w-7xl mx-auto -mt-20 pb-24 px-6 relative z-20 bg-white rounded-t-3xl">
        <div class="text-center mb-12 reveal">
            <p class="text-[#640B21] font-bold uppercase tracking-[0.4em] text-sm mb-2">Estructura Organizacional</p>
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter">Jerarquía del Sistema</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $jerarquia = [
                    ['Organismo', 'Institución principal', 'UPTOS "Clodosbaldo Russián"', 'M3 12h18M12 2v10'],
                    ['Unidad', 'Departamento', 'Informática, Administración, Ingeniería...', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['Dependencia', 'Salón o Laboratorio', 'Lab. Computación 1, Lab. Física, Salon A-101...', 'M3 7h18M3 7a2 2 0 110 4h14a2 2 0 110 4M3 7v10m0 0h18M3 17v2m0-2v-2'],
                    ['Bien', 'Activo registrado', 'Computadoras, Muebles, Equipos...', 'M9 3h6v4H9V3zm6 8h6v10H9v-6h6v6zm-6 2h12v2H9v-2z']
                ];
            @endphp

            @foreach($jerarquia as $item)
            <div class="reveal float-card group bg-white rounded-[2rem] p-8 shadow-2xl shadow-slate-200/50 border border-slate-100 transition-all duration-500 cursor-default text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-red-50 to-white rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:from-[#640B21] group-hover:to-[#4a0819] group-hover:rotate-12 transition-all duration-700 shadow-xl shadow-red-100/20 group-hover:shadow-red-900/40">
                    <svg class="w-8 h-8 text-[#640B21] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item[3] }}" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">{{ $item[0] }}</h3>
                <p class="text-red-600 text-xs font-bold uppercase tracking-wider mb-3">{{ $item[1] }}</p>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $item[2] }}</p>
            </div>
            @endforeach
        </div>
    </main>

    {{-- Funcionalidades Principales --}}
    <section class="py-24 px-6 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 reveal">
                <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-4 tracking-tighter">Funcionalidades</h2>
                <p class="text-[#640B21] font-bold uppercase tracking-[0.4em] text-sm">Módulos del Sistema</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
                {{-- Gestión de Bienes --}}
                <div class="reveal p-8 bg-white rounded-3xl shadow-sm border border-slate-200 hover:border-[#640B21]/30 transition-colors group">
                    <div class="h-1 w-12 bg-[#640B21] mb-6 group-hover:w-full transition-all duration-500"></div>
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-[#640B21]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-4">Gestión de Bienes</h3>
                    <p class="text-slate-500 text-sm mb-6">Registro completo de activos con fotografías, códigos únicos y características específicas por tipo.</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl group-hover:bg-red-50 transition-colors">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            <span class="text-xs font-bold text-slate-700">Hasta 5 fotos por bien</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl group-hover:bg-red-50 transition-colors">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            <span class="text-xs font-bold text-slate-700">Códigos únicos automáticos</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl group-hover:bg-red-50 transition-colors">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            <span class="text-xs font-bold text-slate-700">Tipos: Electrónico, Mobiliario, Vehículo</span>
                        </div>
                    </div>
                </div>

                {{-- Central - Movimientos --}}
                <div class="reveal p-8 bg-[#640B21] rounded-3xl shadow-2xl shadow-red-900/20 text-white transform lg:scale-105 z-10">
                    <div class="animate-pulse absolute top-4 right-4 w-2 h-2 bg-red-400 rounded-full"></div>
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black mb-6">Trazabilidad Total</h3>
                    <p class="text-red-100/70 text-sm mb-8">Control de movimientos y cambios de responsabilidad con historial inmutable.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-2xl font-bold">100%</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Trazabilidad</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-2xl font-bold">PDF</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Actas Oficiales</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-2xl font-bold">6</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Estados</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-2xl font-bold">∞</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Historial</div>
                        </div>
                    </div>
                </div>

                {{-- Reportes y Auditoría --}}
                <div class="reveal p-8 bg-white rounded-3xl shadow-sm border border-slate-200 hover:border-[#640B21]/30 transition-colors group">
                    <div class="h-1 w-12 bg-slate-300 mb-6 group-hover:bg-[#640B21] group-hover:w-full transition-all duration-500"></div>
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-4">Reportes y Auditoría</h3>
                    <p class="text-slate-500 text-sm mb-6">Generación de reportes oficiales en PDF y auditoría automática de todas las operaciones.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-red-50 rounded-full text-[10px] font-black text-red-600 uppercase">Inventario PDF</span>
                        <span class="px-3 py-1 bg-red-50 rounded-full text-[10px] font-black text-red-600 uppercase">Por Dependencia</span>
                        <span class="px-3 py-1 bg-red-50 rounded-full text-[10px] font-black text-red-600 uppercase">Por Responsable</span>
                        <span class="px-3 py-1 bg-red-50 rounded-full text-[10px] font-black text-red-600 uppercase">Auditoría</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Estados y Tipos de Bienes --}}
    <section class="py-24 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                {{-- Estados --}}
                <div class="reveal">
                    <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                        <span class="w-3 h-3 bg-[#640B21] rounded-full"></span>
                        Estados de Bienes
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $estados = [
                                ['Activo', 'bg-green-500', 'En uso institucional'],
                                ['Dañado', 'bg-red-500', 'Requiere reparación'],
                                ['En Mantenimiento', 'bg-yellow-500', 'En taller técnico'],
                                ['En Camino', 'bg-blue-500', 'En traslado'],
                                ['Extraviado', 'bg-gray-500', 'Sin localización'],
                                ['Desincorporado', 'bg-slate-700', 'Dado de baja oficial']
                            ];
                        @endphp
                        @foreach($estados as $estado)
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                            <span class="w-4 h-4 rounded-full {{ $estado[1] }}"></span>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">{{ $estado[0] }}</p>
                                <p class="text-xs text-slate-500">{{ $estado[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tipos de Bienes --}}
                <div class="reveal">
                    <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                        <span class="w-3 h-3 bg-[#640B21] rounded-full"></span>
                        Tipos de Bienes
                    </h3>
                    <div class="space-y-4">
                        @php
                            $tipos = [
                                ['Electrónico', 'Computadoras, impresoras, equipos de red, móviles'],
                                ['Mobiliario', 'Escritorios, sillas, estantes, archivos'],
                                ['Vehículo', 'Automóviles, motos, camionetas institucionales'],
                                ['Inmueble', 'Edificios, tierras, construcciones'],
                                ['Otros', 'Herramientas, equipos agrícolas, otros activos']
                            ];
                        @endphp
                        @foreach($tipos as $tipo)
                        <div class="flex items-start gap-4 p-5 bg-slate-50 rounded-2xl hover:bg-red-50 transition-colors">
                            <div class="w-10 h-10 bg-[#640B21]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-[#640B21]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $tipo[0] }}</p>
                                <p class="text-sm text-slate-500">{{ $tipo[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Información Técnica --}}
    <section class="py-20 px-6 bg-slate-900 text-white">
        <div class="max-w-5xl mx-auto text-center">
            <h3 class="text-2xl font-black mb-12">Información Técnica</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="p-6 bg-white/5 rounded-2xl border border-white/10">
                    <div class="text-3xl font-black text-[#640B21] mb-2">Laravel</div>
                    <div class="text-sm text-slate-400">Framework PHP</div>
                </div>
                <div class="p-6 bg-white/5 rounded-2xl border border-white/10">
                    <div class="text-3xl font-black text-[#640B21] mb-2">MySQL</div>
                    <div class="text-sm text-slate-400">Base de Datos</div>
                </div>
                <div class="p-6 bg-white/5 rounded-2xl border border-white/10">
                    <div class="text-3xl font-black text-[#640B21] mb-2">Blade</div>
                    <div class="text-sm text-slate-400">Motor de Plantillas</div>
                </div>
                <div class="p-6 bg-white/5 rounded-2xl border border-white/10">
                    <div class="text-3xl font-black text-[#640B21] mb-2">Tailwind</div>
                    <div class="text-sm text-slate-400">Estilos CSS</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cómo Usar el Sistema --}}
    <section class="py-24 px-6 bg-gradient-to-b from-white to-red-50/30">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 reveal">
                <p class="text-[#640B21] font-bold uppercase tracking-[0.4em] text-sm mb-2">Guía de Uso</p>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter">Cómo Usar el Sistema</h2>
                <p class="text-slate-500 mt-4 max-w-2xl mx-auto">Aprende a utilizar el sistema de gestión de inventario en pocos pasos</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                {{-- Paso 1 --}}
                <div class="reveal bg-white rounded-3xl p-6 shadow-lg border border-slate-100 hover:border-[#640B21]/30 transition-all group">
                    <div class="w-12 h-12 bg-[#640B21] rounded-xl flex items-center justify-center mb-4 text-white font-black text-xl">1</div>
                    <h3 class="text-lg font-black text-slate-800 mb-2">Inicia Sesión</h3>
                    <p class="text-slate-500 text-sm">Usa tus credenciales institucionales para acceder al sistema. Solicita tu contraseña al administrador si no la tienes.</p>
                </div>

                {{-- Paso 2 --}}
                <div class="reveal bg-white rounded-3xl p-6 shadow-lg border border-slate-100 hover:border-[#640B21]/30 transition-all group" style="animation-delay: 100ms">
                    <div class="w-12 h-12 bg-[#640B21] rounded-xl flex items-center justify-center mb-4 text-white font-black text-xl">2</div>
                    <h3 class="text-lg font-black text-slate-800 mb-2">Registra Bienes</h3>
                    <p class="text-slate-500 text-sm">Agrega nuevos bienes con fotografías, código único y características específicas según el tipo (electrónico, mobiliario, vehículo).</p>
                </div>

                {{-- Paso 3 --}}
                <div class="reveal bg-white rounded-3xl p-6 shadow-lg border border-slate-100 hover:border-[#640B21]/30 transition-all group" style="animation-delay: 200ms">
                    <div class="w-12 h-12 bg-[#640B21] rounded-xl flex items-center justify-center mb-4 text-white font-black text-xl">3</div>
                    <h3 class="text-lg font-black text-slate-800 mb-2">Controla Movimientos</h3>
                    <p class="text-slate-500 text-sm">Registra traslados, cambios de estado y genera actas oficiales en PDF con trazabilidad completa.</p>
                </div>

                {{-- Paso 4 --}}
                <div class="reveal bg-white rounded-3xl p-6 shadow-lg border border-slate-100 hover:border-[#640B21]/30 transition-all group" style="animation-delay: 300ms">
                    <div class="w-12 h-12 bg-[#640B21] rounded-xl flex items-center justify-center mb-4 text-white font-black text-xl">4</div>
                    <h3 class="text-lg font-black text-slate-800 mb-2">Genera Reportes</h3>
                    <p class="text-slate-500 text-sm">Exporta informes por dependencia, responsable o período. Consulta el historial de auditoría en cualquier momento.</p>
                </div>
            </div>

            {{-- Preguntas Frecuentes --}}
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-[#640B21] to-[#4a0819] px-8 py-6">
                    <h3 class="text-2xl font-black text-white flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Preguntas Frecuentes
                    </h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Cómo registro un nuevo bien?
                        </h4>
                        <p class="text-slate-500 text-sm mb-4">Navega a Bienes > Nuevo Bien. Completa el formulario con los datos requeridos, selecciona el tipo y estado, y añade fotografías.</p>

                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Cómo transfiero un bien a otra dependencia?
                        </h4>
                        <p class="text-slate-500 text-sm mb-4">Selecciona el bien > Transferir > Nueva Dependencia. El sistema registrará automáticamente el movimiento.</p>

                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Puedo recuperar un bien desincorporado?
                        </h4>
                        <p class="text-slate-500 text-sm">Sí, los bienes desincorporados se mantienen 30 días en Eliminados. Puedes restaurarlos desde Movimientos > Eliminados.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Cómo genero un reporte en PDF?
                        </h4>
                        <p class="text-slate-500 text-sm mb-4">Ve a Reportes > Selecciona el tipo > Elige formato PDF > Descarga el documento oficial.</p>

                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Qué tipos de bienes puedo registrar?
                        </h4>
                        <p class="text-slate-500 text-sm mb-4">Electrónico (computadoras, impresoras), Mobiliario (escritorios, sillas), Vehículo (automóviles) y Otros.</p>

                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            ¿Cómo contacto al soporte técnico?
                        </h4>
                        <p class="text-slate-500 text-sm">Contacta al administrador del sistema o consulta el manual de usuario completo en la sección de documentación.</p>
                    </div>
                </div>
            </div>

            {{-- Botón de Acceso --}}
            <div class="text-center mt-12">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-[#640B21] hover:bg-[#4a0819] text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg shadow-red-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Ir al Sistema
                </a>
                <p class="text-slate-400 text-sm mt-4">¿Necesitas el manual completo? <a href="{{ route('manual.usuario') }}" class="text-[#640B21] font-bold hover:underline" target="_blank">Descargar Manual PDF</a></p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-500 py-12 px-6 border-t border-white/5">
        <div class="max-w-6xl mx-auto flex flex-col items-center text-center">
            <div class="w-12 h-1 bg-[#640B21] mb-8"></div>
            <p class="text-xs font-black uppercase tracking-[0.5em] mb-4 text-white">UPTOS Clodosbaldo Russián</p>
            <p class="text-sm max-w-sm opacity-60 mb-4">Universidad Politécnica Territorial de Oriente "Clodosbaldo Russián"</p>
            <p class="text-xs opacity-40">Sistema de Gestión de Inventario de Bienes - Versión 1.0</p>
        </div>
    </footer>

    <script>
        // Reveal on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal');
            
            function checkReveal() {
                const windowHeight = window.innerHeight;
                reveals.forEach(function(el) {
                    const elementTop = el.getBoundingClientRect().top;
                    if (elementTop < windowHeight - 150) {
                        el.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', checkReveal);
            checkReveal(); // Check on load
        });
    </script>
</body>
</html>
