@php
    // Inputs: $resource (base route name), $model
    $resource = $resource ?? null;
    $model = $model ?? null;
@endphp

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
            <x-heroicon-o-arrow-left class="w-4 h-4 mr-2"/> Volver
        </a>

        @if(isset($resource) && isset($model))
            @php
                // Resolve parameter name for routes
                try {
                    $param = app('router')->getRoutes()->getByName($resource . '.edit')
                        ? preg_replace('/.*\{([^}]+)\}.*/', '$1', app('router')->getRoutes()->getByName($resource . '.edit')->uri())
                        : \Illuminate\Support\Str::singular($resource);
                } catch (\Throwable $e) {
                    $param = \Illuminate\Support\Str::singular($resource);
                }

                // Edit URL (hide for movimientos and only if route exists)
                try { $editUrl = route($resource . '.edit', [$param => $model->getKey()]); } catch (\Throwable $e) { $editUrl = null; }

                // Try multiple possible PDF route names to increase robustness
                $pdfUrl = null;
                $pdfCandidates = [
                    $resource . '.pdf',
                    $resource . '.reporte',
                    $resource . '.reportePdf',
                    $resource . '.download',
                    $resource . '.export'
                ];
                foreach ($pdfCandidates as $rname) {
                    try {
                        if ($rname === ($resource . '.pdf')) {
                            $pdfUrl = route($rname, $model);
                        } else {
                            $pdfUrl = route($rname, array_merge(request()->query(), ['id' => $model->getKey()]));
                        }
                        break;
                    } catch (\Throwable $e) {
                        $pdfUrl = null;
                    }
                }
                $pdfUrl = $pdfUrl ?? '#';
            @endphp

            {{-- Editar: oculto para movimientos y si no existe ruta o permiso --}}
            @if($resource !== 'movimientos' && $editUrl)
                <a href="{{ $editUrl }}" class="inline-flex items-center px-3 py-2 bg-yellow-50 text-yellow-700 rounded-md hover:bg-yellow-100">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2"/> Editar
                </a>
            @endif

            <a href="{{ $pdfUrl }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 pdf-link" data-heavy="1">
                <x-heroicon-o-printer class="w-4 h-4 mr-2"/> PDF
            </a>

            <button onclick="window.print()" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">
                <x-heroicon-o-printer class="w-4 h-4 mr-2"/> Imprimir
            </button>

            @if(method_exists($model, 'acta_desincorporacion') || isset($model->acta_desincorporacion))
                @if($model->acta_desincorporacion)
                    <a href="{{ Storage::disk('public')->url($model->acta_desincorporacion) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-md hover:bg-green-100">
                        <x-heroicon-o-document-text class="w-4 h-4 mr-2"/> Ver Acta
                    </a>
                @endif
            @endif

            @if($resource === 'movimientos' && $model->tipo === 'TRASLADO' && $model->acta_path)
                <a href="{{ Storage::disk('public')->url($model->acta_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100">
                    <x-heroicon-o-document-text class="w-4 h-4 mr-2"/> Ver Acta de Traslado
                </a>
            @endif
        @endif
    </div>

    <div class="text-sm text-gray-500">Última modificación: {{ optional($model)->updated_at?->diffForHumans() ?? '—' }}</div>
</div>
@push('scripts')
<script>
    document.addEventListener('click', function(e) {
        const a = e.target.closest && e.target.closest('.pdf-link');
        if (!a) return;
        // UX: show quick toast/spinner so user knows PDF is generating
        try {
            const toast = document.createElement('div');
            toast.textContent = 'Generando PDF — por favor espere...';
            toast.className = 'fixed bottom-6 right-6 bg-black text-white px-4 py-2 rounded shadow-lg z-50';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 6000);
        } catch (err) {
            // ignore
        }
    }, { passive: true });
</script>
@endpush
