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

        // Guardar en storage
        $path = "actas/traslado/{$folio}.pdf";
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
