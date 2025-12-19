@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Estadísticas de Postulantes')

@section('body')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-users"></i> Estadísticas de Postulantes
            </div>
            <div class="card-body">
                <!-- Filtros (Vinculados a Proyectos) -->
                <form action="{{ url('admin/statistics/postulantes') }}" method="GET" class="row mb-4">
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
                        <label>Modalidad de Proyecto</label>
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
                        <a href="{{ url('admin/statistics/postulantes') }}" class="btn btn-secondary w-100">Limpiar
                            Filtros</a>
                    </div>
                </form>

                <h4 class="mt-4 mb-4 text-secondary border-bottom pb-2">Total de Postulantes: <span
                        class="text-primary">{{ number_format($totalPostulantes) }}</span></h4>

                <div class="row">
                    <!-- Postulantes por Sexo -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary"><i class="fa fa-venus-mars"></i> Distribución por Sexo
                                </h5>
                                <div style="height: 250px;"><canvas id="genderChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rango de Edad -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary"><i class="fa fa-birthday-cake"></i> Rangos de Edad</h5>
                                <div style="height: 250px;"><canvas id="ageChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Discapacidad -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary"><i class="fa fa-wheelchair"></i> Discapacidad</h5>
                                <div style="height: 250px;"><canvas id="disabilityChart"></canvas></div>
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
            // genderChart
            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($postulantesByGender->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($postulantesByGender->pluck('total')) !!},
                        backgroundColor: ['#e83e8c', '#4e73df', '#858796']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ageChart
            new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($ageRanges)) !!},
                    datasets: [{
                        label: 'Postulantes',
                        data: {!! json_encode(array_values($ageRanges)) !!},
                        backgroundColor: '#f6c23e'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // disabilityChart
            new Chart(document.getElementById('disabilityChart'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode($postulantesByDisability->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($postulantesByDisability->pluck('total')) !!},
                        backgroundColor: ['#1cc88a', '#e74a3b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
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
