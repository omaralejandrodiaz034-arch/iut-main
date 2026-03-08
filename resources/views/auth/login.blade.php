<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Sistema de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/imask"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #fdfbfb 0%, #ebedee 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .input-elegant {
            transition: all 0.2s ease-in-out;
            border: 1.5px solid #e5e7eb;
        }
        .input-elegant:focus {
            border-color: #800020;
            box-shadow: 0 0 0 4px rgba(128, 0, 32, 0.1);
        }
        .btn-vinotinto {
            background: linear-gradient(135deg, #800020 0%, #5a0016 100%);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-vinotinto:hover {
            box-shadow: 0 10px 20px -5px rgba(128, 0, 32, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-red-50/50 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-gray-200/50 blur-[120px]"></div>
    </div>

    <div class="w-full max-w-[440px]">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-sm border border-gray-100 mb-4">
                <span class="text-3xl text-vinotinto italic font-black">B</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Bienvenido de nuevo</h1>
            <p class="text-gray-500 text-sm mt-1">Ingresa tus credenciales para continuar</p>
        </div>

        <div class="glass-card rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-8 md:p-10">
            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="cedula" class="text-[13px] font-bold text-gray-700 ml-1">Cédula de Identidad</label>
                    <div class="relative">
                        <input type="text" name="cedula" id="cedula"
                            class="input-elegant w-full px-4 py-3.5 rounded-xl bg-white/50 text-gray-900 placeholder:text-gray-400 outline-none"
                            placeholder="V-00.000.000" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label for="password" class="text-[13px] font-bold text-gray-700">Contraseña</label>

                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="input-elegant w-full px-4 py-3.5 rounded-xl bg-white/50 text-gray-900 placeholder:text-gray-400 outline-none"
                            placeholder="••••••••">
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center px-1">
                    <label class="relative flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-900"></div>
                        <span class="ml-3 text-sm font-medium text-gray-600">Recordarme</span>
                    </label>
                </div>

                <button type="submit" id="btnSubmit" class="btn-vinotinto w-full py-4 rounded-xl text-white font-bold text-sm tracking-wide uppercase flex items-center justify-center gap-2">
                    <span id="btnText">Iniciar Sesión</span>
                    <svg id="btnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <div id="btnSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </button>
            </form>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Regresar al portal principal
            </a>
            <p class="mt-8 text-[11px] text-gray-400 font-bold tracking-[2px] uppercase">Sistema de Control de Bienes Públicos</p>
        </div>
    </div>

    <script>
        // Máscara Cédula
        IMask(document.getElementById('cedula'), {
            mask: [
                { mask: 'V-00.000.000' },
                { mask: 'E-00.000.000' }
            ]
        });

        // Toggle Password
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
        });

        // Form Loading
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            // Bloquear sólo si se ingresó contraseña con menos de 8 caracteres
            if (password.length > 0 && password.length < 8) {
                e.preventDefault();
                return false;
            }
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            document.getElementById('btnText').innerText = 'Validando...';
            document.getElementById('btnIcon').classList.add('hidden');
            document.getElementById('btnSpinner').classList.remove('hidden');
        });
    </script>
</body>
</html>
