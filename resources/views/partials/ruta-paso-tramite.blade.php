@if ($formulario->puedeCompletar())
    @foreach ($formulario->pasosFormulario as $paso)
        @if ($formulario->muestro($paso['orden']))
            @if($paso['ruta'] === 'partials.etapas-tramite.solicitante')
                <div id="paso-solicitante">
                    @include($paso['ruta'], [
                        'representante' => $representante ?? null,
                        'codigosArea' => $codigosArea ?? null,
                        'caracteres' => $caracteres ?? null
                    ])
                </div>
            @else
                <div>
                    @include($paso['ruta'])
                </div>
            @endif
            @break
        @endif
    @endforeach
@endif

{{-- @include('tiles.modal.operatoria.modal-mostrar-pdf') --}}
<style>
    .titulo-seccion-tramite {
        background-color: lightblue;
        padding: 5px;
    }

    .cuerpo-seccion-tramite {
        padding: 5px;
    }

    .uppercase {
        text-transform: uppercase;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '#boton-buscar-contribuyente', function(event) {
            event.preventDefault();
            const cuit = document.getElementById('documentoSolicitante').value;
            console.log('cuit: ', cuit)

            if (cuit === '' /* || No cumple formato del CUIT */) {
                Swal.fire({
                    position: "top-end",
                    icon: "warning",
                    title: 'Debe ingresar un CUIT',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            } else {
                fetch(`/instanciaTramite/buscarContribuyente/${cuit}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.mensaje) {
                            Swal.fire({
                                position: "top-end",
                                icon: "warning",
                                title: data.mensaje,
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                        }
                        document.getElementById("paso-solicitante").innerHTML = data.htmlVista;
                        inicializarMascaraCuit();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: 'Hubo un problema al procesar la solicitud.',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true
                        });
                    });
            }
        });
    });

    function guardarDatosDelSolicitante() {
        const cuentaUsuario = document.querySelector('#cuentasUsuario').value;
        const cuentaInput = document.querySelector('#cuentaGeneralSinCuentas').value;

        const cuenta = (cuentaUsuario && cuentaUsuario !== 'Otra') ? cuentaUsuario : cuentaInput;
        const correo = document.querySelector('#correo').value;

        fetch('{{ route('instanciaTramite.guardarDatosDelSolicitante') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cuenta, correo }),
        }).then(res => {
            if (!res.ok) {
                console.error('Error al guardar datos');
            }
        });
    }
</script>