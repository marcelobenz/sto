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
        @include('partials.ruta-paso-tramite', ['formulario' => $formulario, 'instanciaMultinota' => $instanciaMultinota])
    </div>
    <div id="botones-avance-tramite">
        @include('partials.botones-avance-tramite', ['formulario' => $formulario])
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
                    $input.inputmask(mask);
                }
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

        function isVisible(input) {
            return input.offsetParent !== null;
        }

        $(document).ready(function () {
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
                    error.tipo = 'validacion';

                    throw error;
                }
            }

            function guardarDatosDelRepresentante() {
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

                fetch('{{ route('instanciaTramite.guardarDatosDelRepresentante') }}', {
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

            $(document).on('click', '#boton-avanzar-paso', async function () {
                try {
                    ordenActual += 1;

                    switch(ordenActual) {
                        case 1:
                            await validarDatosSeccionInicio();
                            break;
                        case 2:
                            if(persona === 'Juridica') {
                                const inputs = validarDatosSeccionSolicitante();
                                guardarDatosDelRepresentante();
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

                    if(!error.tipo === 'validacion') {
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
    </style>
@endpush