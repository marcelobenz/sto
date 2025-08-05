<div style="margin: 20px; border: 2px solid gray;">
  <div class="titulo-seccion-tramite">
    <h3>DATOS DEL SOLICITANTE</h3>
  </div>
  <div class="cuerpo-seccion-tramite" style="padding: 15px; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;"">
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Cuit
      </dt>
      <dd>
        {{ $solicitante->getCuit()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Cuenta
      </dt>
      <dd>
        {{ $solicitante->getCuenta()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Correo electrónico
      </dt>
      <dd>
        {{ $solicitante->getCorreo()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Titular
      </dt>
      <dd>
        {{ $solicitante->getApellido()  }}
      </dd>
    </dl>
    <dl style="grid-column: span 6 / span 6;">
      <dt>
        Dirección
      </dt>
      <dd>
        {{ $solicitante->getDireccion()  }}
      </dd>
    </dl>
  </div>
</div>