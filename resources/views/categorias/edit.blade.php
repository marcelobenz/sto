@extends('navbar')

@section('heading')
    <h1>Editar Categoría</h1>
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
                    <div class="card-header">Editar Categoría</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('categorias.update', $categoria->id_categoria) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_padre">ID Padre</label>
                                <input type="number" class="form-control @error('id_padre') is-invalid @enderror" id="id_padre" name="id_padre" value="{{ old('id_padre', $categoria->id_padre) }}">
                                @error('id_padre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="flag_activo">Activo</label>
                                <select class="form-control @error('flag_activo') is-invalid @enderror" id="flag_activo" name="flag_activo" required>
                                    <option value="1" {{ old('flag_activo', $categoria->flag_activo) == 1 ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ old('flag_activo', $categoria->flag_activo) == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                @error('flag_activo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
