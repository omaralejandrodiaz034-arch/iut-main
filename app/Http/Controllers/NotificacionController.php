<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = auth()->user()->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function ir(DatabaseNotification $notification)
    {
        $user = auth()->user();

        abort_unless($user->notifications()->where('id', $notification->id)->exists(), 404);

        $notification->markAsRead();

        $actionUrl = $notification->data['action_url'] ?? $notification->data['url'] ?? route('notificaciones.index');

        return redirect($actionUrl);
    }

    public function eliminar(Request $request, DatabaseNotification $notification)
    {
        $user = auth()->user();

        abort_unless($user->notifications()->where('id', $notification->id)->exists(), 404);

        $actionUrl = $notification->data['action_url'] ?? $notification->data['url'] ?? route('notificaciones.index');

        $notification->delete();

        return redirect($actionUrl)->with('success', 'Notificación eliminada.');
    }

    public function marcarTodas(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    public function contar()
    {
        $notificaciones = auth()->user()->unreadNotifications()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
            'ultimas' => $notificaciones->map(fn ($n) => [
                'id' => $n->id,
                'titulo' => $n->data['titulo'] ?? 'Notificación',
                'mensaje' => $n->data['mensaje'] ?? '',
                'tipo' => $n->data['tipo'] ?? null,
                'url' => route('notificaciones.ir', $n),
                'action_url' => $n->data['action_url'] ?? $n->data['url'] ?? route('notificaciones.index'),
                'eliminar_url' => route('notificaciones.eliminar', $n),
                'creada' => $n->created_at->diffForHumans(),
            ])->values(),
        ]);
    }
}
