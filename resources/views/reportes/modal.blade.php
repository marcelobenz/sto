@extends('navbar')

@section('heading')
    <h1>PDF</h1>
@endsection

@section('contenidoPrincipal')
<div class="container">
  <div class="text-center mt-4">
    <h2>Generando Constancia del Trámite #{{ $idTramite }}</h2>
    <p>El PDF se está cargando a continuación...</p>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Constancia generada</h5>
          <button type="button" class="btn btn-secondary" id="btnCerrarPdfModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 384 512">
              <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
            </svg>
          </button>
        </div>
        <div class="modal-body p-0">
          <iframe src="{{ route('reporte.constancia', $idTramite) }}"
                  width="100%" height="600" style="border:none;"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    let modal = new bootstrap.Modal(document.getElementById('pdfModal'));
    modal.show();

    document.getElementById('btnCerrarPdfModal').addEventListener('click', function () {
      window.location.href = "{{ route('tramites.index') }}";
    });

    document.getElementById('pdfModal').addEventListener('click', function (e) {
      if (e.target.id === 'pdfModal') {
        window.location.href = "{{ route('tramites.index') }}";
      }
    });
  });
</script>
@endpush
