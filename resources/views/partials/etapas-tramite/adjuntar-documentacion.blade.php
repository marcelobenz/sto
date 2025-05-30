<div>
  <div class="titulo-seccion-tramite">
    <h3>Adjuntar Documentación</h3>
  </div>
  <div class="cuerpo-seccion-tramite" style="padding: 15px;">
    <div style="display: flex; justify-content: center; margin-bottom: 10px;">
      <form method="POST" action="{{ route('archivo.subirTemporal') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="archivo" />
        <button type="submit" class="btn btn-secondary" style="background-color: #27ace3;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 512 512">
            <path d="M288 109.3L288 352c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-242.7-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352l128 0c0 35.3 28.7 64 64 64s64-28.7 64-64l128 0c35.3 0 64 28.7 64 64l0 32c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64l0-32c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/>
          </svg> CARGAR
        </button>
      </form>
    </div>
    <table style="width: 100%; border: 1px solid gray;">
      <thead style="border-bottom: 1px solid gray; background-color: #c7c7c7;">
        <tr>
          <th style="text-align: center;">Fecha de carga</th>
          <th style="text-align: center;">Nombre</th>
          <th style="text-align: center;">Acciones</th>
          <th style="text-align: center;">Comentario</th>
        </tr>
      </thead>
      <tbody style="background-color: #f9f9f9;">
        @if($archivos)
          @foreach($archivos as $a)
            <tr>
              <td style="text-align: center;">{{ $a->fechaCarga }}</td>
              <td style="text-align: center;">{{ $a->nombre }}</td>
              <td style="text-align: center;">
                <button id="boton-eliminar-archivo" class="btn btn-danger">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 384 512">
                    <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                  </svg>
                </button>
              </td>
              <td style="text-align: center;">{{ $a->comentario }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="4" style="text-align: center;">No se agregaron archivos</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>