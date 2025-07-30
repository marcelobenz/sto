<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Trámites Online</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @yield('heading')
</head>
<body class="bg-gray-100 text-gray-900">

  <nav class="bg-gray-800 text-white px-4 py-3 fixed top-0 left-0 right-0 z-50">
    <div class="w-full flex justify-between items-center">
        <div class="flex items-center space-x-6 pl-44">
            <a href="/dashboard" class="text-white hover:text-gray-300 font-semibold">Principal</a>

            <a href="{{ route('bandeja-usuario-externo') }}" class="text-white hover:text-gray-300 font-semibold">
                Bandeja
            </a>

            <a href="#" class="text-white hover:text-gray-300 font-semibold">Formularios</a>
        </div>


            <div class="flex items-center space-x-4">
                @if(session('error'))
                    <div id="success-alert" class="bg-red-600 text-white text-sm px-4 py-2 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <x-notificaciones />

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="focus:outline-none">
                        <img src="https://via.placeholder.com/30" alt="Avatar" class="rounded-full w-8 h-8">
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded-md shadow-md z-50 py-2 text-sm">
                        @if(Session::has('contribuyente_multinota'))
                            @php $contribuyente = Session::get('contribuyente_multinota'); @endphp
                            <div class="px-4 py-2 border-b border-gray-200">
                                <strong>{{ $contribuyente->nombre }} {{ $contribuyente->apellido }}</strong><br>
                                CUIT: {{ $contribuyente->cuit }}
                            </div>
                        @else
                            <div class="px-4 py-2 text-gray-500">No estás autenticado</div>
                        @endif

                        <a href="perfil-externo" class="block px-4 py-2 hover:bg-gray-100">Perfil</a>
                        <a href="cambiar-clave" class="block px-4 py-2 hover:bg-gray-100">Cambiar Clave</a>
                        <a href="/clear-session" class="block px-4 py-2 hover:bg-gray-100">Salir</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <div class="pt-20 px-4 max-w-7xl mx-auto">
        @yield('contenidoPrincipal')
    </div>

    <!-- Scripts -->
    <script>
        setTimeout(function () {
            const alert = document.getElementById('success-alert');
            if (alert) alert.style.display = 'none';
        }, 5000);
    </script>

    @yield('scripting')

</body>
</html>
