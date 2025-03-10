@extends('navbar')

@section('heading')
    <h1>Edición Multinota</h1>
@endsection

@section('contenidoPrincipal')
    <div class="container-xxl" style="margin-left: 20%; margin-right: 20%;">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        Edición Multinota
                        <a href="/multinotas" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 30px;">
                            <div style="display: flex; width: 100%; justify-content: space-between; gap: 50px;">
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Categoría</label>
                                    <select id="categorias">
                                        <option value="">Seleccione...</option>
                                        <option selected value="{{ $multinotaSelected->nombre_categoria_padre }}">{{ $multinotaSelected->nombre_categoria_padre }}</option>
                                    </select>
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Subcategorías</label>
                                    <select id="subcategorias">
                                        <option value="">Seleccione...</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Código del trámite</label>
                                    <input id="codigo" type="text" disabled value="{{ $multinotaSelected->codigo }}" />
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Nombre del Trámite</label>
                                    <input id="nombre-tramite" type="text" value="{{ $multinotaSelected->nombre }}" />
                                </div>
                            </div>
                            <div style="display: flex; width: 100%; gap: 50px;">
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Público</label>
                                    <input id="publico" type="checkbox" {{ $multinotaSelected->publico == 1 ? 'checked' : '' }}>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Lleva documentación</label>
                                    <input id="lleva-documentacion" type="checkbox" {{ $multinotaSelected->lleva_documentacion == 1 ? 'checked' : '' }}>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Mensaje inicial</label>
                                    <input id="muestra-mensaje" type="checkbox" {{ $multinotaSelected->muestra_mensaje == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                        @csrf
                            <textarea id="myeditorinstance">{{ $mensajeInicial ?? '' }}</textarea>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripting')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    
</script>
@endsection
