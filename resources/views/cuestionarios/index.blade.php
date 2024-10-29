@extends('navbar')

@section('heading')
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .table-container { margin-left: 20px; }
    .toggle-icon {
        cursor: pointer;
        margin-right: 5px;
    }
    .usuarios-list {
        display: none; /* Ocultamos la lista de usuarios por defecto */
    }
    .grupo-label {
        cursor: pointer;
        font-weight: bold;
    }
</style>
@endsection

@section('contenidoPrincipal')
<div class="container mt-5">
    <br/>
    <br/>
        <h1>Cuestionarios Activos</h1>
        
        <!-- Botón para agregar un nuevo cuestionario -->
        <a href="{{ route('cuestionarios.create') }}" class="btn btn-primary mb-3">Agregar Nuevo Cuestionario</a>
        
        <!-- Tabla de cuestionarios activos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha de Sistema</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuestionarios as $cuestionario)
                    <tr>
                        <td>{{ $cuestionario->id_cuestionario }}</td>
                        <td>{{ $cuestionario->fecha_sistema }}</td>
                        <td>{{ $cuestionario->titulo }}</td>
                        <td>{{ $cuestionario->descripcion }}</td>
                        <td>
                            
                            <a href="{{ route('cuestionarios.edit', $cuestionario->id_cuestionario) }}" class="btn btn-sm btn-warning">Editar</a>

                          
                            <form action="{{ route('cuestionarios.destroy', $cuestionario->id_cuestionario) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de querer desactivar este cuestionario?')">Eliminar</button>
                        </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay cuestionarios activos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endsection