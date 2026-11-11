@extends('layouts.base')

@section('title', 'Dashboard | Sistema de Bienes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Dashboard']]" />
@endpush

<div class="rounded-[28px] border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-slate-400">Sistema</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-800">Panel de Control</h1>
        </div>
        <span class="text-xs font-medium text-slate-500">Actualizado: {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    <div class="space-y-6 p-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @php
                $kpis = [
                    ['label' => 'ACTIVOS', 'value' => number_format($totalActivos), 'icon' => '✓', 'bg' => 'bg-[#e9f9ef]', 'border' => 'border-[#bfead0]', 'color' => 'text-[#166534]', 'href' => route('bienes.index', ['estado[]' => 'ACTIVO'])],
                    ['label' => 'EN MANTENIMIENTO', 'value' => number_format($totalMantenimiento), 'icon' => '🛠', 'bg' => 'bg-[#fdf7d9]', 'border' => 'border-[#f0e29a]', 'color' => 'text-[#8a6a00]', 'href' => route('bienes.index', ['estado[]' => 'EN_MANTENIMIENTO'])],
                    ['label' => 'EXTRAVIADOS', 'value' => number_format($totalExtraviados), 'icon' => '⚠', 'bg' => 'bg-[#fdecec]', 'border' => 'border-[#f3c3c3]', 'color' => 'text-[#9e1c1c]', 'href' => route('bienes.index', ['estado[]' => 'EXTRAVIADO'])],
                    ['label' => 'TOTAL BIENES', 'value' => number_format($totalBienes), 'icon' => '▣', 'bg' => 'bg-[#eef3ff]', 'border' => 'border-[#c9d8ff]', 'color' => 'text-[#29428d]', 'href' => route('bienes.index')],
                ];
            @endphp

            @foreach($kpis as $kpi)
                <a href="{{ $kpi['href'] }}" class="group block rounded-[20px] border {{ $kpi['border'] }} {{ $kpi['bg'] }} p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200/70 bg-white/70 text-2xl shadow-sm">{{ $kpi['icon'] }}</div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">{{ $kpi['label'] }}</span>
                    </div>
                    <div class="mt-6 text-4xl font-black {{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
                </a>
            @endforeach
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @php
                $kpis2 = [
                    ['label' => 'ORGANISMOS', 'value' => $totalOrganismos, 'icon' => '🏛', 'href' => route('organismos.index')],
                    ['label' => 'UNIDADES', 'value' => $totalUnidades, 'icon' => '🏢', 'href' => route('unidades.index')],
                    ['label' => 'DEPENDENCIAS', 'value' => $totalDependencias, 'icon' => '📁', 'href' => route('dependencias.index')],
                    ['label' => 'USUARIOS', 'value' => $totalUsuarios, 'icon' => '👥', 'href' => route('usuarios.index')],
                ];
            @endphp

            @foreach($kpis2 as $k)
                <a href="{{ $k['href'] }}" class="group block rounded-[20px] border border-slate-200 bg-slate-50 p-5 text-center transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white hover:shadow-sm">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-2xl shadow-sm">{{ $k['icon'] }}</div>
                    <div class="mt-4 text-4xl font-black text-slate-800">{{ $k['value'] }}</div>
                    <div class="mt-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $k['label'] }}</div>
                </a>
            @endforeach
        </div>

        <div class="rounded-[20px] bg-gradient-to-r from-[#7a081d] to-[#510817] px-6 py-5 text-white shadow-lg shadow-[#510817]/20">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-red-100/80">Valor total del inventario activo</p>
            <p class="mt-2 text-3xl font-black tracking-tight">Bs. {{ number_format($valorTotal, 2, ',', '.') }}</p>
        </div>
    </div>
</div>

@if($bienesPorMes->isNotEmpty())
    <div class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-800">Bienes Registrados (últimos 12 meses)</h3>
        @php $maxVal = $bienesPorMes->max() ?: 1; @endphp
        <div class="mt-6 flex h-32 items-end gap-2">
            @foreach($bienesPorMes as $mes => $total)
                @php $pct = round($total / $maxVal * 100); @endphp
                <div class="group flex flex-1 flex-col items-center gap-2">
                    <span class="text-[10px] text-slate-500 opacity-0 transition-opacity group-hover:opacity-100">{{ $total }}</span>
                    <div class="w-full rounded-t-xl bg-[#800020] transition-all duration-300 hover:bg-[#5a0016]" style="height: {{ max($pct, 8) }}%;"></div>
                    <span class="text-[9px] text-slate-400 font-medium">{{ substr($mes, 5) }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
