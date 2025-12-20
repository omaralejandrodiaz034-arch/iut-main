@php
    // Required inputs:
    // - $resource (string): base route name, e.g. 'bienes'
    // - $model: model instance for route model binding
    // Optional:
    // - $canDelete (bool): whether to show delete button (default true)
    // - $confirm (string): confirm message for delete
    $canDelete = $canDelete ?? true;
    $confirm = $confirm ?? '¿Estás seguro? Esta acción no se puede deshacer.';
    // Determine the correct route parameter name (some resource routes use unexpected singular forms)
    try {
        $route = app('router')->getRoutes()->getByName($resource . '.edit');
        if ($route) {
            // Prefer extracting parameter name from the URI pattern like 'bienes/{biene}'
            $uri = $route->uri();
            if (preg_match('/\{([^}]+)\}/', $uri, $m)) {
                $paramName = $m[1];
            } else {
                $paramName = \Illuminate\Support\Str::singular($resource);
            }
        } else {
            $paramName = \Illuminate\Support\Str::singular($resource);
        }
    } catch (\Throwable $e) {
        $paramName = \Illuminate\Support\Str::singular($resource);
    }

    // Build explicit URLs using the discovered parameter name to avoid UrlGenerationException
    try {
        $showUrl = route($resource . '.show', [$paramName => $model->getKey()]);
    } catch (\Throwable $e) {
        // Fallback: try passing the model directly (may fail on some route parameter names)
        try {
            $showUrl = route($resource . '.show', $model);
        } catch (\Throwable $e) {
            $showUrl = '#';
        }
    }

    try {
        $editUrl = route($resource . '.edit', [$paramName => $model->getKey()]);
    } catch (\Throwable $e) {
        try {
            $editUrl = route($resource . '.edit', $model);
        } catch (\Throwable $e) {
            $editUrl = '#';
        }
    }

    try {
        $destroyUrl = route($resource . '.destroy', [$paramName => $model->getKey()]);
    } catch (\Throwable $e) {
        try {
            $destroyUrl = route($resource . '.destroy', $model);
        } catch (\Throwable $e) {
            $destroyUrl = '#';
        }
    }
@endphp

<a href="{{ $showUrl }}"
   class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100">
    <x-heroicon-o-eye class="w-4 h-4 mr-1"/> Ver
</a>

<a href="{{ $editUrl }}"
   class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 bg-yellow-50 rounded hover:bg-yellow-100">
    <x-heroicon-o-pencil-square class="w-4 h-4 mr-1"/> Editar
</a>

@php
    $buttonText = $buttonText ?? 'Eliminar';
@endphp

@if($canDelete)
    <form action="{{ $destroyUrl }}" method="POST" class="inline delete-form" data-can-delete="{{ auth()->user() && auth()->user()->canDeleteData() ? '1' : '0' }}" data-confirm="{{ e($confirm) }}" data-label="{{ e($label ?? '') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">
            <x-heroicon-o-trash class="w-4 h-4 mr-1"/> {{ $buttonText }}
        </button>
    </form>
@endif

