<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\TipoResponsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResponsableController extends Controller
{
    /**
     * Buscar responsable por cédula en el API externo o en respuesta.json
     * y guardarlo en la tabla responsables.
     */
   public function buscar(Request $request)
{
    $cedulaRaw = (string) $request->input('cedula');
    $cedula = preg_replace('/\D/', '', $cedulaRaw) ?: $cedulaRaw;

    $persona = null;

    // 1) Intentar API externa primero
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
                    return preg_replace('/\D/', '', (string) ($item['pin'] ?? '')) === $cedula;
                });
            }
        }
    } catch (\Throwable $e) {
        Log::warning('Fallo API externa people_api en ResponsableController', ['error' => $e->getMessage()]);
    }

    // 2) Fallback al JSON local si no hubo resultado
    if (! $persona) {
        $jsonPath = storage_path('app/respuesta.json');
        if (file_exists($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $data = json_decode($json, true);
            $persona = collect($data[0]['data'] ?? [])->first(function ($item) use ($cedula) {
                return preg_replace('/\D/', '', (string) ($item['pin'] ?? '')) === $cedula;
            });
        }
    }

    if (!$persona) {
        if ($request->expectsJson()) {
            return response()->json(['status' => 'error', 'message' => 'Cédula no encontrada'], 404);
        }
        return redirect()->back()->with('error', 'No se encontró persona con esa cédula');
    }

    // Lógica de Guardado
    $tipoNombre = is_array($persona['type_str'] ?? null) ? implode(', ', $persona['type_str']) : (string) ($persona['type_str'] ?? 'Responsable');
    $tipo = TipoResponsable::firstOrCreate(['nombre' => $tipoNombre]);

    $responsable = Responsable::updateOrCreate(
        ['cedula' => (string) $persona['pin']],
        [
            'tipo_id'  => $tipo->id,
            'nombre'   => $persona['fullname'],
            'correo'   => null,
            'telefono'=> null,
        ]
    );
 
    // RESPUESTA
    if ($request->expectsJson()) {
        return response()->json([
            'status' => 'ok',
            'message' => '¡Responsable registrado/actualizado con éxito!',
            'data' => [
                'nombre' => $responsable->nombre,
                'cedula' => $responsable->cedula,
                'tipo'   => $tipo->nombre
            ]
        ]);
    }

    // SI SE USA EL BOTÓN "SUBMIT" NORMAL:
    // Redirige de vuelta a la misma página (create) con un mensaje de éxito
    return redirect()->back()->with('success', 'Responsable guardado correctamente.');
}
}


