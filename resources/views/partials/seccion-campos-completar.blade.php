@php
    $selectStyle = 'width: 100%; border-radius: 0.375rem; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;';
    $inputStyle = 'width: 100%; border-radius: 0.375rem;';
@endphp

@foreach ($campos as $c)
    <div style="grid-column: span {{ $c->gridSpan }} / span {{ $c->gridSpan }};">
        <label>
            {{ $c->nombre }}
            @if($c->obligatorio === 1)
                *
            @endif
        </label>
        @if ($c->isSelect)
            <select 
                class="form-control" 
                style="{{ $selectStyle }}"
            >
                <option value="" selected>Seleccione...</option>
                @foreach ($c->opciones as $opc)
                    <option value={{ $opc->id_opcion_campo }}>{{ $opc->opcion }}</option>
                @endforeach
            </select>
        @elseif ($c->isCajasSeleccion)
            <select class="choices-multiselect" multiple>
                @foreach ($c->opciones as $opc)
                    <option value={{ $opc->id_opcion_campo }}>{{ $opc->opcion }}</option>
                @endforeach
            </select>
        @elseif ($c->isTextarea)
            <textarea 
                class="form-control"
                rows="5"
                cols="10"
                maxlength="500"
            ></textarea>
        @elseif ($c->isDate)
            <input 
                class="form-control mascara-input"
                style="{{ $inputStyle }}"
                data-mascara="99/99/9999" 
            />
        @elseif ($c->isString)
            @if ($c->mascara)
                <input 
                    class="form-control mascara-input"
                    style="{{ $inputStyle }}"
                    data-mascara="{{ $c->mascara }}" 
                />
            @else
                @if ($c->limite_minimo && $c->limite_maximo)
                    <input 
                        class="form-control" 
                        minlength="{{ $c->limite_minimo }}"
                        maxlength="{{ $c->limite_maximo }}"
                        style="{{ $inputStyle }}"
                    />
                    <label style="font-size: 14px; color: darkslategray;">
                        Límite caracteres: Min {{ $c->limite_minimo }} / Max {{ $c->limite_maximo }}
                    </label>
                @else
                    <input 
                        class="form-control" 
                        style="{{ $inputStyle }}"
                    />
                @endif
            @endif
        @elseif ($c->isInteger)
            @if ($c->mascara)
                <input 
                    class="form-control mascara-input"
                    style="{{ $inputStyle }}"
                    data-mascara="{{ $c->mascara }}" 
                />
            @else
                @if ($c->limite_minimo && $c->limite_maximo)
                    <input
                        type="text"
                        class="form-control" 
                        pattern="\d{{ '{' . $c->limite_minimo . ',' . $c->limite_maximo . '}' }}"
                        title="Debe tener entre {{ $c->limite_minimo }} y {{ $c->limite_maximo }} dígitos"
                        style="{{ $inputStyle }}"
                    />
                    <label style="font-size: 14px; color: darkslategray;">
                        Límite caracteres: Min {{ $c->limite_minimo }}
                        {{ $c->limite_minimo == 1 ? '(0)' : '(1' . str_repeat('0', $c->limite_minimo - 1) . ')' }}
                        / Max {{ $c->limite_maximo }}
                        {{ $c->limite_maximo == 1 ? '(9)' : '(9' . str_repeat('9', $c->limite_maximo - 1) . ')' }}
                    </label>
                @else
                    <input 
                        type="number"
                        class="form-control" 
                        style="{{ $inputStyle }}"
                    />
                @endif
            @endif
        @endif
    </div>
@endforeach