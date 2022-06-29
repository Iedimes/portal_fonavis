@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content_header')
<h1>{{ $title }}</h1>
<ol class="breadcrumb">
    <li class="active"><a href="{{url('/')}}"><i class="fa fa-home"></i>Inicio</a></li>
  </ol>
@stop

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">

    <div class="pull-right"><a href="{{ url('projects/create') }}" class="announce">
            <button class="btn btn-primary" hr type="button"><i class="fa fa-fw fa-plus"></i> Crear Proyecto</button>
        </a></div>
      </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
      <table class="table table-striped">
        <tbody>
        <tr>
          <th>Proyecto</th>
          <th>Empresa/Sat</th>
          <th>Terreno</th>
          <th>Departamento</th>
          <th>Distrito</th>
          <th>Modalidad</th>
          <th>Estado</th>
          <th style="text-align:center">Acciones</th>
        </tr>
        @foreach($projects as $project)
        <tr>
        <td>{{$project->name}}</td>
        <td>{{$project->sat_id?$project->getSat->NucNomSat:""}}</td>
        <td>{{utf8_encode($project->land_id?$project->getLand->name:"")}}</td>
        <td>{{utf8_encode($project->state_id?$project->getState->DptoNom:"")}}</td>
        <td>{{utf8_encode($project->city_id)}}</td>
        <td>{{utf8_encode($project->modalidad_id?$project->getModality->name:"")}}</td>
        <td>
                @if (isset($project->getEstado->stage_id))
                <label for="" class="text-green"> {{ $project->getEstado->stage_id?$project->getEstado->getStage->name:"" }}</label>
                @else
                <label for="" class="text-yellow">Pendiente</label>
                @endif
        </td>
        <td style="text-align:center; width: 150px;">
                <div class="btn-group">
                        <button type="button" class="btn btn-info">Acciones</button>
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="{{ url('projects/'.$project->id) }}">Ver</a></li>
                          @if (!isset($project->getEstado->stage_id))
                         <!-- <li><a href="">Editar</a></li> -->
                          @endif
                          <li><a href="">Postulantes</a></li>
                        </ul>
                      </div>
        </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
            <th>Proyecto</th>
            <th>Empresa/Sat</th>
            <th>Terreno</th>
            <th>Departamento</th>
            <th>Distrito</th>
            <th>Modalidad</th>
            <th>Estado</th>
            <th style="text-align:center">Acciones</th>
        </tr>
    </tfoot>
</table>
    </div>

  </div>

@stop
