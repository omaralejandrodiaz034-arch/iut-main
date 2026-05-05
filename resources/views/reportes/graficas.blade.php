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
            <span class="text-sm text-gray-500">Granularidad:</span>
            <select id="granularitySelect" class="border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="daily">Diaria</option>
                <option value="weekly">Semanal</option>
                <option value="monthly" selected>Mensual</option>
            </select>
            <button id="refreshCharts" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recargar
            </button>
        </div>
    </div>

    <!-- Skeleton loader container -->
    <div id="chartsLoader" class="hidden">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @for ($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-xl border border-gray-200 p-6 animate-pulse">
                <div class="h-6 bg-gray-200 rounded w-3/4 mb-4"></div>
                <div class="h-48 bg-gray-100 rounded"></div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Charts Grid -->
    <div id="chartsContainer" class="space-y-8">
        
        <!-- === BIENES === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📦 Bienes del Inventario</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartTipo" data-type="pie" data-data="{{ json_encode($bienesPorTipo ?? []) }}" data-palette="CHART_PALETTE">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Tipo</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartTipo"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartEstado" data-type="bar" data-data="{{ json_encode($bienesPorEstado ?? []) }}" data-color="#3B82F6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Estado</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === REGISTRO PROGRESIVO === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📈 Registro Progresivo</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartRegistro" data-type="line" data-data="{{ json_encode($bienesPorRegistro ?? []) }}" data-color="#10B981" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes por Registro</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartRegistro"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartDesincorporados" data-type="line" data-data="{{ json_encode($bienesDesincorporados ?? []) }}" data-color="#EF4444" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes Desincorporados</h3>
                    <div class="flex-1 relative">
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
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartDependencia" data-type="line" data-data="{{ json_encode($registroDependencias ?? []) }}" data-color="#8B5CF6" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Dependencias</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartDependencia"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartUnidad" data-type="line" data-data="{{ json_encode($registroUnidades ?? []) }}" data-color="#06B6D4" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Unidades Administradoras</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartUnidad"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartOrganismo" data-type="line" data-data="{{ json_encode($registroOrganismos ?? []) }}" data-color="#F59E0B" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Organismos</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartOrganismo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === USUARIOS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">👥 Usuarios</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartUsuariosRol" data-type="doughnut" data-data="{{ json_encode($usuariosPorRol ?? []) }}" data-palette="CHART_PALETTE">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios por Rol</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartUsuariosRol"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartUsuariosActivos" data-type="doughnut" data-data="{{ json_encode($usuariosActivos ?? []) }}" data-colors='["#10B981", "#EF4444"]'>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usuarios Activos vs Inactivos</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartUsuariosActivos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === DEPENDENCIAS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📍 Dependencias</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartDepsUnidad" data-type="bar" data-data="{{ json_encode($dependenciasPorUnidad ?? []) }}" data-horizontal="true" data-color="#8B5CF6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dependencias por Unidad</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartDepsUnidad"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartDepsResponsable" data-type="doughnut" data-data="{{ json_encode($dependenciasResponsable ?? []) }}" data-colors='["#3B82F6", "#EF4444"]'>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dependencias con/sin responsable</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartDepsResponsable"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === MOVIMIENTOS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📊 Movimientos</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartMovTipo" data-type="bar" data-data="{{ json_encode($movimientosPorTipo ?? []) }}" data-color="#06B6D4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Movimientos por Tipo</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartMovTipo"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartMovUsuario" data-type="bar" data-data="{{ json_encode($movimientosPorUsuario ?? []) }}" data-horizontal="true" data-color="#F97316">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 10 Usuarios por Movimientos</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartMovUsuario"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartMovFecha" data-type="line" data-data="{{ json_encode($movimientosPorFecha ?? []) }}" data-color="#F59E0B" data-granularity="{{ $granularity ?? 'monthly' }}">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Movimientos por Fecha (Progresivo)</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartMovFecha"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === VALORES === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">💰 Valores Económicos</h2>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartValorEstado" data-type="bar" data-data="{{ json_encode($valorPorEstado ?? []) }}" data-currency="true" data-color="#10B981">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Valor total por Estado (Bs.)</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartValorEstado"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartTopDeps" data-type="bar" data-data="{{ json_encode($topDependenciasValor ?? []) }}" data-horizontal="true" data-currency="true" data-color="#8B5CF6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 10 Dependencias por Valor (Bs.)</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartTopDeps"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- === FOTOGRAFÍAS === -->
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">📷 Fotografías</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartFotos" data-type="doughnut" data-data="{{ json_encode($fotoCoverage ?? []) }}" data-colors='["#10B981", "#F59E0B", "#EF4444"]'>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cobertura de Fotografías</h3>
                    <div class="flex-1 relative">
                        <canvas id="chartFotos"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col" id="card-chartMovimientos" data-type="doughnut" data-data="{{ json_encode($movimientoCoverage ?? []) }}" data-colors='["#F59E0B", "#EF4444"]'>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bienes sin Movimientos (12 meses)</h3>
                    <div class="flex-1 relative">
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

    @push('scripts')
<script>
    // ===== CONFIGURACIÓN GLOBAL =====
    const CHART_PALETTE = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
        '#8B5CF6', '#06B6D4', '#F97316', '#6366F1'
    ];

    // Formatters
    const formatNumber = (v, currency = false) => {
        if (v === null || v === undefined) return '-';
        try {
            const num = Number(v) || 0;
            if (currency) {
                return num.toLocaleString('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' Bs.';
            }
            return num.toLocaleString('es-VE');
        } catch (e) {
            return v;
        }
    };

    // Common chart options
    const commonOptions = ({ yLabel = '', currency = false, stacked = false, indexAxis = 'x', granularity = 'monthly' } = {}) => {
        const granularityLabel = granularity === 'daily' ? 'Diaria' :
                                 granularity === 'weekly' ? 'Semanal' : 'Mensual';
        return {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis,
            interaction: { intersect: false, mode: 'nearest' },
            elements: {
                bar: { maxBarThickness: 60, borderWidth: 0 },
                line: { tension: 0.25 }
            },
            layout: { padding: 8 },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => currency ? formatNumber(val, true) : formatNumber(val, false),
                        maxTicksLimit: 8,
                        autoSkip: true
                    },
                    title: yLabel ? { display: true, text: yLabel } : undefined
                },
                x: {
                    beginAtZero: true,
                    ticks: { maxRotation: 45, minRotation: 0, autoSkip: true }
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const v = ctx.raw;
                            return `${ctx.dataset.label || ''}: ${currency ? formatNumber(v, true) : formatNumber(v, false)}`;
                        }
                    }
                },
                title: { display: false }
            }
        };
    };

    // ===== CHART MANAGER =====
    const ChartManager = {
        charts: new Map(),

        create(id, config) {
            const canvas = document.getElementById(id);
            if (!canvas) {
                console.warn(`Canvas #${id} no encontrado`);
                return null;
            }
            
            // Destruir chart existente
            if (this.charts.has(id)) {
                this.charts.get(id).destroy();
            }

            const ctx = canvas.getContext('2d');
            const chart = new Chart(ctx, config);
            this.charts.set(id, chart);
            return chart;
        },

        update(id, data) {
            const chart = this.charts.get(id);
            if (chart) {
                chart.data = data;
                chart.update('none');
            }
        },

        destroy(id) {
            const chart = this.charts.get(id);
            if (chart) {
                chart.destroy();
                this.charts.delete(id);
            }
        },

        destroyAll() {
            this.charts.forEach(chart => chart.destroy());
            this.charts.clear();
        }
    };

    // ===== TOAST NOTIFICATIONS =====
    const showToast = (message, type = 'info') => {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        if (!toast || !toastMessage) return;
        
        toastMessage.textContent = message;
        toast.className = `fixed bottom-4 right-4 transform translate-y-0 opacity-100 transition-all duration-300 z-50`;
        
        setTimeout(() => {
            toast.className = `fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300 z-50`;
        }, 3000);
    };

    // ===== CHART DEFINITIONS =====
    const chartDefinitions = {
        chartTipo: () => ({
            type: 'pie',
            data: {
                labels: Object.keys({{ json_encode($bienesPorTipo ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($bienesPorTipo ?? ['Sin datos' => 0]) }}),
                    backgroundColor: CHART_PALETTE
                }]
            },
            options: {
                ...commonOptions(),
                plugins: { ...commonOptions().plugins, title: { display: true, text: 'Distribución de Bienes por Tipo' } }
            }
        }),

        chartEstado: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($bienesPorEstado ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Cantidad',
                    data: Object.values({{ json_encode($bienesPorEstado ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#3B82F6',
                    maxBarThickness: 40
                }]
            },
            options: commonOptions()
        }),

        chartRegistro: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($bienesPorRegistro ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Cantidad Acumulada',
                        data: Object.values({{ json_encode($bienesPorRegistro ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#10B98133',
                        borderColor: '#10B981',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Registro progresivo de Bienes (${label})` } }
                }
            };
        },

        chartDesincorporados: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($bienesDesincorporados ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Cantidad Desincorporada',
                        data: Object.values({{ json_encode($bienesDesincorporados ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#EF444433',
                        borderColor: '#EF4444',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Bienes Desincorporados (${label})` } }
                }
            };
        },

        chartDependencia: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($registroDependencias ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Dependencias Registradas',
                        data: Object.values({{ json_encode($registroDependencias ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#8B5CF633',
                        borderColor: '#8B5CF6',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Registro de Dependencias (${label})` } }
                }
            };
        },

        chartUnidad: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($registroUnidades ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Unidades Registradas',
                        data: Object.values({{ json_encode($registroUnidades ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#06B6D433',
                        borderColor: '#06B6D4',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Registro de Unidades Administradoras (${label})` } }
                }
            };
        },

        chartOrganismo: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($registroOrganismos ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Organismos Registrados',
                        data: Object.values({{ json_encode($registroOrganismos ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#F59E0B33',
                        borderColor: '#F59E0B',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Registro de Organismos (${label})` } }
                }
            };
        },

        chartUsuariosRol: () => ({
            type: 'doughnut',
            data: {
                labels: Object.keys({{ json_encode($usuariosPorRol ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($usuariosPorRol ?? ['Sin datos' => 0]) }}),
                    backgroundColor: CHART_PALETTE
                }]
            },
            options: {
                ...commonOptions(),
                plugins: { ...commonOptions().plugins, title: { display: true, text: 'Usuarios por Rol' } }
            }
        }),

        chartUsuariosActivos: () => ({
            type: 'doughnut',
            data: {
                labels: Object.keys({{ json_encode($usuariosActivos ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($usuariosActivos ?? ['Sin datos' => 0]) }}),
                    backgroundColor: ['#10B981', '#EF4444']
                }]
            },
            options: {
                ...commonOptions(),
                plugins: { ...commonOptions().plugins, title: { display: true, text: 'Usuarios Activos vs Inactivos' } }
            }
        }),

        chartDepsUnidad: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($dependenciasPorUnidad ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Número de Dependencias',
                    data: Object.values({{ json_encode($dependenciasPorUnidad ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#8B5CF6',
                    barThickness: 18
                }]
            },
            options: commonOptions({ indexAxis: 'y' })
        }),

        chartDepsResponsable: () => ({
            type: 'doughnut',
            data: {
                labels: Object.keys({{ json_encode($dependenciasResponsable ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($dependenciasResponsable ?? ['Sin datos' => 0]) }}),
                    backgroundColor: ['#3B82F6', '#EF4444']
                }]
            },
            options: {
                ...commonOptions(),
                plugins: { ...commonOptions().plugins, title: { display: true, text: 'Dependencias: con/sin responsable' } }
            }
        }),

        chartMovTipo: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($movimientosPorTipo ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Cantidad',
                    data: Object.values({{ json_encode($movimientosPorTipo ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#06B6D4',
                    maxBarThickness: 60
                }]
            },
            options: commonOptions()
        }),

        chartMovUsuario: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($movimientosPorUsuario ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Movimientos',
                    data: Object.values({{ json_encode($movimientosPorUsuario ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#F97316',
                    barThickness: 16
                }]
            },
            options: commonOptions({ indexAxis: 'y' })
        }),

        chartMovFecha: () => {
            const gran = {{ json_encode($granularity ?? 'monthly') }};
            const label = gran === 'daily' ? 'Diaria' : gran === 'weekly' ? 'Semanal' : 'Mensual';
            return {
                type: 'line',
                data: {
                    labels: Object.keys({{ json_encode($movimientosPorFecha ?? ['Sin datos' => 0]) }}),
                    datasets: [{
                        label: 'Movimientos Acumulados',
                        data: Object.values({{ json_encode($movimientosPorFecha ?? ['Sin datos' => 0]) }}),
                        backgroundColor: '#F59E0B33',
                        borderColor: '#F59E0B',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    ...commonOptions({ granularity: gran }),
                    plugins: { ...commonOptions().plugins, title: { display: true, text: `Movimientos por Fecha (${label})` } }
                }
            };
        },

        chartValorEstado: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($valorPorEstado ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Valor (Bs.)',
                    data: Object.values({{ json_encode($valorPorEstado ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#10B981',
                    maxBarThickness: 60
                }]
            },
            options: commonOptions({ currency: true })
        }),

        chartTopDeps: () => ({
            type: 'bar',
            data: {
                labels: Object.keys({{ json_encode($topDependenciasValor ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    label: 'Valor (Bs.)',
                    data: Object.values({{ json_encode($topDependenciasValor ?? ['Sin datos' => 0]) }}),
                    backgroundColor: '#8B5CF6',
                    maxBarThickness: 60
                }]
            },
            options: commonOptions({ currency: true, indexAxis: 'y' })
        }),

        chartFotos: () => ({
            type: 'doughnut',
            data: {
                labels: Object.keys({{ json_encode($fotoCoverage ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($fotoCoverage ?? ['Sin datos' => 0]) }}),
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                }]
            },
            options: { ...commonOptions(), plugins: { ...commonOptions().plugins, title: { display: true, text: 'Cobertura de Fotografías' } } }
        }),

        chartMovimientos: () => ({
            type: 'doughnut',
            data: {
                labels: Object.keys({{ json_encode($movimientoCoverage ?? ['Sin datos' => 0]) }}),
                datasets: [{
                    data: Object.values({{ json_encode($movimientoCoverage ?? ['Sin datos' => 0]) }}),
                    backgroundColor: ['#F59E0B', '#EF4444']
                }]
            },
            options: {
                ...commonOptions(),
                plugins: { ...commonOptions().plugins, title: { display: true, text: 'Bienes sin Movimientos (12 meses)' } }
            }
        })
    };

    // ===== INITIALIZATION =====
    const initCharts = () => {
        const loader = document.getElementById('chartsLoader');
        const container = document.getElementById('chartsContainer');

        if (loader) loader.classList.remove('hidden');
        if (container) container.style.display = 'none';

        requestAnimationFrame(() => {
            ChartManager.destroyAll();

            Object.keys(chartDefinitions).forEach(id => {
                try {
                    const config = chartDefinitions[id]();
                    ChartManager.create(id, config);
                } catch (err) {
                    console.error(`Error creando chart ${id}:`, err);
                }
            });

            setTimeout(() => {
                if (loader) loader.classList.add('hidden');
                if (container) container.style.display = 'block';
            }, 200);
        });
    };

    // ===== GRANULARIDAD =====
    const updateGranularity = (value) => {
        const params = new URLSearchParams(window.location.search);
        params.set('granularity', value);
        window.location.search = params.toString();
    };

    // ===== REFRESH =====
    const refreshCharts = () => {
        initCharts();
        showToast('Gráficas actualizadas correctamente', 'success');
    };

    // ===== EVENT LISTENERS =====
    document.addEventListener('DOMContentLoaded', () => {
        initCharts();

        const granSelect = document.getElementById('granularitySelect');
        if (granSelect) {
            const currentGran = {{ json_encode($granularity ?? 'monthly') }};
            granSelect.value = currentGran;
            granSelect.addEventListener('change', (e) => updateGranularity(e.target.value));
        }

        const refreshBtn = document.getElementById('refreshCharts');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', refreshCharts);
        }

        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const chart = this.getAttribute('data-chart');
                const params = new URLSearchParams(window.location.search);
                if (chart) params.set('chart', chart);
                const g = document.getElementById('granularitySelect')?.value;
                if (g) params.set('granularity', g);
                const url = `${window.location.pathname.replace(/\/graficas.*$/, '')}/graficas/pdf?${params.toString()}`;
                window.open(url, '_blank');
            });
        });
    });

    // ===== RECALCULAR EN RESIZE =====
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            ChartManager.charts.forEach(chart => chart.resize());
        }, 250);
    });
</script>
@endpush