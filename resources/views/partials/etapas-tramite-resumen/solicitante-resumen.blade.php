<div style="margin: 20px; border: 2px solid gray;">
  <div class="titulo-seccion-tramite">
    <h3>DATOS DEL REPRESENTANTE</h3>
  </div>
  <div class="cuerpo-seccion-tramite" style="padding: 15px; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;"">
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Tipo Documento
      </dt>
      <dd>
        {{ $representante->getDocumento()->getTipo()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        DNI
      </dt>
      <dd>
        {{ $representante->getDocumento()->getNumero()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Carácter
      </dt>
      <dd>
        {{ $representante->getTipoCaracter()->getNombre()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Nombre
      </dt>
      <dd>
        {{ $representante->getNombre()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Apellido
      </dt>
      <dd>
        {{ $representante->getApellido()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Correo electrónico
      </dt>
      <dd>
        {{ $representante->getCorreo()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Teléfono
      </dt>
      <dd>
        {{ '(' . $representante->getCodigoArea()->getCodigo() . ')-' . $representante->getTelefono() }}
      </dd>
    </dl>
  </div>
</div>