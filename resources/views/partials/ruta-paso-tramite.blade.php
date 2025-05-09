@if ($formulario->puedeCompletar())
    @foreach ($formulario->pasosFormulario as $paso)
        @if ($formulario->muestro($paso['orden']))
            <div>
                @include($paso['ruta'])
            </div>
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