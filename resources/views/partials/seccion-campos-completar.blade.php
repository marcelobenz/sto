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
            <select class="form-control" style="{{ $selectStyle }}">
                <option value="" selected>Seleccione...</option>
            </select>
        @else
            @if ($c->mascara)
                <input class="form-control mascara-input" style="{{ $inputStyle }}" data-mascara="{{ $c->mascara }}" />
            @else
                <input class="form-control" style="{{ $inputStyle }}" />
            @endif
            @if ($c->limite_minimo && $c->limite_maximo)
                <label style="font-size: 14px; color: darkslategray;">
                    Límite caracteres: Min {{ $c->limite_minimo }} / Max {{ $c->limite_maximo }}
                </label>
            @endif
        @endif
    </div>
@endforeach