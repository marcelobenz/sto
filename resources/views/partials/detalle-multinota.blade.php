<div class="card-body">
    <h3>Detalle</h3>
    <div style="display: flex; width: 100%; border-top: 2px solid gray;">
        <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); width: 100%; gap: 20px; margin-top: 10px;">
            <div style="display: flex; flex-direction: column;">
                <label>Código</label>
                <label>{{ $multinotaSelected->codigo }}</label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Categoría</label>
                <label>
                    @if ($multinotaSelected->nombre_categoria_padre != null)
                        {{ $multinotaSelected->nombre_categoria_padre }}
                    @else
                        -
                    @endif
                </label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Subcategoría</label>
                <label>{{ $multinotaSelected->nombre_subcategoria }}</label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Tipo</label>
                <label>{{ $multinotaSelected->nombre }}
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Pública</label>
                <label>
                    @if ($multinotaSelected->publico == '1')
                        Sí
                    @else
                        No
                    @endif
                </label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Lleva documentación</label>
                <label>
                    @if ($multinotaSelected->lleva_documentacion == '1')
                        Sí
                    @else
                        No
                    @endif
                </label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Muestra mensaje inicial</label>
                <label>
                    @if ($multinotaSelected->muestra_mensaje == '1')
                        Sí
                    @else
                        No
                    @endif
                </label>
            </div>
            <div style="display: flex; flex-direction: column;">
                <label>Lleva expediente</label>
                <label>
                    @if ($multinotaSelected->lleva_expediente == '1')
                        Sí
                    @else
                        No
                    @endif
                </label>
            </div>
        </div>
    </div>
</div>
<div class="card-body">
    <h3>Mensaje</h3>
    <div style="display: flex; width: 100%; border-top: 2px solid gray;">
        <div style="width: 100%; margin-top: 10px;">
            {!! $multinotaSelected->mensaje_inicial !!}
        </div>
    </div>
</div>
<div class="card-body">
    <div style="display: flex; width: 100%; border-top: 2px solid gray;">
        <div style="display: flex; flex-direction: column; width: 100%; margin-top: 10px; gap: 10px;">
            @foreach ($seccionesAsociadas as $s)
                @if (count($s->campos) !== 0)
                    <div style="background-color: #ededed; border: 1px solid black; border-radius: 10px; padding: 10px;">
                        <h3>{{ $s->titulo }}</h3>
                        <div style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;">
                            @include('partials.seccion-campos', ['campos' => $s->campos])
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>