<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
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
    /**
     * Listar usuarios con filtros avanzados (nombre, cédula, correo, rol).
     */
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

        // Búsqueda general
        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('cedula', 'like', "%{$buscar}%")
                  ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        // Filtro por cédula
        if (!empty($validated['cedula'])) {
            $query->where('cedula', 'like', '%' . $validated['cedula'] . '%');
        }

        // Filtro por nombre
        if (!empty($validated['nombre'])) {
            $query->where('nombre', 'like', '%' . $validated['nombre'] . '%');
        }

        // Filtro por correo
        if (!empty($validated['correo'])) {
            $query->where('correo', 'like', '%' . $validated['correo'] . '%');
        }

        // Filtro por rol
        if (!empty($validated['rol_id'])) {
            $query->where('rol_id', $validated['rol_id']);
        }

        // Filtro por estado activo/inactivo
        if (isset($validated['activo'])) {
            $query->where('activo', $validated['activo']);
        }

        $usuarios = $query->orderBy('nombre')->paginate(10)->appends($request->query());
        $roles = \App\Models\Rol::orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'validated'));
    }

    /**
     * Mostrar formulario para crear usuario.
     */
    public function create()
    {
        // Excluir roles obsoletos como "Gerente de Bienes" para que no se muestren en el formulario
        $roles = \App\Models\Rol::where('nombre', '!=', 'Gerente de Bienes')->get();

        return view('usuarios.create', compact('roles'));
    }

    /**
     * Guardar un nuevo usuario.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado.'], 403);
        }

        // Normalizar usando el Modelo
        if ($request->filled('cedula')) {
            $request->merge(['cedula' => Usuario::normalizeCedula($request->input('cedula'))]);
        }

        // Primero verificar que la cédula exista en el API externo (como en el login)
        $cedulaDigits = preg_replace('/\D/', '', $request->input('cedula'));
        $personaApi = $this->buscarPersonaEnApiPorCedula($cedulaDigits);
        
        if (!$personaApi) {
            return back()
                ->withInput()
                ->with('error', 'La cédula ingresada no existe en los registros autorizados. Solo se pueden crear usuarios que estén registrados en la base de datos institucional.');
        }

        // Verificar que el usuario no exista ya en el sistema
        $existingUser = Usuario::where('cedula', $request->input('cedula'))->first();
        if ($existingUser) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un usuario registrado con esta cédula en el sistema.');
        }

        $validated = $request->validate([
            'rol_id'        => ['required', 'exists:roles,id'],
            'cedula'        => ['required', 'string', 'unique:usuarios,cedula', 'regex:/^V-\d{2}\.\d{3}\.\d{3}$/'],
            'nombre'        => ['required', 'string', 'max:150'],
            'apellido'      => ['required', 'string', 'max:150'],
            'correo'        => ['required', 'email', 'unique:usuarios,correo'],
            'hash_password' => ['required', 'string', 'min:8'],
            'activo'        => ['boolean'],
        ], [
            'cedula.regex' => 'El formato de cédula debe ser V-00.000.000',
            'cedula.unique' => 'Ya existe un usuario con esta cédula',
            'correo.unique' => 'Ya existe un usuario con este correo',
        ]);

        $rolAdmin = Rol::where('nombre', 'Administrador')->first();
        $validated['is_admin'] = ($rolAdmin && (int)$validated['rol_id'] === (int)$rolAdmin->id);
        $validated['hash_password'] = Hash::make($validated['hash_password']);

        $usuario = Usuario::create($validated);

        return $request->expectsJson() 
            ? response()->json(['message' => 'Creado', 'usuario' => $usuario], 201)
            : redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Verificar Cédula en API Externo
     * Método auxiliar para validar Cédula contra la API externa
     */
    private function buscarPersonaEnApiPorCedula(string $cedula): ?array
    {
        // 1) Intentar API externa
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
            \Log::warning('Fallo API externa en usuario.store', ['error' => $e->getMessage()]);
        }

        // 2) Fallback al JSON local
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

    /**
     * Mostrar formulario para editar un usuario.
     */
    public function edit(Usuario $usuario)
    {
        // Evitar exponer roles obsoletos en la edición
        $roles = \App\Models\Rol::where('nombre', '!=', 'Gerente de Bienes')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Mostrar detalles de un usuario.
     */
    public function show(Usuario $usuario)
    {
        $usuario->load(['rol', 'reportes', 'movimientos']);

        if (request()->expectsJson()) {
            return response()->json($usuario);
        }

        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Descargar los detalles del usuario en PDF.
     */
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

    /**
     * Actualizar datos de un usuario.
     */
    public function update(Request $request, Usuario $usuario)
    {
        if (! auth()->user()->canDeleteData()) abort(403);

        if ($request->filled('cedula')) {
            $request->merge(['cedula' => Usuario::normalizeCedula($request->input('cedula'))]);
        }

        $validated = $request->validate([
            'rol_id'   => ['sometimes', 'exists:roles,id'],
            'cedula'   => ['sometimes', Rule::unique('usuarios')->ignore($usuario->id)],
            'nombre'   => ['sometimes', 'string'],
            'apellido' => ['sometimes', 'string'],
            'correo'   => ['sometimes', 'email', Rule::unique('usuarios')->ignore($usuario->id)],
            'hash_password' => ['nullable', 'string', 'min:8'],
            'activo'   => ['boolean'],
        ]);

        // Protección adicional: un usuario con flag admin no puede ver modificados
        // su rol ni su estado desde la interfaz web. Esto evita cambios por manipulación.
        if ($usuario->isAdmin()) {
            if (array_key_exists('rol_id', $validated) || array_key_exists('activo', $validated)) {
                abort(403, 'No se puede modificar el rol o el estado de un administrador.');
            }
        }

        $rolAdmin = Rol::where('nombre', 'Administrador')->first();
        if (isset($validated['rol_id']) && $rolAdmin) {
            $validated['is_admin'] = ((int)$validated['rol_id'] === (int)$rolAdmin->id);
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
        // Verificar permisos primero - si no tiene permiso, mostrar mensaje claro
        if (!auth()->user()->canDeleteUser($usuario)) {
            // Si es solicitud AJAX, devolver JSON con error
            if (request()->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para eliminar este usuario. Solo los administradores pueden eliminar usuarios.'], 403);
            }
            // Si es solicitud normal, redirigir con mensaje de error
            return redirect()->route('usuarios.index')->with('error', 'No tienes permiso para eliminar este usuario. Solo los administradores pueden eliminar usuarios.');
        }
        
        try {
            // Archivar en tabla de eliminados antes de borrar
            \App\Services\EliminadosService::archiveModel($usuario, auth()->id());
            $usuario->delete();
            
            // Responder según tipo de solicitud
            if (request()->expectsJson()) {
                return response()->json(['success' => 'Usuario eliminado correctamente'], 200);
            }
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
            
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error al eliminar usuario: ' . $e->getMessage()], 500);
            }
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }


    /**
     * Normaliza una cédula a formato V-XX.XXX.XXX.
     * Acepta entradas con o sin 'V-' y con o sin puntos. Extrae dígitos y formatea.
     */
    /**
 * Lógica de normalización exacta del usuario
 */
public function importarPorCedula(Request $request)
{
    // Log para ver qué llega
    \Log::info('Iniciando importación para cédula: ' . $request->cedula);

    $cedulaNormalizada = Usuario::normalizeCedula($request->cedula);

    try {
        $jsonPath = storage_path('app/respuesta.json');
        $json = json_decode(file_get_contents($jsonPath), true);

        // Buscar en el JSON
        $persona = collect($json[0]['data'] ?? [])->first(function ($item) use ($request) {
            // Comparamos números limpios para no fallar
            return preg_replace('/\D/', '', $item['pin']) === preg_replace('/\D/', '', $request->cedula);
        });

        if (!$persona) {
            \Log::warning('Cédula no encontrada en JSON: ' . $request->cedula);
            throw new \Exception('La cédula no existe en el API');
        }

        // DETERMINAR ROLES (Asegúrate de que estos nombres existan en tu tabla 'roles')
        $rolUser = DB::table('roles')->where('nombre', 'Usuario Normal')->first();
        if (!$rolUser) {
             // Si no existe el rol, el sistema fallará. Creamos un fallback.
             $rolId = DB::table('roles')->first()->id; 
        } else {
             $rolId = $rolUser->id;
        }

        $usuario = Usuario::updateOrCreate(
            ['cedula' => $cedulaNormalizada], // Condición de búsqueda
            [
                'rol_id'   => $rolId,
                'nombre'   => $persona['firstnames'] ?? 'Sin Nombre',
                'apellido' => $persona['lastnames'] ?? 'Sin Apellido',
                'correo'   => strtolower(Str::slug($persona['pin_str'] ?? $request->cedula, '.')) . '@sistema.local',
                'activo'   => true,
                'is_admin' => false,
                'hash_password' => Hash::make(preg_replace('/\D/', '', $request->cedula)),
            ]
        );

        \Log::info('Usuario procesado con éxito: ' . $usuario->id);

        return redirect()->back()->with('success', 'Usuario registrado/actualizado.');

    } catch (\Exception $e) {
        \Log::error('Error en importación: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}
