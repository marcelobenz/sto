@if ($formulario->puedeCompletar())
    @foreach ($formulario->pasosFormulario as $paso)
        @if ($formulario->muestro($paso['orden']))
            <div>
                @include($paso['ruta'])
            </div>
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
</style>
<script>
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