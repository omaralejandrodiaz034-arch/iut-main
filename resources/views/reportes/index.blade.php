@extends('layouts.base')

@section('title', 'Reportes')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reportes</h1>
            <p class="text-gray-600 mt-2 max-w-2xl">
                Selecciona alguno de los reportes disponibles para generar un archivo PDF
                con la información más relevante del sistema.
            </p>
        </div>
        <div>
            <a href="{{ route('graficas') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <x-heroicon-o-chart-bar class="w-4 h-4 mr-2" />
                Ver Gráficas
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($reportTypes as $key => $report)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-5 flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ $report['title'] }}</h2>
                    <p class="text-sm text-gray-600 mb-3">{{ $report['description'] }}</p>
                </div>
                <div class="mt-3 flex justify-end">
                    <a
                        href="{{ route('reportes.pdf', ['tipo' => $key]) }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-100 rounded hover:bg-indigo-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                        target="_blank"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                        Generar PDF
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
