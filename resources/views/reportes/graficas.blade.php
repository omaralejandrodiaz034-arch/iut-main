@extends('layouts.base')

@section('title', 'Gráficas')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Reportes', 'url' => route('reportes.index')], ['label' => 'Gráficas']]" />
@endpush

<div class="max-w-7xl mx-auto space-y-8 pb-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gráficas</h1>
            <p class="text-gray-600 mt-1 max-w-2xl">
                Visualización gráfica de los bienes por tipo, estado, registro y desincorporación.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('graficas') }}" class="flex items-center gap-2">
                @php
                    $queryParams = request()->except('granularity');
                @endphp
                @foreach($queryParams as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <span class="text-sm text-gray-500">Granularidad:</span>
                <select name="granularity" onchange="this.form.submit()" class="border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="daily" {{ ($granularity ?? 'monthly') == 'daily' ? 'selected' : '' }}>Diaria</option>
                    <option value="weekly" {{ ($granularity ?? 'monthly') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                    <option value="monthly" {{ ($granularity ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                </select>
            </form>
            <button id="refreshCharts" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recargar
            </button>
        </div>
    </div>

    <!-- Charts Grid -->
    <div id="chartsContainer" class="space-y-8">
        
        <!-- === BIENES === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📦 Bienes del Inventario</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Tipo</h3>
                    <div class="h-64">
                        <canvas id="chartTipo"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Estado</h3>
                    <div class="h-64">
                        <canvas id="chartEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === REGISTRO PROGRESIVO === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📈 Registro Progresivo</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Registro</h3>
                    <div class="h-64">
                        <canvas id="chartRegistro"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes Desincorporados</h3>
                    <div class="h-64">
                        <canvas id="chartDesincorporados"></canvas>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-700">
                    <strong>Nota:</strong> Los gráficos progresivos muestran la acumulación de registros a lo largo del tiempo.
                </p>
            </div>
        </div>

        <!-- === ENTIDADES === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">🏢 Entidades Registradas</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Dependencias</h3>
                    <div class="h-64">
                        <canvas id="chartDependencia"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Unidades Administradoras</h3>
                    <div class="h-64">
                        <canvas id="chartUnidad"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Organismos</h3>
                    <div class="h-64">
                        <canvas id="chartOrganismo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === USUARIOS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">👥 Usuarios</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios por Rol</h3>
                    <div class="h-64">
                        <canvas id="chartUsuariosRol"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios Activos vs Inactivos</h3>
                    <div class="h-64">
                        <canvas id="chartUsuariosActivos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === DEPENDENCIAS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📍 Dependencias</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dependencias por Unidad</h3>
                    <div class="h-64">
                        <canvas id="chartDepsUnidad"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dependencias con/sin responsable</h3>
                    <div class="h-64">
                        <canvas id="chartDepsResponsable"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MOVIMIENTOS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📊 Movimientos</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Movimientos por Tipo</h3>
                    <div class="h-64">
                        <canvas id="chartMovTipo"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 10 Usuarios por Movimientos</h3>
                    <div class="h-64">
                        <canvas id="chartMovUsuario"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Movimientos por Fecha (Progresivo)</h3>
                    <div class="h-64">
                        <canvas id="chartMovFecha"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === VALORES === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">💰 Valores Económicos</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Valor total por Estado (Bs.)</h3>
                    <div class="h-64">
                        <canvas id="chartValorEstado"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 10 Dependencias por Valor (Bs.)</h3>
                    <div class="h-64">
                        <canvas id="chartTopDeps"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === FOTOGRAFÍAS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📷 Fotografías</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cobertura de Fotografías</h3>
                    <div class="h-64">
                        <canvas id="chartFotos"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes sin Movimientos (12 meses)</h3>
                    <div class="h-64">
                        <canvas id="chartMovimientos"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Toast notification -->
    <div id="toast" class="fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300 z-50">
        <div class="bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg text-sm">
            <span id="toastMessage"></span>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    canvas {
        max-width: 100%;
        height: auto !important;
    }
</style>
@endpush

@push('scripts')
<!-- Cargar Chart.js desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== DATOS DEL SERVIDOR =====
    // Pasar los datos de PHP a JavaScript de forma segura
    const serverData = {
        bienesPorTipo: @json($bienesPorTipo ?? []),
        bienesPorEstado: @json($bienesPorEstado ?? []),
        bienesPorRegistro: @json($bienesPorRegistro ?? []),
        bienesDesincorporados: @json($bienesDesincorporados ?? []),
        registroDependencias: @json($registroDependencias ?? []),
        registroUnidades: @json($registroUnidades ?? []),
        registroOrganismos: @json($registroOrganismos ?? []),
        usuariosPorRol: @json($usuariosPorRol ?? []),
        usuariosActivos: @json($usuariosActivos ?? []),
        dependenciasPorUnidad: @json($dependenciasPorUnidad ?? []),
        dependenciasResponsable: @json($dependenciasResponsable ?? []),
        movimientosPorTipo: @json($movimientosPorTipo ?? []),
        movimientosPorUsuario: @json($movimientosPorUsuario ?? []),
        movimientosPorFecha: @json($movimientosPorFecha ?? []),
        valorPorEstado: @json($valorPorEstado ?? []),
        topDependenciasValor: @json($topDependenciasValor ?? []),
        fotoCoverage: @json($fotoCoverage ?? []),
        movimientoCoverage: @json($movimientoCoverage ?? []),
        granularity: @json($granularity ?? 'monthly')
    };

    // ===== CONFIGURACIÓN GLOBAL =====
    const CHART_PALETTE = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
        '#8B5CF6', '#06B6D4', '#F97316', '#6366F1'
    ];

    // Verificar si hay datos
    const hasData = (data) => {
        return data && typeof data === 'object' && Object.keys(data).length > 0;
    };

    // ===== CHART MANAGER =====
    const charts = {};

    // Helper para crear datasets con datos vacíos
    const getEmptyDataHandler = (defaultLabel = 'Sin datos') => {
        return {
            labels: [defaultLabel],
            datasets: [{
                data: [1],
                backgroundColor: ['#9CA3AF']
            }]
        };
    };

    // ===== DEFINICIÓN DE GRÁFICOS =====
    
    // 1. Bienes por Tipo (Pie)
    if (document.getElementById('chartTipo')) {
        const data = serverData.bienesPorTipo;
        charts.chartTipo = new Chart(document.getElementById('chartTipo'), {
            type: 'pie',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: CHART_PALETTE,
                    borderWidth: 0
                }]
            } : getEmptyDataHandler('Sin datos de bienes por tipo'),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw} bienes` } }
                }
            }
        });
    }

    // 2. Bienes por Estado (Bar)
    if (document.getElementById('chartEstado')) {
        const data = serverData.bienesPorEstado;
        charts.chartEstado = new Chart(document.getElementById('chartEstado'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Cantidad',
                    data: Object.values(data),
                    backgroundColor: '#3B82F6',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } } }
            }
        });
    }

    // 3. Bienes por Registro (Line - Progresivo)
    if (document.getElementById('chartRegistro')) {
        const data = serverData.bienesPorRegistro;
        charts.chartRegistro = new Chart(document.getElementById('chartRegistro'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Cantidad Acumulada',
                    data: Object.values(data),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#10B981'
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}` } } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Cantidad Acumulada' } } }
            }
        });
    }

    // 4. Bienes Desincorporados (Line)
    if (document.getElementById('chartDesincorporados')) {
        const data = serverData.bienesDesincorporados;
        charts.chartDesincorporados = new Chart(document.getElementById('chartDesincorporados'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Cantidad Desincorporada',
                    data: Object.values(data),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#EF4444'
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } } }
            }
        });
    }

    // 5. Registro de Dependencias (Line)
    if (document.getElementById('chartDependencia')) {
        const data = serverData.registroDependencias;
        charts.chartDependencia = new Chart(document.getElementById('chartDependencia'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Dependencias',
                    data: Object.values(data),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // 6. Registro de Unidades (Line)
    if (document.getElementById('chartUnidad')) {
        const data = serverData.registroUnidades;
        charts.chartUnidad = new Chart(document.getElementById('chartUnidad'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Unidades',
                    data: Object.values(data),
                    borderColor: '#06B6D4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // 7. Registro de Organismos (Line)
    if (document.getElementById('chartOrganismo')) {
        const data = serverData.registroOrganismos;
        charts.chartOrganismo = new Chart(document.getElementById('chartOrganismo'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Organismos',
                    data: Object.values(data),
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // 8. Usuarios por Rol (Doughnut)
    if (document.getElementById('chartUsuariosRol')) {
        const data = serverData.usuariosPorRol;
        charts.chartUsuariosRol = new Chart(document.getElementById('chartUsuariosRol'), {
            type: 'doughnut',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: CHART_PALETTE,
                    borderWidth: 0
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 9. Usuarios Activos vs Inactivos (Doughnut)
    if (document.getElementById('chartUsuariosActivos')) {
        const data = serverData.usuariosActivos;
        charts.chartUsuariosActivos = new Chart(document.getElementById('chartUsuariosActivos'), {
            type: 'doughnut',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#10B981', '#EF4444'],
                    borderWidth: 0
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 10. Dependencias por Unidad (Bar horizontal)
    if (document.getElementById('chartDepsUnidad')) {
        const data = serverData.dependenciasPorUnidad;
        charts.chartDepsUnidad = new Chart(document.getElementById('chartDepsUnidad'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Dependencias',
                    data: Object.values(data),
                    backgroundColor: '#8B5CF6',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, title: { display: true, text: 'Cantidad' } } }
            }
        });
    }

    // 11. Dependencias con/sin responsable (Doughnut)
    if (document.getElementById('chartDepsResponsable')) {
        const data = serverData.dependenciasResponsable;
        charts.chartDepsResponsable = new Chart(document.getElementById('chartDepsResponsable'), {
            type: 'doughnut',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#3B82F6', '#EF4444'],
                    borderWidth: 0
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 12. Movimientos por Tipo (Bar)
    if (document.getElementById('chartMovTipo')) {
        const data = serverData.movimientosPorTipo;
        charts.chartMovTipo = new Chart(document.getElementById('chartMovTipo'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Movimientos',
                    data: Object.values(data),
                    backgroundColor: '#06B6D4',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } } }
            }
        });
    }

    // 13. Top 10 Usuarios por Movimientos (Bar horizontal)
    if (document.getElementById('chartMovUsuario')) {
        const data = serverData.movimientosPorUsuario;
        charts.chartMovUsuario = new Chart(document.getElementById('chartMovUsuario'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data).slice(0, 10),
                datasets: [{
                    label: 'Movimientos',
                    data: Object.values(data).slice(0, 10),
                    backgroundColor: '#F97316',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, title: { display: true, text: 'Cantidad' } } }
            }
        });
    }

    // 14. Movimientos por Fecha (Line)
    if (document.getElementById('chartMovFecha')) {
        const data = serverData.movimientosPorFecha;
        charts.chartMovFecha = new Chart(document.getElementById('chartMovFecha'), {
            type: 'line',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Movimientos',
                    data: Object.values(data),
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true } } }
        });
    }

    // 15. Valor por Estado (Bar)
    if (document.getElementById('chartValorEstado')) {
        const data = serverData.valorPorEstado;
        charts.chartValorEstado = new Chart(document.getElementById('chartValorEstado'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Valor (Bs.)',
                    data: Object.values(data),
                    backgroundColor: '#10B981',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw.toLocaleString('es-VE')} Bs.` } } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Valor (Bs.)' } } }
            }
        });
    }

    // 16. Top Dependencias por Valor (Bar horizontal)
    if (document.getElementById('chartTopDeps')) {
        const data = serverData.topDependenciasValor;
        charts.chartTopDeps = new Chart(document.getElementById('chartTopDeps'), {
            type: 'bar',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Valor (Bs.)',
                    data: Object.values(data),
                    backgroundColor: '#8B5CF6',
                    borderRadius: 8
                }]
            } : getEmptyDataHandler(),
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw.toLocaleString('es-VE')} Bs.` } } },
                scales: { x: { beginAtZero: true, title: { display: true, text: 'Valor (Bs.)' } } }
            }
        });
    }

    // 17. Cobertura de Fotografías (Doughnut)
    if (document.getElementById('chartFotos')) {
        const data = serverData.fotoCoverage;
        charts.chartFotos = new Chart(document.getElementById('chartFotos'), {
            type: 'doughnut',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 18. Bienes sin Movimientos (Doughnut)
    if (document.getElementById('chartMovimientos')) {
        const data = serverData.movimientoCoverage;
        charts.chartMovimientos = new Chart(document.getElementById('chartMovimientos'), {
            type: 'doughnut',
            data: hasData(data) ? {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#F59E0B', '#EF4444'],
                    borderWidth: 0
                }]
            } : getEmptyDataHandler(),
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // ===== FUNCIONES DE UTILIDAD =====
    const showToast = (message) => {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        if (toast && toastMessage) {
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }
    };

    const refreshCharts = () => {
        Object.values(charts).forEach(chart => {
            if (chart && typeof chart.update === 'function') {
                chart.update();
            }
        });
        showToast('Gráficas actualizadas correctamente');
    };

    // ===== EVENT LISTENERS =====
    const refreshBtn = document.getElementById('refreshCharts');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshCharts);
    }

    // Mensaje de éxito
    console.log('✅ Todas las gráficas fueron inicializadas correctamente');
});
</script>
@endpush