@extends('navbar')

@section('contenidoPrincipal')
<div class="container mt-4">
    <h3>Detalle del Trámite</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Campo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalleTramite as $detalle)
                <tr>
                    <td>{{ $detalle->id_multinota_seccion_valor }}</td>
                    <td>{{ $detalle->titulo }}</td>
                    <td>{{ $detalle->nombre }}</td>
                    <td>{{ $detalle->valor }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('tramites.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
