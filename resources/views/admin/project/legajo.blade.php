@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Legajo del Proyecto')

@section('body')

    <div class="card">
        <div class="card-header text-center">
            DATOS {{ $project->name }}
            <a href="{{ route('adminprojectslegajo.descargar', ['project' => $project->id]) }}" class="btn btn-success"
                style="float: right;">
                <i class="fa fa-download"></i> Descargar Legajo Completo (ZIP)
            </a>
        </div>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card-body">
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <address>
                        <strong>Lider:</strong> {{ $project->leader_name }}<br>
                        <strong>Departamento:
                        </strong>{{ utf8_encode($project->state_id ? $project->getState->DptoNom : '') }}<br>
                        <strong>Modalidad:</strong>
                        {{ utf8_encode($project->modalidad_id ? $project->getModality->name : '') }}<br>
                        <strong>Estado:</strong> <span class="badge bg-success " style="font-size:1.1em; color:white">
                            {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}</span><br>
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <address>
                        <strong>Telefono:</strong> {{ $project->phone }}<br>
                        <strong>Distrito:</strong> {{ $project->getCity->CiuNom }}<br>
                        <strong>Tipo de Terreno:</strong>
                        {{ utf8_encode($project->land_id ? $project->getLand->name : '') }}<br>
                        <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <address>
                        <strong>SAT:</strong> {{ utf8_encode($project->sat_id ? $project->getSat->NucNomSat : '') }}<br>
                        <strong>Localidad:</strong> {{ $project->localidad }}<br>
                        <strong>Tipologia:</strong>
                        {{ utf8_encode($project->typology_id ? $project->getTypology->name : '') }}<br>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    DOCUMENTOS PRESENTADOS
                </div>
                <div class="card-body">
                    <table class="table table-hover table-listing">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedAssignments as $categoryName => $assignments)
                                @php
                                    // Verificar si hay archivos subidos para esta categoría
                                    $hasUploadedFiles = $assignments->contains(function ($assignment) use (
                                        $uploadedDocs,
                                    ) {
                                        return $uploadedDocs->has($assignment->document_id) &&
                                            $uploadedDocs[$assignment->document_id]->file_path;
                                    });
                                @endphp

                                @if ($hasUploadedFiles)
                                    {{-- Encabezado de categoría eliminado por solicitud del usuario --}}
                                    {{-- <tr><th colspan="3">{{ strtoupper($categoryName) }}</th></tr> --}}

                                    @foreach ($assignments as $assignment)
                                        @php
                                            $docId = $assignment->document_id;
                                            $uploadedDoc = $uploadedDocs->get($docId);
                                        @endphp
                                        @if ($uploadedDoc && $uploadedDoc->file_path)
                                            <tr>
                                                <td>
                                                    <i class="fa fa-file-text-o mr-2" aria-hidden="true"
                                                        style="margin-right: 5px;"></i>
                                                    {{ $assignment->document->name }}
                                                </td>
                                                <td>
                                                    <div class="btn-group-custom">
                                                        <a class="btn btn-sm btn-info"
                                                            href="{{ route('adminprojectsviewFileDoc', ['project' => $project->id, 'document_id' => $docId, 'file_name' => $uploadedDoc->file_path]) }}"
                                                            target="_blank" title="Ver">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a class="btn btn-sm btn-success"
                                                            href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $docId, 'file_name' => $uploadedDoc->file_path]) }}"
                                                            title="Descargar">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    DICTÁMENES Y RESOLUCIONES
                </div>
                <div class="card-body">
                    <table class="table table-hover table-listing">
                        <thead>
                            <tr>
                                <th>Etapa / Estado</th>
                                <th>Documento</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proyectoEstado as $status)
                                @foreach ($status->imagen as $imagen)
                                    <tr>
                                        <td>
                                            <i class="fa fa-file-pdf-o mr-2" aria-hidden="true"
                                                style="margin-right: 5px;"></i>
                                            {{ $status->getStage ? $status->getStage->name : 'Estado ' . $status->stage_id }}
                                        </td>
                                        <td>{{ $imagen->file_name }}</td>
                                        <td>
                                            <div class="btn-group-custom">
                                                <a class="btn btn-sm btn-info"
                                                    href="/media/{{ $imagen->id }}/{{ $imagen->file_name }}"
                                                    target="_blank" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-success"
                                                    href="/media/{{ $imagen->id }}/{{ $imagen->file_name }}" download
                                                    title="Descargar">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @if ($proyectoEstado->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">No hay dictámenes o resoluciones disponibles para
                                        este proyecto.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
