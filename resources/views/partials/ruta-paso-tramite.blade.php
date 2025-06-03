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
            @elseif($paso['ruta'] === 'partials.etapas-tramite.adjuntar-documentacion')
                <div id="paso-adjuntar-documentacion">
                    @include($paso['ruta'], [
                        'archivos' => $archivos ?? null
                    ])
                </div>
            @else
                <div>
                    @include($paso['ruta'], [
                        'instanciaMultinota' => $instanciaMultinota,
                        'formulario' => $formulario
                    ])
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
@push('scripts')
<script>
    let archivos = @json($archivos);
    
    $(document).ready(function() {
        $(document).on('click', '#boton-buscar-contribuyente', function(event) {
            event.preventDefault();
            const cuit = document.getElementById('documentoSolicitante').value;

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
                        inicializarMascaraTelefono();
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

        $(document).on('click', '#boton-subir-archivo', function () {
            $('#archivo').click();
        });

        $(document).on('change', '#archivo', function () {
            const input = document.getElementById('archivo');
            const archivo = input.files[0];

            if (!archivo) {
                alert('Selecciona un archivo.');
                return;
            }

            const formData = new FormData();
            formData.append('archivo', archivo);

            fetch('{{ route('archivo.subirTemporal') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("paso-adjuntar-documentacion").innerHTML = data.htmlVista;
                archivos = data.archivos;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: 'Hubo un problema al subir el archivo.',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            });
        });

        $(document).on('change', '.comentario', function () {
            const comentario = $(this).val();
            const fechaCarga = $(this).data('fecha');

            fetch('{{ route('archivo.cargarComentario') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    comentario,
                    fechaCarga
                }),
            })
        });

        $(document).on('click', '.boton-eliminar-archivo', function(event) {
            const fechaCarga = $(this).data('fecha');

            fetch('{{ route('archivo.eliminarTemporal') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    fechaCarga
                }),
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("paso-adjuntar-documentacion").innerHTML = data.htmlVista;
                archivos = data.archivos;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: 'Hubo un problema al eliminar el archivo.',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            });
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
                console.error('Error al guardar datos del solicitante');
            }
        });
    }

    function validarDatosSeccionAdjuntarDocumentacion() {
        if(archivos.length === 0) {
            return false;
        } else {
            return true;
        }
    }
</script>
@endpush