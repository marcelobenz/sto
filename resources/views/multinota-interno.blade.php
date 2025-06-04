@extends('layouts.comun-formulario-interno')

@section('contenido-formulario')
    <div class="container-fluid px-3">
        <div class="row mb-3 px-3" style="justify-content: end;">            
            <div class="col-md-12">
                <br/>
                    <br/>
                        <br/>
            </div>
        </div>
    </div>

    @include('formulario.formulario-multinota', [
        'formulario' => $formulario,
        'solicitante' => $solicitante
    ])
@endsection