@extends("layouts.app")

@section("content")
    <div class="container">
        <form method="POST" action="{{ route("roles.store") }}">
            @csrf

            <br />
            <br />
            <br />
            <div class="form-group">
                <label for="nombre">Nombre del Rol</label>
                <input
                    type="text"
                    class="form-control"
                    id="nombre"
                    name="nombre"
                    required
                />
            </div>

            <h3>Permisos</h3>
            <div class="form-group">
                @foreach ($permisos as $permiso)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="permisos[]"
                            value="{{ $permiso->id_permiso }}"
                        />
                        <label class="form-check-label" for="permisos[]">
                            {{ $permiso->permiso }} -
                            {{ $permiso->descripcion }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Crear Rol</button>
            <a href="{{ route("roles.index") }}" class="btn btn-secondary">
                Volver
            </a>
        </form>
    </div>
@endsection
