@php
    $selectStyle = 'width: 100%; border-radius: 0.375rem; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;';
    $inputStyle = 'width: 100%; border-radius: 0.375rem;';
@endphp

@foreach ($campos as $c)
    <div style="grid-column: span {{ $c->gridSpan }} / span {{ $c->gridSpan }};">
        <x-input-label :value="__($c->nombre)" />
        @if ($c->isSelect)
            <select class="form-control" style="{{ $selectStyle }}">
                <option value="" selected>Seleccione...</option>
            </select>
        @else
            <input class="form-control" style="{{ $inputStyle }}" />
        @endif
    </div>
@endforeach
