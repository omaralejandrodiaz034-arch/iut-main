<?php

namespace App\Http\Controllers;

use App\Models\Eliminado;
use App\Models\Movimiento;
use App\Models\Usuario;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    public function index(Request $request)
    {
        // 1) Tomar filtros
        $filters = $request->only(['tipo', 'usuario', 'entidad', 'fecha_desde', 'fecha_hasta']);

        // 2) Query base
        $query = Movimiento::query()->with(['bien', 'usuario', 'subject'])->orderByDesc('fecha');

        // 3) Aplicar filtros
        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['usuario'])) {
            $qUsuario = trim($filters['usuario']);
            $query->whereHas('usuario', function ($q) use ($qUsuario) {
                $q->where('nombre', 'like', "%{$qUsuario}%")
                    ->orWhere('correo', 'like', "%{$qUsuario}%");

            });
        }

        if (! empty($filters['entidad'])) {
            $ent = trim($filters['entidad']);
            $query->where(function ($q) use ($ent) {
                // match por class_basename (último segmento del FQCN)
                $q->orWhereRaw("LOWER(SUBSTRING_INDEX(subject_type, '\\\', -1)) LIKE ?", ['%'.strtolower($ent).'%']);
                // match por FQCN completo opcional
                $q->orWhere('subject_type', 'like', "%{$ent}%");
            });
        }

        $desde = $filters['fecha_desde'] ?? null;
        $hasta = $filters['fecha_hasta'] ?? null;

        if ($desde && $hasta) {
            $query->whereBetween('fecha', [$desde, $hasta]);
        } elseif ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        } elseif ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        // 4) Paginar preservando filtros
        $movimientos = $query->paginate(10)->appends($filters);

        // 5) Eliminados (admin)
        $eliminados = null;
        if (Auth::check() && Auth::user() instanceof Usuario && Auth::user()->isAdmin()) {
            $eliminados = Eliminado::orderByDesc('deleted_at')->paginate(10, ['*'], 'eliminados_page');

            $userIds = $eliminados->pluck('deleted_by')->unique()->filter()->values()->all();
            $users = ! empty($userIds)
                ? Usuario::whereIn('id', $userIds)->get()->keyBy('id')
                : [];

            $eliminados->getCollection()->transform(function ($item) use ($users) {
                $item->deleted_by_user = $users[$item->deleted_by]->nombre_completo
                    ?? $users[$item->deleted_by]->correo
                    ?? $item->data['_archived_by']
                    ?? null;

                return $item;
            });
        }

        // 6) JSON opcional, AJAX, y vista

        // 🥇 Comprobación de Petición AJAX (la que estabas enviando desde JS)
        if ($request->ajax()) {
            // Devolvemos el HTML de la vista renderizado como una cadena de texto.
            // Esto evita que el navegador intente navegar a la nueva "página" y permite
            // a tu JavaScript analizar y reemplazar las secciones del DOM.
            return view('movimientos.index', compact('movimientos', 'eliminados', 'filters'))->render();
        }

        // 🥈 Comprobación de Petición JSON (útil para APIs)
        if ($request->wantsJson()) {
            return response()->json(['movimientos' => $movimientos, 'eliminados' => $eliminados, 'filters' => $filters]);
        }

        // 🥉 Vista completa para navegación normal
        return view('movimientos.index', compact('movimientos', 'eliminados', 'filters'));
    }

    public function create()
    {
        return view('movimientos.create');
    }

    public function store(Request $request)
    {
        if (! $request->expectsJson()) {
            $user = Auth::user();
            if (! ($user instanceof Usuario && $user->isAdmin())) {
                abort(403, 'Solo administradores pueden crear movimientos manualmente.');
            }
        }

        $validated = $request->validate([
            'bien_id' => ['nullable', 'exists:bienes,id'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer'],
            'tipo' => ['required', 'string', 'max:50'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:500'],
            'usuario_id' => ['required', 'exists:usuarios,id'],
        ]);

        $allowed = [
            \App\Models\Organismo::class,
            \App\Models\UnidadAdministradora::class,
            \App\Models\Dependencia::class,
            \App\Models\Bien::class,
            \App\Models\Usuario::class,
        ];

        if (! empty($validated['subject_type'])) {
            if (! in_array($validated['subject_type'], $allowed, true)) {
                return back()->withErrors(['subject_type' => 'Tipo de sujeto no permitido'])->withInput();
            }

            $modelClass = $validated['subject_type'];
            if (! $modelClass::where('id', $validated['subject_id'] ?? 0)->exists()) {
                return back()->withErrors(['subject_id' => 'El sujeto indicado no existe'])->withInput();
            }

            if ($modelClass === \App\Models\Bien::class && empty($validated['bien_id'])) {
                $validated['bien_id'] = $validated['subject_id'];
            }
        }

        $movimiento = Movimiento::create($validated);

        return $request->expectsJson()
            ? response()->json($movimiento, 201)
            : redirect()->route('movimientos.index')->with('success', 'Movimiento registrado correctamente.');
    }

    public function edit(Movimiento $movimiento)
    {
        return view('movimientos.edit', compact('movimiento'));
    }

    public function show($id)
    {
        $movimiento = Movimiento::with([
            'usuario',
            'bien',
            'subject',
            'historialMovimientos' => fn ($q) => $q->orderBy('fecha', 'desc'),
        ])->findOrFail($id);

        return view('movimientos.show', compact('movimiento'));
    }

    public function update(Request $request, Movimiento $movimiento)
    {
        $validated = $request->validate([
            'bien_id' => ['sometimes', 'exists:bienes,id'],
            'subject_type' => ['sometimes', 'string', 'max:255'],
            'subject_id' => ['sometimes', 'integer'],
            'tipo' => ['sometimes', 'string', 'max:50'],
            'fecha' => ['sometimes', 'date'],
            'observaciones' => ['nullable', 'string', 'max:500'],
            'usuario_id' => ['sometimes', 'exists:usuarios,id'],
        ]);

        $allowed = [
            \App\Models\Organismo::class,
            \App\Models\UnidadAdministradora::class,
            \App\Models\Dependencia::class,
            \App\Models\Bien::class,
            \App\Models\Usuario::class,
        ];

        if (! empty($validated['subject_type'])) {
            if (! in_array($validated['subject_type'], $allowed, true)) {
                return back()->withErrors(['subject_type' => 'Tipo de sujeto no permitido'])->withInput();
            }

            $modelClass = $validated['subject_type'];
            if (! $modelClass::where('id', $validated['subject_id'] ?? 0)->exists()) {
                return back()->withErrors(['subject_id' => 'El sujeto indicado no existe'])->withInput();
            }

            if ($modelClass === \App\Models\Bien::class && empty($validated['bien_id'])) {
                $validated['bien_id'] = $validated['subject_id'];
            }
        }

        $movimiento->update($validated);

        return response()->json($movimiento);
    }

    public function destroy(Movimiento $movimiento)
    {
        $deletedBy = Auth::check() && is_numeric(Auth::user()->id) ? Auth::user()->id : null;

        \App\Services\EliminadosService::archiveModel($movimiento, $deletedBy);
        $movimiento->delete();

        return response()->json(null, 204);
    }

    public function restoreEliminado(Eliminado $eliminado)
    {
        $user = Auth::user();
        if (! ($user instanceof Usuario && $user->isAdmin())) {
            abort(403, 'Solo administradores pueden restaurar registros eliminados.');
        }

        $ok = \App\Services\EliminadosService::restoreEliminado($eliminado);

        return redirect()->route('movimientos.index')->with(
            $ok ? 'success' : 'error',
            $ok ? 'Registro restaurado correctamente.' : 'La restauración falló. Revisa los logs.'
        );
    }

    public function pdf(Movimiento $movimiento)
    {
        $movimiento->load(['usuario', 'subject', 'bien', 'historialMovimientos']);

        return Pdf::loadView('movimientos.pdf', compact('movimiento'))
            ->download("movimiento_{$movimiento->id}.pdf");
    }

    public function eliminados()
    {
        $bienes = \App\Models\Bien::where('estado', 'desincorporado')->with('movimiento')->get();

        return view('movimientos.eliminados', compact('bienes'));
    }

    public function reintegrar(\App\Models\Bien $bien)
    {
        $bien->update(['estado' => 'activo']);

        // Registrar movimiento de reintegración
        \App\Models\Movimiento::create([
            'bien_id' => $bien->id,
            'tipo' => 'reintegración',
            'descripcion' => 'Bien reintegrado',
            'usuario_id' => auth()->id(),
        ]);

        return redirect()->route('movimientos.eliminados')->with('success', 'Bien reintegrado correctamente.');
    }

    /**
     * Generar reporte PDF de movimientos con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $filters = $request->only(['tipo', 'usuario', 'entidad', 'fecha_desde', 'fecha_hasta']);

        $query = Movimiento::query()->with(['usuario', 'subject'])->orderByDesc('fecha');

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['usuario'])) {
            $qUsuario = trim($filters['usuario']);
            $query->whereHas('usuario', function ($q) use ($qUsuario) {
                $q->where('nombre', 'like', "%{$qUsuario}%")
                    ->orWhere('correo', 'like', "%{$qUsuario}%");
            });
        }

        if (! empty($filters['entidad'])) {
            $ent = trim($filters['entidad']);
            $query->where(function ($q) use ($ent) {
                $q->orWhereRaw("LOWER(SUBSTRING_INDEX(subject_type, '\\\\', -1)) LIKE ?", ['%'.strtolower($ent).'%']);
                $q->orWhere('subject_type', 'like', "%{$ent}%");
            });
        }

        $desde = $filters['fecha_desde'] ?? null;
        $hasta = $filters['fecha_hasta'] ?? null;

        if ($desde && $hasta) {
            $query->whereBetween('fecha', [$desde, $hasta]);
        } elseif ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        } elseif ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        $movimientos = $query->get();
        $now = now();

        $tipoReporte = $this->determinarTipoReporte($filters);

        return match ($tipoReporte) {
            'tipo' => $this->fpdf->generarMovimientosPorTipo(
                'reporte_movimientos_por_tipo_'.$now->format('dmY_His').'.pdf',
                'MOVIMIENTOS POR TIPO',
                'Listado de movimientos agrupados por tipo',
                $now->format('d/m/Y H:i'),
                $movimientos
            ),
            'usuario' => $this->fpdf->generarMovimientosPorUsuario(
                'reporte_movimientos_por_usuario_'.$now->format('dmY_His').'.pdf',
                'MOVIMIENTOS POR USUARIO',
                'Listado de movimientos agrupados por usuario',
                $now->format('d/m/Y H:i'),
                $movimientos
            ),
            default => $this->fpdf->downloadMovimientosListado(
                'reporte_movimientos_general_'.$now->format('dmY_His').'.pdf',
                'REPORTE DE MOVIMIENTOS',
                'Listado general de movimientos',
                $now->format('d/m/Y H:i'),
                $movimientos
            ),
        };
    }

    /**
     * Determina el tipo de reporte según los filtros aplicados.
     */
    private function determinarTipoReporte(array $filtros): string
    {
        if (! empty($filtros['tipo'])) {
            return 'tipo';
        }
        if (! empty($filtros['usuario'])) {
            return 'usuario';
        }

        return 'general';
    }
}
