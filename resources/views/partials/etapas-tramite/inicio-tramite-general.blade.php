@php
    $cuentasCodigos = array_map(function($c) {
        return str_replace(' ', '', $c->getCodigo());
    }, $formulario->cuentas);
    $otraSeleccionada = !in_array($instanciaMultinota->cuenta, $cuentasCodigos);
@endphp

<div>
    <div class="titulo-seccion-tramite">
        <h3>Iniciar Trámite</h3>
    </div>
    <div class="cuerpo-seccion-tramite">
        <div style="display: flex;">
            @if(count($formulario->cuentas) > 0)
                <div class="col-md-3 col-xs-12">
                    <div class="form-group requerido">
                        <label for="cuentasUsuario">Cuenta *</label>
                        <select name="cuentasUsuario" id="cuentasUsuario" class="form-control" onchange="mostrarInputCuenta(); guardarDatosDelSolicitante();">
                            @foreach ($formulario->cuentas as $cuenta)
                                @php
                                    $cuenta = str_replace(' ', '', $cuenta->getCodigo());
                                @endphp
                                <option value="{{ $cuenta }}"
                                    {{ $cuenta == $instanciaMultinota->cuenta ?? '' ? 'selected' : '' }}>
                                    {{ $cuenta }}
                                </option>
                            @endforeach
                            <option value="Otra" 
                                {{ ($instanciaMultinota->cuenta !== '' && $otraSeleccionada) ? 'selected' : '' }}>
                                Otra
                            </option>
                        </select>
                    </div>
                </div>
            @else
                <div class="col-md-4 col-xs-12" id="outputPanelCuenta">
                    <div class="col-xs-12 no-padding">
                        <div class="form-group requerido">
                            <label for="cuentaGeneralSinCuentas">Dominio *</label>
                            <input required type="text" id="cuentaGeneralSinCuentas" class="form-control uppercase"
                                name="cuentaGeneralSinCuentas" value="{{ $instanciaMultinota->cuenta ?? '' }}" onblur="guardarDatosDelSolicitante()">
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-4 col-xs-12" id="outputPanel" style="display: {{ ($instanciaMultinota->cuenta !== '' && $otraSeleccionada) ? 'block' : 'none' }};">
                <div class="col-xs-12 no-padding">
                    <div class="form-group requerido">
                        <label for="cuentaGeneralSinCuentas">Dominio *</label>
                        <input required type="text" id="cuentaGeneralSinCuentas" class="form-control uppercase"
                            name="cuentaGeneralSinCuentas" value="{{ $instanciaMultinota->cuenta ?? '' }}" onblur="guardarDatosDelSolicitante()">
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="col-xs-12 no-padding">
                    <div class="form-group requerido">
                        <label for="correo">Correo *</label>
                        <input required type="email" id="correo" class="form-control uppercase"
                            name="correo" value="{{ $instanciaMultinota->correo ?? '' }}"
                            onblur="guardarDatosDelSolicitante()">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <h6>Los campos marcados con * son obligatorios</h6>
        </div>
    </div>
</div>
<script>
    function mostrarInputCuenta() {
        const select = document.getElementById("cuentasUsuario");
        const option = select.options[select.selectedIndex].value;
        const panel = document.getElementById("outputPanel");
        if (option === "Otra") {
            panel.style.display = "block";
        } else {
            panel.style.display = "none";
        }
    }
</script>
