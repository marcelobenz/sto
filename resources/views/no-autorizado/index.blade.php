@extends('layouts.app')

@push('styles')
    <style>
        /* Estilos personalizados para los iconos de flecha */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 12px; /* Ajusta el tamaño de la fuente */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
            padding: 0.5rem 0.75rem; /* Ajusta el padding */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link svg {
            width: 16px; /* Ajusta el ancho del icono */
            height: 16px; /* Ajusta el alto del icono */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3">
        <div class="row mb-3 px-3" style="justify-content: end;">            
            <div class="col-md-12">
                <br/>
                    <br/>
                        <br/>
                            <div style="display: flex; flex-direction: column; gap: 10px;"">
                                <h2 style="margin-bottom: 0 !important;">No autorizado</h2>
                                <div style="background-color: #f39c12; color: white; border-radius: 5px; padding: 1rem;">No se encuentra autorizado para iniciar un trámite de esta categoría.</div>
                                <a href={{ route('bandeja-personal.index') }} class="btn btn-secondary" style="width: fit-content;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                                    </svg>
                                </a>
                            </div>
            </div>
        </div>
    </div>
@endsection