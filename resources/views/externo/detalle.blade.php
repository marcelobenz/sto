@extends('navbarExterno')

@section('heading')
<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection

@section('contenidoPrincipal')
<div class="w-full">

    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <x-loader />

    <br/>
    <br/>
    <br/>

    {{-- Header Trámite --}}
    <div class="w-full flex flex-col lg:flex-row justify-between items-start lg:items-center bg-gray-100 p-4 border-b border-gray-300">
        <div class="flex flex-col lg:flex-row gap-2 w-full">
            <div class="flex-1 border bg-white p-3">
                Trámite Nro <strong>{{ $idTramite }}</strong>
                <span class="mx-1">-</span>
                {{ $tramiteInfo->nombre ?? 'Sin nombre' }}
                <span class="mx-1">-</span>
                {{ $tramiteInfo->fecha_alta ? date('d/m/Y', strtotime($tramiteInfo->fecha_alta)) : 'Sin fecha' }}
            </div>

            <div class="flex-1 border bg-white p-3">
                Estado actual:
                <span class="ml-1 inline-flex items-center rounded px-2 py-1 text-sm font-semibold
                    @if($tramiteInfo->estado_actual === 'Aprobado') bg-green-600 text-white
                    @elseif($tramiteInfo->estado_actual === 'Rechazado') bg-red-600 text-white
                    @elseif($tramiteInfo->estado_actual === 'Dado de Baja') bg-gray-600 text-white
                    @elseif($tramiteInfo->estado_actual === 'Iniciado') bg-blue-600 text-white
                    @elseif($tramiteInfo->estado_actual === 'Finalizado') bg-green-700 text-white
                    @else bg-black text-white @endif">
                    {{ $tramiteInfo->estado_actual ?? 'Desconocido' }}
                    @if($tramiteInfo->espera_documentacion)
                        <span class="ml-1 text-xs font-normal opacity-90">(Espera Documentación)</span>
                    @endif
                </span>
            </div>

            <div class="flex-1 border bg-white p-3">
                Prioridad:
                <span class="ml-1 inline-flex items-center rounded px-2 py-1 text-sm font-semibold
                    @if(strtolower($tramiteInfo->prioridad) === 'baja') bg-green-600 text-white
                    @elseif(strtolower($tramiteInfo->prioridad) === 'normal') bg-yellow-400 text-black
                    @elseif(strtolower($tramiteInfo->prioridad) === 'alta') bg-red-600 text-white
                    @else bg-gray-500 text-white @endif">
                    {{ ucfirst($tramiteInfo->prioridad) ?? 'Sin prioridad' }}
                </span>
            </div>

            <div class="flex-1 border bg-white p-3">
                Asignado a: <strong>{{ $tramiteInfo->nombre_usuario }} {{ $tramiteInfo->apellido_usuario }}</strong>
            </div>
        </div>

        {{-- Botones de acción --}}
     <div class="flex gap-2 items-center">
            @if($tramiteInfo->puede_pedir_documentacion && !$tramiteInfo->espera_documentacion)
            <button class="rounded bg-blue-600 px-3 py-2 text-white hover:bg-blue-800"
                    onclick="pedirDocumentacion({{$idTramite}})" title="Pedir Documentación">
                <i class="fas fa-file"></i>
            </button>
            @endif

            <button class="rounded bg-red-600 px-3 py-2 text-white hover:bg-red-800"
                    onclick="darDeBajaTramite({{ $idTramite }})" title="Dar de baja">
                <i class="fas fa-ban"></i>
            </button>

            <button class="rounded bg-red-600 px-3 py-2 text-white hover:bg-red-800"
                    onclick="window.open('{{ route('reporte.constancia', ['idTramite' => $idTramite]) }}', '_blank')"
                    title="Imprimir">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

   {{-- Grid principal --}}
