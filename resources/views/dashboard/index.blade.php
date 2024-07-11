@extends('navbar')

@section('heading')
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
    body {
        padding-top: 70px; /* Ajusta según el tamaño de tu navbar */
    }
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
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('contenidoPrincipal')

<div class="container-fluid">
    <!-- Valores -->
    <div class="row card-columns">
        @foreach($chartData as $data)
        <div class="card">
            <div class="card-body text-center">
                <h8 class="card-title">{{ $data['tipo'] }}</h8>
                <h1>{{ $data['total'] }}</h1>
            </div>
        </div>
        @endforeach
    </div>
    <!-- Gráfico -->
    <div class="row">
        <div class="col-sm-12 mb-3 mb-sm-0 center">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="card-title text-center">Trámites en Gestión</h5>
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripting')
<script>
    var chartData = {!! json_encode($chartData) !!}; // Convertir PHP a JSON directamente

    document.addEventListener('DOMContentLoaded', function () {
        var canvas = document.getElementById('myChart');
        if (canvas) {
            var ctx = canvas.getContext('2d');

            var labels = chartData.map(function(item) {
                return item.tipo;
            });

            var data = chartData.map(function(item) {
                return item.total;
            });

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            console.error('Element with id "myChart" not found.');
        }
    });
</script>
@endsection
