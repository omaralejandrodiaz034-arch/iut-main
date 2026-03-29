@extends('layouts.base')

@section('title', 'Dashboard | Sistema de Bienes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Dashboard']]" />
@endpush

{{-- ── Encabezado ──────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Panel de Control</h1>
        <p class="text-gray-500 text-sm mt-1">Resumen del inventario de bienes patrimoniales</p>
    </div>
    <span class="text-xs text-gray-400 font-medium">Actualizado: {{ now()->format('d/m/Y H:i') }}</span>
</div>

{{-- ── KPIs principales ────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
    $kpis = [
        ['label' => 'Total Bienes',    'value' => number_format($totalBienes),        'icon' => '📦', 'color' => 'bg-blue-50 border-blue-200',     'text' => 'text-blue-700',  'href' => route('bienes.index')],
        ['label' => 'Activos',         'value' => number_format($totalActivos),        'icon' => '✅', 'color' => 'bg-green-50 border-green-200',   'text' => 'text-green-700', 'href' => route('bienes.index', ['estado[]' => 'ACTIVO'])],
        ['label' => 'En Mantenimiento','value' => number_format($totalMantenimiento), 'icon' => '🔧', 'color' => 'bg-yellow-50 border-yellow-200', 'text' => 'text-yellow-700','href' => route('bienes.index', ['estado[]' => 'EN_MANTENIMIENTO'])],
        ['label' => 'Extraviados',     'value' => number_format($totalExtraviados),   'icon' => '⚠️', 'color' => 'bg-red-50 border-red-200',       'text' => 'text-red-700',   'href' => route('bienes.index', ['estado[]' => 'EXTRAVIADO'])],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <a href="{{ $kpi['href'] }}" class="block border rounded-xl p-5 {{ $kpi['color'] }} hover:shadow-md transition-shadow">
        <div class="text-2xl mb-2">{{ $kpi['icon'] }}</div>
        <div class="text-2xl font-black {{ $kpi['text'] }}">{{ $kpi['value'] }}</div>
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mt-1">{{ $kpi['label'] }}</div>
    </a>
    @endforeach
</div>

{{-- ── Segunda fila de KPIs ────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
    $kpis2 = [
        ['label' => 'Organismos',   'value' => $totalOrganismos,   'icon' => '🏛️', 'href' => route('organismos.index')],
        ['label' => 'Unidades',     'value' => $totalUnidades,     'icon' => '🏢', 'href' => route('unidades.index')],
        ['label' => 'Dependencias', 'value' => $totalDependencias, 'icon' => '📂', 'href' => route('dependencias.index')],
        ['label' => 'Usuarios',     'value' => $totalUsuarios,     'icon' => '👥', 'href' => route('usuarios.index')],
    ];
    @endphp
    @foreach($kpis2 as $k)
    <a href="{{ $k['href'] }}" class="block border border-gray-200 rounded-xl p-5 bg-white hover:shadow-md transition-shadow">
        <div class="text-2xl mb-2">{{ $k['icon'] }}</div>
        <div class="text-2xl font-black text-gray-800">{{ $k['value'] }}</div>
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-1">{{ $k['label'] }}</div>
    </a>
    @endforeach
</div>

{{-- ── Valor del inventario ─────────────────────────────────────────── --}}
<div class="bg-gradient-to-r from-[#800020] to-[#5a0016] rounded-2xl p-6 mb-8 text-white">
    <p class="text-sm font-semibold opacity-80 uppercase tracking-widest mb-1">Valor Total del Inventario Activo</p>
    <p class="text-4xl font-black">Bs. {{ number_format($valorTotal, 2, ',', '.') }}</p>
    <p class="text-xs opacity-60 mt-2">Excluye bienes desincorporados</p>
</div>

{{-- ── Gráficas y Tablas ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Por Estado --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 mb-4">Distribución por Estado</h3>
        <div class="space-y-3">
            @php
            $coloresEstado = ['Activo' => '#22c55e', 'Dañado' => '#f97316', 'En mantenimiento' => '#eab308', 'En camino' => '#3b82f6', 'Extraviado' => '#ef4444', 'Desincorporado' => '#6b7280'];
            @endphp
            @foreach($porEstado as $label => $count)
            @php $pct = $totalBienes > 0 ? round($count / $totalBienes * 100) : 0; $color = $coloresEstado[$label] ?? '#800020'; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">{{ $label }}</span>
                    <span class="text-gray-500 font-mono">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Por Tipo --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 mb-4">Distribución por Tipo de Bien</h3>
        <div class="space-y-3">
            @php
            $coloresTipo = ['Electrónico' => '#6366f1', 'Mobiliario' => '#f59e0b', 'Vehículo' => '#10b981', 'Inmueble' => '#8b5cf6', 'Otros' => '#64748b'];
            @endphp
            @foreach($porTipo as $label => $count)
            @php $pct = $totalBienes > 0 ? round($count / $totalBienes * 100) : 0; $color = $coloresTipo[$label] ?? '#800020'; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">{{ $label }}</span>
                    <span class="text-gray-500 font-mono">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Top dependencias --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 mb-4">Top 5 Dependencias con más Bienes</h3>
        @forelse($topDependencias as $dep)
        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
            <div>
                <p class="text-sm font-semibold text-gray-800">{{ $dep->nombre }}</p>
                <p class="text-xs text-gray-400">{{ optional($dep->unidadAdministradora)->nombre ?? '—' }}</p>
            </div>
            <span class="text-lg font-black text-[#800020]">{{ $dep->bienes_count }}</span>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Sin datos</p>
        @endforelse
    </div>

    {{-- Últimos movimientos --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-800">Últimos Movimientos</h3>
            <a href="{{ route('movimientos.index') }}" class="text-xs text-[#800020] font-semibold hover:underline">Ver todos →</a>
        </div>
        <div class="space-y-3">
            @forelse($ultimosMovimientos as $mov)
            <div class="flex items-start gap-3 py-1">
                <span class="text-lg flex-shrink-0">📄</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $mov->tipo }}</p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ optional($mov->bien)->codigo }} • {{ optional($mov->usuario)->nombre }}
                    </p>
                </div>
                <span class="text-[11px] text-gray-400 flex-shrink-0">{{ optional($mov->fecha)->diffForHumans() }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Sin movimientos recientes</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Bienes registrados por mes ──────────────────────────────────── --}}
@if($bienesPorMes->isNotEmpty())
<div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-8">
    <h3 class="text-base font-bold text-gray-800 mb-6">Bienes Registrados (últimos 12 meses)</h3>
    @php $maxVal = $bienesPorMes->max() ?: 1; @endphp
    <div class="flex items-end gap-2 h-32">
        @foreach($bienesPorMes as $mes => $total)
        @php $pct = round($total / $maxVal * 100); @endphp
        <div class="flex-1 flex flex-col items-center gap-1 group">
            <span class="text-[10px] text-gray-500 font-mono opacity-0 group-hover:opacity-100 transition-opacity">{{ $total }}</span>
            <div class="w-full rounded-t-lg bg-[#800020] transition-all duration-300 hover:bg-[#5a0016]" style="height: {{ max($pct, 4) }}%;"></div>
            <span class="text-[9px] text-gray-400 font-mono">{{ substr($mes, 5) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
