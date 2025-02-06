@extends('navbar')

@section('contenidoPrincipal')
    <div id="app">
        <detalle-tramite :detalle-tramite="{{ json_encode($detalleTramite) }}"></detalle-tramite>
    </div>
@endsection