<div class="grid grid-cols-2 gap-4 mt-0">

    {{-- Columna izquierda --}}
    <div class="space-y-4 p-4">
        {{-- Título Información --}}
        <div class="overflow-hidden border border-gray-300">
            <div class="bg-gray-800 py-2 text-center text-lg font-semibold text-white">Información</div>
        </div>

        {{-- Acordeón de información del trámite --}}
        <div class="space-y-3">
            @php
                $grupoDetalles = $detalleTramite->groupBy('titulo');
            @endphp
            @foreach($grupoDetalles as $titulo => $detalles)
                @php $panelId = 'panel_'.Str::slug($titulo); @endphp
                <div class="overflow-hidden border">
                    <button type="button"
                            class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-left text-gray-800 hover:bg-gray-100"
                            onclick="toggleSection('{{ $panelId }}')">
                        <span class="font-medium">{{ $titulo }}</span>
                        <i class="fa-solid fa-chevron-down transition-transform" id="{{ $panelId }}_icon"></i>
                    </button>
                    <div id="{{ $panelId }}" class="hidden">
                        <div class="p-4">
                            <table class="w-full border-collapse">
                                <tbody class="divide-y">
                                    @foreach($detalles as $detalle)
                                    <tr>
                                        <td class="w-1/2 p-2 font-medium">{{ $detalle->nombre }}</td>
                                        <td class="w-1/2 p-2">{{ $detalle->valor }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Columna derecha --}}
    <div class="space-y-4 p-4">
        {{-- Adjuntos --}}
        <div class="overflow-hidden border border-gray-300">
            <div class="bg-gray-800 py-2 text-center text-lg font-semibold text-white">Adjuntos</div>
            <div class="max-h-[200px] overflow-y-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-sm text-gray-600">
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2">Descripción</th>
                            <th class="px-3 py-2 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($tramiteArchivo as $archivo)
                        <tr>
                            <td class="px-3 py-2">{{ $archivo->fecha_alta }}</td>
                            <td class="px-3 py-2">{{ $archivo->nombre }}</td>
                            <td class="px-3 py-2">{{ $archivo->descripcion }}</td>
                            <td class="px-3 py-2 text-center">
                                <a href="{{ route('archivo.descargar', $archivo->id_archivo) }}" class="text-blue-600 hover:underline" title="Descargar">
                                    <i class="fa-solid fa-download text-lg"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if(count($tramiteArchivo) === 0)
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Sin archivos adjuntos.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Subir archivo + Volver --}}
        <div class="flex flex-col items-stretch justify-between gap-3 sm:flex-row">
            <a href="{{ route('bandeja-usuario-externo') }}"
               class="inline-flex items-center justify-center rounded bg-gray-500 px-4 py-2 font-medium text-white hover:bg-gray-600">
               Volver
            </a>
            <form action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data"
                  class="flex w-full items-center gap-2 sm:w-auto">
                @csrf
                <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
                <input type="file" name="archivo" required
                       class="w-full rounded border border-gray-300 p-2 text-sm file:mr-2 file:rounded file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:text-white hover:file:bg-blue-700 sm:w-72">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700">
                    <i class="fa-solid fa-upload"></i> Subir Archivo
                </button>
            </form>
        </div>

        {{-- Historial --}}
        <div class="overflow-hidden border border-gray-300">
            <div class="bg-gray-800 py-2 text-center text-lg font-semibold text-white">Historial</div>
            <div class="max-h-[300px] overflow-y-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 text-left text-sm text-gray-600">
                        <tr>
                            <th class="px-3 py-2">Descripción</th>
                            <th class="px-3 py-2">Clave</th>
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2 text-center">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($historialTramite as $evento)
                        <tr>
                            <td class="px-3 py-2">{{ $evento->descripcion }}</td>
                            <td class="px-3 py-2">
                                <span class="rounded bg-blue-600 px-2 py-1 text-xs font-semibold text-white">{{ $evento->clave }}</span>
                            </td>
                            <td class="px-3 py-2">{{ date('d/m/Y H:i', strtotime($evento->fecha_alta)) }}</td>
                            <td class="px-3 py-2 text-center">{{ $evento->legajo }} - {{ $evento->usuario }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">No hay eventos en el historial.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
  function showModal(id) { const el = document.getElementById(id); el && el.classList.remove('hidden'); el && el.classList.add('flex'); }
  function hideModal(id) { const el = document.getElementById(id); el && el.classList.add('hidden'); el && el.classList.remove('flex'); }

  function toggleSection(id) {
    const panel = document.getElementById(id);
    const icon = document.getElementById(id + '_icon');
    if (!panel) return;
    panel.classList.toggle('hidden');
    if (icon) icon.classList.toggle('rotate-180');
  }

  function toggleDetalle(idPregunta, valor, dataset) {
    const cont = document.getElementById('detalle_' + idPregunta);
    if (!cont) return;
    const si = Number(dataset.flagDetalleSi || dataset.flagDetalleSi === "0" ? dataset.flagDetalleSi : cont.dataset.flagDetalleSi);
    const no = Number(dataset.flagDetalleNo || dataset.flagDetalleNo === "0" ? dataset.flagDetalleNo : cont.dataset.flagDetalleNo);
    const show = (valor === "1" && si == 1) || (valor === "0" && no == 1);
    cont.classList.toggle('hidden', !show);
  }
</script>
@endpush
