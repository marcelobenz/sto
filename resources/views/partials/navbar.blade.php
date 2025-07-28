@php
    $usuarioInterno = Session::get("usuario_interno");
@endphp

<nav class="bg-gray-900 h-full w-full shadow" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left section: Brand + links -->
            <div class="flex items-center space-x-6">
                <a href="/dashboard" class="text-white font-bold text-xl">
                    Principal
                </a>

                <!-- Desktop menu, hidden on mobile -->
                <div class="hidden md:flex space-x-4 items-center">
                    <!-- Bandejas dropdown -->
                    <div
                        x-data="{ open: false }"
                        @keydown.escape="open = false"
                        class="relative"
                    >
                        <button
                            @click="open = !open"
                            class="text-white px-4 py-2 hover:bg-blue-700 rounded-md text-sm font-medium flex items-center"
                            type="button"
                            aria-haspopup="true"
                            :aria-expanded="open.toString()"
                        >
                            Bandejas
                            <svg
                                class="ml-1 h-4 w-4 fill-current"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                            >
                                <path d="M5.25 7.5l4.5 4.5 4.5-4.5z" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                            style="display: none"
                            tabindex="-1"
                        >
                            <a
                                href="#"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Bandeja Personal
                            </a>
                            <a
                                href="{{ route("tramites.enCurso") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Trámites en Curso
                            </a>
                            <a
                                href="{{ route("tramites.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Todos los Trámites
                            </a>
                        </div>
                    </div>

                    <!-- Formularios dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="text-white px-4 py-2 hover:bg-blue-700 rounded-md text-sm font-medium flex items-center"
                        >
                            Formularios
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute left-0 top-full mt-2 w-[600px] bg-white rounded-md shadow-xl p-4 grid gap-4 z-50 max-h-96 overflow-y-auto"
                        >
                            @foreach ($categoriasSubcategoriasMap as $idPadre => $categorias)
                                @foreach ($categorias as $c)
                                    @if ($c->id_categoria == $idPadre)
                                        @if ($categoriaPadreTieneCategoriasConMultinotasActivas[$idPadre] == true)
                                            <h3
                                                class="font-semibold text-gray-800 mb-2"
                                            >
                                                {{ $c->nombre }}
                                            </h3>
                                            @foreach ($categorias as $c)
                                                @if ($c->id_categoria != $idPadre)
                                                    @if (count($subcategoriasMultinotasMap[$c->id_categoria]) > 0)
                                                        <h4
                                                            class="font-semibold text-gray-800 mb-2"
                                                        >
                                                            {{ $c->nombre }}
                                                        </h4>
                                                        <ul class="space-y-1">
                                                            @foreach ($subcategoriasMultinotasMap[$c->id_categoria] as $multinota)
                                                                <li>
                                                                    <a
                                                                        href="{{ route("estadoTramite.tienePermiso", ["multinota" => $multinota]) }}"
                                                                        class="block text-sm text-gray-600 hover:text-blue-700 py-1"
                                                                    >
                                                                        {{ $multinota->nombre }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Administracion dropdown -->
                    <div
                        x-data="{ open: false }"
                        @keydown.escape="open = false"
                        class="relative"
                    >
                        <button
                            @click="open = !open"
                            class="text-white px-4 py-2 hover:bg-blue-700 rounded-md text-sm font-medium flex items-center"
                            type="button"
                            aria-haspopup="true"
                            :aria-expanded="open.toString()"
                        >
                            Administracion
                            <svg
                                class="ml-1 h-4 w-4 fill-current"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                            >
                                <path d="M5.25 7.5l4.5 4.5 4.5-4.5z" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                            style="display: none"
                            tabindex="-1"
                        >
                            <a
                                href="{{ route("categorias.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Categorias
                            </a>
                            <a
                                href="{{ route("usuarios.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Administrativos
                            </a>
                            <a
                                href="{{ route("limites.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Limite de asignaciones
                            </a>
                            <a
                                href="{{ route("contribuyente.buscar") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Usuarios
                            </a>
                            <a
                                href="{{ route("cuestionarios.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Cuestionarios
                            </a>
                            <a
                                href="{{ route("estados.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Estados
                            </a>
                            <a
                                href="{{ route("secciones-multinota.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Secciones Multinota
                            </a>
                            <a
                                href="{{ route("multinotas.index") }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Multinotas
                            </a>
                        </div>
                    </div>

                    <!-- Sistema dropdown -->
                    <div
                        x-data="{ open: false }"
                        @keydown.escape="open = false"
                        class="relative"
                    >
                        <button
                            @click="open = !open"
                            class="text-white px-4 py-2 hover:bg-blue-700 rounded-md text-sm font-medium flex items-center"
                            type="button"
                            aria-haspopup="true"
                            :aria-expanded="open.toString()"
                        >
                            Sistema
                            <svg
                                class="ml-1 h-4 w-4 fill-current"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                            >
                                <path d="M5.25 7.5l4.5 4.5 4.5-4.5z" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                            style="display: none"
                            tabindex="-1"
                        >
                            <a
                                href="sistema"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            >
                                Configuración Mail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page title -->
            <div
                class="hidden md:block text-white font-semibold truncate max-w-xs ml-6"
            >
                {{ $tituloPagina ?? "" }}
            </div>

            <!-- Right section: Mobile menu button + user dropdown -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu button -->
                <button
                    type="button"
                    aria-label="Toggle menu"
                    class="md:hidden text-gray-400 hover:text-white focus:outline-none focus:text-white"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{ 'hidden': mobileOpen, 'inline-flex': !mobileOpen }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{ 'hidden': !mobileOpen, 'inline-flex': mobileOpen }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <!-- User dropdown -->
                <div
                    x-data="{ openUser: false }"
                    @keydown.escape="openUser = false"
                    class="relative"
                >
                    <button
                        @click="openUser = !openUser"
                        class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                        id="user-menu"
                        type="button"
                        aria-haspopup="true"
                        :aria-expanded="openUser.toString()"
                    >
                        <img
                            class="h-8 w-8 rounded-full"
                            src="https://ui-avatars.com/api/?name={{ urlencode($usuarioInterno->nombre . " " . $usuarioInterno->apellido) }}"
                            alt="User avatar"
                        />
                    </button>

                    <div
                        x-show="openUser"
                        @click.away="openUser = false"
                        x-transition
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-30"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="user-menu"
                        style="display: none"
                        tabindex="-1"
                    >
                        @if (Session::has("usuario_interno"))
                            <div class="px-4 py-2 text-gray-700 text-sm">
                                <p class="font-semibold">
                                    {{ $usuarioInterno->nombre }}
                                    {{ $usuarioInterno->apellido }}
                                </p>
                                <p>
                                    Nro Legajo: {{ $usuarioInterno->legajo }}
                                </p>
                            </div>
                        @else
                            <div class="px-4 py-2 text-gray-700 text-sm">
                                No estás autenticado
                            </div>
                        @endif
                        <a
                            href="#"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            role="menuitem"
                        >
                            Perfil
                        </a>
                        <a
                            href="/clear-session"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                            role="menuitem"
                        >
                            Salir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, shown when menu button is clicked -->
    <div
        x-show="mobileOpen"
        class="md:hidden bg-gray-800"
        style="display: none"
        @click.away="mobileOpen = false"
    >
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            {{--
                <a
                href="/dashboard"
                class="block px-3 py-2 rounded-md text-base font-medium text-white bg-gray-900"
                >
                Principal
                </a>
                <a
                href="#"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700"
                >
                Bandeja Personal
                </a>
                <a
                href="{{ route("tramites.enCurso") }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700"
                >
                Trámites en Curso
                </a>
                <a
                href="{{ route("tramites.index") }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700"
                >
                Todos los Trámites
                </a>
            --}}
        </div>
    </div>
</nav>
