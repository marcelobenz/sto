<div>
    <div class="titulo-seccion-tramite">
        <h3>Datos del Representante</h3>
    </div>
    <div class="cuerpo-seccion-tramite" style="display: flex; flex-wrap: wrap;">
        <!-- CUIT + Buscar -->
        <div class="col-lg-2 col-md-4 col-xs-12">
            <div class="form-group requerido">
                <label for="documentoSolicitante">CUIT *</label>
                <div style="display: flex;">
                    <div class="col-xs-10" style="margin-right: 10px;">
                        <input id="documentoSolicitante" name="documentoSolicitante" 
                            type="text" class="form-control"
                            @if($representante)
                                value="{{ $representante->getDocumento()->getNumero() }}"
                            @endif>
                    </div>
                    <div class="col-xs-2 no-padding">
                        <button id="boton-buscar-contribuyente" type="button" class="btn btn-secondary" style="background-color: #27ace3;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 512 512">
                                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nombre -->
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <div class="form-group requerido">
                <label for="nombreSolicitante">Nombre *</label>
                <input id="nombreSolicitante" name="nombreSolicitante" type="text"
                    class="form-control uppercase"
                    @if($representante)
                        value="{{ $representante->getNombre() }}"
                    @else
                        disabled
                    @endif>
            </div>
        </div>

        <!-- Apellido -->
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <div class="form-group requerido">
                <label for="apellidoSolicitante">Apellido *</label>
                <input id="apellidoSolicitante" name="apellidoSolicitante" type="text"
                    class="form-control uppercase"
                    @if($representante)
                        value="{{ $representante->getApellido() }}"
                    @else
                        disabled
                    @endif>
            </div>
        </div>

        <!-- Cod. Área -->
        <div class="col-lg-2 col-md-3 col-xs-12">
            <div class="form-group requerido">
                <label for="codarea">Cod. Área *</label>
                <select id="codarea" name="codarea" class="form-control" @if(!$representante) disabled @endif {{-- @change="actualizarTelefono" --}}>
                    <option value="">SELECCIONE</option>
                    @if($codigosArea)
                        @foreach($codigosArea as $c)
                            @if($c->codigo === $representante->getCodigoArea()->getCodigo())
                                <option value="{{ $c->codigo }}" selected>
                                    {{ $c->codigo }}
                                </option>
                            @else
                                <option value="{{ $c->codigo }}">
                                    {{ $c->codigo }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Teléfono -->
        <div class="col-lg-2 col-md-3 col-xs-12">
            <div class="form-group requerido">
                <label for="telefonoSolicitante">Teléfono *</label>
                <input id="telefonoSolicitante" name="telefonoSolicitante" type="text" 
                    class="form-control uppercase"
                    @if($representante)
                        value="{{ $representante->getTelefono() }}"
                    @else
                        disabled
                    @endif
                    {{-- v-mask="obtenerMascara()" --}}>
            </div>
        </div>

        <!-- Caracter -->
        <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="form-group requerido">
                <label for="tipoCaracterSolicitante">Caracter *</label>
                <select id="tipoCaracterSolicitante" name="tipoCaracterSolicitante" class="form-control" @if(!$representante) disabled @endif>
                    <option value="">SELECCIONE</option>
                    @if($caracteres)
                        @foreach($caracteres as $c)
                            @if($c === $representante->getTipoCaracter()->getNombre())
                                <option value="{{ $c }}" selected>
                                    {{ $c }}
                                </option>
                            @else
                                <option value="{{ $c }}">
                                    {{ $c }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Correo -->
        <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="form-group requerido">
                <label for="correoSolicitante">Correo electrónico *</label>
                <input id="correoSolicitante" name="correoSolicitante" type="text" class="form-control uppercase"
                    @if($representante)
                        value="{{ $representante->getCorreo() }}"
                    @else
                        disabled
                    @endif
                    pattern="[a-z0-9_\.-@]+">
            </div>
        </div>

        <!-- Repetir Correo -->
        <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="form-group requerido">
                <label for="correoSolicitanteRepetido">Repetir correo electrónico *</label>
                <input id="correoSolicitanteRepetido" name="correoSolicitanteRepetido" type="text" class="form-control uppercase"
                    @if(!$representante)
                        disabled
                    @endif
                    pattern="[a-z0-9_\.-@]+">
            </div>
        </div>

        <!-- Geolocalización -->
        @if ($representante)
            <div class="col-xs-12" style="display: {{ $representante->getEsCuitRegistrado() === true ? 'block' : 'none' }};">
                @include('partials.geolocalizacion', ['representante' => $representante])
            </div>
        @endif

        <!-- Nota -->
        <div class="col-xs-12">
            <h6>Los campos marcados con * son obligatorios</h6>
        </div>
    </div>
</div>