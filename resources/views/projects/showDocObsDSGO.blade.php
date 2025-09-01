@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content')
<br>
<div class="invoice p-3 mb-3">

    <!-- TÍTULO DEL PROYECTO -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-university"></i> Proyecto: {{ $project->name }}</h4>

            <div class="btn-group">
                @if ($project->getEstado && $project->getEstado->stage_id == 8)
                    <a href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger">
                        <i class="fas fa-download"></i> Imprimir PDF
                    </a>
                @endif

                @if (!$project->getEstado)
                    <button id="enviarBtn" class="btn btn-success" onclick="allchecked()" {{ $todosCargados ? '' : 'disabled' }}>
                        <i class="fa fa-plus-circle"></i> Enviar al MUVH
                    </button>
                @endif

                @if ($project->getEstado && $project->getEstado->stage_id == 4)
                    <a href="{{ url('projectsDocTec/'.$project->id) }}" class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Enviar Documento solicitado
                    </a>
                @endif

                @if (!$project->getEstado || $project->getEstado->stage_id != 11)
                    <button id="enviarDocumentosBtn" class="btn btn-success" onclick="allchecked()">
                        <i class="fa fa-plus-circle"></i> Enviar Documentos
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN DEL PROYECTO -->
    <div class="row invoice-info">
        <div class="col-sm-4">
            <address>
                <strong>Líder:</strong> {{ $project->leader_name }}<br>
                <strong>Departamento:</strong> {{ $project->state_id ? $project->getState->DptoNom : '' }}<br>
                <strong>Modalidad:</strong> {{ $project->modalidad_id ? $project->getModality->name : '' }}<br>
                <strong>Estado:</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}
            </address>
        </div>
        <div class="col-sm-4">
            <address>
                <strong>Teléfono:</strong> {{ $project->phone }}<br>
                <strong>Distrito:</strong> {{ $project->city_id ? strtoupper($project->getCity->CiuNom) : '' }}<br>
                <strong>Tipo de Terreno:</strong> {{ $project->land_id ? $project->getLand->name : '' }}<br>
                <strong>Cant. Viviendas:</strong> {{ $postulantes->count() }}
            </address>
        </div>
        <div class="col-sm-4">
            <address>
                <strong>SAT:</strong> {{ $project->sat_id ? $project->getSat->NucNomSat : '' }}<br>
                <strong>Localidad:</strong> {{ $project->localidad }}<br>
                <strong>Tipología:</strong> {{ $project->typology_id ? $project->getTypology->name : '' }}
            </address>
        </div>
    </div>

    <!-- DOCUMENTOS -->
    <div class="card card-primary card-tabs mt-4">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="pill" href="#documentos" role="tab">Documentos VTA y ETH</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade active show" id="documentos" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Documento</th>
                                    <th>Observación</th>
                                    <th>Archivo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $todosCargados = true; @endphp

                                @foreach ($docproyecto as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->document->name }}</td>
                                        <td>{{ $observaciones[$item->document_id] ?? 'Sin observación' }}</td>

                                        <td>
                                            @if ($uploadedFiles[$item->document_id])
                                                <span class="badge badge-success">Documento adjuntado</span>
                                                <a href="{{ route('downloadFile', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles[$item->document_id]]) }}" class="btn btn-info btn-sm">
                                                    <i class="fa fa-search"></i> Ver
                                                </a>
                                            @else
                                                <form action="/levantarObsDSGO" method="POST" enctype="multipart/form-data" class="form-inline">
                                                    @csrf
                                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                    <input type="hidden" name="title" value="{{ $item->document->name }}">
                                                    <input type="hidden" name="document_id" value="{{ $item->document->id }}">
                                                    <input type="file" name="archivo" class="form-control form-control-sm mr-2">
                                                    <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                                </form>
                                                @php $todosCargados = false; @endphp
                                            @endif
                                        </td>

                                        <td>
                                            @if ($project->getEstado && in_array($project->getEstado->stage_id, [17]) && $uploadedFiles[$item->document_id])
                                                <form action="{{ route('eliminar', ['project_id' => $project->id, 'document_id' => $item->document->id]) }}" method="GET">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (session('message'))
                        <div class="alert alert-success mt-3" id="success-message">{{ session('message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mt-3" id="error-message">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    function allchecked() {
        let todosCargados = true;
        const rows = document.querySelectorAll('tr');
        rows.forEach(row => {
            const uploadForm = row.querySelector('form[action="/levantarTecnico"]');
            if (uploadForm) todosCargados = false;
        });

        if (todosCargados) {
            var projectId = {{ $project->id }};
            $.ajax({
                url: '/projectsTecnico/' + projectId,
                type: "GET",
                dataType: "json",
                success: async function(data) {
                    if (data.message == 'success') {
                        $(document).Toasts('create', {
                            icon: 'fas fa-exclamation',
                            class: 'bg-success m-1',
                            autohide: true,
                            delay: 5000,
                            title: 'Importante!',
                            body: 'Los documentos han sido enviados'
                        });
                        await delay(3000);
                        location.reload();
                    }
                }
            });
        } else {
            alert('Debe adjuntar y cargar todos los documentos para enviar al MUVH.');
        }
    }

    var successMessage = document.getElementById('success-message');
    var errorMessage = document.getElementById('error-message');
    var tiempoDesaparicion = 5000;

    if (successMessage) {
        successMessage.style.backgroundColor = 'lightgreen';
        successMessage.style.opacity = 1;
        successMessage.style.border = 'none';
    }

    if (errorMessage) {
        errorMessage.style.backgroundColor = 'lightcoral';
        errorMessage.style.opacity = 1;
        errorMessage.style.border = 'none';
    }

    function ocultarMensajes() {
        if (successMessage) successMessage.style.opacity = 0;
        if (errorMessage) errorMessage.style.opacity = 0;
    }
    setTimeout(ocultarMensajes, tiempoDesaparicion);

    function todos() {
        var fileInputs = document.querySelectorAll('input[type="file"]');
        var allFilesUploaded = true;

        fileInputs.forEach(input => {
            if (!input.files.length) allFilesUploaded = false;
        });

        document.querySelector('.btn-success').disabled = !allFilesUploaded;
    }
</script>
@endsection
