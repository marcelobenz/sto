@if ($formulario->pasosFormulario[0]['completado'] == true)
<div style="display: flex; justify-content: space-between;">
    <button id="boton-retroceder-paso" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 448 512">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
        </svg>
    </button>
    @if ($formulario->pasosFormulario[array_key_last($formulario->pasosFormulario)-1]['completado'] == true)
        <button @if(!$confirmarDeclaracionChecked) disabled @endif id="boton-registrar-tramite" class="btn btn-secondary" style="background-color: #27ace3;">
            CONFIRMAR
        </button>
    @else
        <button id="boton-avanzar-paso" class="btn btn-secondary" style="background-color: #27ace3;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 448 512">
                <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
            </svg>
        </button>
    @endif
</div>
@else
<div style="justify-self: end;">
    <button id="boton-avanzar-paso" class="btn btn-secondary" style="background-color: #27ace3;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 448 512">
            <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
        </svg>
    </button>
</div>
@endif