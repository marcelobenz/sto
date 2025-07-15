<div style="display: grid; width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 20px;">
    <div>
        <h3>
            FORMULARIO: {{ $formulario->nombre }} ({{ $formulario->categoria }})
        </h3>
        <h3>{{ $formulario->fechaActual }}</h3>
    </div>
    @if ($formulario->llevaMensaje)
        @include('partials.requisitos') 
    @endif
    <div id="pasos-container">
        @include('partials.pasos-container', ['formulario' => $formulario])
    </div>
    <div id="ruta-paso-tramite" style="border: 2px solid gray; margin-bottom: 0.5rem;">
        @include('partials.ruta-paso-tramite', ['formulario' => $formulario, 'solicitante' => $solicitante])
    </div>
    <div id="botones-avance-tramite">
        @include('partials.botones-avance-tramite', ['formulario' => $formulario, 'confirmarDeclaracionChecked' => $confirmarDeclaracionChecked])
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1/dist/cleave.min.js"></script>
    <script>
        function inicializarMascarasSeccionesMultinota() {
            $('.mascara-input').each(function() {
                var $input = $(this);
                var mask = $input.data('mascara');
                if (mask) {
                    $input.inputmask({
                        mask: mask,
                        oncomplete: function() {
                            $input.get(0).setCustomValidity('');
                        },
                        onincomplete: function() {
                            $input.get(0).setCustomValidity('Formato inválido');
                        }
                    });
                }
            });
        }

        function inicializarInputsCajaSeleccionMultiple() {
            document.querySelectorAll('.choices-multiselect').forEach(function (element) {
                new Choices(element, {
                    removeItemButton: true,
                    placeholderValue: 'Seleccione...',
                    searchEnabled: false,
                    shouldSort: false,
                    noChoicesText: '',
                    noResultsText: '',
                    itemSelectText: '',
                });
            });
        }

        function inicializarMascaraCuit() {
            const cuitInput = document.querySelector('#documentoSolicitante');
            if (cuitInput) {
                if (cuitInput.cleaveInstance) {
                    cuitInput.cleaveInstance.destroy();
                }
                cuitInput.cleaveInstance = new Cleave(cuitInput, {
                    delimiter: '-',
                    blocks: [2, 8, 1],
                });
            }
        }

        function inicializarMascaraTelefono() {
            const telefonoInput = document.querySelector('#telefonoSolicitante');
            if (telefonoInput) {
                if (telefonoInput.cleaveInstance) {
                    telefonoInput.cleaveInstance.destroy();
                }
                telefonoInput.cleaveInstance = new Cleave(telefonoInput, {
                    delimiter: '-',
                    blocks: [4, 4],
                    numericOnly: true
                });
            }
        }

        function actualizarCharCount(textarea) {
            if(textarea) {
                const maxLength = textarea.getAttribute("maxlength");
                const currentLength = textarea.value.length;
                const remaining = maxLength - currentLength;
                const charCountInfoElement = document.getElementById(`charCountInfo-${textarea.id}`);
                if(charCountInfoElement) {
                    charCountInfoElement.textContent = `quedan ${remaining} caracteres`;
                }
            }
        }

        function isVisible(input) {
            return input.offsetParent !== null;
        }

        function mostrarCustomTooltip(targetElement, message) {
            eliminarCustomTooltip();

            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';

            // Icon + text
            tooltip.innerHTML = `
                <div class="custom-tooltip-icon">!</div>
                <div>${message}</div>
            `;

            document.body.appendChild(tooltip);

            const rect = targetElement.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            // Se posiciona arriba del elemento
            tooltip.style.top = `${window.scrollY + rect.top - tooltipRect.height - 12}px`;
            tooltip.style.left = `${window.scrollX + rect.left}px`;

            // Desaparece con la interacción del usuario
            ['click', 'keydown', 'change'].forEach(event =>
                targetElement.addEventListener(event, eliminarCustomTooltip, { once: true })
            );
        }

        function eliminarCustomTooltip() {
            const existing = document.querySelector('.custom-tooltip');
            if (existing) existing.remove();
        }

        // Handler para el checkbox de la etapa final "Resumen" que refiere a la confirmación de la declaración jurada
        function handleCheckConfirmarDeclaracion(checkbox) {
            const checked = checkbox.checked;
            refreshBotonesTrasCheckboxDeclaracion(checked);
        }

        function refreshBotonesTrasCheckboxDeclaracion(checked) {
            fetch('{{ route('instanciaTramite.handleCheckConfirmarDeclaracion') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    checked
                }),
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("botones-avance-tramite").innerHTML = data.htmlVista;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: 'Hubo un error al confirmar la declaración.',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            });
        }

        $(document).ready(function () {
            const formulario = @json($formulario);
            
            // Se recorren y guardan en un array los campos de las secciones asociadas
            const secciones = formulario.secciones;
            const campos = [];
            for (const s of secciones) {
                for(const c of s.campos) {
                    campos.push(c);
                }
            }
            let inputsSecciones = [];

            // Se obtiene el orden actual de etapas del formulario
            let ordenActual = @json($getOrdenActual);
            ordenActual -= 1;
            const persona = @json($persona);

            async function validarDatosSeccionInicio() {
                const response = await fetch('/instanciaTramite/session-data');
                const data = await response.json();

                const cuenta = data.cuenta;
                const correo = data.correo;

                const inputs = [
                    document.getElementById('cuentaGeneralSinCuentas'),
                    document.getElementById('correo')
                ];

                let isValid = true;

                for (const input of inputs) {
                    if(isVisible(input)) {
                        if (!input.checkValidity()) {
                            input.focus();
                            input.reportValidity();
                            isValid = false;
                            break;
                        }
                    }
                }

                if (!isValid) {
                    throw new Error('Debe completar todos los datos obligatorios correctamente');
                }
            }

            function validarDatosSeccionSolicitante() {
                const inputs = [
                    document.getElementById('documentoSolicitante'), 
                    document.getElementById('nombreSolicitante'), 
                    document.getElementById('apellidoSolicitante'),
                    document.getElementById('codarea'),
                    document.getElementById('telefonoSolicitante'),
                    document.getElementById('tipoCaracterSolicitante'),
                    document.getElementById('correoSolicitante'),
                    document.getElementById('correoSolicitanteRepetido'),
                    document.getElementById('nombreCalleLoc'),
                    document.getElementById('numeroCalleLoc'),
                    document.getElementById('provinciaLoc'),
                    document.getElementById('localidadLoc'),
                    document.getElementById('codigoPostalLoc'),
                    document.getElementById('paisLoc'),
                ];

                let isValid = true;

                for (const input of inputs) {
                    if (!input.checkValidity()) {
                        input.focus();
                        input.reportValidity();
                        isValid = false;
                        break;
                    }

                    if (input.id === 'correoSolicitanteRepetido') {
                        let correo = document.getElementById('correoSolicitante').value;
                        let correoRepetido = document.getElementById('correoSolicitanteRepetido');

                        // Evitar problemas de case
                        correo = correo.trim().toLowerCase();
                        correoRepetido.value = correoRepetido.value.trim().toLowerCase();

                        if (correo !== correoRepetido.value) {
                            correoRepetido.setCustomValidity('Los correos no coinciden');
                            correoRepetido.focus();
                            correoRepetido.reportValidity();
                            
                            correoRepetido.addEventListener('input', () => {
                                correoRepetido.setCustomValidity('');
                            }, { once: true });
                            throw new Error('Correos no coinciden');
                        }
                    }
                }

                if (!isValid) {
                    const error = new Error('Debe completar todos los datos obligatorios correctamente');
                    error.mostrar = false;

                    throw error;
                }
            }

            function guardarDatosSeccionSolicitante() {
                const documento = document.getElementById('documentoSolicitante').value;
                const nombre = document.getElementById('nombreSolicitante').value;
                const apellido = document.getElementById('apellidoSolicitante').value;
                const codArea = document.getElementById('codarea').value;
                const telefono = document.getElementById('telefonoSolicitante').value;
                const tipoCaracter = document.getElementById('tipoCaracterSolicitante').value;
                const correo = document.getElementById('correoSolicitante').value;
                const nombreCalle = document.getElementById('nombreCalleLoc').value;
                const numeroCalle = document.getElementById('numeroCalleLoc').value;
                const provincia = document.getElementById('provinciaLoc').value;
                const localidad = document.getElementById('localidadLoc').value;
                const codigoPostal = document.getElementById('codigoPostalLoc').value;
                const pais = document.getElementById('paisLoc').value;
                const piso = document.getElementById('piso')?.value;
                const dpto = document.getElementById('dpto')?.value;
                const latitud = document.getElementById('latitudLoc')?.value;
                const longitud = document.getElementById('longitudLoc')?.value;

                fetch('{{ route('instanciaTramite.guardarDatosSeccionSolicitante') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        documento, 
                        nombre,
                        apellido,
                        codArea,
                        telefono,
                        tipoCaracter,
                        correo,
                        nombreCalle,
                        numeroCalle,
                        provincia,
                        localidad,
                        codigoPostal,
                        pais,
                        piso,
                        dpto,
                        latitud,
                        longitud
                    }),
                }).then(res => {
                    if (!res.ok) {
                        console.error('Error al guardar datos del representante');
                    }
                });
            }

            function validarDatosSeccionDatosACompletar() {
                inputsSecciones = campos.map(c => document.getElementById(`${c.id_campo}`));

                let isValid = true;

                // 1. Se validan inputs normales
                for (const input of inputsSecciones) {
                    // Skip selects múltiples
                    if (input.classList.contains('choices-multiselect')) continue;

                    if (!input.checkValidity()) {
                        input.focus();
                        input.reportValidity();
                        isValid = false;
                        break;
                    }
                }

                // 2. Se validan selects múltiples
                if (isValid) {
                    const selects = document.querySelectorAll('.choices-multiselect');

                    for (const select of selects) {
                        const selectedOptions = select.querySelectorAll('option:checked');

                        if (selectedOptions.length === 0) {
                            select.setCustomValidity('Debe seleccionar al menos una opción');

                            const choicesInner = select.closest('.choices')?.querySelector('.choices__inner');
                            if (choicesInner) {
                                mostrarCustomTooltip(choicesInner, 'Debe seleccionar al menos una opción');
                                choicesInner.focus();
                            }

                            isValid = false;
                            break;
                        } else {
                            select.setCustomValidity('');
                            eliminarCustomTooltip();
                        }
                    }
                }

                if (!isValid) {
                    const error = new Error('Debe completar todos los datos obligatorios correctamente');
                    error.mostrar = false;

                    throw error;
                }
            }

            function guardarDatosSeccionDatosACompletar() {
                const arrayCampos = []

                for (const input of inputsSecciones) {
                    let campo = campos.find(c => c.id_campo === Number(input.id));

                    let valor;

                    if (input.multiple) {
                        // Campos de selección múltiple
                        valor = Array.from(input.selectedOptions).map(opt => opt.value);
                    } else {
                        // Campos comunes
                        valor = input.value;
                    }

                    arrayCampos.push({
                        id_campo: campo.id_campo,
                        nombre: campo.nombre,
                        valor: valor
                    });
                }

                fetch('{{ route('instanciaTramite.guardarDatosSeccionDatosACompletar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(arrayCampos),
                }).then(res => {
                    if (!res.ok) {
                        console.error('Error al guardar datos de las secciones asociadas');
                    }
                });
            }

            function guardarDatosSeccionInformacionAdicional() {
                const informacionAdicional = document.getElementById('informacionAdicional')?.value;

                fetch('{{ route('instanciaTramite.guardarDatosSeccionInformacionAdicional') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        informacionAdicional
                    }),
                }).then(res => {
                    if (!res.ok) {
                        console.error('Error al guardar información adicional');
                    }
                });
            }

            $(document).on('click', '#boton-avanzar-paso', async function () {
                try {
                    ordenActual += 1;

                    switch(ordenActual) {
                        case 1:
                            await validarDatosSeccionInicio();
                            break;
                        case 2:
                            if(persona === 'Juridica') {
                                // Seccion 'Datos del Representante'
                                validarDatosSeccionSolicitante();
                                guardarDatosSeccionSolicitante();
                            } else {
                                // Seccion 'Datos a Completar'
                                
                                //validarDatosSeccionDatosACompletar();
                                //guardarDatosSeccionSolicitante();
                            }
                            break;
                        case 3:
                            if(persona === 'Juridica') {
                                // Seccion 'Datos del Completar'
                                validarDatosSeccionDatosACompletar();
                                guardarDatosSeccionDatosACompletar();
                            } else {
                                // Seccion 'Información Adicional'

                                //guardarDatosSeccionInformacionAdicional();
                            }
                            break;
                        case 4:
                            if(persona === 'Juridica') {
                                // Seccion 'Información Adicional'
                                guardarDatosSeccionInformacionAdicional();
                            } else {
                                // Seccion 'Adjuntar Documentación'

                                //validarDatosSeccionAdjuntarDocumentacion(); //Esta en ruta-paso-tramite
                                //guardar -> se hace en ruta-paso-tramite con los handlers de comentario, archivo, subir archivo y eliminar archivo
                            }
                            break;
                        case 5:
                            if(persona === 'Juridica') {
                                // Seccion 'Adjuntar Documentación'
                                const result = validarDatosSeccionAdjuntarDocumentacion(); //Esta en ruta-paso-tramite
                                if(!result) {
                                    const error = new Error('Debe adjuntar la documentación solicitada');
                                    error.mostrar = true;

                                    throw error;
                                }
                                //guardar -> se hace en ruta-paso-tramite con los handlers de comentario, archivo, subir archivo y eliminar archivo
                            } else {
                                
                            }
                            break;
                        default:
                            break;
                    }

                    fetch('/instanciaTramite/avanzarPaso')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("pasos-container").innerHTML = data.htmlPasos;
                        document.getElementById('ruta-paso-tramite').innerHTML = data.htmlRuta;
                        document.getElementById("botones-avance-tramite").innerHTML = data.htmlBotones;
                        inicializarMascaraCuit();
                        inicializarMascaraTelefono();
                        inicializarMascarasSeccionesMultinota();
                        inicializarInputsCajaSeleccionMultiple();
                        actualizarCharCount(document.getElementById("informacionAdicional"));
                        for (const input of inputsSecciones) {
                            if (input.tagName === 'TEXTAREA') {
                                actualizarCharCount(input);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: `${error.message}`,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    })
                } catch (error) {
                    ordenActual -= 1;
                    console.error(error.message);

                    if(error.mostrar) {
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: `${error.message}`,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }
                }
            });

            $(document).on('click', '#boton-registrar-tramite', async function (e) {
                e.preventDefault(); // ← importante si está en un <form>

                try {
                    const response = await fetch('/instanciaTramite/registrarTramite');

                    if (!response.ok) throw new Error('Error HTTP ' + response.status);

                    const data = await response.json();

                    if (data.url) {
                        window.location.href = data.url; // ← redirige correctamente
                    } else {
                        throw new Error('No se recibió una URL válida');
                    }

                } catch (error) {
                    console.error('Error:', error);

                    Swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: error.message,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            });

            $(document).on('click', '#boton-retroceder-paso', function () {
                ordenActual -= 1;

                fetch('/instanciaTramite/retrocederPaso')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("pasos-container").innerHTML = data.htmlPasos;
                        document.getElementById('ruta-paso-tramite').innerHTML = data.htmlRuta;
                        document.getElementById("botones-avance-tramite").innerHTML = data.htmlBotones;
                        inicializarMascaraCuit();
                        inicializarMascaraTelefono();
                        inicializarMascarasSeccionesMultinota();
                        inicializarInputsCajaSeleccionMultiple();
                        actualizarCharCount(document.getElementById("informacionAdicional"));
                        for (const input of inputsSecciones) {
                            if (input.tagName === 'TEXTAREA') {
                                actualizarCharCount(input);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: `${error.message}`,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    })
            });
        });
    </script>
    <style>
        .cont-pasos .f1-steps {
            line-height: 15px;
            text-align: center;
        }

        .cont-pasos strong { font-weight: 500; }

        .cont-pasos a, .cont-pasos a:hover, .cont-pasos a:focus {
            color: #f35b3f;
            text-decoration: none;
            -o-transition: all .3s; -moz-transition: all .3s; -webkit-transition: all .3s; -ms-transition: all .3s; transition: all .3s;
        }
       .f1 {
            word-break: break-word;
        }

        .f1 h3 {
            margin-top: 0; margin-bottom: 5px; text-transform: uppercase;
        }

        .f1-steps {
            overflow: hidden; position: relative; margin: 2em 2em 2em 2em;
        }

        .f1-progress { 
            position: absolute; top: 24px; left: 0; width: 100%; height: 1px; background: #ddd; 
        }

        .f1-progress-line { 
            position: absolute; top: 0; left: 0; height: 2px; background: #27ace3; 
        }

        .f1-step { 
            position: relative; float: left; padding: 0 5px; 
        }

        .f1-step-icon {
            display: inline-block; width: 40px; height: 40px; margin-top: 4px; background: #ddd;
            font-size: 16px; color: #666; line-height: 40px;
            -moz-border-radius: 50%; -webkit-border-radius: 50%; border-radius: 50%;
        }
        .f1-step.activated .f1-step-icon {
            background: #27ace3;
            border: 1px solid #27ace3;
            color: #f35b3f;
            line-height: 38px;
            margin: 4px;
        }
        .f1-step.activated .f1-step-icon i {
            color: #ffffff;
        }

        .f1-step.active .f1-step-icon {
            width: 48px; height: 48px; margin-top: 0; background: #27ace3; font-size: 22px; line-height: 48px;
        }
        .f1-step.active .f1-step-icon i {
            color: #fff;
        }

        .f1-step p { 
            color: #ccc; 
        }

        .f1-step.activated p { 
            color: #27ace3; margin: 4px;
        }

        .f1-step.active p { 
            color: #27ace3; margin: 4px;
        }

        .f1 fieldset { 
            display: none; text-align: left; 
        }

        .f1-buttons { 
            text-align: right; margin-bottom: 50px;
        }

        .f1 .input-error { 
            border-color: #f35b3f; 
        }
        .f1-steps p {
            margin-bottom: 0;
            margin-top: 4px;
        }

        .custom-tooltip {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 8px;

            background-color: white;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            padding: 10px 12px;
            font-size: 0.9rem;
            color: #000;
            max-width: 260px;
            z-index: 1000;
        }

        .custom-tooltip::before {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid white;
            filter: drop-shadow(0 -1px 1px rgba(0,0,0,0.1));
        }

        .custom-tooltip-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            background-color: #FF8C00;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.2rem;
            font-family: sans-serif;
        }
    </style>
@endpush