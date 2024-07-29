<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tramites Online</title>
    <!-- Incluye Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    @yield('heading')
</head>



<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
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
                        <span class="dropdown-item-text">
                            <strong>Nombre Apellido</strong><br>
                            Nro Legajo: 12345
                        </span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Perfil</a>
                        <a class="dropdown-item" href="/">Salir</a>
                    </div>
                </li>
            </ul>
        </div>

    </nav>

    <!-- Aquí puedes añadir más contenido -->
    <div>
        @yield('contenidoPrincipal')
    </div>

    <!-- Incluye jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @yield('scripting')

</body>
</html>
