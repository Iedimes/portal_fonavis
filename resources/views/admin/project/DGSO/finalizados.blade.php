@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Re-habilitar Proyectos DGSO')

<style>
    /* Fail-proof custom checkbox styling */
    .custom-checkbox-container {
        display: inline-block;
        position: relative;
        width: 22px;
        height: 22px;
        cursor: pointer;
        user-select: none;
        margin: 0 !important;
        vertical-align: middle;
    }

    .custom-checkbox-container input {
        position: absolute;
        opacity: 0 !important;
        cursor: pointer;
        height: 0;
        width: 0;
        margin: 0;
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 22px;
        width: 22px;
        background-color: #ffffff;
        border: 2px solid #002b5c;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s ease-in-out;
    }

    .custom-checkbox-container:hover input ~ .checkmark {
        background-color: #e6f0fa;
        border-color: #004085;
    }

    .custom-checkbox-container input:checked ~ .checkmark {
        background-color: #002b5c;
        border-color: #002b5c;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .custom-checkbox-container input:checked ~ .checkmark:after {
        display: block;
    }

    .custom-checkbox-container .checkmark:after {
        left: 7px;
        top: 2px;
        width: 6px;
        height: 12px;
        border: solid #ffffff;
        border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
    }
</style>

@section('body')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> RE-HABILITAR PROYECTOS (DGSO)
                    <p class="float-right m-0" style="text-align: right">DEPENDENCIA - DGSO</p>
                </div>
                <div class="card-body">
                    <div class="card-block">
                        {{-- Alert Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-triangle"></i> {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-times-circle"></i> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Search & Bulk Action Bar --}}
                        <div class="row justify-content-between mb-3">
                            <div class="col-md-6 form-group mb-0">
                                <form method="GET" action="{{ url('admin/projects/finalizados-dgso') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="BUSCAR PROYECTO O CÓDIGO..."
                                            value="{{ request('search') }}">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i>&nbsp; BUSCAR
                                            </button>
                                            @if (request('search'))
                                                <a href="{{ url('admin/projects/finalizados-dgso') }}" class="btn btn-secondary">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-warning font-weight-bold" onclick="submitBulkForm()">
                                    <i class="fa fa-check-square-o"></i> RE-HABILITAR SELECCIONADOS
                                </button>
                            </div>
                        </div>

                        {{-- Main Table --}}
                        <form id="bulk-rehabilitar-form" action="{{ url('admin/projects/rehabilitar-masivo-dgso') }}" method="POST">
                            @csrf
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 50px; text-align: center; vertical-align: middle;">
                                            <label class="custom-checkbox-container" title="Seleccionar todos">
                                                <input type="checkbox" id="check-all">
                                                <span class="checkmark"></span>
                                            </label>
                                        </th>
                                        <th style="width: 80px;">CÓDIGO</th>
                                        <th>PROYECTO</th>
                                        <th>SAT</th>
                                        <th>DEPARTAMENTO</th>
                                        <th>CIUDAD</th>
                                        <th style="text-align: center;">CALIFICACIÓN</th>
                                        <th style="text-align: center;">MIGRADO SHD</th>
                                        <th style="text-align: center;">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <label class="custom-checkbox-container">
                                                    <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" class="project-checkbox">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </td>
                                            <td style="vertical-align: middle; font-weight: bold;">
                                                {{ $project->id }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <strong>{{ $project->name }}</strong>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $project->getSat ? $project->getSat->NucNomSat : 'N/A' }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $project->getState ? $project->getState->DptoNom : '' }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {{ $project->getCity ? $project->getCity->CiuNom : '' }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span class="badge badge-success px-2 py-1" style="font-size: 0.9em; color: white;">
                                                    FINALIZADA
                                                </span>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if ($project->shd_migrated)
                                                    <span class="badge badge-info px-2 py-1" style="font-size: 0.9em; color: white; background-color: #17a2b8;">
                                                        SÍ
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary px-2 py-1" style="font-size: 0.9em; color: white; background-color: #6c757d;">
                                                        NO
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="confirmRehabilitar('{{ $project->id }}', '{{ addslashes($project->name) }}')"
                                                    title="Re-habilitar proyecto">
                                                    <i class="fa fa-unlock"></i> RE-HABILITAR
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="fa fa-folder-open-o fa-2x d-block mb-2"></i>
                                                No hay proyectos finalizados pendientes de re-habilitación.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </form>

                        {{-- Individual Form --}}
                        <form id="individual-rehabilitar-form" action="" method="POST" style="display: none;">
                            @csrf
                        </form>

                        {{-- Pagination --}}
                        <div class="row justify-content-center mt-3">
                            {{ $projects->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    var checkboxes = document.querySelectorAll('.project-checkbox');
                    checkboxes.forEach(function (cb) {
                        cb.checked = checkAll.checked;
                        // Disparar evento de cambio para refrescar la marca visual
                        cb.dispatchEvent(new Event('change'));
                    });
                });
            }
        });

        function confirmRehabilitar(projectId, projectName) {
            if (confirm('¿Está seguro de que desea volver a habilitar el proyecto "' + projectName + '" (Código: ' + projectId + ')?\n\nAl re-habilitar, volverá a habilitarse la edición en DGSO.')) {
                var form = document.getElementById('individual-rehabilitar-form');
                form.action = '{{ url("admin/projects") }}/' + projectId + '/rehabilitar-dgso';
                form.submit();
            }
        }

        function submitBulkForm() {
            var checkboxes = document.querySelectorAll('.project-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Por favor, marque las casillas de verificación de los proyectos que desea re-habilitar.');
                return;
            }

            if (confirm('¿Está seguro de que desea re-habilitar los ' + checkboxes.length + ' proyecto(s) seleccionados?')) {
                document.getElementById('bulk-rehabilitar-form').submit();
            }
        }
    </script>
@endsection
