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

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Volver</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
