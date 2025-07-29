<label style="font-weight: bold">Subcategoría</label>
<select
    id="select-subcategorias"
    name="subcategoria"
    id="subcategoria"
    required
>
    @if ($isEditar)
        @if ($multinotaSelected->id_categoria == null)
            <option selected value="">Seleccione...</option>
        @else
            <option value="">Seleccione...</option>
        @endif
    @else
        <option selected value="">Seleccione...</option>
    @endif
    @foreach ($subcategorias as $cat)
        @if ($isEditar)
            @if ($cat->id_categoria === $multinotaSelected->id_categoria)
                <option selected value="{{ $cat->id_categoria }}">
                    {{ $cat->nombre }}
                </option>
            @else
                <option value="{{ $cat->id_categoria }}">
                    {{ $cat->nombre }}
                </option>
            @endif
        @else
            <option value="{{ $cat->id_categoria }}">
                {{ $cat->nombre }}
            </option>
        @endif
    @endforeach
</select>
