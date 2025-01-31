@foreach ($campos as $c)
    @if ($c->dimension == 1)
        <div style="grid-column: span 1 / span 1;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 2)
        <div style="grid-column: span 2 / span 2;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 3)
        <div style="grid-column: span 3 / span 3;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 4)
        <div style="grid-column: span 4 / span 4;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 5)
        <div style="grid-column: span 5 / span 5;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 6)
        <div style="grid-column: span 6 / span 6;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 7)
        <div style="grid-column: span 7 / span 7;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 8)
        <div style="grid-column: span 8 / span 8;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 9)
        <div style="grid-column: span 9 / span 9;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 10)
        <div style="grid-column: span 10 / span 10;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 11)
        <div style="grid-column: span 11 / span 11;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @elseif ($c->dimension == 12)
        <div style="grid-column: span 12 / span 12;">
            <x-input-label :value="__($c->nombre)" />
            @if ($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION')
                <select style="width: 100%; border-radius: 0.375rem;
                border: 2px solid gray; padding-block: 1px; padding-inline: 2px; padding: 1px 0px;" disabled >
                    <option value="" disabled selected>Seleccione...</option>
                </select>
            @else
                <input style="width: 100%; border-radius: 0.375rem;" disabled />
            @endif
        </div>
    @endif
@endforeach