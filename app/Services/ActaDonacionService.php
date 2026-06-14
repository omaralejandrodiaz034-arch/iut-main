<?php

namespace App\Services;

use App\Models\Bien;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ActaDonacionService
{
    public function generar(Bien $bien, array $datosDonante, $usuario): string
    {
        if (! extension_loaded('gd')) {
            throw new \RuntimeException("La extensión PHP GD es necesaria para generar el acta. \n".
                "Active 'extension=gd' en el php.ini del servidor web y reinicie Apache/Tomcat.");
        }

        $folio = 'DON-'.now()->format('Y').'-'.str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien' => $bien,
            'tipo_donante' => $datosDonante['tipo_donante'] ?? '—',
            'donante_nombre' => $datosDonante['donante_nombre'] ?? '—',
            'donante_documento' => $datosDonante['donante_documento'] ?? '—',
            'donante_direccion' => $datosDonante['donante_direccion'] ?? '—',
            'fecha' => now()->format('d/m/Y'),
            'hora' => now()->format('H:i'),
            'usuario' => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'responsable' => $bien->dependencia?->responsable?->nombre_completo ?? '—',
            'dependencia' => $bien->dependencia?->nombre ?? '—',
            'folio' => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-donacion', $data);

        $path = "actas/donacion/{$folio}.pdf";
        Storage::disk('public')->makeDirectory(dirname($path));
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function descargar(Bien $bien, array $datosDonante, $usuario)
    {
        $folio = 'DON-'.now()->format('Y').'-'.str_pad($bien->id, 6, '0', STR_PAD_LEFT);

        $data = [
            'bien' => $bien,
            'tipo_donante' => $datosDonante['tipo_donante'] ?? '—',
            'donante_nombre' => $datosDonante['donante_nombre'] ?? '—',
            'donante_documento' => $datosDonante['donante_documento'] ?? '—',
            'donante_direccion' => $datosDonante['donante_direccion'] ?? '—',
            'fecha' => now()->format('d/m/Y'),
            'hora' => now()->format('H:i'),
            'usuario' => $usuario->nombre_completo ?? $usuario->name ?? auth()->user()?->nombre_completo ?? 'Usuario del sistema',
            'responsable' => $bien->dependencia?->responsable?->nombre_completo ?? '—',
            'dependencia' => $bien->dependencia?->nombre ?? '—',
            'folio' => $folio,
        ];

        $pdf = Pdf::loadView('bienes.pdf.acta-donacion', $data);

        return $pdf->download('acta-donacion-'.$bien->codigo.'.pdf');
    }
}
