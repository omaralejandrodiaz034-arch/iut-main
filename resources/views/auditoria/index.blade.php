@extends('layouts.base')

@section('title', 'Log de Auditoría')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Auditoría']]" />
@endpush

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">🔍 Log de Auditoría</h1>
        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full font-mono">
            {{ $registros->total() }} registros
        </span>
    </div>

    {{-- Filtros --}}
    <form id="filtrosForm" method="GET" class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Tabla</label>
                <select name="tabla" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#800020]">
                    <option value="">Todas</option>
                    @foreach($tablas as $t)
                    <option value="{{ $t }}" @selected(request('tabla') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Operación</label>
                <select name="operacion" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#800020]">
                    <option value="">Todas</option>
                    @foreach($operaciones as $op)
                    <option value="{{ $op }}" @selected(request('operacion') == $op)>{{ $op }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#800020]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#800020]">
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="px-5 py-2 bg-[#800020] text-white rounded-xl text-sm font-bold hover:bg-[#5a0016] transition">Filtrar</button>
            <a href="{{ route('auditoria.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition">Limpiar</a>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Fecha</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Usuario</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Operación</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Tabla</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Registro ID</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Descripción</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($registros as $reg)
                    @php
                    $opColor = match($reg->operacion) {
                        'CREATE', 'create' => 'bg-green-100 text-green-700',
                        'UPDATE', 'update' => 'bg-yellow-100 text-yellow-700',
                        'DELETE', 'delete' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleDetalle({{ $reg->id }})">
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs whitespace-nowrap">
                            {{ $reg->created_at?->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 text-gray-800 font-medium text-xs">
                            {{ optional($reg->usuario)->nombre }} {{ optional($reg->usuario)->apellido }}
                            <span class="text-gray-400 block font-mono">{{ optional($reg->usuario)->cedula }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $opColor }}">
                                {{ strtoupper($reg->operacion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $reg->tabla }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $reg->registro_id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">{{ $reg->descripcion }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $reg->ip_address }}</td>
                    </tr>
                    {{-- Detalle expandible --}}
                    <tr id="detalle-{{ $reg->id }}" class="hidden bg-gray-50">
                        <td colspan="7" class="px-4 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                @if($reg->valores_anteriores)
                                <div>
                                    <p class="font-bold text-red-600 mb-1">Valores Anteriores:</p>
                                    <pre class="bg-red-50 border border-red-100 rounded-lg p-3 overflow-auto max-h-40 font-mono text-[11px]">{{ json_encode($reg->valores_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                                @if($reg->valores_nuevos)
                                <div>
                                    <p class="font-bold text-green-600 mb-1">Valores Nuevos:</p>
                                    <pre class="bg-green-50 border border-green-100 rounded-lg p-3 overflow-auto max-h-40 font-mono text-[11px]">{{ json_encode($reg->valores_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            No se encontraron registros de auditoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($registros->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $registros->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleDetalle(id) {
    const row = document.getElementById('detalle-' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
@endpush

@endsection
