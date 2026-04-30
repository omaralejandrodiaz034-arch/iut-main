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

        // 1️⃣ Normalizar la cédula de entrada inmediatamente
        $cedulaInput = Usuario::normalizeCedula($request->cedula);

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

            // 5️⃣ Si el usuario no existe, le creamos una contraseña (su propia cédula sin puntos)
            $usuarioExistente = Usuario::where('cedula', $cedulaInput)->first();
            if (! $usuarioExistente) {
                $datos['hash_password'] = Hash::make(preg_replace('/\D/', '', $cedulaInput));
            }

            $usuario = Usuario::updateOrCreate(['cedula' => $cedulaInput], $datos);

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
