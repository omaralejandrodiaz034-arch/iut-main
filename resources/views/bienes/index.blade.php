@extends('layouts.base')

@section('title', 'Bienes')

@section('content')
@push('breadcrumbs')
<x-breadcrumbs :items="[['label' => 'Bienes']]" />
@endpush

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
        <x-heroicon-o-cube class="w-8 h-8 text-blue-600" /> Bienes
    </h1>

    <div class="flex gap-3">
        <a href="{{ route('bienes.galeria') }}"
           class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Galería</span>
        </a>

        <a href="{{ route('bienes.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Nuevo Bien</span>
        </a>

        @php
            $queryParams = request()->except(['page']);
            $queryString = http_build_query($queryParams);
        @endphp

        <a id="btnGenerarPdf" href="{{ route('bienes.reporte') . ($queryString ? '?' . $queryString : '') }}"
           class="btn-pdf inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95"
           title="Generar reporte PDF con filtros aplicados">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span>PDF</span>
        </a>

        <a href="{{ route('graficas') . ($queryString ? '?' . $queryString : '') }}"
           title="Ver gráficas basadas en los filtros actuales"
           class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M6 10h10M6 6h.01M6 14h.01M6 18h.01" />
            </svg>
            <span>Gráficas</span>
        </a>

        

        <a href="{{ route('bienes.exportar', request()->query()) }}"
           title="Exportar bienes a Excel"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span>Exportar</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm flex items-center gap-3">
        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 flex-shrink-0" />
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Panel de Filtros Mejorado -->
<div class="mb-6 bg-white rounded-xl shadow-sm border border-slate-200/60 overflow-hidden">
    <!-- Cabecera del panel -->
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Filtros de Búsqueda</h3>
                <p class="text-xs text-slate-500">Refina los resultados según tus criterios</p>
            </div>
        </div>
    </div>

    <form action="{{ route('bienes.index') }}" method="GET" id="filtrosForm" class="p-6 space-y-6">
        
        <!-- Primera fila -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Búsqueda rápida -->
            <div class="space-y-1.5">
                <label for="search" class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Búsqueda rápida
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search"
                           value="{{ request('search') }}"
                           maxlength="40"
                           placeholder="Código o descripción..."
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 rounded-lg 
                                  focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                  transition-all duration-200 filtro-auto text-sm
                                  placeholder:text-slate-400">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Tipo de Bien - Dropdown con Checkboxes -->
            <div class="space-y-1.5" x-data="{ open: false, selected: {{ json_encode(array_values((array)request('tipo_bien', []))) }} }">
                <label class="text-sm font-medium text-slate-700">Tipo de Bien</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                   transition-all duration-200 text-sm text-left flex items-center justify-between">
                        <span class="text-slate-700" x-text="selected.length > 0 ? selected.length + ' seleccionado(s)' : 'Seleccionar...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" 
                         class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-2 space-y-1">
                            @foreach($tiposBien as $valor => $label)
                                <label class="flex items-center px-2 py-1.5 hover:bg-slate-50 rounded-md cursor-pointer transition-colors duration-150">
                                    <input type="checkbox" name="tipo_bien[]" value="{{ $valor }}"
                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 filtro-auto"
                                           {{ in_array($valor, (array)request('tipo_bien', [])) ? 'checked' : '' }}
                                           @change="let value = '{{ $valor }}'; if($event.target.checked) { if(!selected.includes(value)) selected.push(value) } else { selected = selected.filter(v => v !== value) }">
                                    <span class="ml-2 text-sm text-slate-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organismo - Dropdown con Checkboxes -->
            <div class="space-y-1.5" x-data="{ open: false, selected: {{ json_encode(array_values((array)request('organismo_id', []))) }} }">
                <label class="text-sm font-medium text-slate-700">Organismo</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                   transition-all duration-200 text-sm text-left flex items-center justify-between">
                        <span class="text-slate-700" x-text="selected.length > 0 ? selected.length + ' seleccionado(s)' : 'Seleccionar...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-2 space-y-1">
                            @foreach($organismos as $organismo)
                                <label class="flex items-center px-2 py-1.5 hover:bg-slate-50 rounded-md cursor-pointer transition-colors duration-150">
                                    <input type="checkbox" name="organismo_id[]" value="{{ $organismo->id }}"
                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 filtro-auto"
                                           {{ in_array($organismo->id, (array)request('organismo_id', [])) ? 'checked' : '' }}
                                           @change="let value = '{{ $organismo->id }}'; if($event.target.checked) { if(!selected.includes(value)) selected.push(value) } else { selected = selected.filter(v => v !== value) }">
                                    <span class="ml-2 text-sm text-slate-700">{{ $organismo->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unidad Administradora - Dropdown con Checkboxes -->
            <div class="space-y-1.5" x-data="{ open: false, selected: {{ json_encode(array_values((array)request('unidad_id', []))) }} }">
                <label class="text-sm font-medium text-slate-700">Unidad Administradora</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                   transition-all duration-200 text-sm text-left flex items-center justify-between">
                        <span class="text-slate-700" x-text="selected.length > 0 ? selected.length + ' seleccionado(s)' : 'Seleccionar...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-2 space-y-1">
                            @foreach($unidades as $unidad)
                                <label class="flex items-center px-2 py-1.5 hover:bg-slate-50 rounded-md cursor-pointer transition-colors duration-150">
                                    <input type="checkbox" name="unidad_id[]" value="{{ $unidad->id }}"
                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 filtro-auto"
                                           {{ in_array($unidad->id, (array)request('unidad_id', [])) ? 'checked' : '' }}
                                           @change="let value = '{{ $unidad->id }}'; if($event.target.checked) { if(!selected.includes(value)) selected.push(value) } else { selected = selected.filter(v => v !== value) }">
                                    <span class="ml-2 text-sm text-slate-700">{{ $unidad->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila - Fechas y Dependencia -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Fecha desde -->
            <div class="space-y-1.5">
                <label for="fecha_desde" class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Fecha registro desde
                </label>
                <input type="date" name="fecha_desde" id="fecha_desde"
                       value="{{ request('fecha_desde') }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                              focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                              transition-all duration-200 filtro-auto text-sm">
            </div>

            <!-- Fecha hasta -->
            <div class="space-y-1.5">
                <label for="fecha_hasta" class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Fecha registro hasta
                </label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                       value="{{ request('fecha_hasta') }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                              focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                              transition-all duration-200 filtro-auto text-sm">
                <p id="error-msg-fechas" class="text-red-500 text-xs mt-1.5 hidden flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    La fecha "hasta" debe ser igual o posterior a "desde"
                </p>
            </div>

            <!-- Dependencia - Dropdown con Checkboxes -->
            <div class="space-y-1.5" x-data="{ open: false, selected: {{ json_encode(array_values((array)request('dependencias', []))) }} }">
                <label class="text-sm font-medium text-slate-700">Dependencia</label>
                <div class="relative">
                    <button type="button" @click="open = !open" 
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-lg px-3 py-2.5 
                                   focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                   transition-all duration-200 text-sm text-left flex items-center justify-between">
                        <span class="text-slate-700" x-text="selected.length > 0 ? selected.length + ' seleccionado(s)' : 'Seleccionar...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="p-2 space-y-1">
                            @foreach($dependencias as $dependencia)
                                <label class="flex items-center px-2 py-1.5 hover:bg-slate-50 rounded-md cursor-pointer transition-colors duration-150">
                                    <input type="checkbox" name="dependencias[]" value="{{ $dependencia->id }}"
                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 filtro-auto"
                                           {{ in_array($dependencia->id, (array)request('dependencias', [])) ? 'checked' : '' }}
                                           @change="let value = '{{ $dependencia->id }}'; if($event.target.checked) { if(!selected.includes(value)) selected.push(value) } else { selected = selected.filter(v => v !== value) }">
                                    <span class="ml-2 text-sm text-slate-700">{{ $dependencia->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tercera fila - Rango de Precios -->
        <div class="bg-slate-50/50 rounded-lg p-4 border border-slate-100">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-slate-700">Rango de Precios</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="precio_desde" class="text-xs font-medium text-slate-600">Precio desde (Bs.)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Bs.</span>
                        <input type="number" name="precio_desde" id="precio_desde"
                               value="{{ request('precio_desde') }}"
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 text-slate-900 rounded-lg 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition-all duration-200 filtro-auto text-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label for="precio_hasta" class="text-xs font-medium text-slate-600">Precio hasta (Bs.)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Bs.</span>
                        <input type="number" name="precio_hasta" id="precio_hasta"
                               value="{{ request('precio_hasta') }}"
                               min="0"
                               step="0.01"
                               placeholder="999999.99"
                               class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 text-slate-900 rounded-lg 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                                      transition-all duration-200 filtro-auto text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Estados y Acciones -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-5 border-t border-slate-200">
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-sm font-medium text-slate-700">Estado:</span>
                <div class="flex flex-wrap gap-3">
                    @foreach($estados as $valor => $label)
                        <label class="inline-flex items-center text-sm cursor-pointer group">
                            <input type="checkbox" name="estado[]" value="{{ $valor }}"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 
                                          transition-all duration-200 filtro-auto
                                          hover:border-blue-400"
                                   {{ in_array($valor, (array)request('estado', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-slate-600 group-hover:text-blue-600 transition-colors duration-200">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <label class="inline-flex items-center text-sm cursor-pointer group bg-red-50 hover:bg-red-100 
                              px-3 py-1.5 rounded-lg transition-all duration-200 border border-red-200 hover:border-red-300">
                    <input type="checkbox" name="solo_desincorporados" value="1"
                           class="w-4 h-4 rounded border-red-300 text-red-600 focus:ring-2 focus:ring-red-500/20 
                                  transition-all duration-200 filtro-auto"
                           {{ request('solo_desincorporados') ? 'checked' : '' }}>
                    <span class="ml-2 text-red-700 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Solo Desincorporados
                    </span>
                </label>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" onclick="clearAllFilters()"
                        class="flex-1 sm:flex-none px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg 
                               hover:bg-slate-50 hover:border-slate-400 transition-all duration-200 text-sm font-medium
                               flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Limpiar todos
                </button>
                <button type="submit"
                        class="flex-1 sm:flex-none bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2.5 
                               rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 text-sm font-medium
                               shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Script para limpiar filtros -->
<script>
function clearAllFilters() {
    // Limpiar inputs normales
    document.querySelectorAll('#filtrosForm input[type="text"], #filtrosForm input[type="number"], #filtrosForm input[type="date"]').forEach(input => {
        input.value = '';
    });
    
    // Limpiar checkboxes de estado
    document.querySelectorAll('input[name="estado[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Limpiar checkbox de desincorporados
    const desincorporadoCheckbox = document.querySelector('input[name="solo_desincorporados"]');
    if (desincorporadoCheckbox) {
        desincorporadoCheckbox.checked = false;
    }
    
    // Limpiar selects múltiples tradicionales si existen
    document.querySelectorAll('select[multiple]').forEach(select => {
        for (let i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }
    });
    
    // Enviar el formulario
    document.getElementById('filtrosForm').submit();
}
</script>
<!-- Chips de filtros activos mejorado -->
<div id="activeFiltersContainer" class="mb-5">
    @php
        $params = request()->only(['search', 'tipo_bien', 'organismo_id', 'unidad_id', 'fecha_desde', 'fecha_hasta', 'estado', 'dependencias', 'solo_desincorporados', 'precio_desde', 'precio_hasta']);
        $activeFilters = collect($params)->filter(fn($v) => filled($v) && $v !== [] && $v !== '');
    @endphp

    @if($activeFilters->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-gray-600">Filtros activos:</span>
            @foreach($activeFilters as $key => $value)
                @if(is_array($value) && !empty($value))
                    @php
                        $displayValues = [];
                        if ($key === 'tipo_bien') {
                            foreach($value as $v) {
                                if (isset($tiposBien[$v])) $displayValues[] = $tiposBien[$v];
                            }
                        } elseif ($key === 'organismo_id') {
                            foreach($value as $v) {
                                $org = $organismos->firstWhere('id', $v);
                                if ($org) $displayValues[] = $org->nombre;
                            }
                        } elseif ($key === 'unidad_id') {
                            foreach($value as $v) {
                                $uni = $unidades->firstWhere('id', $v);
                                if ($uni) $displayValues[] = $uni->nombre;
                            }
                        } elseif ($key === 'dependencias') {
                            foreach($value as $v) {
                                $dep = $dependencias->firstWhere('id', $v);
                                if ($dep) $displayValues[] = $dep->nombre;
                            }
                        } elseif ($key === 'estado') {
                            foreach($value as $v) {
                                if (isset($estados[$v])) $displayValues[] = $estados[$v];
                            }
                        }
                        $display = implode(', ', array_slice($displayValues, 0, 3)) . (count($displayValues) > 3 ? ' (+' . (count($displayValues) - 3) . ')' : '');
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        <span class="font-semibold mr-1.5">
                            @switch($key)
                                @case('search') Búsqueda @break
                                @case('tipo_bien') Tipos @break
                                @case('organismo_id') Organismos @break
                                @case('unidad_id') Unidades @break
                                @case('fecha_desde') Desde @break
                                @case('fecha_hasta') Hasta @break
                                @case('estado') Estados @break
                                @case('dependencias') Dependencias @break
                                @case('solo_desincorporados') Solo Desincorporados @break
                                @case('precio_desde') Precio â‰¥ @break
                                @case('precio_hasta') Precio â‰¤ @break
                                @default {{ ucfirst($key) }}
                            @endswitch:
                        </span>
                        {{ $display }}
                        <a href="{{ route('bienes.index', request()->except($key)) }}"
                           class="ml-2 text-red-500 hover:text-red-700 font-bold">×</a>
                    </span>
                @elseif(!is_array($value) && filled($value))
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        <span class="font-semibold mr-1.5">
                            @switch($key)
                                @case('search') Búsqueda @break
                                @case('fecha_desde') Desde @break
                                @case('fecha_hasta') Hasta @break
                                @case('solo_desincorporados') Solo Desincorporados @break
                                @case('precio_desde') Precio â‰¥ @break
                                @case('precio_hasta') Precio â‰¤ @break
                                @default {{ ucfirst($key) }}
                            @endswitch:
                        </span>
                        {{ $value }}
                        <a href="{{ route('bienes.index', request()->except($key)) }}"
                           class="ml-2 text-red-500 hover:text-red-700 font-bold">×</a>
                    </span>
                @endif
            @endforeach
        </div>
    @endif
</div>

<!-- Tabla -->
<div id="tablaBienesContainer" class="transition-opacity duration-300">
    @include('bienes.partials.table', ['bienes' => $bienes])
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filtrosForm');
    const container = document.getElementById('tablaBienesContainer');
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');
    const errorFechas = document.getElementById('error-msg-fechas');
        window.generarReportePDF = function() {
        // Validar fechas antes de generar PDF
        if (!validarFechas()) {
            Swal.fire({
                icon: 'warning',
                title: 'Rango de fechas inválido',
                text: 'La fecha "hasta" debe ser igual o posterior a la fecha "desde".',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Generando reporte...',
            text: 'Por favor espere mientras se genera el PDF',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Obtener todos los datos del formulario
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Construir URL para el reporte
        const url = '{{ route("bienes.reporte") }}?' + params.toString();

        // Ir a la URL en la misma pestaña
        window.location.href = url;
    };
    
// ========== BOTÓN DE PDF ==========
    // Buscar botón de PDF por ID o clase
    const btnPdf = document.getElementById('btnGenerarPdf') || document.querySelector('.btn-pdf');
    
    if (btnPdf) {
        btnPdf.addEventListener('click', function(e) {
            e.preventDefault();
            generarReportePDF();
        });
    }
     
    // Validación rango fechas
    function validarFechas() {
    const select = document.getElementById(selectId);
    if (select) {
        for(let i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }
        // Disparar evento change
        select.dispatchEvent(new Event('change'));
    }
}

function clearAllFilters() {
    // Limpiar inputs de texto
    document.querySelectorAll('input[type="text"], input[type="number"], input[type="date"]').forEach(input => {
        if (input.id !== 'search') input.value = '';
    });
    
    // Limpiar selects múltiples
    document.querySelectorAll('select[multiple]').forEach(select => {
        for(let i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
        }
    });
    
    // Desmarcar checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Mantener solo el campo de búsqueda si tiene valor
    const searchInput = document.getElementById('search');
    if (searchInput && searchInput.value) {
        searchInput.value = '';
    }
    
    // Redirigir al index sin filtros
    window.location.href = '{{ route("bienes.index") }}';
}
</script>
@endpush
@endsection

