@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Agregar Categoría</div>

                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route("categorias.store") }}"
                        >
                            @csrf

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input
                                    type="text"
                                    class="form-control @error("nombre") is-invalid @enderror"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ old("nombre") }}"
                                    required
                                />
                                @error("nombre")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_padre">Categoría Padre</label>
                                <select
                                    class="form-control @error("id_padre") is-invalid @enderror"
                                    id="id_padre"
                                    name="id_padre"
                                >
                                    <option value="">Ninguna</option>
                                    @foreach ($categoriasActivas as $categoria)
                                        <option
                                            value="{{ $categoria->id_categoria }}"
                                            {{ old("id_padre") == $categoria->id_categoria ? "selected" : "" }}
                                        >
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("id_padre")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="flag_activo">Activo</label>
                                <select
                                    class="form-control @error("flag_activo") is-invalid @enderror"
                                    id="flag_activo"
                                    name="flag_activo"
                                    required
                                >
                                    <option
                                        value="1"
                                        {{ old("flag_activo") == 1 ? "selected" : "" }}
                                    >
                                        Sí
                                    </option>
                                    <option
                                        value="0"
                                        {{ old("flag_activo") == 0 ? "selected" : "" }}
                                    >
                                        No
                                    </option>
                                </select>
                                @error("flag_activo")
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Agregar Categoría
                            </button>
                            <a
                                href="{{ route("categorias.index") }}"
                                class="btn btn-secondary"
                            >
                                Volver
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
