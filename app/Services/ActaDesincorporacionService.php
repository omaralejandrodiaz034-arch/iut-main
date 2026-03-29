<?php

namespace App\Services;

use App\Models\Bien;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ActaDesincorporacionService
{
    public function generar(Bien $bien, string $motivo, $usuario)
    {
        $folio = 'DES-' . now()->format('Y') . '-' . str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien'        => $bien,
            'motivo'      => $motivo,
            'fecha'       => Carbon::now()->format('d/m/Y'),
            'hora'        => Carbon::now()->format('H:i'),
            'usuario'     => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'responsable' => $bien->dependencia?->responsable?->nombre_completo ?? '—',
            'dependencia' => $bien->dependencia?->nombre ?? '—',
            'folio'       => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-desincorporacion', $data);

        // Guardar en storage
        $path = "actas/desincorporacion/{$folio}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        // Retornar URL para guardar en movimiento
        return $path;
    }

    public function descargar(Bien $bien, string $motivo, $usuario)
    {
        $folio = 'DES-' . now()->format('Y') . '-' . str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien'        => $bien,
            'motivo'      => $motivo,
            'fecha'       => Carbon::now()->format('d/m/Y'),
            'hora'        => Carbon::now()->format('H:i'),
            'usuario'     => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'responsable' => $bien->dependencia?->responsable?->nombre_completo ?? '—',
            'dependencia' => $bien->dependencia?->nombre ?? '—',
            'folio'       => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-desincorporacion', $data);

        return $pdf->download('acta-desincorporacion-' . $bien->codigo . '.pdf');
    }
}
