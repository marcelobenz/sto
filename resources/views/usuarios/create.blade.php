@extends('navbar')

@section('heading')
    <h1>Crear Usuario</h1>
@endsection

@section('contenidoPrincipal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">Crear Usuario</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('usuarios.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="legajo">Legajo</label>
                                <input type="text" class="form-control @error('legajo') is-invalid @enderror" id="legajo" name="legajo" value="{{ old('legajo') }}" required>
                                @error('legajo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control @error('legajo') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" class="form-control @error('legajo') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                @error('apellido')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label for="correo_municipal">Correo</label>
                                <input type="text" class="form-control @error('correo_municipal') is-invalid @enderror" id="correo_municipal" name="correo_municipal" value="{{ old('correo_municipal') }}" required>
                                @error('correo_municipal')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="cuit">Cuit</label>
                                <input type="text" class="form-control @error('legajo') is-invalid @enderror" id="cuit" name="cuit" value="{{ old('cuit') }}" required>
                                @error('cuit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" class="form-control @error('dni') is-invalid @enderror" id="dni" name="dni" value="{{ old('dni') }}" required>
                                @error('dni')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label for="id_rol">Rol</label>
                                <select class="form-control @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol" required>
                                <option value="" disabled selected>Seleccione un rol</option>
                                @foreach($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                                @endforeach
                                </select>
                                @error('id_rol')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

<!-- Tabla que mostrará los permisos -->
<div id="tabla-permisos" style="display:none;">
    <h3>Permisos del Rol Seleccionado</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permiso</th>
                <th>Descripcion</th>
            </tr>
        </thead>
        <tbody id="permisos-body">
            <!-- Aquí se llenará la lista de permisos -->
        </tbody>
    </table>
</div>




                            <button type="submit" class="btn btn-primary">Crear Usuario</button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Volver</a>
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
    $(document).ready(function() {
        $('#id_rol').change(function() {
            var idRol = $(this).val();

            if (idRol) {
                // Hacer una solicitud AJAX para obtener los permisos del rol seleccionado
                $.ajax({
                    url: '/roles/' + idRol + '/permisos',
                    type: 'GET',
                    success: function(permisos) {
                        // Limpiar el contenido anterior
                        $('#permisos-body').empty();

                        // Si no hay permisos, mostrar un mensaje
                        if (permisos.length === 0) {
                            $('#permisos-body').append('<tr><td colspan="2">No tiene permisos asignados.</td></tr>');
                        } else {
                            // Rellenar la tabla con los permisos
                            $.each(permisos, function(index, permiso) {
                                $('#permisos-body').append('<tr><td>' + permiso.permiso + '</td><td>' + permiso.descripcion + '</td></tr>');
                            });
                        }

                        // Mostrar la tabla de permisos
                        $('#tabla-permisos').show();
                    },
                    error: function() {
                        alert('Error al obtener los permisos del rol.');
                    }
                });
            } else {
                // Ocultar la tabla si no hay rol seleccionado
                $('#tabla-permisos').hide();
            }
        });
    });
</script>
@endsection