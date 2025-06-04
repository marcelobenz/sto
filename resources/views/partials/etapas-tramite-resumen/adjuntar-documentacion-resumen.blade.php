<div style="margin: 20px; border: 2px solid gray;">
  <div class="titulo-seccion-tramite">
    <h3>DOCUMENTOS ADJUNTOS</h3>
  </div>
  <div class="cuerpo-seccion-tramite" style="padding: 15px;">
    <table style="width: 100%; border: 1px solid gray;">
      <thead style="border-bottom: 1px solid gray; background-color: #c7c7c7;">
        <tr>
          <th style="text-align: center;">Fecha de carga</th>
          <th style="text-align: center;">Nombre</th>
          <th style="text-align: center;">Comentario</th>
        </tr>
      </thead>
      <tbody style="background-color: #f9f9f9;">
        @if($archivos)
          @foreach($archivos as $a)
            <tr>
              <td style="text-align: center;">{{ $a['fechaCarga'] ?? '' }}</td>
              <td style="text-align: center;">{{ $a['nombre'] }}</td>
              <td style="text-align: center;">{{ $a['comentario'] }}</td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
</div>