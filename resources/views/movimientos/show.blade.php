@extends('layouts.base')

@section('title', 'Detalle del Movimiento')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Movimientos', 'url' => route('movimientos.index')], ['label' => 'Movimiento #'.$movimiento->id]]" />
@endpush
<div class="max-w-3xl mx-auto">
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <div class="flex items-center">
                <!-- Reemplazo de emojis por Heroicons -->
                <x-heroicon-o-check class="w-6 h-6 text-green-500" />
                <div>
                    <p class="font-bold">¡Éxito!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <!-- Botón regresar -->
        <div>
            <a
                href="{{ route('movimientos.index') }}"
                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-1"
            >
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Regresar
            </a>
        </div>

        <!-- Encabezado -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-800 leading-tight">
        Movimiento #{{ $movimiento->id }} — {{ $movimiento->tipo }}
    </h1>
    <div class="flex flex-wrap gap-2 md:justify-end">
        @include('components.show-actions', ['resource' => 'movimientos', 'model' => $movimiento])
    </div>
</div>


        <!-- Información principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información del Movimiento</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Fecha</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ optional($movimiento->fecha)->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Tipo</p>
                        <p class="text-base font-medium text-gray-800">{{ $movimiento->tipo }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Usuario</p>
                        <p class="text-base font-medium text-gray-800">
                            {{ $movimiento->usuario->nombre_completo ?? $movimiento->usuario->nombre ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Observaciones</p>
                        <p class="text-base font-medium text-gray-800">{{ $movimiento->observaciones ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Entidad Relacionada</h2>

                <div class="space-y-3">
                    @php
                        $s = $movimiento->subject;
                        $label = $s?->nombre_completo
                            ?? $s?->nombre
                            ?? $s?->descripcion
                            ?? $s?->codigo
                            ?? ($movimiento->bien?->codigo
                                ? $movimiento->bien->codigo.' - '.$movimiento->bien->descripcion
                                : 'ID '.$movimiento->subject_id);
                    @endphp

                    <div>
                        <p class="text-sm text-gray-600">Entidad</p>
                        <p class="text-base font-medium text-gray-800">
                            @if($s)
                                <strong>{{ class_basename($movimiento->subject_type) }}</strong> — {{ $label }}
                            @elseif($movimiento->bien)
                                <strong>Bien</strong> — {{ $label }}
                            @else
                                <span class="text-gray-500">Sin entidad asociada</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acta de Desincorporación -->
        @if ($movimiento->tipo === 'DESINCORPORACION' && $movimiento->acta_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta de Desincorporación</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial de desincorporación del bien</p>
                    </div>
                           <a href="/storage/{{ $movimiento->acta_path }}" target="_blank"
                        class="inline-flex items-center px-4 py-2.5 bg-[#800020] text-white font-semibold rounded-lg hover:bg-[#9a0026] shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                        Ver / Descargar Acta
                    </a>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DESINCORPORACION' && $movimiento->acta_estado === 'PENDIENTE_FIRMA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta pendiente de firma</h2>
                            <p class="text-sm text-gray-600 mt-1">Debe adjuntar la acta firmada y autorizada antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg hover:bg-amber-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta firmada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DESINCORPORACION' && $movimiento->acta_estado === 'FIRMADA' && $movimiento->acta_firmada_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta firmada y autorizada</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial firmado y autorizado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/'.$movimiento->acta_firmada_path) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                            Ver / Descargar Acta Firmada
                        </a>
                        @auth
                            @if(auth()->user()?->isAdmin())
                                <button type="button" onclick="document.getElementById('modal-rechazar-{{ $movimiento->id }}').showModal()"
                                        class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                    <x-heroicon-o-x-circle class="w-5 h-5 mr-2"/>
                                    Rechazar
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DESINCORPORACION' && $movimiento->acta_estado === 'RECHAZADA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta rechazada</h2>
                            <p class="text-sm text-gray-600 mt-1">Motivo: {{ $movimiento->motivo_cancelacion_acta }}</p>
                            <p class="text-sm text-red-600 mt-1">Debe adjuntar una nueva versión corregida antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta corregida
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DESINCORPORACION' && $movimiento->acta_estado === 'PENDIENTE_FIRMA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta pendiente de firma</h2>
                            <p class="text-sm text-gray-600 mt-1">Debe adjuntar la acta firmada y autorizada antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg hover:bg-amber-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta firmada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Acta de Traslado -->
        @if ($movimiento->tipo === 'TRASLADO' && $movimiento->acta_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta de Traslado</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial de traslado del bien</p>
                    </div>
                                        <a href="/storage/{{ $movimiento->acta_path }}" target="_blank"
                       class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                        Ver / Descargar Acta
                    </a>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'TRASLADO' && $movimiento->acta_estado === 'PENDIENTE_FIRMA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta pendiente de firma</h2>
                            <p class="text-sm text-gray-600 mt-1">Debe adjuntar la acta firmada y autorizada antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg hover:bg-amber-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta firmada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'TRASLADO' && $movimiento->acta_estado === 'FIRMADA' && $movimiento->acta_firmada_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta firmada y autorizada</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial firmado y autorizado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/'.$movimiento->acta_firmada_path) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                            Ver / Descargar Acta Firmada
                        </a>
                        @auth
                            @if(auth()->user()?->isAdmin())
                                <button type="button" onclick="document.getElementById('modal-rechazar-{{ $movimiento->id }}').showModal()"
                                        class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                    <x-heroicon-o-x-circle class="w-5 h-5 mr-2"/>
                                    Rechazar
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'TRASLADO' && $movimiento->acta_estado === 'RECHAZADA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta rechazada</h2>
                            <p class="text-sm text-gray-600 mt-1">Motivo: {{ $movimiento->motivo_cancelacion_acta }}</p>
                            <p class="text-sm text-red-600 mt-1">Debe adjuntar una nueva versión corregida antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta corregida
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Acta de Donación -->
        @if ($movimiento->tipo === 'DONACION' && $movimiento->acta_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta de Donación</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial de donación del bien</p>
                    </div>
                    <a href="/storage/{{ $movimiento->acta_path }}" target="_blank"
                       class="inline-flex items-center px-4 py-2.5 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                        Ver / Descargar Acta
                    </a>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DONACION' && $movimiento->acta_estado === 'PENDIENTE_FIRMA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta pendiente de firma</h2>
                            <p class="text-sm text-gray-600 mt-1">Debe adjuntar la acta firmada y autorizada antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-lg hover:bg-amber-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta firmada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DONACION' && $movimiento->acta_estado === 'FIRMADA' && $movimiento->acta_firmada_path)
            <div class="col-span-1 md:col-span-2">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Acta firmada y autorizada</h2>
                        <p class="text-sm text-gray-600 mt-1">Documento oficial firmado y autorizado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/'.$movimiento->acta_firmada_path) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2"/>
                            Ver / Descargar Acta Firmada
                        </a>
                        @auth
                            @if(auth()->user()?->isAdmin())
                                <button type="button" onclick="document.getElementById('modal-rechazar-{{ $movimiento->id }}').showModal()"
                                        class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                    <x-heroicon-o-x-circle class="w-5 h-5 mr-2"/>
                                    Rechazar
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @endif

        @if ($movimiento->tipo === 'DONACION' && $movimiento->acta_estado === 'RECHAZADA')
            <div class="col-span-1 md:col-span-2">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Acta rechazada</h2>
                            <p class="text-sm text-gray-600 mt-1">Motivo: {{ $movimiento->motivo_cancelacion_acta }}</p>
                            <p class="text-sm text-red-600 mt-1">Debe adjuntar una nueva versión corregida antes del {{ $movimiento->fecha_limite_acta?->format('d/m/Y H:i') }} o la operación será cancelada.</p>
                        </div>
                        <form action="{{ route('bienes.acta-firmada', $movimiento->bien) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-start">
                            @csrf
                            <input type="file" name="acta_firmada" accept="application/pdf,image/*" required class="text-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition">
                                <x-heroicon-o-paper-clip class="w-5 h-5 mr-2" />
                                Subir acta corregida
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Historial de cambios -->
        @if($movimiento->tipo === 'Actualización' && $movimiento->historialMovimientos && $movimiento->historialMovimientos->isNotEmpty())
            <div class="border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Historial de cambios</h2>
                <div class="space-y-2">
                    @foreach($movimiento->historialMovimientos as $h)
                        <div class="flex items-start space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ optional($h->fecha)->format('d/m/Y H:i') ?? '—' }}
                            </span>
                            <span class="text-gray-700">{{ $h->detalle }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@auth
    @if(auth()->user()?->isAdmin())
        <dialog id="modal-rechazar-{{ $movimiento->id }}" class="p-6 rounded-lg shadow-xl">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Rechazar acta firmada</h2>
            <p class="text-sm text-gray-600 mb-4">Indique el motivo del rechazo para que el usuario pueda corregir el acta.</p>
            <form action="{{ route('bienes.acta-rechazar', $movimiento->bien) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="motivo_rechazo" rows="3" required class="w-full border border-gray-300 rounded-lg p-2 text-sm"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-rechazar-{{ $movimiento->id }}').close()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition">
                        Rechazar acta
                    </button>
                </div>
            </form>
        </dialog>
    @endif
@endauth
@endsection