@once
    {{-- Modal para confirmación/el mensaje de no permiso (incluido una sola vez por página) con estilos y animación Tailwind --}}
    <div id="delete-modal-backdrop" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 transition-opacity duration-200 ease-out">
        <div id="delete-modal" class="bg-white rounded-lg shadow-2xl max-w-xl w-full mx-4 p-6 transform transition-all duration-200 ease-out scale-95 opacity-0">
            <div class="flex items-start space-x-4">
                <div id="delete-modal-icon" class="flex-shrink-0">
                    {{-- default warning icon (may be replaced for permission denied) --}}
                    <svg class="h-10 w-10 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>

                <div class="flex-1">
                    <h3 id="delete-modal-title" class="text-xl font-semibold text-gray-800">Confirmar acción</h3>
                    <p id="delete-modal-message" class="mt-2 text-sm text-gray-600">¿Estás seguro?</p>

                    <div class="mt-5 flex justify-end space-x-3">
                        <button id="delete-modal-cancel" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition">Cancelar</button>
                        <button id="delete-modal-confirm" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Eliminar</button>
                        <button id="delete-modal-close" class="px-4 py-2 bg-blue-600 text-white rounded-md hidden">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const backdrop = document.getElementById('delete-modal-backdrop');
            const modal = document.getElementById('delete-modal');
            const iconEl = document.getElementById('delete-modal-icon');
            const titleEl = document.getElementById('delete-modal-title');
            const messageEl = document.getElementById('delete-modal-message');
            const btnCancel = document.getElementById('delete-modal-cancel');
            const btnConfirm = document.getElementById('delete-modal-confirm');
            const btnClose = document.getElementById('delete-modal-close');

            let currentForm = null;

            function showBackdrop(){
                // make backdrop a flex container so the modal is centered
                backdrop.classList.remove('hidden');
                backdrop.classList.add('flex');
                // animate opacity
                requestAnimationFrame(()=>{
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                });
            }

            function hideBackdrop(){
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                // after transition: hide and remove flex to restore layout
                setTimeout(()=>{ backdrop.classList.remove('flex'); backdrop.classList.add('hidden'); }, 200);
            }

            function openModal({title, message, showConfirm = true, denied = false}){
                titleEl.textContent = title;
                messageEl.textContent = message;

                if(denied){
                    // change icon to lock
                    iconEl.innerHTML = `\n                        <svg class="h-10 w-10 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3 .895 3 2v1H9v-1c0-1.105 1.343-2 3-2z"/>\n                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11V9a7 7 0 1114 0v2"/>\n                        </svg>\n                    `;
                } else {
                    iconEl.innerHTML = `\n                        <svg class="h-10 w-10 text-red-600" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                          <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z\" />\n                        </svg>\n                    `;
                }

                // show backdrop and animate modal
                showBackdrop();
                // small delay to allow backdrop to appear
                setTimeout(()=>{
                    modal.classList.remove('scale-95','opacity-0');
                    modal.classList.add('scale-100','opacity-100');
                }, 20);

                if(showConfirm){
                    btnConfirm.classList.remove('hidden');
                    btnClose.classList.add('hidden');
                } else {
                    btnConfirm.classList.add('hidden');
                    btnClose.classList.remove('hidden');
                }
            }

            function closeModal(){
                // animate modal out
                modal.classList.remove('scale-100','opacity-100');
                modal.classList.add('scale-95','opacity-0');
                hideBackdrop();
                currentForm = null;
            }

            // Close when clicking on backdrop outside modal
            document.addEventListener('click', function(e){
                if(e.target === backdrop){
                    closeModal();
                }
            });

            btnCancel.addEventListener('click', function(e){
                e.preventDefault();
                closeModal();
            });

            btnClose.addEventListener('click', function(e){
                e.preventDefault();
                closeModal();
            });

            btnConfirm.addEventListener('click', function(e){
                e.preventDefault();
                if(currentForm){
                    currentForm.submit();
                }
            });

            // Intercept delete form submissions
            document.addEventListener('submit', function(e){
                const form = e.target.closest && e.target.closest('.delete-form') || null;
                if(! form) return;

                e.preventDefault();
                currentForm = form;

                const canDelete = form.getAttribute('data-can-delete') === '1';
                const confirmMessage = form.getAttribute('data-confirm') || '¿Estás seguro?';
                const label = form.getAttribute('data-label') || '';

                if(! canDelete){
                    openModal({
                        title: 'Permiso denegado',
                        message: 'No tienes permisos para eliminar datos del sistema. Contacta a un administrador si crees que esto es un error.' + (label ? '\n\n' + label : ''),
                        showConfirm: false,
                        denied: true
                    });
                    return false;
                }

                openModal({
                    title: 'Confirmar eliminación',
                    message: confirmMessage + (label ? '\n\n' + label : ''),
                    showConfirm: true,
                    denied: false
                });

                return false;
            }, true);
        })();
    </script>
@endonce
