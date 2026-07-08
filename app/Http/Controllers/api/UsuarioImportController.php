<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsuarioImportController extends Controller
{
    public function importarPorCedula(Request $request)
    {
        $request->validate(['cedula' => ['required', 'string']]);

        // 1️⃣ Normalizar la cédula de entrada inmediatamente (forma legible) y obtener solo dígitos
        $cedulaInput = Usuario::normalizeCedula($request->cedula);
        $cedulaDigits = preg_replace('/\D/', '', $request->cedula);

        try {
            $jsonPath = storage_path('app/respuesta.json');
            if (! file_exists($jsonPath)) {
                throw new \Exception('No se encontró el archivo respuesta.json');
            }

            $json = json_decode(file_get_contents($jsonPath), true);

            // 2️⃣ Buscar en el JSON comparando con la cédula normalizada
            $persona = collect($json[0]['data'] ?? [])
                ->first(function ($item) use ($cedulaInput) {
                    return isset($item['pin']) &&
                           Usuario::normalizeCedula((string) $item['pin']) === $cedulaInput;
                });

            if (! $persona) {
                throw new \Exception('La cédula no existe en el API');
            }

            // 3️⃣ Mapear y Roles
            $rolAdmin = DB::table('roles')->where('nombre', 'Administrador')->first();
            $rolUser = DB::table('roles')->where('nombre', 'Usuario Normal')->first();

            // Lógica de Admin (usando cédula limpia para la condición fija)
            $esAdmin = str_contains($cedulaInput, '31.077.912');
            $rolId = $esAdmin ? ($rolAdmin->id ?? $rolUser->id) : ($rolUser->id ?? $rolAdmin->id);

            // 4️⃣ Preparar datos para Guardar
            $datos = [
                'rol_id' => $rolId,
                'nombre' => trim($persona['firstnames'] ?? $persona['fullname'] ?? 'Usuario'),
                'apellido' => trim($persona['lastnames'] ?? null),
                'correo' => strtolower(Str::slug($persona['pin_str'] ?? $cedulaInput, '.')).'@externo.local',
                'activo' => ($persona['status'] ?? '0') === '1',
                'is_admin' => $esAdmin,
            ];

            // 5️⃣ Buscar usuario existente comparando sólo los dígitos de la cédula,
            // para evitar problemas por formatos distintos (V-31.077.912 vs 31077912)
            $usuarioExistente = Usuario::whereRaw("REPLACE(REPLACE(REPLACE(cedula, '.', ''), '-', ''), 'V', '') = ?", [$cedulaDigits])->first();

            // Preparar correo único: si ya existe en otro usuario, generar alternativa
            $baseCorreo = strtolower(Str::slug($persona['pin_str'] ?? $cedulaInput, '.')).'@externo.local';
            $correoFinal = $baseCorreo;
            $suffix = 1;
            while (Usuario::where('correo', $correoFinal)
                ->when($usuarioExistente, function ($q) use ($usuarioExistente) { return $q->where('id', '!=', $usuarioExistente->id); })
                ->exists()) {
                $correoFinal = strtolower(Str::slug($persona['pin_str'] ?? $cedulaInput, '.'))."+{$suffix}@externo.local";
                $suffix++;
            }

            $datos['correo'] = $correoFinal;

            if (! $usuarioExistente) {
                // Si no existe, asignar contraseña por defecto (cédula limpia)
                $datos['hash_password'] = Hash::make($cedulaDigits);
                $usuario = Usuario::create(array_merge(['cedula' => $cedulaInput], $datos));
            } else {
                // Actualizar usuario existente
                $usuarioExistente->update(array_merge($datos, ['cedula' => $cedulaInput]));
                $usuario = $usuarioExistente;
            }

            if ($request->expectsJson()) {
                return response()->json(['status' => 'ok', 'usuario' => $usuario]);
            }

            return redirect()->back()->with('success', 'Usuario actualizado correctamente');

        } catch (\Throwable $e) {
            Log::error('ERROR IMPORT:', ['cedula' => $cedulaInput, 'error' => $e->getMessage()]);

            return $request->expectsJson()
                ? response()->json(['status' => 'error', 'message' => $e->getMessage()], 500)
                : redirect()->back()->with('error', $e->getMessage());
        }
    }
}
