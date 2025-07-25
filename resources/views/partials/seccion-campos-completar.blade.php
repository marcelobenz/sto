@php
    $selectStyle = 'width: 100%; border-radius: 0.375rem; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;';
    $inputStyle = 'width: 100%; border-radius: 0.375rem;';
@endphp

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

    <div style="grid-column: span {{ $c->gridSpan }} / span {{ $c->gridSpan }};">
        <label>
            {{ $c->nombre }}
            @if($c->obligatorio === 1)
                *
            @endif
        </label>
        @if ($c->isSelect)
            <select
                id={{ $c->id_campo }}
                class="form-control" 
                style="{{ $selectStyle }}"
                @if($c->obligatorio === 1)
                    required
                @endif
            >
                <option value="" selected>Seleccione...</option>
                @foreach ($c->opciones as $opc)
                    <option 
                        value={{ $opc->id_opcion_campo }}
                        @if($valorCampo == $opc->id_opcion_campo) selected @endif
                    >
                        {{ $opc->opcion }}
                    </option>
                @endforeach
            </select>
        @elseif ($c->isCajasSeleccion)
            <select 
                id={{ $c->id_campo }}
                class="choices-multiselect"
                multiple
            >
                @foreach ($c->opciones as $opc)
                    <option 
                        value={{ $opc->id_opcion_campo }}
                        @if(is_array($valorCampo) && in_array($opc->id_opcion_campo, $valorCampo)) selected @endif
                    >
                        {{ $opc->opcion }}
                    </option>
                @endforeach
            </select>
        @elseif ($c->isTextarea)
            <textarea 
                id={{ $c->id_campo }}
                class="form-control"
                style="resize: none;"
                rows="5"
                cols="10"
                maxlength="500"
                oninput="actualizarCharCount(this)"
                @if($c->obligatorio === 1)
                    required
                @endif
            >{{ $valorCampo }}</textarea>
            <label id="charCountInfo-{{ $c->id_campo }}" style="font-size: 14px; color: darkslategray;">
                quedan 500 caracteres
            </label>
        @elseif ($c->isDate)
            <input 
                id={{ $c->id_campo }}
                class="form-control mascara-input"
                style="{{ $inputStyle }}"
                data-mascara="99/99/9999" 
                @if($c->obligatorio === 1)
                    required
                @endif
                value="{{ $valorCampo }}"
            />
        @elseif ($c->isString)
            @if ($c->mascara)
                <input 
                    id={{ $c->id_campo }}
                    class="form-control mascara-input"
                    style="{{ $inputStyle }}"
                    data-mascara="{{ $c->mascara }}"
                    @if($c->obligatorio === 1)
                        required
                    @endif
                    value="{{ $valorCampo }}"
                />
            @else
                @if ($c->limite_minimo && $c->limite_maximo)
                    <input 
                        id={{ $c->id_campo }}
                        class="form-control" 
                        minlength="{{ $c->limite_minimo }}"
                        maxlength="{{ $c->limite_maximo }}"
                        style="{{ $inputStyle }}"
                        @if($c->obligatorio === 1)
                            required
                        @endif
                        value="{{ $valorCampo }}"
                    />
                    <label style="font-size: 14px; color: darkslategray;">
                        Límite caracteres: Min {{ $c->limite_minimo }} / Max {{ $c->limite_maximo }}
                    </label>
                @else
                    <input 
                        id={{ $c->id_campo }}
                        class="form-control" 
                        style="{{ $inputStyle }}"
                        @if($c->obligatorio === 1)
                            required
                        @endif
                        value="{{ $valorCampo }}"
                    />
                @endif
            @endif
        @elseif ($c->isInteger)
            @if ($c->mascara)
                <input 
                    id={{ $c->id_campo }}
                    class="form-control mascara-input"
                    style="{{ $inputStyle }}"
                    data-mascara="{{ $c->mascara }}" 
                    @if($c->obligatorio === 1)
                        required
                    @endif
                    value="{{ $valorCampo }}"
                />
            @else
                @if ($c->limite_minimo && $c->limite_maximo)
                    <input
                        id={{ $c->id_campo }}
                        type="text"
                        class="form-control" 
                        pattern="\d{{ '{' . $c->limite_minimo . ',' . $c->limite_maximo . '}' }}"
                        title="Debe tener entre {{ $c->limite_minimo }} y {{ $c->limite_maximo }} dígitos"
                        style="{{ $inputStyle }}"
                        @if($c->obligatorio === 1)
                            required
                        @endif
                        value="{{ $valorCampo }}"
                    />
                    <label style="font-size: 14px; color: darkslategray;">
                        Límite caracteres: Min {{ $c->limite_minimo }}
                        {{ $c->limite_minimo == 1 ? '(0)' : '(1' . str_repeat('0', $c->limite_minimo - 1) . ')' }}
                        / Max {{ $c->limite_maximo }}
                        {{ $c->limite_maximo == 1 ? '(9)' : '(9' . str_repeat('9', $c->limite_maximo - 1) . ')' }}
                    </label>
                @else
                    <input 
                        id={{ $c->id_campo }}
                        type="number"
                        class="form-control" 
                        style="{{ $inputStyle }}"
                        @if($c->obligatorio === 1)
                            required
                        @endif
                        value="{{ $valorCampo }}"
                    />
                @endif
            @endif
        @endif
    </div>
@endforeach