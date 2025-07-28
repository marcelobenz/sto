@extends("layouts.app")

@section("content")
    <div class="container">
        <br />
        <br />
        <br />
        <form method="POST" action="{{ route("roles.update", $rol->id_rol) }}">
            @csrf
            @method("PUT")

            <div class="form-group">
                <label for="nombre">Nombre del Rol</label>
                <input
                    type="text"
                    class="form-control"
                    id="nombre"
                    name="nombre"
                    value="{{ $rol->nombre }}"
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
                            {{ in_array($permiso->id_permiso, $permisosAsignados) ? "checked" : "" }}
                        />
                        <label class="form-check-label" for="permisos[]">
                            {{ $permiso->permiso }} -
                            {{ $permiso->descripcion }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                Actualizar Rol
            </button>
            <a href="{{ route("roles.index") }}" class="btn btn-secondary">
                Volver
            </a>
        </form>
    </div>
@endsection
