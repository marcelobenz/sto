<div style="display: flex; flex-wrap: wrap;" id="domicilio">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="form-group requerido">
            <label>Calle *</label>
            <input type="text" id="nombreCalleLoc" name="calle"
                class="form-control input-geo margen-abajo-10 uppercase" required
                value="{{ $representante->getDomicilio()->getCalle() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group requerido">
            <label>Número *</label>
            <input type="number" id="numeroCalleLoc" name="numero"
                class="form-control input-geo uppercase" required
                value="{{ $representante->getDomicilio()->getNumero() }}">
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group">
            <label>Piso</label>
            <input type="text" id="piso" name="piso"
                class="form-control uppercase"
                value="{{ $representante->getDomicilio()->getPiso() }}">
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group">
            <label>Dpto</label>
            <input type="text" id="dpto" name="dpto"
                class="form-control uppercase"
                value="{{ $representante->getDomicilio()->getDepartamento() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group requerido">
            <label>Provincia *</label>
            <input type="text" id="ciudadLoc" name="provincia"
                class="form-control input-geo uppercase" required
                value="{{ $representante->getDomicilio()->getProvincia() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group">
            <label>Localidad *</label>
            <input type="text" id="localidadLoc" name="localidad"
                class="form-control input-geo uppercase" required
                value="{{ $representante->getDomicilio()->getLocalidad() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group requerido">
            <label>Código Postal *</label>
            <input type="text" id="codigoPostalLoc" name="codigo_postal"
                class="form-control input-geo uppercase" required
                value="{{ $representante->getDomicilio()->getCodigoPostal() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <div class="form-group requerido">
            <label>País *</label>
            <input type="text" id="paisLoc" name="pais"
                class="form-control input-geo uppercase" required
                value="{{ $representante->getDomicilio()->getPais() }}">
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <label>Latitud</label>
        <input type="text" id="latitudLoc" name="latitud"
            class="form-control input-geo uppercase"
            value="{{ $representante->getDomicilio()->getLatitud() }}">
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12 margen-abajo-10">
        <label>Longitud</label>
        <input type="text" id="longitudLoc" name="longitud"
            class="form-control input-geo uppercase"
            value="{{ $representante->getDomicilio()->getLongitud() }}">
    </div>
</div>
