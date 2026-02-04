{{-- resources/views/bienes/partials/table.blade.php --}}
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organismo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dependencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Bien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bienes as $bien)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-blue-600 font-mono">{{ $bien->codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $bien->descripcion }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $bien->dependencia->unidadAdministradora->organismo->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $bien->dependencia->unidadAdministradora->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $bien->dependencia->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $bien->dependencia->responsable->nombre ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $tipoBienLabel = $bien->tipo_bien?->label() ?? 'N/A';
                                $tipoBienColor = match($bien->tipo_bien?->value) {
                                    'ELECTRONICO' => 'bg-blue-100 text-blue-800',
                                    'INMUEBLE' => 'bg-amber-100 text-amber-800',
                                    'MOBILIARIO' => 'bg-purple-100 text-purple-800',
                                    'VEHICULO' => 'bg-red-100 text-red-800',
                                    'OTROS' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tipoBienColor }}">
                                {{ $tipoBienLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">
                            {{ number_format((float) $bien->precio, 2, ',', '.') }} Bs.
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($bien->fotografia && file_exists(public_path('storage/' . $bien->fotografia)))
                                <img src="{{ asset('storage/' . $bien->fotografia) }}"
                                     alt="Foto del bien"
                                     class="w-48 h-48 object-cover rounded-lg shadow">
                            @else
                                <span class="text-gray-400 italic">Sin foto</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $estadoValor = $bien->estado?->value;
                                $estadoLabel = $bien->estado?->label() ?? $estadoValor;
                                $estadoColor = match($estadoValor) {
                                    \App\Enums\EstadoBien::ACTIVO->value => 'bg-green-100 text-green-800',
                                    \App\Enums\EstadoBien::DANADO->value => 'bg-red-100 text-red-800',
                                    \App\Enums\EstadoBien::EN_MANTENIMIENTO->value => 'bg-yellow-100 text-yellow-800',
                                    \App\Enums\EstadoBien::EN_CAMINO->value => 'bg-blue-100 text-blue-800',
                                    \App\Enums\EstadoBien::EXTRAVIADO->value => 'bg-gray-200 text-gray-900',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $estadoColor }}">
                                {{ $estadoLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $bien->ubicacion ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ optional($bien->fecha_registro)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right space-x-2">
                            @include('components.action-buttons', [
                                'resource' => 'bienes',
                                'model' => $bien,
                                'confirm' => "¿Seguro que deseas desincorporar este bien?",
                                'label' => $bien->codigo,
                                'buttonText' => 'Desincorporar'
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="px-6 py-12 text-center text-sm text-gray-500 italic">
                            No hay bienes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-6" id="bienesPagination">
    @if($bienes->hasPages())
        {{ $bienes->links() }}
    @endif
</div>