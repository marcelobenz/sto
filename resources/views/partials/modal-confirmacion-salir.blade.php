<dialog
    id="modal-salir"
    style="border: none; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.8)"
>
    <form method="dialog">
        <section>
            <h1 style="font-size: 16px">Confirmación</h1>
            <hr />
            <p>
                Al salir, perderá todos los cambios realizados ¿está de acuerdo?
            </p>
            <p></p>
        </section>
        <div style="display: flex; justify-content: space-between">
            <button id="cancel" type="button" class="btn btn-warning">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-x"
                    viewBox="0 0 16 16"
                >
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"
                    />
                </svg>
                No
            </button>
            <a href="{{ $path }}" class="btn btn-secondary">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-check"
                    viewBox="0 0 16 16"
                >
                    <path
                        d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"
                    />
                </svg>
                Si
            </a>
        </div>
    </form>
</dialog>
