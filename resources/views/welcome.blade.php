<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina de Bienes Nacionales | UPTOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Animación de entrada suave */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* Rotación de frases */
        .phrase-container { height: 1.2em; overflow: hidden; display: inline-block; vertical-align: bottom; }
        .phrase-inner { display: flex; flex-direction: column; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Efecto flotante para áreas de relación */
        .float-card:hover { transform: translateY(-10px) scale(1.02); }

        /* Línea de tiempo animada */
        .step-line { position: relative; }
        .step-line::after {
            content: ''; position: absolute; top: 50%; left: 100%; width: 0; height: 2px;
            background: #640B21; transition: width 0.5s ease; z-index: 0;
        }
        .group:hover .step-line::after { width: 1.5rem; }

        .phrase-container {
            /* Ajusta esta altura según el tamaño de tu fuente */
            height: 1.2em;
            line-height: 1.2em;
        }
        .phrase-inner span {
            height: 1.2em;
            display: block;
            white-space: nowrap;
        }

    </style>
</head>

<body class="bg-[#fcfcfc] text-slate-900 antialiased font-sans overflow-x-hidden">

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
        @include('layouts.head')
    </header>

    {{-- Hero Section con Rotación de Frases --}}
    <section class="relative bg-slate-900 py-32 md:py-48 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#4a0819] via-[#640B21] to-[#800f2f] opacity-95"></div>

        {{-- Partículas decorativas animadas --}}
        <div class="absolute top-10 left-10 w-2 h-2 bg-white/20 rounded-full animate-ping"></div>
        <div class="absolute bottom-20 right-20 w-3 h-3 bg-white/10 rounded-full animate-bounce"></div>

        <div class="max-w-5xl mx-auto px-6 text-center relative z-10 reveal">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-red-200 text-xs font-bold uppercase tracking-widest mb-8 backdrop-blur-xl">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                Ecosistema Digital Patrimonial
            </div>

            <h1 class="text-5xl md:text-8xl font-black text-white mb-8 tracking-tighter leading-none">
                Gestión de <br>
                <span class="phrase-container block md:inline-block relative overflow-hidden text-transparent bg-clip-text bg-gradient-to-r from-red-200 via-white to-red-100">
                    <span class="phrase-inner flex flex-col transition-transform duration-500 ease-in-out" id="rotatingPhrases">
                        <span class="py-1">Bienes Nacionales</span>
                        <span class="py-1">Activos Públicos</span>
                        <span class="py-1">Recursos UPTOS</span>
                        <span class="py-1">Bienes Nacionales</span> {{-- Repetida para loop suave --}}
                    </span>
                </span>
            </h1>

            <p class="max-w-2xl mx-auto text-xl text-red-100/70 leading-relaxed font-light mb-12">
                Arquitectura inteligente para la custodia y administración del patrimonio institucional con trazabilidad absoluta.
            </p>

            <div class="flex justify-center items-center gap-3">
                <span class="h-1 w-12 bg-red-500 rounded-full"></span>
                <span class="h-1 w-3 bg-red-500/50 rounded-full"></span>
                <span class="h-1 w-3 bg-red-500/20 rounded-full"></span>
            </div>
        </div>
    </section>

    {{-- Cards con Efecto Float --}}
    <main class="max-w-7xl mx-auto -mt-20 pb-24 px-6 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @php
                $cards = [
                    ['Misión', 'Administrar con enfoque de transparencia y rigor legal.', 'M13 10V3L4 14h7v7l9-11h-7z'],
                    ['Visión', 'Liderazgo en modernización tecnológica patrimonial.', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['Funciones', 'Registro, transferencias y desincorporación técnica.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
                    ['Control', 'Inspección física y auditoría digital constante.', 'M9 12l2 2 4-4m5.618-4.016A11.955']
                ];
            @endphp

            @foreach($cards as $card)
            <div class="reveal float-card group bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-slate-200/50 border border-slate-100 transition-all duration-500 cursor-default">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-red-50 to-white rounded-3xl flex items-center justify-center group-hover:from-[#640B21] group-hover:to-[#4a0819] group-hover:rotate-12 transition-all duration-700 shadow-xl shadow-red-100/20 group-hover:shadow-red-900/40">
                        <svg class="w-10 h-10 text-[#640B21] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card[2] }}" />
                        </svg>
                    </div>
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-black text-slate-800 mb-4">{{ $card[0] }}</h2>
                        <p class="text-slate-500 text-lg leading-relaxed">{{ $card[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    {{-- Áreas de Relación (Componentes) con Micro-interacciones --}}
    <section class="py-24 px-6 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 reveal">
                <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-4 tracking-tighter">Áreas de Relación</h2>
                <p class="text-[#640B21] font-bold uppercase tracking-[0.4em] text-sm">Componentes del Sistema</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
                {{-- Capa Usuario --}}
                <div class="reveal p-8 bg-white rounded-3xl shadow-sm border border-slate-200 hover:border-[#640B21]/30 transition-colors group">
                    <div class="h-1 w-12 bg-[#640B21] mb-6 group-hover:w-full transition-all duration-500"></div>
                    <h3 class="text-2xl font-black text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#640B21]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Front-End Relacional
                    </h3>
                    <p class="text-slate-500 text-sm mb-6">Punto de contacto entre el operador y el activo digital.</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl group-hover:bg-red-50 transition-colors">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            <span class="text-xs font-bold text-slate-700">Módulo de Usuarios</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl group-hover:bg-red-50 transition-colors">
                            <span class="w-2 h-2 bg-[#640B21] rounded-full"></span>
                            <span class="text-xs font-bold text-slate-700">Panel de Auditoría</span>
                        </div>
                    </div>
                </div>

                {{-- Central --}}
                <div class="reveal p-8 bg-[#640B21] rounded-3xl shadow-2xl shadow-red-900/20 text-white transform lg:scale-105 z-10">
                    <div class="animate-pulse absolute top-4 right-4 w-2 h-2 bg-red-400 rounded-full"></div>
                    <h3 class="text-2xl font-black mb-6">Business Intelligence</h3>
                    <p class="text-red-100/70 text-sm mb-8">El núcleo lógico donde se procesan las reglas de negocio y depreciación.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-xl font-bold">100%</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Trazabilidad</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10 text-center">
                            <div class="text-xl font-bold">Real-time</div>
                            <div class="text-[10px] uppercase opacity-60 font-bold">Sincronía</div>
                        </div>
                    </div>
                </div>

                {{-- Capa Datos --}}
                <div class="reveal p-8 bg-white rounded-3xl shadow-sm border border-slate-200 hover:border-[#640B21]/30 transition-colors group">
                    <div class="h-1 w-12 bg-slate-300 mb-6 group-hover:bg-[#640B21] group-hover:w-full transition-all duration-500"></div>
                    <h3 class="text-2xl font-black text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#640B21]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 7v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V7M4 7c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2" /></svg>
                        Storage Engine
                    </div>
                    <p class="text-slate-500 text-sm mb-6">Persistencia de datos con alta disponibilidad institucional.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-600 uppercase">PostgreSQL</span>
                        <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-600 uppercase">Cloud S3</span>
                        <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-600 uppercase">Backups</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-500 py-12 px-6 border-t border-white/5">
        <div class="max-w-6xl mx-auto flex flex-col items-center text-center">
            <div class="w-12 h-1 bg-[#640B21] mb-8"></div>
            <p class="text-xs font-black uppercase tracking-[0.5em] mb-4 text-white">UPTOS Clodosbaldo Russián</p>
            <p class="text-sm max-w-sm opacity-60">Optimizando la transparencia administrativa mediante tecnología de vanguardia.</p>
        </div>
    </footer>

    <script>
        // Lógica de Rotación de Frases
        let currentPhrase = 0;
        const phraseInner = document.getElementById('rotatingPhrases');
        const totalPhrases = 3; // Sin contar la repetida al final para el loop

        setInterval(() => {
            currentPhrase++;
            phraseInner.style.transform = `translateY(-${currentPhrase * 25}%)`;

            if (currentPhrase >= totalPhrases) {
                setTimeout(() => {
                    phraseInner.style.transition = 'none';
                    currentPhrase = 0;
                    phraseInner.style.transform = `translateY(0)`;
                    setTimeout(() => {
                        phraseInner.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    }, 50);
                }, 500);
            }
        }, 3000);

        // Lógica de Reveal al hacer Scroll
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        // Ejecutar al cargar para elementos visibles
        reveal();
        document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('rotatingPhrases');
        const phrases = container.getElementsByTagName('span');
        const totalPhrases = phrases.length - 1; // Excluimos el duplicado final
        let currentIndex = 0;

        function rotate() {
            currentIndex++;

            // Obtenemos la altura real de una frase en ese momento (responsive)
            const stepHeight = phrases[0].offsetHeight;

            container.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
            container.style.transform = `translateY(-${currentIndex * stepHeight}px)`;

            // Cuando llega al duplicado (final)
            if (currentIndex === totalPhrases) {
                setTimeout(() => {
                    // Quitamos la animación para volver al inicio instantáneamente
                    container.style.transition = "none";
                    currentIndex = 0;
                    container.style.transform = `translateY(0)`;
                }, 500); // Espera a que termine la animación de 0.5s
            }
        }

        // Rotar cada 3 segundos
        setInterval(rotate, 3000);
    });
    </script>
</body>
</html>
