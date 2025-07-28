@extends("layouts.app")

@push("styles")
    <style>
        /* Estilo para el título del dashboard */
        .dashboard-title {
            font-size: 2.5rem; /* Tamaño grande de fuente */
            margin-bottom: 20px; /* Separación inferior */
            text-align: center; /* Centrado */
        }
        /* Estilo para los contenedores de gráficos */
        .dashboard-container {
            margin-top: 50px; /* Separación superior ajustable */
            font-size: 3.5rem; /* Tamaño grande de fuente */
            display: flex;
            justify-content: center; /* Centrado horizontal */
            align-items: center; /* Centrado vertical */
        }
        /* Añade un espacio entre el navbar y el gráfico */
        .card-columns {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-around;
        }
        .card {
            flex: 1;
            margin: 10px;
        }
        .chart-card {
            max-width: 600px; /* Ajusta el tamaño máximo del gráfico */
            margin: 0 auto; /* Centra la tarjeta del gráfico */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .chart-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* Proporción de 16:9 */
        }
        .chart-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .chart-row {
            display: flex;
            justify-content: center;
        }
        .chart-column {
            flex: 1;
            padding: 10px;
        }
        .equal-height {
            display: flex;
        }
    </style>
@endpush

@section("content")
    <div class="container-fluid">
        <!-- Valores -->
        <div class="row card-columns">
            @foreach ($chartData as $data)
                <div class="card">
                    <div class="card-body text-center">
                        <h8 class="card-title">{{ $data["tipo"] }}</h8>
                        <h1>{{ $data["total"] }}</h1>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Gráficos -->
        <div class="row chart-row equal-height">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Trámites</h5>
                    <h1>{{ $totales[0]->sumatotal }}</h1>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div class="chart-column">
                <div class="card chart-card">
                    <div class="card-body">
                        <h5 class="card-title text-center">
                            Trámites en Gestión
                        </h5>
                        <div class="chart-container">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Gráfico de tortas -->
            <div class="chart-column">
                <div class="card chart-card">
                    <div class="card-body">
                        <h5 class="card-title text-center">
                            Distribución de Trámites
                        </h5>
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        var chartData = {!! json_encode($chartData) !!}; // Convertir PHP a JSON directamente

        document.addEventListener('DOMContentLoaded', function () {
            var barCanvas = document.getElementById('barChart');
            var pieCanvas = document.getElementById('pieChart');

            if (barCanvas) {
                var barCtx = barCanvas.getContext('2d');
                var labels = chartData.map(function (item) {
                    return item.tipo;
                });
                var data = chartData.map(function (item) {
                    return item.total;
                });

                var barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Cantidad de Trámites',
                                data: data,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            } else {
                console.error('Element with id "barChart" not found.');
            }

            if (pieCanvas) {
                var pieCtx = pieCanvas.getContext('2d');

                var pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Distribución de Trámites',
                                data: data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(25, 6, 186, 0.2)',
                                    'rgba(75, 100, 40, 0.2)',
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(25, 6, 186, 1)',
                                    'rgba(75, 100, 40, 1)',
                                ],
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                    },
                });
            } else {
                console.error('Element with id "pieChart" not found.');
            }
        });
    </script>
@endpush
