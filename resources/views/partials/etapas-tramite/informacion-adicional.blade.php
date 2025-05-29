<div>
  <div class="titulo-seccion-tramite">
    <h3>Información Adicional</h3>
  </div>
  <div class="cuerpo-seccion-tramite" style="padding: 15px;">
    <textarea 
      id="informacionAdicional"
      class="form-control"
      style="resize: none;"
      rows="5"
      cols="10"
      maxlength="500"
      oninput="actualizarCharCount(this)"
    >{{ $informacionAdicional }}</textarea>
    <label id="charCountInfo-informacionAdicional" style="font-size: 14px; color: darkslategray;">
      quedan 500 caracteres
    </label>
  </div>
</div>