@if (session("error"))
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: @json(session("error")),
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });
    </script>
@endif

@if (session("success"))
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: @json(session("success")),
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });
    </script>
@endif
