@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Estadísticas de Proyectos')

@section('body')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-building"></i> Estadísticas de Proyectos
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form action="{{ url('admin/statistics/projects') }}" method="GET" class="row mb-4">
                    <div class="col-md-3">
                        <label>Año de Proyecto</label>
                        <select name="year" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los años</option>
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Modalidad</label>
                        <select name="modality_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas las modalidades</option>
                            @foreach ($modalities as $m)
                                <option value="{{ $m->id }}" {{ $modalityId == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Departamento</label>
                        <select name="department_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los departamentos</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->DptoId }}" {{ $departmentId == $d->DptoId ? 'selected' : '' }}>
                                    {{ $d->DptoNom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="{{ url('admin/statistics/projects') }}" class="btn btn-secondary w-100">Limpiar
                            Filtros</a>
                    </div>
                </form>

                <h4 class="mt-4 mb-4 text-secondary border-bottom pb-2">Total de Proyectos: <span
                        class="text-primary">{{ number_format($totalProjects) }}</span></h4>

                <hr>

                <div class="row">
                    <!-- Proyectos por Año -->
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fa fa-line-chart"></i> Tendencia de Proyectos
                                    por Año</h5>
                                <div style="height: 300px;"><canvas id="yearsChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Proyectos por SAT (Top 15) -->
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fa fa-university"></i> Cantidad de Proyectos
                                    por SAT (Top 15)</h5>
                                <div style="height: 400px;"><canvas id="satChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Proyectos por Modalidad -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fa fa-pie-chart"></i> Proyectos por Modalidad
                                </h5>
                                <div style="height: 250px;"><canvas id="modalityChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Proyectos por Estado -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fa fa-tasks"></i> Proyectos por Estado Actual
                                </h5>
                                <div style="height: 250px;"><canvas id="statusChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Proyectos por Departamento -->
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><i class="fa fa-map-marker"></i> Distribución por
                                    Departamento</h5>
                                <div style="height: 350px;"><canvas id="deptChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#858796', '#5a5c69', '#6610f2', '#6f42c1', '#e83e8c'
            ];

            // Chart 1: Years
            new Chart(document.getElementById('yearsChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($projectsByYear->keys()) !!},
                    datasets: [{
                        label: 'Cantidad de Proyectos',
                        data: {!! json_encode($projectsByYear->values()) !!},
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Chart: SATs (Bar Horizontal)
            const satLabels = {!! json_encode($projectsBySat->pluck('label')) !!};
            const satData = {!! json_encode($projectsBySat->pluck('total')) !!};
            console.log('SAT Labels:', satLabels);
            console.log('SAT Data:', satData);

            new Chart(document.getElementById('satChart'), {
                type: 'bar',
                data: {
                    labels: satLabels,
                    datasets: [{
                        label: 'Proyectos',
                        data: satData,
                        backgroundColor: '#4e73df'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y'
                }
            });

            // Chart 2: Modality
            new Chart(document.getElementById('modalityChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($projectsByModality->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($projectsByModality->pluck('total')) !!},
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Chart 3: Status
            new Chart(document.getElementById('statusChart'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode($projectsByStatus->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($projectsByStatus->pluck('total')) !!},
                        backgroundColor: colors.slice().reverse()
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Chart 4: Department
            new Chart(document.getElementById('deptChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($projectsByDept->pluck('label')) !!},
                    datasets: [{
                        label: 'Proyectos',
                        data: {!! json_encode($projectsByDept->pluck('total')) !!},
                        backgroundColor: '#1cc88a'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y'
                }
            });
        });
    </script>

    <style>
        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .shadow-sm:hover {
            transform: translateY(-3px);
            transition: transform 0.2s;
        }
    </style>
@endsection
