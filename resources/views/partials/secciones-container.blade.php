<div>
    <select id="secciones-precargadas" style="width: 30%;">
        <option value="">Seleccione...</option>
        @foreach($todasLasSecciones as $s)
            @if (count($s->campos) !== 0 && !in_array($s->id_seccion, array_column($seccionesAsociadas, 'id_seccion')))
                <option value="{{ $s->id_seccion }}">{{ $s->titulo }}</option>
            @endif
        @endforeach
    </select>
    <button id="boton-agregar-seccion" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square-fill" viewBox="0 0 16 16">
            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0"/>
        </svg>
    </button>
</div>
<div style="display: flex; width: 100%; padding-top: 20px;">
    <div class="secciones-container" style="display: flex; flex-direction: column; width: 100%; gap: 10px;">
        @foreach ($seccionesAsociadas as $s)
            @if (count($s->campos) !== 0)
                <div class="seccion" draggable="true" data-id="{{ $s->id_seccion }}" 
                     ondragstart="handleDragSeccionesStart(event)" 
                     ondragover="handleDragSeccionesOver(event)" 
                     ondrop="handleDropSecciones(event)">
                    <h3>{{ $s->titulo }}</h3>
                    <div style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;">
                        @include('partials.seccion-campos', ['campos' => $s->campos])
                    </div>
                    <button class="boton-quitar-seccion">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 384 512">
                            <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                        </svg>
                    </button>
                </div>
            @endif
        @endforeach
    </div>
</div>