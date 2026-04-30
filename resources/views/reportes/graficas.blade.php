@extends('layouts.base')

@section('title', 'Gráficas')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Reportes', 'url' => route('reportes.index')], ['label' => 'Gráficas']]" />
@endpush
        <div class="max-w-6xl mx-auto space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gráficas</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Visualización gráfica de los bienes por tipo, estado, registro y desincorporación.
                    </p>
            <!-- Registro de Organismos -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Registro de Organismos</h2>
                <button data-chart="registroOrganismos" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartOrganismo" class="w-96 h-56"></canvas>
            </div>

            <!-- === GRÁFICAS DE USUARIOS === -->
            <!-- Usuarios por Rol -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Usuarios por Rol</h2>
                <button data-chart="usuariosPorRol" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartUsuariosRol" class="w-96 h-56"></canvas>
            </div>

            <!-- Usuarios Activos vs Inactivos -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Usuarios Activos / Inactivos</h2>
                <button data-chart="usuariosActivos" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartUsuariosActivos" class="w-96 h-56"></canvas>
            </div>

            <!-- === GRÁFICAS DE DEPENDENCIAS === -->
            <!-- Dependencias por Unidad -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Dependencias por Unidad</h2>
                <button data-chart="dependenciasPorUnidad" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartDepsUnidad" class="w-96 h-56"></canvas>
            </div>

            <!-- Dependencias con/sin responsable -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Dependencias: con/sin responsable</h2>
                <button data-chart="dependenciasResponsable" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartDepsResponsable" class="w-96 h-56"></canvas>
            </div>

            <!-- === GRÁFICAS DE MOVIMIENTOS === -->
            <!-- Movimientos por Tipo -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Movimientos por Tipo</h2>
                <button data-chart="movimientosPorTipo" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartMovTipo" class="w-96 h-56"></canvas>
            </div>

            <!-- Movimientos por Usuario (Top 10) -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Top 10 Usuarios por Movimientos</h2>
                <button data-chart="movimientosPorUsuario" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartMovUsuario" class="w-96 h-56"></canvas>
            </div>

            <!-- Movimientos por Fecha (Progresivo) -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 md:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Movimientos por Fecha (Progresivo)</h2>
                    <button data-chart="movimientosPorFecha" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Granularidad:</label>
                        <select id="granularity" name="granularity" class="border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="daily" @selected(request('granularity') == 'daily')>Diaria</option>
                            <option value="weekly" @selected(request('granularity') == 'weekly')>Semanal</option>
                            <option value="monthly" @selected(request('granularity', 'monthly') == 'monthly')>Mensual</option>
                        </select>
                    </div>
                </div>
                <canvas id="chartMovFecha" class="w-full h-64"></canvas>
            </div>
        </div> <!-- fin grid -->

            <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-2">
                <!-- Valor total por Estado -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Valor total por Estado (Bs.)</h2>
                    <button data-chart="valorPorEstado" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartValorEstado" class="w-full h-64"></canvas>
                </div>

                <!-- Top 10 Dependencias por Valor -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Top 10 Dependencias por Valor (Bs.)</h2>
                    <button data-chart="topDependencias" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartTopDeps" class="w-full h-64"></canvas>
                </div>

                <!-- Cobertura de Fotografías -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Cobertura de Fotografías</h2>
                    <button data-chart="fotos" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartFotos" class="w-96 h-56"></canvas>
                </div>

                <!-- Bienes sin Movimientos 12 meses -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes sin Movimientos (12 meses)</h2>
                    <button data-chart="movimientos" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartMovimientos" class="w-96 h-56"></canvas>
                </div>
                <!-- Gráfico de Bienes por Tipo -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes por Tipo</h2>
                    <button data-chart="bienesPorTipo" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartTipo" class="w-96 h-56"></canvas>
                </div>

                <!-- Gráfico de Bienes por Estado -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes por Estado</h2>
                    <button data-chart="bienesPorEstado" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartEstado" class="w-full h-64"></canvas>
                </div>

                <!-- Gráfico de Bienes por Registro (Progresivo) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 md:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Bienes por Registro (Progresivo)</h2>
                        <button data-chart="bienesPorRegistro" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Granularidad:</label>
                            <select id="granularity" name="granularity" class="border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="daily" @selected(request('granularity') == 'daily')>Diaria</option>
                                <option value="weekly" @selected(request('granularity') == 'weekly')>Semanal</option>
                                <option value="monthly" @selected(request('granularity', 'monthly') == 'monthly')>Mensual</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="chartRegistro" class="w-full h-64"></canvas>
                </div>

                <!-- Gráfico de Bienes Desincorporados -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes Desincorporados</h2>
                    <button data-chart="bienesDesincorporados" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                    <canvas id="chartDesincorporados" class="w-full h-64"></canvas>
                </div>


            <!-- Registro de Dependencias -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Registro de Dependencias</h2>
                <button data-chart="registroDependencias" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartDependencia" class="w-96 h-56"></canvas>
            </div>

            <!-- Registro de Unidades Administradoras -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Registro de Unidades Administradoras</h2>
                <button data-chart="registroUnidades" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartUnidad" class="w-96 h-56"></canvas>
            </div>

            <!-- Registro de Organismos -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Registro de Organismos</h2>
                <button data-chart="registroOrganismos" class="ml-2 inline-block px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200 download-btn">Descargar PDF</button>
                <canvas id="chartOrganismo" class="w-96 h-56"></canvas>
            </div>
            </div>
            </div>


        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Paleta y utilidades comunes para todas las gráficas
            const CHART_PALETTE = [
                '#3B82F6', // blue
                '#10B981', // green
                '#F59E0B', // amber
                '#EF4444', // red
                '#8B5CF6', // purple
                '#06B6D4', // cyan
                '#F97316', // orange
                '#6366F1'  // indigo
            ];

            // Wrap canvases in a constrained container but preserve a usable height
            (function wrapCanvases() {
                document.querySelectorAll('canvas').forEach(c => {
                    const parent = c.parentElement;
                    if (!parent) return;
                    if (parent.classList.contains('chart-wrapper')) return;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'chart-wrapper';
                    wrapper.style.width = '100%';
                    wrapper.style.overflow = 'auto';

                    // try to detect an initial canvas height; fall back to class heuristics or a sensible default
                    let origH = c.clientHeight || 0;
                    if (!origH) {
                        if (c.classList.contains('h-64')) origH = 256;
                        else if (c.classList.contains('h-56')) origH = 224;
                        else if (c.classList.contains('h-48')) origH = 192;
                        else origH = 300; // default
                    }

                    // enforce a minimum and maximum for the wrapper height
                    const minH = 320;
                    const maxH = 900;
                    const finalH = Math.max(minH, Math.min(origH, maxH));

                    wrapper.style.maxHeight = finalH + 'px';
                    wrapper.style.height = finalH + 'px';

                    parent.insertBefore(wrapper, c);
                    wrapper.appendChild(c);

                    // make canvas occupy the wrapper space
                    c.style.display = 'block';
                    c.style.width = '100%';
                    c.style.height = '100%';
                });
            })();

            const formatNumber = (v, currency = false) => {
                if (v === null || v === undefined) return '-';
                try {
                    if (currency) return (Number(v) || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Bs.';
                    return (Number(v) || 0).toLocaleString('es-VE');
                } catch (e) {
                    return v;
                }
            };

            const commonOptions = ({yLabel = '', currency = false, stacked = false, indexAxis = 'x'} = {}) => ({
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
                        }
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
                            label: context => {
                                const v = context.raw;
                                return `${context.dataset.label || ''}: ${currency ? formatNumber(v, true) : formatNumber(v, false)}`;
                            }
                        }
                    },
                    title: { display: true }
                }
            });
            // Bienes por Tipo
            const dataTipo = (Object.keys(@json($bienesPorTipo)).length ? @json($bienesPorTipo) : { 'Sin datos': 0 });
            const labelsTipo = Object.keys(dataTipo);
            const valuesTipo = Object.values(dataTipo);

            (function(){
                const opts = commonOptions({currency:false});
                opts.plugins.title.text = 'Distribución de Bienes por Tipo';
                const bg = labelsTipo.map((_,i)=> CHART_PALETTE[i % CHART_PALETTE.length]);
                new Chart(document.getElementById('chartTipo').getContext('2d'), {
                    type: 'pie',
                    data: { labels: labelsTipo, datasets:[{ data: valuesTipo, backgroundColor: bg }] },
                    options: opts
                });
            })();

            // Bienes por Estado
            const dataEstado = (Object.keys(@json($bienesPorEstado)).length ? @json($bienesPorEstado) : { 'Sin datos': 0 });
            const labelsEstado = Object.keys(dataEstado);
            const valuesEstado = Object.values(dataEstado);

            (function(){
                const opts = commonOptions({currency:false});
                opts.plugins.title.text = 'Bienes por Estado';
                new Chart(document.getElementById('chartEstado').getContext('2d'), {
                    type: 'bar',
                            data: { labels: labelsEstado, datasets:[{ label:'Cantidad', data: valuesEstado, backgroundColor: CHART_PALETTE[0], maxBarThickness: 40, barThickness: 'flex' }] },
                    options: opts
                });
            })();

            // Bienes por Registro (Progresivo)
            const dataRegistro = (Object.keys(@json($bienesPorRegistro)).length ? @json($bienesPorRegistro) : { 'Sin datos': 0 });
            const labelsRegistro = Object.keys(dataRegistro);
            const valuesRegistro = Object.values(dataRegistro);

            const granularity = @json($granularity ?? 'monthly');
            const granularityLabel = granularity === 'daily' ? 'Diaria' : (granularity === 'weekly' ? 'Semanal' : 'Mensual');

            (function(){
                const opts = commonOptions({currency:false});
                opts.plugins.title.text = `Registro progresivo de Bienes (${granularityLabel})`;
                new Chart(document.getElementById('chartRegistro').getContext('2d'), {
                    type: 'line',
                    data: { labels: labelsRegistro, datasets:[{ label:'Cantidad Acumulada', data: valuesRegistro, backgroundColor: CHART_PALETTE[1]+'33', borderColor: CHART_PALETTE[1], borderWidth:2, fill:true, tension:0.25, pointRadius:2 }] },
                    options: opts
                });
            })();

            // Bienes Desincorporados
            const dataDesincorporados = (Object.keys(@json($bienesDesincorporados)).length ? @json($bienesDesincorporados) : { 'Sin datos': 0 });
            const labelsDesincorporados = Object.keys(dataDesincorporados);
            const valuesDesincorporados = Object.values(dataDesincorporados);

            (function(){
                const opts = commonOptions({currency:false});
                opts.plugins.title.text = `Bienes Desincorporados (${granularityLabel})`;
                new Chart(document.getElementById('chartDesincorporados').getContext('2d'), {
                    type: 'line',
                    data: { labels: labelsDesincorporados, datasets:[{ label:'Cantidad Desincorporada', data: valuesDesincorporados, backgroundColor: CHART_PALETTE[3]+'33', borderColor: CHART_PALETTE[3], borderWidth:2, fill:true, tension:0.25, pointRadius:2 }] },
                    options: opts
                });
            })();
            // Registro de Dependencias
    const dataDependencia = (Object.keys(@json($registroDependencias)).length ? @json($registroDependencias) : { 'Sin datos': 0 });
    const labelsDependencia = Object.keys(dataDependencia);
    const valuesDependencia = Object.values(dataDependencia);

    (function(){
        const opts = commonOptions({currency:false});
        opts.plugins.title.text = `Registro de Dependencias (${granularityLabel})`;
        new Chart(document.getElementById('chartDependencia').getContext('2d'), {
            type: 'line',
            data: { labels: labelsDependencia, datasets:[{ label:'Dependencias Registradas', data: valuesDependencia, backgroundColor: CHART_PALETTE[4]+'33', borderColor: CHART_PALETTE[4], borderWidth:2, fill:true, tension:0.25, pointRadius:2 }] },
            options: opts
        });
    })();

    // Listener para cambiar granularidad y recargar con el parámetro
    document.getElementById('granularity')?.addEventListener('change', function () {
        const g = this.value;
        const params = new URLSearchParams(window.location.search);
        params.set('granularity', g);
        // Mantener otros filtros
        window.location.search = params.toString();
    });

    // Registro de Unidades Administradoras
    const dataUnidad = (Object.keys(@json($registroUnidades)).length ? @json($registroUnidades) : { 'Sin datos': 0 });
    const labelsUnidad = Object.keys(dataUnidad);
    const valuesUnidad = Object.values(dataUnidad);

    (function(){
        const opts = commonOptions({currency:false});
        opts.plugins.title.text = `Registro de Unidades Administradoras (${granularityLabel})`;
        new Chart(document.getElementById('chartUnidad').getContext('2d'), {
            type: 'line',
            data: { labels: labelsUnidad, datasets:[{ label:'Unidades Registradas', data: valuesUnidad, backgroundColor: CHART_PALETTE[5]+'33', borderColor: CHART_PALETTE[5], borderWidth:2, fill:true, tension:0.25, pointRadius:2 }] },
            options: opts
        });
     })();

     // === DATOS DE NUEVAS ENTIDADES ===
     // Usuarios por Rol
     const dataUsuariosRol = (Object.keys(@json($usuariosPorRol ?? [])).length ? @json($usuariosPorRol) : { 'Sin datos': 0 });
     const labelsUsuariosRol = Object.keys(dataUsuariosRol);
     const valuesUsuariosRol = Object.values(dataUsuariosRol);

     // Usuarios Activos/Inactivos
     const dataUsuariosActivos = (Object.keys(@json($usuariosActivos ?? [])).length ? @json($usuariosActivos) : { 'Sin datos': 0 });
     const labelsUsuariosActivos = Object.keys(dataUsuariosActivos);
     const valuesUsuariosActivos = Object.values(dataUsuariosActivos);

     // Dependencias por Unidad
     const dataDepsUnidad = (Object.keys(@json($dependenciasPorUnidad ?? [])).length ? @json($dependenciasPorUnidad) : { 'Sin datos': 0 });
     const labelsDepsUnidad = Object.keys(dataDepsUnidad);
     const valuesDepsUnidad = Object.values(dataDepsUnidad);

     // Dependencias con/sin responsable
     const dataDepsResponsable = (Object.keys(@json($dependenciasResponsable ?? [])).length ? @json($dependenciasResponsable) : { 'Sin datos': 0 });
     const labelsDepsResponsable = Object.keys(dataDepsResponsable);
     const valuesDepsResponsable = Object.values(dataDepsResponsable);

     // Movimientos por Tipo
     const dataMovTipo = (Object.keys(@json($movimientosPorTipo ?? [])).length ? @json($movimientosPorTipo) : { 'Sin datos': 0 });
     const labelsMovTipo = Object.keys(dataMovTipo);
     const valuesMovTipo = Object.values(dataMovTipo);

     // Movimientos por Usuario (Top 10)
     const dataMovUsuario = (Object.keys(@json($movimientosPorUsuario ?? [])).length ? @json($movimientosPorUsuario) : { 'Sin datos': 0 });
     const labelsMovUsuario = Object.keys(dataMovUsuario);
     const valuesMovUsuario = Object.values(dataMovUsuario);

     // Movimientos por Fecha (Progresivo)
     const dataMovFecha = (Object.keys(@json($movimientosPorFecha ?? [])).length ? @json($movimientosPorFecha) : { 'Sin datos': 0 });
     const labelsMovFecha = Object.keys(dataMovFecha);
     const valuesMovFecha = Object.values(dataMovFecha);

     // Si por alguna razón se inyectó una tabla en esta vista, ocultarla
     document.addEventListener('DOMContentLoaded', function () {
         const tabla = document.getElementById('tablaBienesContainer');
         if (tabla) tabla.style.display = 'none';
     });

     // === NUEVOS GRÁFICOS ===
     // Usuarios por Rol (dona)
     (function(){
         const opts = commonOptions({currency:false});
         opts.plugins.title.text = 'Usuarios por Rol';
         new Chart(document.getElementById('chartUsuariosRol').getContext('2d'), {
             type: 'doughnut',
             data: { labels: labelsUsuariosRol, datasets:[{ data: valuesUsuariosRol, backgroundColor: CHART_PALETTE.slice(0, labelsUsuariosRol.length) }] },
             options: opts
         });
     })();

     // Usuarios Activos/Inactivos (dona)
     (function(){
         const opts = commonOptions({currency:false});
         opts.plugins.title.text = 'Usuarios Activos vs Inactivos';
         new Chart(document.getElementById('chartUsuariosActivos').getContext('2d'), {
             type: 'doughnut',
             data: { labels: labelsUsuariosActivos, datasets:[{ data: valuesUsuariosActivos, backgroundColor: [CHART_PALETTE[1], CHART_PALETTE[3]] }] },
             options: opts
         });
     })();

     // Dependencias por Unidad (barra horizontal)
     (function(){
         const opts = commonOptions({currency:false, indexAxis:'y'});
         opts.plugins.title.text = 'Dependencias por Unidad';
         new Chart(document.getElementById('chartDepsUnidad').getContext('2d'), {
             type: 'bar',
             data: { labels: labelsDepsUnidad, datasets:[{ label:'Número de Dependencias', data: valuesDepsUnidad, backgroundColor: CHART_PALETTE[4], barThickness: 18 }] },
             options: opts
         });
     })();

     // Dependencias con/sin responsable (dona)
     (function(){
         const opts = commonOptions({currency:false});
         opts.plugins.title.text = 'Dependencias: con/sin responsable';
         new Chart(document.getElementById('chartDepsResponsable').getContext('2d'), {
             type: 'doughnut',
             data: { labels: labelsDepsResponsable, datasets:[{ data: valuesDepsResponsable, backgroundColor: [CHART_PALETTE[0], CHART_PALETTE[3]] }] },
             options: opts
         });
     })();

     // Movimientos por Tipo (barra)
     (function(){
         const opts = commonOptions({currency:false});
         opts.plugins.title.text = 'Movimientos por Tipo';
         new Chart(document.getElementById('chartMovTipo').getContext('2d'), {
             type: 'bar',
             data: { labels: labelsMovTipo, datasets:[{ label:'Cantidad', data: valuesMovTipo, backgroundColor: CHART_PALETTE[5], maxBarThickness: 60 }] },
             options: opts
         });
     })();

     // Movimientos por Usuario - Top 10 (barra horizontal)
     (function(){
         const opts = commonOptions({currency:false, indexAxis:'y'});
         opts.plugins.title.text = 'Top 10 Usuarios por Movimientos';
         new Chart(document.getElementById('chartMovUsuario').getContext('2d'), {
             type: 'bar',
             data: { labels: labelsMovUsuario, datasets:[{ label:'Movimientos', data: valuesMovUsuario, backgroundColor: CHART_PALETTE[6], barThickness: 16 }] },
             options: opts
         });
     })();

     // Movimientos por Fecha (Progresivo)
     const granMov = @json($granularity ?? 'monthly');
     const granMovLabel = granMov === 'daily' ? 'Diaria' : (granMov === 'weekly' ? 'Semanal' : 'Mensual');
     (function(){
         const opts = commonOptions({currency:false});
         opts.plugins.title.text = `Movimientos por Fecha (${granMovLabel})`;
         new Chart(document.getElementById('chartMovFecha').getContext('2d'), {
             type: 'line',
             data: { labels: labelsMovFecha, datasets:[{ label:'Movimientos Acumulados', data: valuesMovFecha, backgroundColor: CHART_PALETTE[7]+'33', borderColor: CHART_PALETTE[7], borderWidth:2, fill:true, tension:0.25, pointRadius:2 }] },
             options: opts
         });
     })();

     // Descarga de PDFs por gráfica
     document.querySelectorAll('.download-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const chart = this.getAttribute('data-chart');
            const params = new URLSearchParams(window.location.search);
            if (chart) params.set('chart', chart);
            // Mantener granularity si existe en selector
            const g = document.getElementById('granularity')?.value;
            if (g) params.set('granularity', g);

            const url = `${window.location.pathname.replace(/\/graficas.*$/, '')}/graficas/pdf?${params.toString()}`;
            window.open(url, '_blank');
        });
    });



        </script>
@endsection
