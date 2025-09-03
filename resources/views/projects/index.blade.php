@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content')
<br>
<div class="invoice p-3 mb-3">
    <div class="row">
        <div class="col-12">
            <h4>
            <i class="fas fa-university"></i> Proyectos
            <a href="{{ url('projects/create') }}" class="announce">
                 <button class="btn btn-primary float-right" type="button"><i class="fa fa-fw fa-plus"></i> Crear Proyecto</button>
            </a>
            </h4>
        </div>
    </div>
    <br>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="{{ url('projects') }}">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Buscar proyecto por código o nombre" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <div class="row invoice-info">
        <table class="table table-striped">
            <tbody>
            <tr>
              <th>Codigo Proyecto</th>
              <th>Proyecto</th>
              <th>Empresa/Sat</th>
              <th>Terreno</th>
              <th>Departamento</th>
              <th>Distrito</th>
              <th>Modalidad</th>
              <th>Estado</th>
              <th style="width: 160px;">Acciones</th>
            </tr>
            @foreach($projects as $project)
            <tr>
            <td>{{$project->id}}</td>
            <td>{{$project->name}}</td>
            <td>{{$project->sat_id ? $project->getSat->NucNomSat : ""}}</td>
            <td>{{utf8_encode($project->land_id ? $project->getLand->name : "")}}</td>
            <td>{{utf8_encode($project->state_id ? $project->getState->DptoNom : "")}}</td>
            <td>{{utf8_encode($project->city_id ? $project->getCity->CiuNom : "")}}</td>
            <td>{{utf8_encode($project->modalidad_id ? $project->getModality->name : "")}}</td>
            <td>
                    @if (isset($project->getEstado->stage_id))
                    <label for="" class="text-green"> {{ $project->getEstado->stage_id ? $project->getEstado->getStage->name : "" }}</label>
                    @else
                    <label for="" class="text-yellow">Pendiente</label>
                    @endif
            </td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-info">Acciones</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" role="menu" style="">
                    @if (isset($project->getEstado->stage_id) && $project->getEstado->stage_id == 10)
                            <a class="dropdown-item" href="{{ url('projectsDocTec/'.$project->id) }}">Documentos VTA y ETH</a>
                    @elseif (isset($project->getEstado->stage_id) && $project->getEstado->stage_id == 14)
                            <a class="dropdown-item" href="{{ url('docObservados/'.$project->id) }}">Documentos obs. DIGH</a>
                    @elseif (isset($project->getEstado->stage_id) && $project->getEstado->stage_id == 17)
                            <a class="dropdown-item" href="{{ url('docObservadosDSGO/'.$project->id) }}">Documentos obs. DSGO</a>
                    @else
                    <a class="dropdown-item" href="{{ url('projects/'.$project->id) }}">Ver</a>
                    @endif
                    <a class="dropdown-item {{ $project->getEstado ? 'disabled' : ''}} " href="{{ url('projects/'.$project->id.'/edit') }}">Editar</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item {{ $project->getEstado && $project->getEstado->stage_id == 7 ? '' : 'disabled' }}" href="{{ url('projects/'.$project->id.'/postulantes') }}">Grupo Familiar</a>
                    </div>
                </div>
            </td>
            </tr>
            @endforeach
          </tbody>
    </table>
</div>
</div>

@stop
