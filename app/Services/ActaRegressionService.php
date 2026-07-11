<?php

namespace App\Services;

use App\Enums\EstadoBien;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de regresión de actas.
 *
 * Cuando el sistema emite un acta de traslado, donación o desincorporación de
 * bienes, ésta debe ser firmada y sellada (adjuntada como imagen o PDF) por los
 * organismos correspondientes. Si la acta no se reincorpora firmada dentro del
 * plazo establecido, la operación que la emitió queda cancelada y se revierte.
 */
class ActaRegressionService
{
    public const ESTADO_PENDIENTE = 'PENDIENTE_FIRMA';

    public const ESTADO_FIRMADA = 'FIRMADA';

    public const ESTADO_CANCELADA = 'CANCELADA';

    public const ESTADO_RECHAZADA = 'RECHAZADA';

    public const PLAZO_DIAS = 2;

    public function __construct(private ActaNotificacionService $actaNotificacionService) {}

    /**
     * Procesa y cancela todas las actas vencidas del sistema (pendientes de firma
     * cuya fecha límite ya pasó). Devuelve la cantidad de actas canceladas.
     */
    public function procesarActasVencidas(): int
    {
        $movimientos = Movimiento::query()
            ->whereIn('acta_estado', [self::ESTADO_PENDIENTE, self::ESTADO_RECHAZADA])
            ->whereNotNull('fecha_limite_acta')
            ->where('fecha_limite_acta', '<=', now())
            ->get();

        $cancelados = 0;

        foreach ($movimientos as $movimiento) {
            $this->cancelarActaPendiente(
                $movimiento,
                sprintf('Se venció el plazo de %d días para adjuntar el acta firmada y autorizada.', self::PLAZO_DIAS)
            );
            $this->actaNotificacionService->notificarActaCancelada($movimiento);
            $cancelados++;
        }

        return $cancelados;
    }

    /**
     * Cancela la operación asociada a un acta pendiente de firma y revierte sus
     * efectos en el bien (desincorporación, traslado o donación).
     */
    public function cancelarActaPendiente(Movimiento $movimiento, string $motivo): void
    {
        if ($movimiento->acta_estado === self::ESTADO_CANCELADA || $movimiento->acta_estado === self::ESTADO_RECHAZADA) {
            return;
        }

        $bien = $movimiento->bien;

        if ($movimiento->tipo === 'DESINCORPORACION') {
            if ($bien) {
                $bien->estado = EstadoBien::ACTIVO;
                $bien->save();
                $bien->desincorporado()?->delete();
            }
        }

        if ($movimiento->tipo === 'TRASLADO' && $bien) {
            $metadata = $movimiento->metadata ?? [];
            if (! empty($metadata['dependencia_id_anterior']) && ! empty($metadata['codigo_anterior'])) {
                $bien->update([
                    'dependencia_id' => $metadata['dependencia_id_anterior'],
                    'codigo' => $metadata['codigo_anterior'],
                ]);
            }
        }

        if ($movimiento->tipo === 'DONACION' && $bien) {
            $bien->update([
                'es_donacion' => false,
                'tipo_donante' => null,
                'donante_nombre' => null,
                'donante_documento' => null,
                'donante_direccion' => null,
                'acta_donacion' => null,
            ]);
        }

        $movimiento->update([
            'acta_estado' => self::ESTADO_CANCELADA,
            'motivo_cancelacion_acta' => $motivo,
            'fecha_cancelacion_acta' => now(),
        ]);

        $this->actaNotificacionService->resolverPendientesDe($movimiento);

        if ($bien) {
            Movimiento::create([
                'bien_id' => $bien->id,
                'usuario_id' => Auth::id(),
                'tipo' => 'CANCELACION_ACTA',
                'observaciones' => $motivo,
                'fecha' => now(),
            ]);
        }
    }

    public function rechazarActaFirmada(Movimiento $movimiento, string $motivo): void
    {
        if ($movimiento->acta_estado !== self::ESTADO_FIRMADA) {
            return;
        }

        $movimiento->update([
            'acta_estado' => self::ESTADO_RECHAZADA,
            'motivo_cancelacion_acta' => $motivo,
            'fecha_cancelacion_acta' => now(),
            'fecha_limite_acta' => now()->addDays(self::PLAZO_DIAS),
        ]);

        $this->actaNotificacionService->resolverPendientesDe($movimiento);

        $bien = $movimiento->bien;

        if ($bien) {
            Movimiento::create([
                'bien_id' => $bien->id,
                'usuario_id' => Auth::id(),
                'tipo' => 'RECHAZO_ACTA',
                'observaciones' => $motivo,
                'fecha' => now(),
            ]);
        }
    }
}
