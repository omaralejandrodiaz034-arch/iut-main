<?php

namespace App\Http\Controllers;

use App\Enums\EstadoBien;
use App\Enums\TipoBien;
use App\Models\Bien;
use App\Models\Dependencia;
use App\Models\Movimiento;
use App\Models\Organismo;
use App\Models\UnidadAdministradora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPIs generales ──────────────────────────────────────────────
        $totalBienes        = Bien::count();
        $totalActivos       = Bien::where('estado', EstadoBien::ACTIVO->value)->count();
        $totalDañados       = Bien::where('estado', EstadoBien::DANADO->value)->count();
        $totalMantenimiento = Bien::where('estado', EstadoBien::EN_MANTENIMIENTO->value)->count();
        $totalExtraviados   = Bien::where('estado', EstadoBien::EXTRAVIADO->value)->count();
        $totalDesincorporados = Bien::where('estado', EstadoBien::DESINCORPORADO->value)->count();

        $totalOrganismos    = Organismo::count();
        $totalUnidades      = UnidadAdministradora::count();
        $totalDependencias  = Dependencia::count();
        $totalUsuarios      = Usuario::where('activo', true)->count();

        // Valor total del inventario
        $valorTotal = Bien::whereNotNull('precio')
            ->where('estado', '!=', EstadoBien::DESINCORPORADO->value)
            ->sum('precio');

        // ── Distribución por estado ──────────────────────────────────────
        $porEstado = collect(EstadoBien::cases())->mapWithKeys(fn ($e) => [
            $e->label() => Bien::where('estado', $e->value)->count()
        ])->filter(fn ($v) => $v > 0);

        // ── Distribución por tipo ────────────────────────────────────────
        $porTipo = collect(TipoBien::cases())->mapWithKeys(fn ($t) => [
            $t->label() => Bien::where('tipo_bien', $t->value)->count()
        ])->filter(fn ($v) => $v > 0);

        // ── Top 5 dependencias con más bienes ───────────────────────────
        $topDependencias = Dependencia::withCount('bienes')
            ->orderByDesc('bienes_count')
            ->limit(5)
            ->get();

        // ── Últimos 10 movimientos ───────────────────────────────────────
        $ultimosMovimientos = Movimiento::with(['bien', 'usuario'])
            ->orderByDesc('fecha')
            ->limit(10)
            ->get();

        // ── Bienes registrados por mes (últimos 12 meses) ────────────────
        $bienesPorMes = Bien::selectRaw(
            DB::getDriverName() === 'mysql'
                ? "DATE_FORMAT(fecha_registro, '%Y-%m') as mes, COUNT(*) as total"
                : "strftime('%Y-%m', fecha_registro) as mes, COUNT(*) as total"
        )
            ->whereNotNull('fecha_registro')
            ->where('fecha_registro', '>=', now()->subMonths(12))
            ->groupByRaw(
                DB::getDriverName() === 'mysql'
                    ? "DATE_FORMAT(fecha_registro, '%Y-%m')"
                    : "strftime('%Y-%m', fecha_registro)"
            )
            ->orderBy('mes')
            ->pluck('total', 'mes');

        return view('dashboard', compact(
            'totalBienes', 'totalActivos', 'totalDañados', 'totalMantenimiento',
            'totalExtraviados', 'totalDesincorporados', 'totalOrganismos',
            'totalUnidades', 'totalDependencias', 'totalUsuarios', 'valorTotal',
            'porEstado', 'porTipo', 'topDependencias', 'ultimosMovimientos', 'bienesPorMes'
        ));
    }
}
