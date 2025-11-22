<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Eliminado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;



class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::with(['bien', 'usuario', 'subject'])->orderByDesc('fecha')->paginate(10);

        $eliminados = null;
        if (Auth::check() && Auth::user() instanceof Usuario && Auth::user()->isAdmin()) {
            $eliminados = Eliminado::orderByDesc('deleted_at')->paginate(10, ['*'], 'eliminados_page');

            $userIds = $eliminados->pluck('deleted_by')->unique()->filter()->values()->all();
            $users = !empty($userIds)
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

        if (request()->wantsJson()) {
            return response()->json(['movimientos' => $movimientos, 'eliminados' => $eliminados]);
        }

        return view('movimientos.index', compact('movimientos', 'eliminados'));
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
            'historialMovimientos' => fn($q) => $q->orderBy('fecha', 'desc'),
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

}


