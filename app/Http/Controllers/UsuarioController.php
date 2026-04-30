<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'cedula' => ['nullable', 'string', 'max:20'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'string', 'max:255'],
            'rol_id' => ['nullable', 'integer', 'exists:roles,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $query = Usuario::with('rol');

        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%")
                    ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        if (!empty($validated['cedula'])) {
            $query->where('cedula', 'like', '%'.$validated['cedula'].'%');
        }

        if (!empty($validated['correo'])) {
            $query->where('correo', 'like', '%'.$validated['correo'].'%');
        }

        if (!empty($validated['rol_id'])) {
            $query->where('rol_id', $validated['rol_id']);
        }

        if (isset($validated['activo'])) {
            $query->where('activo', $validated['activo']);
        }

        $usuarios = $query->orderBy('nombre')->paginate(10)->appends($request->query());
        $roles = \App\Models\Rol::orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'validated'));
    }

    public function create()
    {
        $roles = \App\Models\Rol::where('nombre', '!=', 'Gerente de Bienes')->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        if ($request->filled('cedula')) {
            $request->merge(['cedula' => Usuario::normalizeCedula($request->input('cedula'))]);
        }

        $cedulaDigits = preg_replace('/\D/', '', $request->input('cedula'));
        $personaApi = $this->buscarPersonaEnApiPorCedula($cedulaDigits);

        if (!$personaApi) {
            return back()
                ->withInput()
                ->with('error', 'La cédula ingresada no existe en los registros autorizados. Solo se pueden crear usuarios que estén registrados en la base de datos institucional.');
        }

        $existingUser = Usuario::where('cedula', $request->input('cedula'))->first();
        if ($existingUser) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un usuario registrado con esta cédula en el sistema.');
        }

        $validated = $request->validate([
            'rol_id' => ['required', 'exists:roles,id'],
            'cedula' => ['required', 'string', 'unique:usuarios,cedula', 'regex:/^V-\d{2}\.\d{3}\.\d{3}$/'],
            'nombre' => ['required', 'string', 'max:150'],
            'apellido' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'unique:usuarios,correo'],
            'hash_password' => ['required', 'string', 'min:8'],
            'activo' => ['boolean'],
        ], [
            'cedula.regex' => 'El formato de cédula debe ser V-00.000.000',
            'cedula.unique' => 'Ya existe un usuario con esta cédula',
            'correo.unique' => 'Ya existe un usuario con este correo',
        ]);

        $rolAdmin = Rol::where('nombre', 'Administrador')->first();
        $validated['is_admin'] = ($rolAdmin && (int) $validated['rol_id'] === (int) $rolAdmin->id);
        $validated['hash_password'] = Hash::make($validated['hash_password']);

        $usuario = Usuario::create($validated);

        return $request->expectsJson()
            ? response()->json(['message' => 'Creado', 'usuario' => $usuario], 201)
            : redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente.');
    }

    private function buscarPersonaEnApiPorCedula(string $cedula): ?array
    {
        $url = config('services.people_api.url');
        $token = config('services.people_api.token');
        $timeout = (int) config('services.people_api.timeout', 5);

        try {
            if ($url && $token) {
                $resp = Http::timeout($timeout)
                    ->acceptJson()
                    ->get($url, ['pin' => $cedula, 'token' => $token]);

                if ($resp->ok()) {
                    $payload = $resp->json();
                    $list = is_array($payload) ? ($payload[0]['data'] ?? []) : [];
                    $persona = collect($list)->first(function ($item) use ($cedula) {
                        return preg_replace('/\D/', '', ($item['pin'] ?? '')) === $cedula;
                    });
                    if ($persona) {
                        return $persona;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Fallo API externa en usuario.store', ['error' => $e->getMessage()]);
        }

        $jsonPath = storage_path('app/respuesta.json');
        if (!file_exists($jsonPath)) {
            return null;
        }
        $data = json_decode(file_get_contents($jsonPath), true);
        $persona = collect($data[0]['data'] ?? [])->first(function ($item) use ($cedula) {
            return preg_replace('/\D/', '', ($item['pin'] ?? '')) === $cedula;
        });

        return $persona ?: null;
    }

    public function edit(Usuario $usuario)
    {
        $roles = \App\Models\Rol::where('nombre', '!=', 'Gerente de Bienes')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function show(Usuario $usuario)
    {
        $usuario->load(['rol', 'reportes', 'movimientos']);

        if (request()->expectsJson()) {
            return response()->json($usuario);
        }

        return view('usuarios.show', compact('usuario'));
    }

    public function exportPdf(Usuario $usuario)
    {
        $usuario->load(['rol', 'reportes', 'movimientos']);

        $pdf = Pdf::loadView('usuarios.pdf', [
            'usuario' => $usuario,
        ])->setPaper('letter');

        $fileName = sprintf(
            'usuario_%s_%s.pdf',
            Str::slug($usuario->cedula ?? $usuario->id, '_'),
            Str::slug($usuario->nombre_completo ?? $usuario->nombre ?? 'detalle', '_')
        );

        return $pdf->download($fileName);
    }

    public function update(Request $request, Usuario $usuario)
    {
        if (!auth()->user()->canDeleteData()) {
            abort(403);
        }

        if ($request->filled('cedula')) {
            $request->merge(['cedula' => Usuario::normalizeCedula($request->input('cedula'))]);
        }

        $validated = $request->validate([
            'rol_id' => ['sometimes', 'exists:roles,id'],
            'cedula' => ['sometimes', Rule::unique('usuarios')->ignore($usuario->id)],
            'nombre' => ['sometimes', 'string'],
            'apellido' => ['sometimes', 'string'],
            'correo' => ['sometimes', 'email', Rule::unique('usuarios')->ignore($usuario->id)],
            'hash_password' => ['nullable', 'string', 'min:8'],
            'activo' => ['boolean'],
        ]);

        if ($usuario->isAdmin()) {
            if (array_key_exists('rol_id', $validated) || array_key_exists('activo', $validated)) {
                abort(403, 'No se puede modificar el rol o el estado de un administrador.');
            }
        }

        $rolAdmin = Rol::where('nombre', 'Administrador')->first();
        if (isset($validated['rol_id']) && $rolAdmin) {
            $validated['is_admin'] = ((int) $validated['rol_id'] === (int) $rolAdmin->id);
        }

        if (!empty($validated['hash_password'])) {
            $validated['hash_password'] = Hash::make($validated['hash_password']);
        } else {
            unset($validated['hash_password']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.show', $usuario)->with('success', 'Actualizado.');
    }

    public function destroy(Usuario $usuario)
    {
        if (!auth()->user()->canDeleteUser($usuario)) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para eliminar este usuario. Solo los administradores pueden eliminar usuarios.'], 403);
            }

            return redirect()->route('usuarios.index')->with('error', 'No tienes permiso para eliminar este usuario. Solo los administradores pueden eliminar usuarios.');
        }

        try {
            \App\Services\EliminadosService::archiveModel($usuario, auth()->id());
            $usuario->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => 'Usuario eliminado correctamente'], 200);
            }

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error al eliminar usuario: '.$e->getMessage()], 500);
            }

            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar usuario: '.$e->getMessage());
        }
    }

    public function importarPorCedula(Request $request)
    {
        Log::info('Iniciando importación para cédula: '.$request->cedula);

        $cedulaNormalizada = Usuario::normalizeCedula($request->cedula);

        try {
            $jsonPath = storage_path('app/respuesta.json');
            $json = json_decode(file_get_contents($jsonPath), true);

            $persona = collect($json[0]['data'] ?? [])->first(function ($item) use ($request) {
                return preg_replace('/\D/', '', $item['pin']) === preg_replace('/\D/', '', $request->cedula);
            });

            if (!$persona) {
                Log::warning('Cédula no encontrada en JSON: '.$request->cedula);
                throw new \Exception('La cédula no existe en el API');
            }

            $rolUser = DB::table('roles')->where('nombre', 'Usuario Normal')->first();
            if (!$rolUser) {
                $rolId = DB::table('roles')->first()->id;
            } else {
                $rolId = $rolUser->id;
            }

            $usuario = Usuario::updateOrCreate(
                ['cedula' => $cedulaNormalizada],
                [
                    'rol_id' => $rolId,
                    'nombre' => $persona['firstnames'] ?? 'Sin Nombre',
                    'apellido' => $persona['lastnames'] ?? 'Sin Apellido',
                    'correo' => strtolower(Str::slug($persona['pin_str'] ?? $request->cedula, '.')).'@sistema.local',
                    'activo' => true,
                    'is_admin' => false,
                    'hash_password' => Hash::make(preg_replace('/\D/', '', $request->cedula)),
                ]
            );

            Log::info('Usuario procesado con éxito: '.$usuario->id);

            return redirect()->back()->with('success', 'Usuario registrado/actualizado.');

        } catch (\Exception $e) {
            Log::error('Error en importación: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Generar reporte PDF de usuarios con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'cedula' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'string', 'max:255'],
            'rol_id' => ['nullable', 'integer', 'exists:roles,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $query = Usuario::with(['rol']);

        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('cedula', 'like', "%{$buscar}%")
                  ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        if (!empty($validated['cedula'])) {
            $query->where('cedula', 'like', '%' . $validated['cedula'] . '%');
        }

        if (!empty($validated['correo'])) {
            $query->where('correo', 'like', '%' . $validated['correo'] . '%');
        }

        if (!empty($validated['rol_id'])) {
            $query->where('rol_id', $validated['rol_id']);
        }

        if (isset($validated['activo'])) {
            $query->where('activo', $validated['activo']);
        }

        $usuarios = $query->orderBy('nombre')->orderBy('apellido')->get();
        $now = now();

        $tipoReporte = $this->determinarTipoReporte($validated);

        return match ($tipoReporte) {
            'rol' => $this->fpdf->generarUsuariosPorRol(
                'reporte_usuarios_por_rol_' . $now->format('dmY_His') . '.pdf',
                'USUARIOS POR ROL',
                'Listado de usuarios agrupados por rol',
                $now->format('d/m/Y H:i'),
                $usuarios
            ),
            default => $this->fpdf->downloadUsuariosListado(
                'reporte_usuarios_general_' . $now->format('dmY_His') . '.pdf',
                'REPORTE DE USUARIOS DEL SISTEMA',
                'Listado general de usuarios',
                $now->format('d/m/Y H:i'),
                $usuarios
            ),
        };
    }

    /**
     * Determina el tipo de reporte según los filtros aplicados.
     */
    private function determinarTipoReporte(array $filtros): string
    {
        if (!empty($filtros['rol_id'])) {
            return 'rol';
        }
        return 'general';
    }
}
