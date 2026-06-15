
        document.addEventListener('DOMContentLoaded', function() {
            /* 1. Lógica de Responsable */
            const dependenciasData = {
                
            };

            const depSelect = document.getElementById('dependencia_id');
            const respDisplay = document.getElementById('responsable_display');

            if (depSelect && respDisplay) {
                if (depSelect.value) {
                    respDisplay.textContent = dependenciasData[depSelect.value] || 'Seleccione una dependencia...';
                }

                depSelect.addEventListener('change', function () {
                    const responsable = dependenciasData[this.value];
                    respDisplay.textContent = responsable || 'Seleccione una dependencia...';
                    if (responsable && responsable !== 'Sin responsable asignado') {
                        respDisplay.classList.add('text-gray-900', 'font-bold');
                        respDisplay.classList.remove('text-gray-500', 'italic');
                    } else {
                        respDisplay.classList.remove('text-gray-900', 'font-bold');
                        respDisplay.classList.add('text-gray-500', 'italic');
                    }
                });
            }

            /* 2. Lógica de Código con Sugerencia por Dependencia */
            const prefijoInput = document.getElementById('prefijo_bien');
            const secuencialInput = document.getElementById('codigo_secuencial');
            const codigoCompletoInput = document.getElementById('codigo_completo');
            const sugerenciaContainer = document.getElementById('sugerencia-container');
            const spanSugerencia = document.getElementById('span-sugerencia');
            const btnSugerencia = document.getElementById('btn-sugerencia');

            const baseUrl = "BLADE_EXPR";
            let codigoSugeridoDependencia = null;

            function actualizarSugerencia(codigo) {
                codigoSugeridoDependencia = codigo;
                prefijoInput.value = codigo.substring(0, 6);
                spanSugerencia.textContent = codigo;
                sugerenciaContainer.classList.remove('hidden');
                actualizarCodigoCompleto();
            }

            function ocultarSugerencia() {
                codigoSugeridoDependencia = null;
                sugerenciaContainer.classList.add('hidden');
            }

            function actualizarCodigoCompleto() {
                const prefijo = prefijoInput.value || '';
                const secuencial = secuencialInput.value || '';
                codigoCompletoInput.value = prefijo + secuencial;
            }

            function obtenerSugerencia(dependenciaId) {
                if (!dependenciaId) {
                    ocultarSugerencia();
                    prefijoInput.value = '';
                    secuencialInput.value = '';
                    codigoCompletoInput.value = '';
                    return;
                }

                fetch(`${baseUrl}/${dependenciaId}/recomendar-codigo`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                if (data.success === false && data.error === 'rango_exhausto') {
                                    alert(data.mensaje);
                                    ocultarSugerencia();
                                    prefijoInput.value = '';
                                    secuencialInput.value = '';
                                    codigoCompletoInput.value = '';
                                } else {
                                    throw new Error('Error al obtener sugerencia');
                                }
                            }).catch(() => { throw new Error('Error de red'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.success) {
                            actualizarSugerencia(data.codigo);
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        ocultarSugerencia();
                    });
            }

            depSelect.addEventListener('change', function () {
                const depId = this.value;
                prefijoInput.value = '';
                secuencialInput.value = '';
                codigoCompletoInput.value = '';
                ocultarSugerencia();
                if (depId) obtenerSugerencia(depId);
            });

            if (secuencialInput) {
                secuencialInput.addEventListener('input', function (e) {
                    let cleaned = this.value.replace(/\D/g, '').slice(0, 4);
                    this.value = cleaned;
                    actualizarCodigoCompleto();

                    if (codigoSugeridoDependencia && codigoCompletoInput.value !== codigoSugeridoDependencia) {
                        spanSugerencia.textContent = codigoSugeridoDependencia;
                        sugerenciaContainer.classList.remove('hidden');
                    } else if (sugerenciaContainer) {
                        sugerenciaContainer.classList.add('hidden');
                    }
                });

                secuencialInput.addEventListener('blur', function () {
                    if (this.value && this.value.length > 0 && this.value.length < 4) {
                        this.value = this.value.padStart(4, '0');
                        actualizarCodigoCompleto();
                    }
                });
            }

            if (btnSugerencia && sugerenciaContainer) {
                btnSugerencia.addEventListener('click', function () {
                    if (codigoSugeridoDependencia) {
                        prefijoInput.value = codigoSugeridoDependencia.substring(0, 6);
                        secuencialInput.value = codigoSugeridoDependencia.substring(6);
                        codigoCompletoInput.value = codigoSugeridoDependencia;
                        sugerenciaContainer.classList.add('hidden');
                    }
                });
            }

            /* 3. Límite de Caracteres en Descripción */
            const descTextarea = document.getElementById('descripcion');
            const charCount = document.getElementById('char-count');

            if (descTextarea && charCount) {
                function updateCharCount() {
                    const len = descTextarea.value.length;
                    charCount.textContent = `${len} / 255`;
                    if (len >= 255) {
                        charCount.classList.add('text-red-500');
                        charCount.classList.remove('text-gray-400');
                    } else {
                        charCount.classList.remove('text-red-500');
                        charCount.classList.add('text-gray-400');
                    }
                }

                descTextarea.addEventListener('input', updateCharCount);
                updateCharCount();
            }

            /* 4. Campos Dinámicos */
            const camposPorTipo = {
                'ELECTRONICO': {
                    isParent: true,
                    subtipos: {
                        'MONITOR': ['serial', 'pantalla'],
                        'PC': ['serial', 'procesador', 'memoria', 'almacenamiento'],
                        'IMPRESORA': ['serial', 'modelo'],
                        'TELEVISOR': ['serial', 'pantalla', 'modelo'],
                        'LAPTOP': ['serial', 'procesador', 'memoria', 'almacenamiento', 'pantalla', 'modelo'],
                        'TABLET': ['serial', 'modelo', 'pantalla'],
                        'OTRO': ['serial', 'modelo']
                    },
                    fields: [
                        { name: 'subtipo', label: 'Subtipo', type: 'select', options: ['MONITOR', 'PC', 'IMPRESORA', 'TELEVISOR', 'LAPTOP', 'TABLET', 'OTRO'], required: true },
                        { name: 'serial', label: 'Número de Serie', type: 'text', required: true, maxlength: 50, inputmode: 'numeric', pattern: '\\d*' },
                        { name: 'modelo', label: 'Modelo', type: 'text' },
                        { name: 'procesador', label: 'Procesador', type: 'text' },
                        { name: 'memoria', label: 'RAM/Memoria', type: 'text' },
                        { name: 'almacenamiento', label: 'Almacenamiento', type: 'text' },
                        { name: 'pantalla', label: 'Pulgadas de Pantalla', type: 'text' }
                    ]
                },
                'VEHICULO': {
                    fields: [
                        { name: 'placa', label: 'Número de Placa', type: 'text', required: true, maxlength: 20 },
                        { name: 'marca', label: 'Marca', type: 'text', required: true, maxlength: 100 },
                        { name: 'modelo', label: 'Modelo', type: 'text', required: true, maxlength: 100 },
                        { name: 'motor', label: 'Serial de Motor', type: 'text', maxlength: 100 },
                        { name: 'chasis', label: 'Serial de Carrocería', type: 'text', maxlength: 100 }
                    ]
                },
                'MOBILIARIO': {
                    fields: [
                        { name: 'material', label: 'Material', type: 'text' },
                        { name: 'color', label: 'Color', type: 'text' },
                        { name: 'dimensiones', label: 'Dimensiones', type: 'text' }
                    ]
                },
                'OTROS': {
                    fields: [
                        { name: 'especificaciones', label: 'Especificaciones Extra', type: 'textarea' }
                    ]
                }
            };

            const validacionesCampo = {
                'serial': { required: true, maxlength: 50, label: 'Número de Serie', pattern: /^\d+$/ },
                'placa': { required: true, maxlength: 20, label: 'Número de Placa' },
                'marca': { required: true, maxlength: 100, label: 'Marca' },
                'modelo': { required: true, maxlength: 100, label: 'Modelo' },
                'motor': { required: false, maxlength: 100, label: 'Serial de Motor' },
                'chasis': { required: false, maxlength: 100, label: 'Serial de Carrocería' }
            };

            const tipoBienSelect = document.getElementById('tipo_bien');
            const container = document.getElementById('campos-tipo-bien');

            if (tipoBienSelect && container) {
                function loadDynamicFields() {
                    const tipo = tipoBienSelect.value;
                    container.innerHTML = '';

                    if (!tipo || !camposPorTipo[tipo]) return;

                    const config = camposPorTipo[tipo];
                    let html = `<div class="bg-blue-50 border border-blue-200 p-6 rounded-xl space-y-4">
                                    <h3 class="text-blue-800 font-bold text-sm uppercase flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Detalles Técnicos del ${tipo}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

                    config.fields.forEach(campo => {
                        if (campo.type === 'select') {
                            html += `<div>
                                        <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label} ${campo.required ? '<span class="text-red-500">*</span>' : ''}</label>
                                        <select name="${campo.name}" id="subtipo_selector" ${campo.required ? 'required' : ''} class="w-full px-4 py-2 border border-blue-200 rounded-lg outline-none bg-white">
                                            <option value="">Seleccione...</option>
                                            ${campo.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                        </select>
                                    </div>`;
                        } else if (campo.type === 'textarea') {
                            html += `<div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label}</label>
                                        <textarea name="${campo.name}" data-field="${campo.name}" class="dynamic-field w-full px-4 py-2 border border-blue-200 rounded-lg bg-white" rows="2"></textarea>
                                    </div>`;
                        } else {
                            const isReadonly = config.isParent ? 'readonly' : '';
                            const bgClass = config.isParent ? 'bg-gray-100' : 'bg-white';
                            const defaultValue = config.isParent ? 'S/N' : '';
                            const maxlengthAttr = campo.maxlength ? `maxlength="${campo.maxlength}"` : '';

                            html += `<div>
                                        <label class="block text-xs font-bold text-blue-700 mb-1">${campo.label} ${campo.required ? '<span class="text-red-500">*</span>' : ''}</label>
                                        <input type="text" name="${campo.name}" data-field="${campo.name}"
                                            class="dynamic-field w-full px-4 py-2 border border-blue-200 rounded-lg ${bgClass}"
                                            ${isReadonly} value="${defaultValue}" ${maxlengthAttr}>
                                    </div>`;
                        }
                    });

                    html += `</div></div>`;
                    container.innerHTML = html;

                    if (config.isParent) {
                        const selector = document.getElementById('subtipo_selector');
                        if (selector) {
                            selector.addEventListener('change', function() {
                                const st = this.value;
                                const camposVisibles = config.subtipos[st] || [];

                                document.querySelectorAll('.dynamic-field').forEach(input => {
                                    const fieldName = input.getAttribute('data-field');
                                    if (camposVisibles.includes(fieldName)) {
                                        input.classList.remove('bg-gray-100');
                                        input.classList.add('bg-white');
                                        input.removeAttribute('readonly');
                                        if(input.value === 'S/N') input.value = '';
                                    } else {
                                        input.classList.add('bg-gray-100');
                                        input.classList.remove('bg-white');
                                        input.setAttribute('readonly', true);
                                        input.value = 'S/N';
                                    }
                                });
                            });
                        }
                    }
                }

                tipoBienSelect.addEventListener('change', loadDynamicFields);

                if (tipoBienSelect.value) {
                    loadDynamicFields();
                }
            }

                /* 4. Formato de precio tipo Pago Móvil (sin step nativo) */
            const precioInput = document.getElementById('precio');
            if (precioInput) {
                const formatearPrecio = (valor) => {
                    const limpio = String(valor).replace(/[^0-9.,]/g, '').replace(',', '.');
                    if (limpio === '' || isNaN(limpio)) return '';
                    const num = parseFloat(limpio);
                    return isNaN(num) ? '' : num.toFixed(2);
                };

                precioInput.addEventListener('blur', () => {
                    if (precioInput.value !== '') {
                        precioInput.value = formatearPrecio(precioInput.value);
                    }
                });

                precioInput.addEventListener('input', () => {
                    const val = precioInput.value;
                    if (val && !/^\d+(\.\d{0,2})?$/.test(val)) {
                        precioInput.value = val.slice(0, -1);
                    }
                });
            }

            /* 5. Lógica de donación */
            const donacionCheckbox = document.getElementById('es_donacion');
            const camposDonacion = document.getElementById('campos-donacion');
            const fotoInput = document.getElementById('fotografia');
            const fotoError = document.getElementById('foto-error');
            const tipoDonanteInput = document.getElementById('tipo_donante');
            const donanteDocumentoInput = document.getElementById('donante_documento');

            const actualizarDocumentoDonante = () => {
                if (!donanteDocumentoInput || !tipoDonanteInput) {
                    return;
                }

                const tipo = tipoDonanteInput.value;
                if (tipo === 'PERSONA') {
                    donanteDocumentoInput.placeholder = 'V-12345678 / E-12345678 / P-12345678';
                    donanteDocumentoInput.setAttribute('maxlength', '10');
                } else if (tipo === 'INSTITUCION') {
                    donanteDocumentoInput.placeholder = 'J-123456789 / G-123456789 / P-123456789';
                    donanteDocumentoInput.setAttribute('maxlength', '11');
                } else {
                    donanteDocumentoInput.placeholder = 'Ej: V-12345678 o J-123456789';
                    donanteDocumentoInput.setAttribute('maxlength', '11');
                }
            };

            if (tipoDonanteInput) {
                tipoDonanteInput.addEventListener('change', actualizarDocumentoDonante);
                actualizarDocumentoDonante();
            }

            if (donanteDocumentoInput) {
                donanteDocumentoInput.addEventListener('input', function () {
                    let value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
                    if (value.length > 1 && /^[A-Z][0-9]/.test(value) && value[1] !== '-') {
                        value = value.charAt(0) + '-' + value.slice(1);
                    }
                    this.value = value.slice(0, parseInt(this.getAttribute('maxlength') || '11', 10));
                });
            }

            if (fotoInput && fotoError) {
                fotoInput.addEventListener('change', () => {
                    const file = fotoInput.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];

                    if (file && !validTypes.includes(file.type)) {
                        fotoError.classList.remove('hidden');
                        fotoInput.value = '';
                    } else {
                        fotoError.classList.add('hidden');
                    }
                });
            }

            if (donacionCheckbox && camposDonacion && precioInput) {
                const toggleDonacion = () => {
                    const activo = donacionCheckbox.checked;
                    camposDonacion.classList.toggle('hidden', !activo);
                    precioInput.value = activo ? '0.00' : precioInput.defaultValue || '';
                    precioInput.readOnly = activo;

                    if (!activo && precioInput.readOnly) {
                        precioInput.readOnly = false;
                    }
                };

                donacionCheckbox.addEventListener('change', toggleDonacion);
                toggleDonacion();
            }

            /* 6. Validación antes de enviar */
            const form = document.querySelector('form[action*="bienes"]');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const codigoCompletoInput = document.getElementById('codigo_completo');
                    const codigoValue = codigoCompletoInput ? codigoCompletoInput.value.trim() : '';
                    const descripcion = document.getElementById('descripcion');
                    const descripcionValue = descripcion ? descripcion.value.trim() : '';
                    const tipo = tipoBienSelect ? tipoBienSelect.value : '';
                    const estado = document.getElementById('estado');
                    const estadoValue = estado ? estado.value : '';
                    const dependenciaSel = depSelect ? depSelect.value : '';

                    if (!codigoValue || codigoValue.length !== 10 || !/^\d{10}$/.test(codigoValue)) {
                        e.preventDefault();
                        alert('El código debe contener exactamente 10 dígitos numéricos.');
                        if (secuencialInput) secuencialInput.focus();
                        return;
                    }

                    if (!descripcionValue) {
                        e.preventDefault();
                        alert('La descripción es obligatoria.');
                        if (descripcion) descripcion.focus();
                        return;
                    }

                    if (!dependenciaSel) {
                        e.preventDefault();
                        alert('Debe asignar una dependencia antes de guardar el bien.');
                        if (depSelect) depSelect.focus();
                        return;
                    }

                    if (!tipo) {
                        e.preventDefault();
                        alert('Debe seleccionar el tipo de bien.');
                        if (tipoBienSelect) tipoBienSelect.focus();
                        return;
                    }

                    if (!estadoValue) {
                        e.preventDefault();
                        alert('Debe seleccionar el estado del bien.');
                        if (estado) estado.focus();
                        return;
                    }

                    const fotoInputSubmit = document.getElementById('fotografia');
                    if (fotoInputSubmit && fotoInputSubmit.files.length > 0) {
                        const file = fotoInputSubmit.files[0];
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                        if (!validTypes.includes(file.type)) {
                            e.preventDefault();
                            alert('Solo se permiten archivos de imagen (jpeg, png, jpg, gif, webp).');
                            fotoInputSubmit.focus();
                            return;
                        }
                    }

                    const dynamicFields = document.querySelectorAll('.dynamic-field');
                    let camposDinamicosValidos = true;
                    let camposDinamicosErrores = [];

                    dynamicFields.forEach(input => {
                        const fieldName = input.getAttribute('data-field');
                        const val = input.value.trim();
                        const validacion = validacionesCampo[fieldName];

                        if (validacion) {
                            if (validacion.required && !val) {
                                camposDinamicosValidos = false;
                                camposDinamicosErrores.push(`El campo "${validacion.label}" es obligatorio.`);
                                input.classList.add('border-red-500');
                                input.classList.remove('border-blue-200');
                            } else if (validacion.maxlength && val.length > validacion.maxlength) {
                                camposDinamicosValidos = false;
                                camposDinamicosErrores.push(`El campo "${validacion.label}" no debe exceder ${validacion.maxlength} caracteres.`);
                                input.classList.add('border-red-500');
                                input.classList.remove('border-blue-200');
                            } else if (validacion.pattern && val && !validacion.pattern.test(val)) {
                                camposDinamicosValidos = false;
                                camposDinamicosErrores.push(`El campo "${validacion.label}" solo puede contener dígitos.`);
                                input.classList.add('border-red-500');
                                input.classList.remove('border-blue-200');
                            } else {
                                input.classList.remove('border-red-500');
                                input.classList.add('border-blue-200');
                            }
                        }
                    });

                    if (!camposDinamicosValidos) {
                        e.preventDefault();
                        alert(camposDinamicosErrores.join('\n'));
                        return;
                    }

                    if (donacionCheckbox && donacionCheckbox.checked) {
                        const tipoDonante = document.getElementById('tipo_donante');
                        const donanteNombre = document.querySelector('input[name="donante_nombre"]');
                        const donanteDocumento = document.querySelector('input[name="donante_documento"]');
                        const donanteDireccion = document.querySelector('input[name="donante_direccion"]');

                        if (!tipoDonante || !tipoDonante.value) {
                            e.preventDefault();
                            alert('Debe seleccionar el tipo de donante para el bien donado.');
                            tipoDonante?.focus();
                            return;
                        }

                        if (!donanteNombre?.value.trim()) {
                            e.preventDefault();
                            alert('El nombre del donante es obligatorio para bienes donados.');
                            donanteNombre?.focus();
                            return;
                        }

                        if (!donanteDireccion?.value.trim()) {
                            e.preventDefault();
                            alert('La dirección del donante es obligatoria para bienes donados.');
                            donanteDireccion?.focus();
                            return;
                        }

                        if (!donanteDocumento?.value.trim()) {
                            e.preventDefault();
                            alert('El documento del donante es obligatorio para bienes donados.');
                            donanteDocumento?.focus();
                            return;
                        }

                        const documento = donanteDocumento.value.trim().toUpperCase();
                        let documentoValido = false;

                        if (tipoDonante.value === 'PERSONA') {
                            documentoValido = /^[VEP]-\d{7,8}$/.test(documento);
                        } else if (tipoDonante.value === 'INSTITUCION') {
                            documentoValido = /^[JGP]-\d{8,9}$/.test(documento);
                        }

                        if (!documentoValido) {
                            e.preventDefault();
                            alert('El documento del donante no tiene el formato válido: V-12345678, E-12345678, P-12345678 o J-123456789.');
                            donanteDocumento?.focus();
                            return;
                        }
                    }

                    const fechaInput = document.getElementById('fecha_registro');
                    if (fechaInput && fechaInput.value) {
                        const fechaSeleccionada = new Date(fechaInput.value);
                        const fechaMinima = new Date('2000-01-01');
                        const fechaMaxima = new Date();

                        if (fechaSeleccionada < fechaMinima) {
                            e.preventDefault();
                            alert('La fecha de adquisición no puede ser anterior al año 2000.');
                            fechaInput.focus();
                            return;
                        }

                        if (fechaSeleccionada > fechaMaxima) {
                            e.preventDefault();
                            alert('La fecha de adquisición no puede ser futura.');
                            fechaInput.focus();
                            return;
                        }
                    }
                });
            }
        });
    