<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-8 space-y-10">
        <h2 class="text-2xl font-bold text-center text-gray-800">Crear Cuenta</h2>

        {{-- Datos Personales --}}
        <div class="space-y-4 border border-gray-200 rounded-xl p-6 shadow-sm">
            <h3 class="text-xl font-semibold text-gray-700">Datos Personales</h3>

            @if ($errors->any())
                <div class="mb-2">
                    <ul class="list-disc text-red-600 pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('registro-externo.registrar') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="nombre" placeholder="Nombre" class="input" value="{{ old('nombre') }}">
                    <input type="text" name="apellido" placeholder="Apellido" class="input" value="{{ old('apellido') }}">
                </div>

                <input type="text" name="cuit" placeholder="CUIL / CUIT" class="input" value="{{ old('cuil_cuit') }}">
                <input type="email" name="correo" placeholder="Correo electrónico" class="input" value="{{ old('email') }}">

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="telefono_1" placeholder="Teléfono 1" class="input" value="{{ old('telefono_1') }}">
                    <input type="text" name="telefono_2" placeholder="Teléfono 2 (opcional)" class="input" value="{{ old('telefono_2') }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <input type="password" name="clave" placeholder="Contraseña" class="input">
                    <input type="password" name="clave_confirmation" placeholder="Repetir contraseña" class="input">
                </div>

                {{-- Dirección --}}
<div class="mt-8 border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
    <h3 class="text-xl font-semibold text-gray-700">Dirección</h3>

    <div class="relative">
        <input type="text" id="autocomplete" placeholder="Buscar dirección..." class="input w-full" autocomplete="off" />
        <div id="autocomplete-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto hidden"></div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <input type="text" name="calle" id="route" placeholder="Calle" class="input" disabled>
        <input type="text" name="numero" id="street_number" placeholder="Número" class="input" disabled>
        <input type="text" name="localidad" id="locality" placeholder="Ciudad" class="input" disabled>
        <input type="text" name="provincia" id="administrative_area_level_1" placeholder="Provincia" class="input" disabled>
        <input type="text" name="codigo_postal" id="postal_code" placeholder="Código Postal" class="input" disabled>
        <input type="text" name="pais" id="country" placeholder="País" class="input" disabled>
        <input type="text" name="latitude" id="latitude" placeholder="Latitud" class="input" disabled>
        <input type="text" name="longitude" id="longitude" placeholder="Longitud" class="input" disabled>
    </div>
</div>


                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-xl font-semibold transition">
                    Registrarse
                </button>
            </form>
        </div>
    </div>

    <style>
        .input {
            @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
    </style>

   <script>
const API_KEY = "{{ config('services.google.maps_key') }}";

const inputElement = document.getElementById("autocomplete");
const resultsDiv = document.getElementById("autocomplete-results");

if (inputElement) {
    inputElement.addEventListener("input", handleAutocomplete);
} else {
    console.error("Campo de búsqueda de dirección no encontrado.");
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

        // Limpiar valores
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
