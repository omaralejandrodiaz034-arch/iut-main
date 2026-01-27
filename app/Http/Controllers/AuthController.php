<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'cedula'   => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        // Normalizar cédula a solo dígitos para evitar fallos con formatos (V-xx.xxx.xxx)
        $cedula = $this->normalizeCedulaDigits($validated['cedula']);

        // 1) Buscar usuario local por cédula y activo
        $usuario = Usuario::where('cedula', $cedula)->where('activo', true)->first();

        // 2) Si no existe localmente, intentar obtenerlo del API (mock JSON) y redirigir a set password
        if (! $usuario) {
            $persona = $this->buscarPersonaEnApiPorCedula($cedula);
            if (! $persona) {
                return back()->with('error', 'No se encontró persona con esa cédula en el sistema externo.')->withInput();
            }

            // Guardar datos básicos en sesión para prellenar el formulario de contraseña
            session([
                'registro_api_usuario' => [
                    'pin'        => (string) ($persona['pin'] ?? ''),
                    'firstnames' => (string) ($persona['firstnames'] ?? ''),
                    'lastnames'  => (string) ($persona['lastnames'] ?? ''),
                    'fullname'   => (string) ($persona['fullname'] ?? ''),
                    'status'     => (string) ($persona['status'] ?? '0'),
                    'pin_str'    => (string) ($persona['pin_str'] ?? ''),
                ],
            ]);

            return redirect()->route('auth.set_password.form')->with('info', 'Por favor, establezca una contraseña para completar su registro.');
        }

        // 3) Usuario existe localmente: exigir password y autenticar
        if (empty($validated['password'])) {
            return back()->with('error', 'Debe ingresar su contraseña para iniciar sesión.')->withInput();
        }

        if ($usuario && Hash::check($validated['password'], $usuario->hash_password)) {
            Auth::login($usuario, $request->boolean('remember'));
            $request->session()->regenerate();

            return $usuario->isAdmin()
                ? redirect()->route('usuarios.index')
                : redirect()->route('bienes.index');
        }

        return back()->with('error', 'Las credenciales no coinciden con nuestros registros')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showSetPasswordForm(Request $request)
    {
        $datos = session('registro_api_usuario');
        if (! $datos) {
            return redirect()->route('login');
        }

        return view('auth.set_password', ['persona' => $datos]);
    }

    public function setPassword(Request $request)
    {
        $datos = session('registro_api_usuario');
        if (! $datos) {
            // Intentar recuperar desde el API si llega cedula (opcional)
            if ($request->filled('cedula')) {
                $rec = $this->buscarPersonaEnApiPorCedula($this->normalizeCedulaDigits((string) $request->input('cedula')));
                if ($rec) {
                    $datos = [
                        'pin'        => (string) ($rec['pin'] ?? ''),
                        'firstnames' => (string) ($rec['firstnames'] ?? ''),
                        'lastnames'  => (string) ($rec['lastnames'] ?? ''),
                        'fullname'   => (string) ($rec['fullname'] ?? ''),
                        'status'     => (string) ($rec['status'] ?? '0'),
                        'pin_str'    => (string) ($rec['pin_str'] ?? ''),
                    ];
                } else {
                    return redirect()->route('login');
                }
            } else {
                return redirect()->route('login');
            }
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Preparar mapeo y creación local
        $firstnames = (string) ($datos['firstnames'] ?? '');
        $lastnames  = (string) ($datos['lastnames'] ?? '');
        $fullname   = trim($datos['fullname'] ?? trim($firstnames.' '.$lastnames));
        $estatus    = (string) ($datos['status'] ?? '0');
        $pin        = (string) ($datos['pin'] ?? '');
        $pinStr     = (string) ($datos['pin_str'] ?? ('V-'.$pin));

        // Correo generado para cumplir UNIQUE si no hay email real del API
        $correo = strtolower(Str::slug($pinStr, '.')).'@externo.local';

        // Asignación de rol según cédula: 31077912 => Administrador; resto => Usuario Normal
        $esAdminCedula = ((string) $pin) === '31077912';
        $rolAdminId = optional(DB::table('roles')->where('nombre', 'Administrador')->first())->id;
        $rolUserId  = optional(DB::table('roles')->where('nombre', 'Usuario Normal')->first())->id;
        $rolId = $esAdminCedula ? ($rolAdminId ?? $rolUserId) : ($rolUserId ?? $rolAdminId);

        if (! $rolId) {
            return back()->with('error', 'No hay roles configurados, contacte al administrador.');
        }

        try {
            $usuario = EloquentModel::withoutEvents(function () use ($pin, $rolId, $firstnames, $fullname, $lastnames, $correo, $validated, $estatus, $esAdminCedula) {
                return Usuario::updateOrCreate(
                    ['cedula' => $pin],
                    [
                        'rol_id'        => $rolId,
                        'nombre'        => $firstnames ?: $fullname,
                        'apellido'      => $lastnames,
                        'correo'        => $correo,
                        'hash_password' => Hash::make($validated['password']),
                        'activo'        => ($estatus === '1'),
                        'is_admin'      => $esAdminCedula,
                    ]
                );
            });
        } catch (\Throwable $e) {
            \Log::error('Error creando usuario desde setPassword', [
                'cedula' => $pin,
                'correo' => $correo,
                'error'  => $e->getMessage(),
            ]);
            // Posible colisión UNIQUE en correo: regenerar correo con sufijo y reintentar una vez
            $correoAlt = strtolower(Str::slug($pinStr, '.')).'.'.uniqid().'@externo.local';
            $usuario = EloquentModel::withoutEvents(function () use ($pin, $rolId, $firstnames, $fullname, $lastnames, $correoAlt, $validated, $estatus, $esAdminCedula) {
                return Usuario::updateOrCreate(
                    ['cedula' => $pin],
                    [
                        'rol_id'        => $rolId,
                        'nombre'        => $firstnames ?: $fullname,
                        'apellido'      => $lastnames,
                        'correo'        => $correoAlt,
                        'hash_password' => Hash::make($validated['password']),
                        'activo'        => ($estatus === '1'),
                        'is_admin'      => $esAdminCedula,
                    ]
                );
            });
        }

        // Limpiar sesión temporal y autenticar
        $request->session()->forget('registro_api_usuario');
        Auth::login($usuario, true);
        $request->session()->regenerate();

        return redirect()->route('bienes.index')->with('success', 'Registro completado. Bienvenido.');
    }

    private function buscarPersonaEnApiPorCedula(string $cedula): ?array
    {
        $digits = $this->normalizeCedulaDigits($cedula);

        // 1) Intentar API externa
        $url = config('services.people_api.url');
        $token = config('services.people_api.token');
        $timeout = (int) config('services.people_api.timeout', 5);
        try {
            if ($url && $token) {
                $resp = Http::timeout($timeout)
                    ->acceptJson()
                    ->get($url, ['pin' => $digits, 'token' => $token]);

                if ($resp->ok()) {
                    $payload = $resp->json();
                    // Estructura esperada: [{ data: [...] }]
                    $list = is_array($payload) ? ($payload[0]['data'] ?? []) : [];
                    $persona = collect($list)->first(function ($item) use ($digits) {
                        return $this->normalizeCedulaDigits((string) ($item['pin'] ?? '')) === $digits;
                    });
                    if ($persona) {
                        return $persona;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Fallo API externa people_api, usando fallback JSON', [
                'error' => $e->getMessage(),
            ]);
        }

        // 2) Fallback al JSON local
        $jsonPath = storage_path('app/respuesta.json');
        if (! file_exists($jsonPath)) {
            return null;
        }
        $data = json_decode(file_get_contents($jsonPath), true);
        $persona = collect($data[0]['data'] ?? [])->first(function ($item) use ($digits) {
            return $this->normalizeCedulaDigits((string) ($item['pin'] ?? '')) === $digits;
        });
        return $persona ?: null;
    }

    private function normalizeCedulaDigits(string $raw): string
    {
        return preg_replace('/\D/', '', $raw) ?: '';
    }
}
