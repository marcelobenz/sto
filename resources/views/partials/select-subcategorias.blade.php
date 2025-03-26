<label style="font-weight: bold;">Subcategoría</label>
<select id="select-subcategorias" name="subcategoria" id="subcategoria" required>
    @if($multinotaSelected->id_categoria == null)
        <option selected value="">Seleccione...</option>
    @else
        <option value="">Seleccione...</option> 
    @endif
    @foreach($subcategorias as $cat)
        @if($cat->id_categoria === $multinotaSelected->id_categoria)
            <option selected value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
        @else
            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
        @endif
    @endforeach
</select>