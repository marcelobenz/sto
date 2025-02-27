@extends('navbar')

@section('heading')
    <h1>Ver Multinota</h1>
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
                        Ver Multinota
                        <a href="/multinotas" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="card-body">
                        <h3>Detalle</h3>
                        <div style="display: flex; width: 100%; border-top: 2px solid gray;">
                            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); width: 100%; gap: 20px; margin-top: 10px;">
                                <div style="display: flex; flex-direction: column;">
                                    <label>Código</label>
                                    <label>{{ $multinotaSelected->codigo }}</label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Categoría</label>
                                    <label>
                                        @if ($multinotaSelected->nombre_categoria_padre != null)
                                            {{ $multinotaSelected->nombre_categoria_padre }}
                                        @else
                                            -
                                        @endif
                                    </label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Subcategoría</label>
                                    <label>{{ $multinotaSelected->nombre_subcategoria }}</label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Tipo</label>
                                    <label>{{ $multinotaSelected->nombre }}
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Público</label>
                                    <label>
                                        @if ($multinotaSelected->publico == '1')
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Lleva documentación</label>
                                    <label>
                                        @if ($multinotaSelected->lleva_documentacion == '1')
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Muestra mensaje inicial</label>
                                    <label>
                                        @if ($multinotaSelected->muestra_mensaje == '1')
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </label>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <label>Lleva expediente</label>
                                    <label>
                                        @if ($multinotaSelected->lleva_expediente == '1')
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
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
