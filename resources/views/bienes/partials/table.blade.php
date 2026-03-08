{{-- resources/views/bienes/partials/table.blade.php --}}
<div class="bg-white border border-slate-200 shadow-sm rounded-3xl overflow-hidden reveal">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Código</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Descripción</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Organismo</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Unidad</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Dependencia</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Responsable</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Tipo de Bien</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Precio</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Foto</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Ubicación</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-700 uppercase tracking-widest">Fecha Registro</th>
                    <th class="px-6 py-4 text-right text-xs font-black text-slate-700 uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($bienes as $bien)
                    <tr class="group hover:bg-slate-50 transition-all duration-300 hover:shadow-inner">
                        <td class="px-6 py-5 text-sm font-black font-mono text-[#640B21] tracking-tight">
                            {{ str_pad($bien->codigo, 8, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-5 text-sm font-medium text-slate-900">
                            {{ $bien->descripcion }}
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ $bien->dependencia?->unidadAdministradora?->organismo?->nombre ?? '-' }}
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ $bien->dependencia?->unidadAdministradora?->nombre ?? '-' }}
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ $bien->dependencia?->nombre ?? '-' }}
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ $bien->dependencia?->responsable?->nombre ?? '-' }}
                        </td>
                        <td class="px-6 py-5 text-sm">
                            @php
                                $tipoBienLabel = $bien->tipo_bien?->label() ?? 'N/A';
                                $tipoBienColor = match($bien->tipo_bien?->value) {
                                    'ELECTRONICO'  => 'bg-red-50 text-[#640B21] border border-red-200/70',
                                    'INMUEBLE'     => 'bg-amber-50 text-amber-800 border border-amber-200/70',
                                    'MOBILIARIO'   => 'bg-purple-50 text-purple-800 border border-purple-200/70',
                                    'VEHICULO'     => 'bg-red-100/80 text-red-800 border border-red-200/70',
                                    'OTROS'        => 'bg-gray-100 text-gray-700 border border-gray-200',
                                    default        => 'bg-slate-50 text-slate-600 border border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $tipoBienColor }}">
                                {{ $tipoBienLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm font-bold text-slate-900">
                            {{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.
                        </td>
                        <td class="px-6 py-5 text-sm">
                            @if($bien->fotografia && file_exists(public_path('storage/' . $bien->fotografia)))
                                <img src="{{ asset('storage/' . $bien->fotografia) }}"
                                     alt="Foto del bien"
                                     class="w-20 h-20 md:w-28 md:h-28 object-cover rounded-2xl shadow-md border border-slate-200 group-hover:scale-105 transition-transform duration-500">
                            @else
                                <span class="text-slate-400 italic text-sm font-medium">Sin foto</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-sm">
                            @php
                                $estadoValor = $bien->estado?->value;
                                $estadoLabel = $bien->estado?->label() ?? $estadoValor;
                                $estadoColor = match($estadoValor) {
                                    \App\Enums\EstadoBien::ACTIVO->value            => 'bg-green-100/80 text-green-800 border border-green-200/70',
                                    \App\Enums\EstadoBien::DANADO->value            => 'bg-red-100/80 text-red-800 border border-red-200/70',
                                    \App\Enums\EstadoBien::EN_MANTENIMIENTO->value => 'bg-yellow-100/80 text-yellow-800 border border-yellow-200/70',
                                    \App\Enums\EstadoBien::EN_CAMINO->value         => 'bg-blue-100/80 text-blue-800 border border-blue-200/70',
                                    \App\Enums\EstadoBien::EXTRAVIADO->value        => 'bg-gray-200 text-gray-900 border border-gray-300',
                                    default                                         => 'bg-slate-50 text-slate-700 border border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $estadoColor }}">
                                {{ $estadoLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ $bien->ubicacion ?? '-' }}
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            {{ optional($bien->fecha_registro)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-5 text-sm text-right space-x-2">
                            @include('components.action-buttons', [
                                'resource'   => 'bienes',
                                'model'      => $bien,
                                'confirm'    => "¿Seguro que deseas desincorporar este bien?",
                                'label'      => $bien->codigo,
                                'buttonText' => 'Desincorporar'
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="px-6 py-16 text-center">
                            <div class="text-slate-500 italic text-lg font-medium tracking-wide">
                                No hay bienes registrados en este momento.
                            </div>
                            <p class="mt-2 text-slate-400 text-sm">
                                Puedes crear uno nuevo con el botón superior.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-8 flex justify-center" id="bienesPagination">
    @if($bienes->hasPages())
        {{ $bienes->links('pagination::tailwind') }}
    @endif
</div>
