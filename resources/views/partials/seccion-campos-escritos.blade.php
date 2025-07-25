@foreach ($campos as $c)
    @php
        $valorCampo = null;
        if (!empty($camposSecciones)) {
            $match = collect($camposSecciones)->firstWhere('id_campo', $c->id_campo);
            if ($match) {
                $valorCampo = $match['valor'];
            }
        }
    @endphp

    <dl style="grid-column: span {{ $c->gridSpan }} / span {{ $c->gridSpan }};">
        <dt>
            {{ $c->nombre }}
        </dt>
        <dd>
            @if ($c->isCajasSeleccion)
                @if(is_array($valorCampo))
                    @php
                        $selectedLabels = [];
                        foreach ($c->opciones as $opc) {
                            if (in_array($opc->id_opcion_campo, $valorCampo)) {
                                $selectedLabels[] = $opc->opcion;
                            }
                        }
                    @endphp
                    {{ implode(', ', $selectedLabels) }}
                @else
                    {{ $valorCampo }}
                @endif
            @elseif ($c->isSelect)
                @foreach ($c->opciones as $opc)
                    @if($valorCampo == $opc->id_opcion_campo)
                        {{ $opc->opcion }}
                    @endif
                @endforeach
            @else
                {{ $valorCampo }}
            @endif
        </dd>
    </dl>
@endforeach