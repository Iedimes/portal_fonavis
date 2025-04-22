@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.projects.actions.show'))

@section('body')

<div class="card">
    <div class="card-header text-center">
        DATOS {{utf8_encode($project->name)}}
    </div>
    <div class="card-body">
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
            <address>
            <strong>Lider:</strong> {{($project->leader_name)}}<br>
            <strong>Departamento: </strong>{{utf8_encode($project->state_id?$project->getState->DptoNom:"")}}<br>
            <strong>Modalidad:</strong> {{utf8_encode($project->modalidad_id?$project->getModality->name:"")}}<br>
            <strong>Estado:</strong> <span class="badge bg-success " style="font-size:1.1em; color:white">  {{ $project->getEstado ? $project->getEstado->getStage->name : "Pendiente"}}</span><br>
            </address>
            </div>

            <div class="col-sm-4 invoice-col">
            <address>
            <strong>Telefono:</strong> {{utf8_encode($project->phone)}}<br>
            <strong>Distrito:</strong> {{utf8_encode($project->getCity->CiuNom)}}<br>
            <strong>Tipo de Terreno:</strong> {{utf8_encode($project->land_id?$project->getLand->name:"")}}<br>
            <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
            </address>
            </div>

            <div class="col-sm-4 invoice-col">
            <address>
            <strong>SAT:</strong> {{utf8_encode($project->sat_id?$project->getSat->NucNomSat:"")}}<br>
            <strong>Localidad:</strong> {{$project->localidad}}<br>
            <strong>Tipologia:</strong> {{utf8_encode($project->typology_id?$project->getTypology->name:"")}}<br>
            </address>
            </div>

            </div>

            @if (empty($project->getEstado))
                {{-- <a href="{{ url('admin/projects/'. $project->id .'/transition') }}" type="button"  class="btn btn-primary">CAMBIAR ESTADO</a> --}}
            @else
                @if ( $project->getEstado->stage_id == 1 && Auth::user()->rol_app->dependency_id == 1)

                    @if (collect($uploadedFiles2)->filter()->isEmpty())
                    <div class="alert alert-danger text-center" role="alert" style="font-weight: bold; font-size: 1.5rem;">
                        SAT DEBE PRESENTAR INFORME DE CONDICION DE DOMINIO PARA PODER CAMBIAR AL ESTADO REVISION PRELIMINAR!!!
                    </div>
                    <a href="{{ url('admin/projects/'. $project->id .'/transitionEliminar') }}" type="button"  class="btn btn-primary">VOLVER AL ESTADO PENDIENTE DE ENVIO</a>


                @else
                    <a href="{{ url('admin/projects/'. $project->id .'/transition') }}" type="button"  class="btn btn-primary">CAMBIAR ESTADO</a>
                    {{-- <a href="{{ url('admin/projects/'. $project->id .'/transitionEliminar') }}" type="button"  class="btn btn-primary">VOLVER AL ESTADO PENDIENTE DE ENVIO</a> --}}
                @endif


                    @endif
            @endif
            <a href="{{ url('admin/projects/'. $project->id .'/notificar') }}" class="btn btn-success">NOTIFICAR A SAT</a>

            <a href="{{ url('admin/projects/'.$project->id.'/historial') }}" class="btn btn-warning" style="float: right;">HISTORIAL DEL PROYECTO</a>
    </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 10000);
    </script>
@endif

</div>


<div class="card">
    <div class="card-header text-center">
        DOCUMENTOS PRESENTADOS<a href="{{ url('admin/projects') }}" class="btn btn-primary" style="float: right;">VOLVER</a>
    </div>
    <div class="card-body">
        <div class="card-block">
            <table class="table table-hover table-listing">
                <thead>
                    <tr>
                        {{-- <th>#</th> --}}
                        <th>Documento</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($docproyecto as $key => $item)
                        <tr>
                            {{-- <td>{{ $key+1 }}</td> --}}
                            <td>{{ $item->document->name }}</td>
                            <td>
                                @if ($uploadedFiles[$item->document_id])
                                    <a href="{{ route('downloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles[$item->document_id]]) }}">
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
                                {{-- <td>{{ $key+1 }}</td> --}}
                                <td>{{ $item->document->name }}</td>
                                <td>
                                    <a href="{{ route('downloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles2[$item->document_id]]) }}">
                                        <button class="btn btn-info">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @if($project->land_id == 1)
                    <!-- Mostrar documentos no excluyentes existentes -->
                    <tr>
                        <th colspan="3">Documentos No Excluyentes Cargados</th>
                    </tr>
                    @foreach ($docproyectoNoExcluyentes as $key => $item)
                        @if ($uploadedFiles1[$item->document_id])
                            <tr>
                                {{-- <td>{{ $key+1 }}</td> --}}
                                <td>{{ $item->document->name }}</td>
                                <td>
                                    <a href="{{ route('downloadFileDoc', ['project' => $project->id, 'document_id' => $item->document_id, 'file_name' => $uploadedFiles1[$item->document_id]]) }}">
                                        <button class="btn btn-info">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    <!-- Mostrar documentos no excluyentes faltantes -->
                    <tr>
                        <th colspan="3">Documentos No Excluyentes Faltantes</th>
                    </tr>
                    @foreach ($docproyectoNoExcluyentes as $key => $item)
                        @if (!isset($uploadedFiles1[$item->document_id]) || !$uploadedFiles1[$item->document_id])
                            <tr>
                                {{-- <td>{{ count($docproyecto) + $key + 1 }}</td> --}}
                                <td>{{ $item->document->name }}</td>
                                <td>Documento faltante</td>
                            </tr>
                        @endif
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
