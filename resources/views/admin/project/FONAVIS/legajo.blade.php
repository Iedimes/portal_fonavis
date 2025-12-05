@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Legajo del Proyecto')

@section('body')

    <div class="card">
        <div class="card-header text-center">
            DATOS {{ utf8_encode($project->name) }}
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
                        <strong>Telefono:</strong> {{ utf8_encode($project->phone) }}<br>
                        <strong>Distrito:</strong> {{ utf8_encode($project->getCity->CiuNom) }}<br>
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
                            @foreach ($docproyecto as $key => $item)
                                <tr>
                                    <td>{{ $item->document->name }}</td>
                                    <td>
                                        @if ($uploadedFiles[$item->document_id])
                                            <a
                                                href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles[$item->document_id]]) }}">
                                                <button class="btn btn-info">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="3">INFORME DE CONDICION DE DOMINIO</th>
                            </tr>
                            @foreach ($docproyectoCondominio as $key => $item)
                                @if ($uploadedFiles2[$item->document_id])
                                    <tr>
                                        <td>{{ $item->document->name }}</td>
                                        <td>
                                            <a
                                                href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles2[$item->document_id]]) }}">
                                                <button class="btn btn-info">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($project->land_id == 2)
                                <tr>
                                    <th colspan="3">NO OBEJECIÓN DEL INDI</th>
                                </tr>
                                @foreach ($docproyectoIndi as $key => $item)
                                    @if ($uploadedFiles3[$item->document_id])
                                        <tr>
                                            <td>{{ $item->document->name }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles3[$item->document_id]]) }}">
                                                    <button class="btn btn-info">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            @if ($project->land_id == 1)
                                <tr>
                                    <th colspan="3">Documentos No Excluyentes Cargados</th>
                                </tr>
                                @foreach ($docproyectoNoExcluyentes as $key => $item)
                                    @if ($uploadedFiles1[$item->document_id])
                                        <tr>
                                            <td>{{ $item->document->name }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles1[$item->document_id]]) }}">
                                                    <button class="btn btn-info">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif

                            <tr>
                                <th colspan="3">RESOLUCION INDERT (Si aplica)</th>
                            </tr>
                            @foreach ($uploadedFiles4 as $docId => $fileName)
                                @if ($fileName)
                                    <tr>
                                        <td>Resolución INDERT</td>
                                        <td>
                                            <a
                                                href="{{ route('adminprojectsdownloadFileDoc', ['project' => $project->id, 'document_id' => $docId, 'file_name' => $fileName]) }}">
                                                <button class="btn btn-info">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
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
                                        <td>{{ $status->getStage ? $status->getStage->name : 'Estado ' . $status->stage_id }}
                                        </td>
                                        <td>{{ $imagen->file_name }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-danger"
                                                href="/media/{{ $imagen->id }}/{{ $imagen->file_name }}" target="_blank"
                                                title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                                                <i class="fa fa-file-pdf-o"></i> PDF
                                            </a>
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
