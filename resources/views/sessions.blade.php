<h2>Estadísticas de sesiones</h2>
<ul>
    <li>Total de sesiones admin: {{ $total_admins }}</li>
    <li>Total de sesiones SAT: {{ $total_sat }}</li>
    <li>Total de sesiones: {{ $total_sessions }}</li>
</ul>

<!-- Contenedor para el gráfico -->
<div style="max-width:500px; height:300px; margin-top:20px;">
    <canvas id="sessionChart"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('sessionChart').getContext('2d');
const sessionChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Admin', 'SAT'],
        datasets: [{
            label: 'Sesiones',
            data: [
                {{ $total_admins }},
                {{ $total_sat }}
            ],
            backgroundColor: ['#800000', '#2f4538'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
