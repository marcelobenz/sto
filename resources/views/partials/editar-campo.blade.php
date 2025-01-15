<div>
    <h3>Datos del Campo</h3>
    <div class="border-separate border border-slate-400 p-4" style="display: flex; flex-direction: column; gap: 15px;">
        <div style="display: flex; justify-content: space-between;">
            <x-text-input id="nombre" class="block mt-1" style="width: 48%;" type="text" name="nombre" :value="__($campos[0]->nombre)" required />
            <select name="tipos" class="block mt-1" style="width: 48%;">
                @foreach ($tipos as $t)
                    <option value="{{ $t->tipo }}" @selected($campos[0]->tipo == $t->tipo)>{{ $t->tipo }}</option> 
                @endforeach
            </select>
        </div>
        {{-- <input type="checkbox" name="llevaMascara" value="active" @checked(old('active', $campos[0]->mascara != null))> --}}
        {!! $campos[0]->tipo == 'STRING' ? 
            '<div style="display: flex; flex-direction: column;">
                <label>
                    Lleva máscara
                    {{-- Revisar --}}<input type="checkbox" name="llevaMascara" value="active">
                </label>
                <label>
                    Limitar caractéres
                    {{-- Revisar --}}<input type="checkbox" name="limitarCaracteres" value="active">
                </label>
            </div>'
        : ($campos[0]->tipo == 'INTEGER' ? 
            '<div style="display: flex; flex-direction: column;">
                <label>
                    Limitar caractéres
                    {{-- Revisar --}}<input type="checkbox" name="limitarCaracteres" value="active">
                </label>
            </div>'
            :
            '')
        !!}
        <div>
            <h6>Tamaño</h6>
            <x-text-input id="tamaño" class="block mt-1" style="width: 48%;" type="number" min="1" max="12" name="tamaño" :value="__($campos[0]->dimension)" required /><label>(solo valores de 1 a 12)</label>
        </div>
        <label>
            Obligatorio
            {{-- Revisar --}}<input type="checkbox" name="obligatorio" value="active">
        </label>
        <div style="display: flex; justify-content: end;">
            <button class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                </svg>
            </button>
        </div>
    </div>
</div>