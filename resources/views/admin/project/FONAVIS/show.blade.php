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
            <strong>Distrito:</strong> {{utf8_encode($project->city_id)}}<br>
            <strong>Tipo de Terreno:</strong> {{utf8_encode($project->land_id?$project->getLand->name:"")}}<br>
            <strong>Cantidad de Viviendas:</strong> {{ $postulantes->count() }}<br>
            </address>
            </div>

            <div class="col-sm-4 invoice-col">
            <address>
            <strong>SAT:</strong> {{utf8_encode($project->sat_id?$project->getSat->NucNomSat:"")}}<br>
            <strong>Localidad:</strong> {{utf8_encode($project->localidad)}}<br>
            <strong>Tipologia:</strong> {{utf8_encode($project->typology_id?$project->getTypology->name:"")}}<br>
            </address>
            </div>

            </div>

            @if (empty($project->getEstado))

            @else

                    @if ( $project->getEstado->stage_id == 3 || $project->getEstado->stage_id == 13 && Auth::user()->rol_app->dependency_id == 1)
                        <a href="{{ url('admin/projects/'. $project->id .'/transition') }}" type="button"  class="btn btn-primary">CAMBIAR ESTADO</a>
                    @endif

            @endif
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


@if ($project->getEstado->stage_id == 7 && Auth::user()->rol_app->dependency_id == 1)
    <div class="card">
        <div class="card-header text-center">
            SAT DEBE PRESENTAR CARPETA SOCIAL
        </div>
    </div>

@elseif ($project->getEstado->stage_id == 13 && Auth::user()->rol_app->dependency_id == 1)

    <!-- Código adicional cuando la condición no se cumple -->
    <div class="card">
        <div class="card-header text-center">
            INFORME DIGH
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table table-hover table-listing">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            {{-- <th>Opciones</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $imagenCount = 0; // Variable de conteo inicializada a 0
                        @endphp
                        @foreach ($proyectoEstado as $key => $item)
                            @foreach ($item->imagen as $imagen)
                                <tr>
                                    <td>{{ ++$imagenCount }}</td>
                                    <td>DOCUMENTO ADJUNTO</td>
                                    {{-- <td>{{ $imagen->file_name }}</td> --}}
                                    <td>
                                        <div>
                                            <p class="card-text">
                                                <strong>VER DOCUMENTO ADJUNTO:</strong>
                                                <a class="btn btn-sm btn-danger" href="/media/{{$imagen->id}}/{{$imagen->file_name}}" target="_blank" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                                                    <i class="fa fa-file-pdf-o"></i> PDF
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif ($project->getEstado->stage_id == 18 && Auth::user()->rol_app->dependency_id == 1)
    <!-- Código adicional cuando la condición no se cumple -->
    <div class="card">
        <div class="card-header text-center">
            RESOLUCION DE ADJUDICACION
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table table-hover table-listing">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            {{-- <th>Opciones</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $imagenCount = 0; // Variable de conteo inicializada a 0
                        @endphp
                        @foreach ($proyectoEstado as $key => $item)
                            @foreach ($item->imagen as $imagen)
                                <tr>
                                    <td>{{ ++$imagenCount }}</td>
                                    <td>DOCUMENTO ADJUNTO</td>
                                    {{-- <td>{{ $imagen->file_name }}</td> --}}
                                    <td>
                                        <div>
                                            <p class="card-text">
                                                <strong>VER DOCUMENTO ADJUNTO:</strong>
                                                <a class="btn btn-sm btn-danger" href="/media/{{$imagen->id}}/{{$imagen->file_name}}" target="_blank" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                                                    <i class="fa fa-file-pdf-o"></i> PDF
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <!-- Código adicional cuando la condición no se cumple -->
    <div class="card">
        <div class="card-header text-center">
            INFORME DGJN
        </div>
        <div class="card-body">
            <div class="card-block">
                <table class="table table-hover table-listing">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            {{-- <th>Opciones</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $imagenCount = 0; // Variable de conteo inicializada a 0
                        @endphp
                        @foreach ($proyectoEstado as $key => $item)
                            @foreach ($item->imagen as $imagen)
                                <tr>
                                    <td>{{ ++$imagenCount }}</td>
                                    <td>{{ $imagen->file_name }}</td>

                                    <td>
                                        <div>
                                            <p class="card-text">
                                                <strong>VER </strong>
                                                <a class="btn btn-sm btn-danger" href="/media/{{$imagen->id}}/{{$imagen->file_name}}" target="_blank" title="{{ trans('brackets/admin-ui::admin.btn.show') }}" role="button">
                                                    <i class="fa fa-file-pdf-o"></i> PDF
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif


@endsection
