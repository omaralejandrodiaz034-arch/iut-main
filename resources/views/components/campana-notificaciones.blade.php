@php
    $notificaciones = auth()->user()->unreadNotifications()
        ->orderByDesc('created_at')
        ->limit(10)
        ->get()
        ->map(fn ($n) => [
            'id' => $n->id,
            'titulo' => $n->data['titulo'] ?? 'Notificación',
            'mensaje' => $n->data['mensaje'] ?? '',
            'url' => $n->data['action_url'] ?? route('notificaciones.index'),
            'creada' => $n->created_at->diffForHumans(),
        ])->values();
@endphp

<div
    x-data="notificacionesData()"
    data-unread-count="{{ auth()->user()->unreadNotifications()->count() }}"
    data-notificaciones='@json($notificaciones)'
    data-url="{{ route('notificaciones.contar') }}"
    @click.outside="open = false"
>
    <button @click="open = !open" type="button" class="relative p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition">
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        <template x-if="unreadCount > 0">
            <span x-text="unreadCount" class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center"></span>
        </template>
    </button>

    <div x-show="open" x-transition x-cloak class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Notificaciones</h3>
            <form method="POST" action="{{ route('notificaciones.marcar-todas') }}" class="m-0">
                @csrf
                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Marcar todas como leídas</button>
            </form>
        </div>
        <div class="max-h-80 overflow-y-auto">
            <template x-for="n in ultimas" :key="n.id">
                <div class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                    <a :href="n.action_url || n.url"
                       @click.prevent="marcarYRedirigir(n)"
                       class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800" x-text="n.titulo"></p>
                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="n.mensaje"></p>
                        <p class="text-[10px] text-gray-400 mt-1" x-text="n.creada"></p>
                    </a>
                    <div class="flex items-center gap-2 mt-1">
                        <button type="button"
                                @click="eliminar(n)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium">
                            Eliminar
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="ultimas.length === 0">
                <p class="px-4 py-6 text-sm text-gray-500 text-center">No hay notificaciones nuevas</p>
            </template>
        </div>
        <div class="px-4 py-2 border-t border-gray-100 text-center">
            <a href="{{ route('notificaciones.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todas</a>
        </div>
    </div>
</div>
