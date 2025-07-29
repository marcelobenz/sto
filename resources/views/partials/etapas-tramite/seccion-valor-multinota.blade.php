@if (! empty($formulario->secciones))
    @foreach ($formulario->secciones as $seccion)
        <div style="margin: 20px; border: 2px solid gray">
            <div class="titulo-seccion-tramite">
                <h3 style="text-transform: uppercase">
                    {{ $seccion->titulo }}
                </h3>
            </div>
            <div class="cuerpo-seccion-tramite">
                <div style="width: 100%">
                    <div
                        style="
                            display: grid;
                            grid-template-columns: repeat(12, minmax(0, 1fr));
                            gap: 0.75rem;
                            margin: 1rem;
                        "
                    >
                        @include("partials.seccion-campos-completar", ["campos" => $seccion->campos])
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<div class="col-xs-12" style="padding-left: 5px">
    <h6>Los campos marcados con * son obligatorios</h6>
</div>
