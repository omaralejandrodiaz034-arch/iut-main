<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    // 1. Verificación de permisos con mensaje en español
    if (! auth()->user()->isAdmin()) {
        return response()->json([
            'message' => 'Acceso denegado. Solo los administradores pueden realizar esta acción.',
        ], 403);
    }

    $rolAdmin = \App\Models\Rol::where('nombre', 'Administrador')->first();

    // Normalizar cédula
    if ($request->filled('cedula')) {
        $request->merge(['cedula' => $this->normalizeCedula($request->input('cedula'))]);
    }

    // 2. Validación con mensajes personalizados en español
    $validated = $request->validate(
        [
            'rol_id'        => ['required', 'exists:roles,id'],
            'cedula'        => ['required', 'string', 'max:20', 'unique:usuarios,cedula', 'regex:/^V-\d{2}\.\d{3}\.\d{3}$/'],
            'nombre'        => ['required', 'string', 'max:150'],
            'apellido'      => ['required', 'string', 'max:150'],
            'correo'        => ['required', 'email', 'max:255', 'unique:usuarios,correo'],
            'hash_password' => ['required', 'string', 'min:8'],
            'activo'        => ['boolean'],
            'is_admin'      => ['boolean'],
        ],
        [
            'rol_id.required'        => 'Debe seleccionar un rol para el usuario.',
            'rol_id.exists'          => 'El rol seleccionado no es válido.',
            'cedula.required'        => 'La cédula de identidad es obligatoria.',
            'cedula.regex'           => 'Formato de cédula incorrecto (Ej: V-12.345.678).',
            'cedula.unique'          => 'Esta cédula ya se encuentra registrada en el sistema.',
            'nombre.required'        => 'El nombre es obligatorio.',
            'nombre.max'             => 'El nombre no puede tener más de 150 caracteres.',
            'apellido.required'      => 'El apellido es obligatorio.',
            'apellido.max'           => 'El apellido no puede tener más de 150 caracteres.',
            'correo.required'        => 'El correo electrónico es obligatorio.',
            'correo.email'           => 'Debe ingresar una dirección de correo válida.',
            'correo.unique'          => 'Este correo ya está siendo usado por otro usuario.',
            'hash_password.required' => 'La contraseña es obligatoria.',
            'hash_password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        ]
    );

    // 3. Verificaciones de seguridad adicionales
    if ($rolAdmin && (int)$validated['rol_id'] === (int)$rolAdmin->getKey() && ! auth()->user()->isAdmin()) {
        return response()->json([
            'message' => 'No tiene permisos para asignar privilegios de Administrador.',
        ], 403);
    }

    // Forzar coherencia de is_admin
    if ($rolAdmin) {
        $validated['is_admin'] = ((int) $validated['rol_id'] === (int) $rolAdmin->getKey());
    }

    // Encriptar contraseña
    $validated['hash_password'] = Hash::make($validated['hash_password']);

    // Crear usuario
    $usuario = Usuario::create($validated);

    // 4. Respuesta según el tipo de petición
    if ($request->expectsJson()) {
        return response()->json([
            'message' => '¡Usuario creado con éxito!',
            'usuario' => $usuario,
        ], 201);
    }

    return redirect()->route('usuarios.index')
                     ->with('success', 'El usuario ha sido registrado correctamente en el sistema.');
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
        // Solo administradores pueden actualizar usuarios
        if (! auth()->user()->canDeleteData()) {
            return abort(403, 'No tienes permisos para actualizar usuarios.');
        }

        // Normalizar cédula si se envía (permitir que el usuario escriba sin 'V-' o sin puntos)
        if ($request->filled('cedula')) {
            $request->merge(['cedula' => $this->normalizeCedula($request->input('cedula'))]);
        }

        $validated = $request->validate([
            'rol_id' => ['sometimes', 'exists:roles,id'],
            'cedula' => [
                'sometimes', 'string', 'max:20',
                Rule::unique('usuarios', 'cedula')->ignore($usuario->getKey()),
            ],
            'nombre' => ['sometimes', 'string', 'max:150'],
            'apellido' => ['sometimes', 'string', 'max:150'],
            'correo' => [
                'sometimes', 'email', 'max:255',
                Rule::unique('usuarios', 'correo')->ignore($usuario->getKey()),
            ],
            'hash_password' => ['nullable', 'string', 'min:8'],
            'activo' => ['boolean'],
            'is_admin' => ['boolean'],
        ]);

        // Asegurar que 'rol_id' exista en el array validado para evitar errores de clave indefinida
        $validated['rol_id'] = $validated['rol_id'] ?? null;

        // Sólo administradores pueden cambiar la cédula de un usuario ya creado
        if (array_key_exists('cedula', $validated) && ! auth()->user()->isAdmin()) {
            return abort(403, 'Solo administradores pueden modificar la cédula de un usuario.');
        }

        // Solo administradores pueden asignar permisos de administrador
        if (isset($validated['is_admin']) && $validated['is_admin'] && ! auth()->user()->isAdmin()) {
            return abort(403, 'Solo administradores pueden asignar permisos de administrador.');
        }

        // Forzar coherencia de is_admin con rol seleccionado
        $rolAdmin = \App\Models\Rol::where('nombre', 'Administrador')->first();
        if ($rolAdmin && isset($validated['rol_id'])) {
            $validated['is_admin'] = ((int) $validated['rol_id'] === (int) $rolAdmin->getKey());
        }

        if (! empty($validated['hash_password'])) {
            $validated['hash_password'] = Hash::make($validated['hash_password']);
        }

        $usuario->update($validated);

        if ($request->expectsJson()) {
            return response()->json($usuario);
        }

        return redirect()->route('usuarios.show', $usuario)->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Normaliza una cédula a formato V-XX.XXX.XXX.
     * Acepta entradas con o sin 'V-' y con o sin puntos. Extrae dígitos y formatea.
     */
    private function normalizeCedula(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }

        $s = strtoupper($raw);
        // Extraer sólo dígitos
        $digits = preg_replace('/\D/', '', $s);
        // Limitar a 8 dígitos
        $digits = substr($digits, 0, 8);

        $part1 = substr($digits, 0, 2) ?: '';
        $part2 = substr($digits, 2, 3) ?: '';
        $part3 = substr($digits, 5, 3) ?: '';

        $formatted = 'V-';
        $formatted .= $part1;

        if ($part2 !== '') {
            $formatted .= '.'.$part2;
        }

        if ($part3 !== '') {
            $formatted .= '.'.$part3;
        }

        return $formatted;
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(Usuario $usuario)
    {
        // Solo administradores pueden eliminar datos
        if (! auth()->user()->canDeleteUser($usuario)) {
            return abort(403, 'No tienes permisos para eliminar este usuario. No puedes eliminar administradores ni a ti mismo.');
        }

        // Archivar en eliminados antes de borrar permanentemente
        \App\Services\EliminadosService::archiveModel($usuario, auth()->id());
        $usuario->delete();

        return response()->json(null, 204);
    }
}
