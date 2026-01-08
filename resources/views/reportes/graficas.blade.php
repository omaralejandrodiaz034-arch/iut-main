@extends('layouts.base')

@section('title', 'Gráficas')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gráficas</h1>
            <p class="text-gray-600 mt-2 max-w-2xl">
                Visualización gráfica de los bienes por tipo, estado y registro.
            </p>
        </div>
    </div>

    <div class="grid gap-8 md:grid-cols-1 xl:grid-cols-1">
        <!-- Gráfico de Bienes por Tipo -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes por Tipo</h2>
            <canvas id="chartTipo" width="400" height="200"></canvas>
        </div>

        <!-- Gráfico de Bienes por Estado -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes por Estado</h2>
            <canvas id="chartEstado" width="400" height="200"></canvas>
        </div>

        <!-- Gráfico de Bienes por Registro -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Bienes por Registro (Mensual)</h2>
            <canvas id="chartRegistro" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico de tipos
    const dataTipo = @json($bienesPorTipo);
    const labelsTipo = Object.keys(dataTipo);
    const valuesTipo = Object.values(dataTipo);

    // Gráfico de Bienes por Tipo
    const ctxTipo = document.getElementById('chartTipo').getContext('2d');
    new Chart(ctxTipo, {
        type: 'pie',
        data: {
            labels: labelsTipo,
            datasets: [{
                label: 'Cantidad',
                data: valuesTipo,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribución de Bienes por Tipo'
                }
            }
        }
    });

    // Datos para el gráfico de estados
    const dataEstado = @json($bienesPorEstado);
    const labelsEstado = Object.keys(dataEstado);
    const valuesEstado = Object.values(dataEstado);

    // Gráfico de Bienes por Estado
    const ctxEstado = document.getElementById('chartEstado').getContext('2d');
    new Chart(ctxEstado, {
        type: 'bar',
        data: {
            labels: labelsEstado,
            datasets: [{
                label: 'Cantidad',
                data: valuesEstado,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Bienes por Estado'
                }
            }
        }
    });

    // Datos para el gráfico de registro
    const dataRegistro = @json($bienesPorRegistro);
    const labelsRegistro = Object.keys(dataRegistro);
    const valuesRegistro = Object.values(dataRegistro);

    // Gráfico de Bienes por Registro
    const ctxRegistro = document.getElementById('chartRegistro').getContext('2d');
    new Chart(ctxRegistro, {
        type: 'line',
        data: {
            labels: labelsRegistro,
            datasets: [{
                label: 'Cantidad Registrada',
                data: valuesRegistro,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Registro de Bienes por Mes'
                }
            }
        }
    });
</script>
@endsection