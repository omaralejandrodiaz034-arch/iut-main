@extends('layouts.pdf') {{-- Asegúrate de que el layout no tenga márgenes que choquen --}}

@section('contenido')
<style>
    .tabla-form { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-family: sans-serif; font-size: 10px; }
    .tabla-form td, .tabla-form th { border: 1px solid #000; padding: 4px; }
    .bg-gris { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; font-size: 9px; }
    .titulo-reporte { text-align: center; font-weight: bold; font-size: 12px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
</style>

@php
    // Tomamos el primer bien para llenar los datos de cabecera si existen
    $primerBien = $bienes->first();
    $organismo = $primerBien->dependencia->unidadAdministradora->organismo ?? null;
    $unidad = $primerBien->dependencia->unidadAdministradora ?? null;
@endphp

<table class="tabla-form">
    <tr>
        <td style="width: 30%;">
            UPTOS "CLODOSBALDO RUSSIAN"<br>
            UNIDAD DE BIENES PÚBLICOS
        </td>
        <td class="titulo-reporte" style="width: 50%;">
            {{ strtoupper($titulo) }}
        </td>
        <td style="width: 20%;">
            <strong>Fecha:</strong><br>
            {{ date('d/m/Y', strtotime($generadoEn)) }}
        </td>
    </tr>
</table>

<table class="tabla-form">
    <tr class="bg-gris">
        <td colspan="2">ORGANISMO</td>
    </tr>
    <tr>
        <td style="width: 15%;"><strong>Código</strong></td>
        <td><strong>Denominación</strong></td>
    </tr>
    <tr>
        <td>{{ $organismo->codigo ?? 'N/A' }}</td>
        <td>{{ $organismo->nombre ?? 'MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN UNIVERSITARIA' }}</td>
    </tr>
</table>

<table class="tabla-form">
    <tr class="bg-gris">
        <td style="width: 50%;">UNIDAD ADMINISTRADORA</td>
        <td style="width: 50%;">TIPO DE REPORTE</td>
    </tr>
    <tr>
        <td>
            <small>Código: {{ $unidad->codigo ?? '---' }}</small><br>
            {{ $unidad->nombre ?? 'TODAS LAS UNIDADES' }}
        </td>
        <td class="text-center">
            {{ $subtitulo ?? 'LISTADO GENERAL' }}
        </td>
    </tr>
</table>

<table class="tabla-form">
    <thead>
        <tr class="bg-gris text-center">
            <th style="width: 12%;">Código</th>
            <th style="width: 35%;">Descripción</th>
            <th style="width: 10%;">Estado</th>
            <th style="width: 15%;">Dependencia</th>
            <th style="width: 15%;">Unidad Adm.</th>
            <th style="width: 13%;">Precio (Bs.)</th>
        </tr>
    </thead>
    <tbody>
        @php $totalGeneral = 0; @endphp
        @forelse($bienes as $bien)
            <tr>
                <td class="text-center">{{ $bien->codigo }}</td>
                <td>{{ $bien->descripcion }}</td>
                <td class="text-center">{{ strtoupper($bien->estado ?? 'N/A') }}</td>
                <td>{{ $bien->dependencia->nombre ?? 'N/A' }}</td>
                <td>{{ $bien->dependencia->unidadAdministradora->nombre ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format((float)($bien->precio ?? 0), 2, ',', '.') }}</td>
            </tr>
            @php $totalGeneral += (float)($bien->precio ?? 0); @endphp
        @empty
            <tr>
                <td colspan="6" class="text-center">No se encontraron bienes registrados.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right"><strong>Total General Bs.:</strong></td>
            <td class="text-right" style="border: 2px solid #000; font-weight: bold;">
                {{ number_format($totalGeneral, 2, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>
@endsection
