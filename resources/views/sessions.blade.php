<!-- resources/views/dashboard/sessions.blade.php -->

<h2>Estadísticas de sesiones</h2>
<ul>
    <li>Total de sesiones activas: {{ $total }}</li>
    <li>Sesiones admin (repetidas): {{ count($admins_raw) }}</li>
    <li>Sesiones SAT (repetidas): {{ count($sat_raw) }}</li>
    <li>Usuarios admin únicos: {{ count($admins_unique) }}</li>
    <li>Usuarios SAT únicos: {{ count($sat_unique) }}</li>
    {{-- <li>Sesiones de invitados: {{ $guest_count }}</li> --}}
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
        labels: ['Admin únicos', 'Usuarios SAT únicos', 'Invitados'],
        datasets: [{
            label: 'Sesiones',
            data: [
                {{ count($admins_unique) }},
                {{ count($sat_unique) }},
                {{ $guest_count }}
            ],
            backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56'],
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
