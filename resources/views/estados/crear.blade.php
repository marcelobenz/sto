@extends('navbar')

@section('contenidoPrincipal')
    <div class="container-fluid px-3">
        <h2 class="mt-3">Crear Workflow de Estados - {{ $tipoTramite->nombre_tipo_tramite }}</h2>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Estados del Trámite</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Actuales</th>
                            <th>Nuevos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estados as $estado)
                            <tr>
                                <td>{{ $estado['actual'] }}</td>
                                <td>{{ $estado['nuevo'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info fa fa-search"></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ url('/estados') }}" class="btn btn-secondary mt-3">Volver</a>
            </div>
        </div>
    </div>
@endsection
