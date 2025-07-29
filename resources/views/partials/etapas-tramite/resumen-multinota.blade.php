@include("partials.etapas-tramite-resumen.inicio-tramite-general-resumen", ["solicitante" => $solicitante])
@if ($persona === "Juridica")
    @include("partials.etapas-tramite-resumen.solicitante-resumen", ["representante" => $representante])
@endif

@include("partials.etapas-tramite-resumen.seccion-valor-multinota-resumen", ["formulario" => $formulario])
@include("partials.etapas-tramite-resumen.informacion-adicional-resumen", ["informacionAdicional" => $informacionAdicional])
@include("partials.etapas-tramite-resumen.adjuntar-documentacion-resumen", ["archivos" => $archivos])
<div style="margin: 20px; border: 2px solid gray">
    <div class="titulo-seccion-tramite">
        <h3>DECLARACIÓN JURADA</h3>
    </div>
    <div
        class="cuerpo-seccion-tramite"
        style="
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
        "
    >
        <div>
            Declaro que los datos a transmitir son correctos y completos y que
            he confeccionado el formulario en carácter de declaración jurada,
            sin omitir ni falsear dato alguno que deba contener, siendo fiel
            expresión de la verdad.
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 5px">
            <input
                type="checkbox"
                id="confirmarDeclaracion"
                name="confirmarDeclaracion"
                value="1"
                onchange="handleCheckConfirmarDeclaracion(this)"
            />
            <label for="confirmarDeclaracion" style="margin: 0px">
                Confirmar declaración
            </label>
        </div>
    </div>
</div>
