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
@section('scripting')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1/dist/cleave.min.js"></script>
    <script>
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
                });
            }
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

                const input = document.querySelector('#correo');
                const pattern = new RegExp(input.getAttribute('pattern'));

                const mensajeErrorCorreo = 'El correo electrónico ingresado no es correcto.';
                const mensajeErrorCuenta = 'Debe ingresar una cuenta.';

                if (!pattern.test(correo)) {
                    Swal.fire({
                        position: "top-end",
                        icon: "warning",
                        title: `${mensajeErrorCorreo}`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    throw new Error(`${mensajeErrorCorreo}`);
                }

                if(cuenta === '') {
                    Swal.fire({
                        position: "top-end",
                        icon: "warning",
                        title: `${mensajeErrorCuenta}`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    throw new Error(`${mensajeErrorCuenta}`);
                }
            }

            function validarDatosSeccionSolicitante() {
                console.log('se re valida todo');
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
                                validarDatosSeccionSolicitante();
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
                    })
                    .catch(error => console.error('Error:', error));
                } catch (error) {
                    console.log(error.message);
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
                    })
                    .catch(error => console.error('Error:', error));
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
@endsection