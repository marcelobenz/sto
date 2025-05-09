<div class="cont-pasos f1">
    <div class="f1-steps">
        <div class="f1-progress">
            <div class="f1-progress-line" style="width: {{ $formulario->dimensionBarraProgreso() }}%"></div>
        </div>

        @foreach ($formulario->pasosFormulario as $paso)
            <div class="f1-step {{ $formulario->estilosPaso($paso['orden']) }}" style="width: {{ $formulario->dimensionPaso() }}%">
                <div class="f1-step-icon"><i class="fa {{ $paso['iconoPaso'] }}"></i></div>
                <p>{{ $paso['titulo'] }}</p>
            </div>
        @endforeach
    </div>
</div>