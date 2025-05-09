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
                        <select id="cuentasUsuario" name="cuentaCuentas" class="form-control" onchange="showOption()">
                            @foreach($formulario->cuentas as $c)
                                <option value="{{ $c->getCodigo() }}">{{ $c->getCodigo() }}</option>
                            @endforeach
                            <option value="Otra">Otra</option>
                        </select>
                    </div>
                </div>
            @else
                <div class="col-md-4 col-xs-12" id="outputPanelCuenta">
                    <div class="col-xs-12 no-padding">
                        <div class="form-group requerido">
                            <label for="cuentaGeneral">Dominio *</label>
                            <input type="text" id="cuentaGeneralSinCuentas" class="form-control"
                                name="cuentaGeneral" {{-- value="{{ old('cuentaGeneral', $inicioTramiteGeneral->cuenta) }}" --}}>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-4 col-xs-12" id="outputPanel" style="display: none;">
                <div class="col-xs-12 no-padding">
                    <div class="form-group requerido">
                        <label for="cuentaGeneral">Dominio *</label>
                        <input type="text" id="cuentaGeneralSinCuentas" class="form-control"
                            name="cuentaGeneral" {{-- value="{{ old('cuentaGeneral', $inicioTramiteGeneral->cuenta) }}" --}}>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="col-xs-12 no-padding">
                    <div class="form-group requerido">
                        <label for="cuentaModalAbl">Correo *</label>
                        <input type="text" id="cuentaModalAbl" class="form-control"
                            name="correo" {{-- value="{{ old('correo', $inicioTramiteGeneral->correo) }}" --}}
                            pattern="[a-zA-Z0-9_.@\-]+">
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
    function showOption() {
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
