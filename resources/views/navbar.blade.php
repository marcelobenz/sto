<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tramites Online</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    @yield('heading')
</head>

        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }
    </style>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            @if(session('error'))
            <div id='success-alert' class="alert alert-danger mb-0 ml-3">
                {{ session('error') }}
            </div>
            @endif
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/dashboard">Principal</a>
                            </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="bandejasDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Bandejas
                        </a>
                        <div class="dropdown-menu" aria-labelledby="bandejasDropdown">
                            <a class="dropdown-item" href="#">Bandeja Personal</a>
                            <a class="dropdown-item" href="{{ route('tramites.enCurso') }}">Trámites en Curso</a>
                            <a class="dropdown-item" href="{{ route('tramites.index') }}">Todos los Trámites</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="formulariosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Formularios
                        </a>
                        <div class="dropdown-menu" aria-labelledby="formulariosDropdown">
                            @foreach ($categoriasSubcategoriasMap as $idPadre => $categorias)
                                @foreach($categorias as $c)
                                    @if($c->id_categoria == $idPadre)
                                        @if($categoriaPadreTieneCategoriasConMulinotasActivas[$idPadre] == true)
                                            <div class="dropdown-submenu">
                                                <a class="dropdown-item dropdown-toggle" href="#">
                                                    {{ $c->nombre }}
                                                </a>
                                                <div class="dropdown-menu">
                                                    @foreach($categorias as $c)
                                                        @if($c->id_categoria != $idPadre)
                                                            @if(count($subcategoriasMultinotasMap[$c->id_categoria]) > 0)
                                                                <div class="dropdown-submenu">
                                                                    <a class="dropdown-item dropdown-toggle" href="#">
                                                                        {{ $c->nombre }}
                                                                    </a>
                                                                    <div class="dropdown-menu">
                                                                        @foreach ($subcategoriasMultinotasMap[$c->id_categoria] as $multinota)
                                                                            <a class="dropdown-item" href={{ route('estadoTramite.tienePermiso', ['multinota' => $multinota]) }}>{{ $multinota->nombre }}</a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="administracionDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Administracion
                        </a>
                        <div class="dropdown-menu" aria-labelledby="administracionDropdown">
                            <a class="dropdown-item" href="{{ route('categorias.index') }}">Categorias</a>
                            <a class="dropdown-item" href="{{ route('usuarios.index') }}">Administrativos</a>
                            <a class="dropdown-item" href="{{ route('limites.index') }}">Limite de asignaciones</a>
                            <a class="dropdown-item" href="{{ route('contribuyente.buscar') }}">Usuarios</a>
                            <a class="dropdown-item" href="{{ route('cuestionarios.index') }}">Cuestionarios</a>
                            <a class="dropdown-item" href="{{ route('estados.index') }}">Estados</a>
                            <a class="dropdown-item" href="{{ route('secciones-multinota.index') }}">Secciones Multinota</a>
                            <a class="dropdown-item" href="{{ route('multinotas.index') }}">Multinotas</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="SistemaDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sistema
                        </a>
                        <div class="dropdown-menu" aria-labelledby="SistemaDropdown">
                            <a class="dropdown-item" href="sistema">Configuración Mail</a>
                        </div>
                    </li>
                </ul>

                <li class="nav-item ml-4">
                    <span class="navbar-text text-white font-weight-bold" id="titulo-pagina">
                        {{ $tituloPagina ?? '' }}
                    </span>
                </li>

            </div>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://via.placeholder.com/30" alt="Avatar" class="avatar-img">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            @if(Session::has('usuario_interno'))
                                @php
                                    $usuarioInterno = Session::get('usuario_interno');
                                @endphp
                                <span class="dropdown-item-text">
                                    <strong>{{ $usuarioInterno->nombre }} {{ $usuarioInterno->apellido }}</strong><br>
                                    Nro Legajo: {{ $usuarioInterno->legajo }}
                                </span>
                            @else
                                <span class="dropdown-item-text">No estás autenticado</span>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Perfil</a>
                            <a class="dropdown-item" href="/clear-session">Salir</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <div>
            @yield('contenidoPrincipal')
        </div>
        @endif
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">Principal</a>
                        </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="bandejasDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Bandejas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="bandejasDropdown">
                        <a class="dropdown-item" href="#">Bandeja Personal</a>
                        <a class="dropdown-item" href="#">Trámites en Curso</a>
                        <a class="dropdown-item" href="tramites">Todos los Trámites</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Formularios</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="administracionDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Administracion
                    </a>
                    <div class="dropdown-menu" aria-labelledby="administracionDropdown">
                        <a class="dropdown-item" href="categorias">Categorias</a>
                                <a class="dropdown-item" href="usuarios">Administrativos</a>
                                <a class="dropdown-item" href="limite">Limite de asignaciones</a>
                                <a class="dropdown-item" href="usuario">Usuarios</a>
                                <a class="dropdown-item" href="cuestionarios">Cuestionarios</a>
                                <a class="dropdown-item" href="estados">Estados</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="SistemaDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Sistema
                    </a>
                    <div class="dropdown-menu" aria-labelledby="SistemaDropdown">
                        <a class="dropdown-item" href="sistema">Configuración Mail</a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="https://via.placeholder.com/30" alt="Avatar" class="avatar-img">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        @if(Session::has('usuario_interno'))
                            @php
                                $usuarioInterno = Session::get('usuario_interno');
                            @endphp
                            <span class="dropdown-item-text">
                                <strong>{{ $usuarioInterno->nombre }} {{ $usuarioInterno->apellido }}</strong><br>
                                Nro Legajo: {{ $usuarioInterno->legajo }}
                            </span>
                        @else
                            <span class="dropdown-item-text">No estás autenticado</span>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Perfil</a>
                        <a class="dropdown-item" href="/clear-session">Salir</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div>
        @yield('contenidoPrincipal')
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Esperar 5 segundos y luego ocultar el mensaje
    setTimeout(function() {
        var alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000); // 5000 milisegundos = 5 segundos
</script>
    @yield('scripting')

</body>
</html>
