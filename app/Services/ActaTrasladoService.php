<?php

namespace App\Services;

use App\Models\Bien;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ActaTrasladoService
{
    public function generar(Bien $bien, string $motivo, $usuario, $dependenciaAnterior, $dependenciaNueva)
    {
        if (! extension_loaded('gd')) {
            // lanzar excepción con instrucción clara para desarrolladores/administradores
            throw new \RuntimeException("La extensión PHP GD es necesaria para generar el acta. \n" .
                "Active 'extension=gd' en el php.ini del servidor web y reinicie Apache/Tomcat.");
        }

        $folio = 'TRAS-' . now()->format('Y') . '-' . str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien'        => $bien,
            'motivo'      => $motivo,
            'fecha'       => Carbon::now()->format('d/m/Y'),
            'hora'        => Carbon::now()->format('H:i'),
            'usuario'     => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'dependencia_anterior' => $dependenciaAnterior,
            'dependencia_nueva'    => $dependenciaNueva,
            'folio'       => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-traslado', $data);

        // Asegurar que el directorio existe
        $path = "actas/traslado/{$folio}.pdf";
        Storage::disk('public')->makeDirectory(dirname($path));

        // Guardar en storage
        Storage::disk('public')->put($path, $pdf->output());

        // Retornar URL para guardar en movimiento
        return $path;
    }

    public function descargar(Bien $bien, string $motivo, $usuario, $dependenciaAnterior, $dependenciaNueva)
    {
        $folio = 'TRAS-' . now()->format('Y') . '-' . str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien'        => $bien,
            'motivo'      => $motivo,
            'fecha'       => Carbon::now()->format('d/m/Y'),
            'hora'        => Carbon::now()->format('H:i'),
            'usuario'     => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'dependencia_anterior' => $dependenciaAnterior,
            'dependencia_nueva'    => $dependenciaNueva,
            'folio'       => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-traslado', $data);

        return $pdf->download('acta-traslado-' . $bien->codigo . '.pdf');
    }
}
