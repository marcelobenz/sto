<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield("title", "STO")</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />
        <!-- CSS -->
        <link
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"
        />
        <link
            href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
        />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        />
        <!-- Scripts -->
        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(["resources/css/app.css", "resources/js/app.js"])
        <!-- TinyMCE Config -->
        <x-head.tinymce-config />
        @yield("heading")
        <!-- Navbar styles -->
        <style>
            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                font-size: 12px; /* Ajusta el tamaño de la fuente */
            }
            .dataTables_wrapper
                .dataTables_paginate
                .paginate_button
                .page-link {
                padding: 0.5rem 0.75rem; /* Ajusta el padding */
            }
            .dataTables_wrapper
                .dataTables_paginate
                .paginate_button
                .page-link
                svg {
                width: 16px; /* Ajusta el ancho del icono */
                height: 16px; /* Ajusta el alto del icono */
            }
        </style>
        @stack("styles")
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col bg-gray-100">
        <header
            class="sticky top-0 z-10 h-16 shrink-0 shadow-[0_2px_4px_rgba(0,0,0,0.1)]"
        >
            @if (session("isExterno") === true)
                @include("partials.externo.navbar-externo")
            @else
                @include("partials.navbar")
            @endif
        </header>
        <main class="flex-grow">
            @include("partials.alerts")

            @yield("content")
        </main>
        <footer class="h-10 shrink-0 text-center text-sm/8">
            <p>&copy; {{ date("Y") }} Sistema de Trámites Online.</p>
        </footer>
        <!-- Global libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/cleave.js@1/dist/cleave.min.js"></script>
        <script>
            // Esperar 5 segundos y luego ocultar el mensaje
            setTimeout(function () {
                var alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            }, 5000); // 5000 milisegundos = 5 segundos
        </script>
        @stack("scripts")
    </body>
</html>
