<!-- resources/views/dashboard/index.blade.php -->
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
    /* Estilo para cada columna dentro de la container-fluid */
    .dashboard-column {
        padding: 0 25px; /* Espaciado horizontal */
        margin-bottom: 30px; /* Separación inferior */
    }
    /* Añade un espacio entre el navbar y el gráfico */
    body {
        padding-top: 70px; /* Ajusta según el tamaño de tu navbar */
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('contenidoPrincipal')
<div style="width: 50%; margin: auto;">
    <canvas id="myChart"></canvas>
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
                        label: 'Total de Trámites en Gestión',
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