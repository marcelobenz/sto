@extends("layouts.app")

@section("content")
    <div class="container-fluid px-3">
        <div class="row mb-3 px-3" style="justify-content: end">
            <div class="col-md-12">
                <br />
                <br />
                <br />
                <div
                    style="
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        border-radius: 0 0 5px 5px;
                        box-shadow: 0.1px 0.1px 7px 0.4px;
                    "
                >
                    <h2
                        style="
                            margin-bottom: 0 !important;
                            background-color: #27ace3;
                            padding: 10px;
                            color: white;
                        "
                    >
                        Buscar usuario
                    </h2>
                    <div
                        style="
                            display: flex;
                            flex-direction: column;
                            padding: 0 15px 15px 15px;
                        "
                    >
                        <label>CUIL/CUIT del Usuario *</label>
                        <span style="display: flex; gap: 10px">
                            <form
                                method="GET"
                                style="width: 100%"
                                action="{{ route("instanciaTramite.buscar") }}"
                            >
                                <input
                                    type="hidden"
                                    name="idMultinota"
                                    value="{{ $multinota }}"
                                />
                                <input
                                    name="cuit"
                                    id="cuit"
                                    type="text"
                                    placeholder="99-99999999-9"
                                    minlength="13"
                                    maxlength="13"
                                    style="width: 30%"
                                />
                                <button type="submit" class="btn btn-secondary">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        fill="currentColor"
                                        viewBox="0 0 512 512"
                                    >
                                        <path
                                            d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6 .1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"
                                        />
                                    </svg>
                                </button>
                            </form>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        new Cleave('#cuit', {
            delimiter: '-',
            blocks: [2, 8, 1],
        });
    </script>
@endpush
