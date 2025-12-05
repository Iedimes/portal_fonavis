@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Legajo del Proyecto')

@section('body')

    <div class="card" style="border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.1)">
        <div class="card-header text-center"
            style="font-weight:600; font-size:1.2rem; background:#f8f9fa; border-bottom:1px solid #e1e1e1; position:relative">

            DATOS {{ $project->name }}

            <a href="{{ route('adminprojectslegajo.descargar', ['project' => $project->id]) }}" class="btn btn-success"
                style="position:absolute; right:15px; top:7px; border-radius:6px">
                <i class="fa fa-download"></i> Descargar Legajo Completo (ZIP)
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger text-center" style="margin: 0; border-radius:0px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="card-body" style="padding:25px;">
            <div class="row invoice-info">

                {{-- COLUMNA 1 --}}
                <div class="col-sm-4">
                    <address style="line-height:1.8; font-size:0.95rem;">
                        <strong>Líder:</strong> {{ $project->leader_name }}<br>
                        <strong>Departamento:</strong>
                        {{ utf8_encode($project->state_id ? $project->getState->DptoNom : '') }}<br>
                        <strong>Modalidad:</strong>
                        {{ utf8_encode($project->modalidad_id ? $project->getModality->name : '') }}<br>
                        <strong>Estado:</strong>
                        <span
                            style="background:#28a745; color:white; padding:4px 10px; border-radius:6px; font-size:0.95em;">
                            {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}
                        </span>
                    </address>
                </div>

                {{-- COLUMNA 2 --}}
                <div class="col-sm-4">
                    <address style="line-height:1.8; font-size:0.95rem;">
                        <strong>Teléfono:</strong> {{ $project->phone }}<br>
                        <strong>Distrito:</strong> {{ $project->getCity->CiuNom }}<br>
                        <strong>Tipo de Terreno:</strong>
                        {{ utf8_encode($project->land_id ? $project->getLand->name : '') }}<br>
                        <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}
                    </address>
                </div>

                {{-- COLUMNA 3 --}}
                <div class="col-sm-4">
                    <address style="line-height:1.8; font-size:0.95rem;">
                        <strong>SAT:</strong> {{ utf8_encode($project->sat_id ? $project->getSat->NucNomSat : '') }}<br>
                        <strong>Localidad:</strong> {{ $project->localidad }}<br>
                        <strong>Tipología:</strong>
                        {{ utf8_encode($project->typology_id ? $project->getTypology->name : '') }}
                    </address>
                </div>

            </div>
        </div>
    </div>


    {{-- DOCUMENTOS PRESENTADOS --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card" style="border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.08)">
                <div class="card-header text-center" style="font-weight:600; font-size:1.15rem; background:#f8f9fa;">
                    DOCUMENTOS PRESENTADOS
                </div>

                <div class="card-body" style="padding:20px;">
                    <table class="table table-hover">
                        <thead style="background:#f1f1f1;">
                            <tr>
                                <th style="width:80%">Documento</th>
                                <th style="width:130px; text-align:center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($groupedAssignments as $categoryName => $assignments)
                                @php
                                    $hasUploadedFiles = $assignments->contains(function ($assignment) use (
                                        $uploadedDocs,
                                    ) {
                                        return $uploadedDocs->has($assignment->document_id) &&
                                            $uploadedDocs[$assignment->document_id]->file_path;
                                    });
                                @endphp

                                @if ($hasUploadedFiles)
                                    @foreach ($assignments as $assignment)
                                        @php
                                            $docId = $assignment->document_id;
                                            $uploadedDoc = $uploadedDocs->get($docId);
                                        @endphp

                                        @if ($uploadedDoc && $uploadedDoc->file_path)
                                            <tr>
                                                <td style="vertical-align:middle;">
                                                    <i class="fa fa-file-text-o" style="margin-right:6px;"></i>
                                                    {{ $assignment->document->name }}
                                                </td>

                                                {{-- ACCIONES ALINEADAS --}}
                                                <td
                                                    style="vertical-align:middle; width:130px; text-align:center; white-space:nowrap;">
                                                    <div style="display:flex; gap:5px; justify-content:center;">
                                                        <a class="btn btn-sm btn-info" style="border-radius:6px;"
                                                            href="{{ route('adminprojectsviewFileDoc', ['project' => $project->id, 'document_id' => $docId, 'file_name' => $uploadedDoc->file_path]) }}"
                                                            target="_blank">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <a class="btn btn-sm btn-success" style="border-radius:6px;"
                                                            href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $docId, 'file_name' => $uploadedDoc->file_path]) }}">
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


    {{-- DICTÁMENES Y RESOLUCIONES --}}
    <div class="row mt-4">
        <div class="col-md-12">

            <div class="card" style="border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.08)">
                <div class="card-header text-center" style="font-weight:600; font-size:1.15rem; background:#f8f9fa;">
                    DICTÁMENES Y RESOLUCIONES
                </div>

                <div class="card-body" style="padding:20px;">
                    <table class="table table-hover">
                        <thead style="background:#f1f1f1;">
                            <tr>
                                <th style="width:40%">Etapa / Estado</th>
                                <th style="width:40%">Documento</th>
                                <th style="width:130px; text-align:center;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($proyectoEstado as $status)
                                @foreach ($status->imagen as $imagen)
                                    <tr>
                                        <td style="vertical-align:middle;">
                                            <i class="fa fa-file-pdf-o" style="margin-right:6px;"></i>
                                            {{ $status->getStage ? $status->getStage->name : 'Estado ' . $status->stage_id }}
                                        </td>

                                        <td style="vertical-align:middle;">
                                            {{ $imagen->file_name }}
                                        </td>

                                        {{-- ACCIONES ALINEADAS --}}
                                        <td
                                            style="vertical-align:middle; width:130px; text-align:center; white-space:nowrap;">
                                            <div style="display:flex; gap:5px; justify-content:center;">
                                                <a class="btn btn-sm btn-info" style="border-radius:6px;"
                                                    href="/media/{{ $imagen->id }}/{{ $imagen->file_name }}"
                                                    target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                <a class="btn btn-sm btn-success" style="border-radius:6px;"
                                                    href="/media/{{ $imagen->id }}/{{ $imagen->file_name }}" download>
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @if ($proyectoEstado->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center" style="padding:25px;">
                                        No hay dictámenes o resoluciones disponibles para este proyecto.
                                    </td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection
