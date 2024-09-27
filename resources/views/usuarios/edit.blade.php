@extends('navbar')

@section('heading')
    <h1>Editar Usuario</h1>
@endsection

@section('contenidoPrincipal')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Editar Usuario</div>

                    <div class="card-body">
                        <!-- Formulario de edición -->
                        <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario_interno) }}">
                            @csrf
                            @method('PUT') 

                            <div class="form-group">
                                <label for="legajo">Legajo</label>
                                <input type="text" class="form-control" id="legajo" name="legajo" value="{{ $usuario->legajo }}" required>
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $usuario->nombre }}" required>
                            </div>

                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="{{ $usuario->apellido }}" required>
                            </div>

                            <div class="form-group">
                                <label for="correo_municipal">Correo Municipal</label>
                                <input type="email" class="form-control" id="correo_municipal" name="correo_municipal" value="{{ $usuario->correo_municipal }}" required>
                            </div>

                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" value="{{ $usuario->dni }}" required>
                            </div>

                            <!-- Campo para asignar el rol -->
                            <div class="form-group">
                                <label for="id_rol">Rol</label>
                                <select class="form-control @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol" required>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id_rol }}" 
                                            {{ $usuario->id_rol == $rol->id_rol ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_rol')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Tabla para mostrar los permisos -->
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

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
        // Función para cargar los permisos del rol seleccionado
        function cargarPermisos(idRol) {
            if (idRol) {
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
        }

        // Cargar permisos al cargar la página si hay un rol seleccionado
        var rolInicial = $('#id_rol').val();
        if (rolInicial) {
            cargarPermisos(rolInicial);
        }

        // Cambiar permisos cuando se selecciona un rol diferente
        $('#id_rol').change(function() {
            var idRol = $(this).val();
            cargarPermisos(idRol);
        });
    });
</script>
@endsection
