<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white text-lg font-semibold px-6 py-4 text-center">
            Crear Cuenta
        </div>

        <div class="p-6 space-y-6">
            {{-- Errores --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-700 border border-red-400">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('registro-externo.registrar') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Datos Personales --}}
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Datos Personales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input data-testid="input-nombre" type="text" name="nombre" placeholder="Nombre" class="form-input" value="{{ old('nombre') }}">
                        <input data-testid="input-apellido" type="text" name="apellido" placeholder="Apellido" class="form-input" value="{{ old('apellido') }}">
                    </div>
                    <input data-testid="input-cuit" type="text" name="cuit" placeholder="CUIL / CUIT" class="form-input mt-4" value="{{ old('cuil_cuit') }}">
                    <input data-testid="input-email" type="email" name="correo" placeholder="Correo electrónico" class="form-input mt-4" value="{{ old('email') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <input data-testid="input-telefono1" type="text" name="telefono_1" placeholder="Teléfono 1" class="form-input" value="{{ old('telefono_1') }}">
                        <input data-testid="input-telefono2" type="text" name="telefono_2" placeholder="Teléfono 2 (opcional)" class="form-input" value="{{ old('telefono_2') }}">
                    </div>
                </div>

                {{-- Contraseña --}}
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Contraseña</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input data-testid="input-password" type="password" name="clave" placeholder="Contraseña" class="form-input">
                        <input data-testid="input-passwordconfirm" type="password" name="clave_confirmation" placeholder="Repetir contraseña" class="form-input">
                    </div>
                </div>

                {{-- Dirección --}}
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Dirección</h3>

                    <div class="relative">
                        <input data-testid="input-direccion" type="text" id="autocomplete" placeholder="Buscar dirección..." class="form-input w-full" autocomplete="off" />
                        <div id="autocomplete-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto hidden"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <input data-testid="input-calle" type="text" name="calle" id="route" placeholder="Calle" class="form-input" disabled>
                        <input data-testid="input-numero" type="text" name="numero" id="street_number" placeholder="Número" class="form-input" disabled>
                        <input data-testid="input-localidad" type="text" name="localidad" id="locality" placeholder="Ciudad" class="form-input" disabled>
                        <input data-testid="input-provincia" type="text" name="provincia" id="administrative_area_level_1" placeholder="Provincia" class="form-input" disabled>
                        <input data-testid="input-codigopostal" type="text" name="codigo_postal" id="postal_code" placeholder="Código Postal" class="form-input" disabled>
                        <input data-testid="input-pais" type="text" name="pais" id="country" placeholder="País" class="form-input" disabled>
                        <input data-testid="input-latitud" type="text" name="latitude" id="latitude" placeholder="Latitud" class="form-input" disabled>
                        <input data-testid="input-longitud" type="text" name="longitude" id="longitude" placeholder="Longitud" class="form-input" disabled>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button data-testid="btn-registrar" type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                        Registrarse
                    </button>

                    <a data-testid="btn-volver" href="{{ route('ingreso-externo') }}"
                       class="block w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                       Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-input {
            @apply w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500;
        }
    </style>

    {{-- Script original intacto --}}
    <script>
        const API_KEY = "{{ config('services.google.maps_key') }}";

        const inputElement = document.getElementById("autocomplete");
        const resultsDiv = document.getElementById("autocomplete-results");

        if (inputElement) {
            inputElement.addEventListener("input", handleAutocomplete);
        }

        async function handleAutocomplete() {
            if (!inputElement || !resultsDiv) return;
            const input = inputElement.value;
            if (input.length < 3) {
                resultsDiv.innerHTML = "";
                resultsDiv.style.display = "none";
                return;
            }

            const url = `https://places.googleapis.com/v1/places:autocomplete?key=${API_KEY}`;
            const body = {
                input: input,
                includedRegionCodes: ['ar']
            };

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Goog-FieldMask": "*"
                    },
                    body: JSON.stringify(body),
                });

                const data = await response.json();
                resultsDiv.innerHTML = "";

                if (data.suggestions) {
                    resultsDiv.style.display = "block";
                    data.suggestions.forEach(suggestion => {
                        const placePrediction = suggestion.placePrediction;
                        const description = placePrediction.text.text;

                        const div = document.createElement("div");
                        div.classList.add("p-2", "cursor-pointer", "hover:bg-blue-100");
                        div.textContent = description;
                        div.onclick = () => getPlaceDetails(placePrediction.placeId);
                        resultsDiv.appendChild(div);
                    });
                }
            } catch (error) {
                console.error("Error en autocomplete:", error);
            }
        }

        async function getPlaceDetails(placeId) {
            resultsDiv.innerHTML = "";
            resultsDiv.style.display = "none";

            const url = `https://places.googleapis.com/v1/places/${placeId}?key=${API_KEY}&fields=name,formattedAddress,addressComponents/types,addressComponents/longText,addressComponents/shortText,location`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                ['route','street_number','locality','administrative_area_level_1','postal_code','country','latitude','longitude'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.value = '';
                        el.disabled = false;
                    }
                });

                if (data.location) {
                    document.getElementById("latitude").value = data.location.latitude;
                    document.getElementById("longitude").value = data.location.longitude;
                }

                if (data.addressComponents) {
                    data.addressComponents.forEach(component => {
                        const types = component.types;
                        if (types.includes('route')) document.getElementById("route").value = component.shortText;
                        if (types.includes('street_number')) document.getElementById("street_number").value = component.shortText;
                        if (types.includes('locality')) document.getElementById("locality").value = component.longText;
                        if (types.includes('administrative_area_level_1')) document.getElementById("administrative_area_level_1").value = component.longText;
                        if (types.includes('postal_code')) document.getElementById("postal_code").value = component.shortText;
                        if (types.includes('country')) document.getElementById("country").value = component.longText;
                    });
                }
            } catch (error) {
                console.error("Error al obtener detalles del lugar:", error);
            }
        }

        document.addEventListener("click", (event) => {
            if (!inputElement.contains(event.target) && !resultsDiv.contains(event.target)) {
                resultsDiv.style.display = "none";
            }
        });
    </script>
</body>
</html>
