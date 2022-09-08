@extends('adminlte::page')

@section('title', 'FONAVIS')


@section('content')
<br>
<div class="invoice p-3 mb-3">


<div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-university"></i> Proyecto  {{ $project->name }}</h3>
          @if (!isset($project->getEstado->stage_id))
                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-default">
                    <i class="fa fa-plus-circle"></i> Agregar Postulante
                </button>
                @endif
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-4">
              <p>
                <strong>Departamento: </strong>{{$project->state_id?$project->getState->DptoNom:""}}<br>
                <strong>Distrito:</strong> {{$project->city_id}}<br>
                <strong>Modalidad:</strong> {{$project->modalidad_id?$project->getModality->name:""}}<br>
                <a href="{{ url('projects/'.$project->id) }}">
                    <button type="button" class="btn btn-info">
                        <i class="fa fa-undo"></i> Volver al Proyecto
                    </button>
                </a>
              </p>
            </div>
              <div class="col-md-4">
                <p>
                    <strong>SAT:</strong> {{ $project->sat_id?$project->getSat->NucNomSat:""}}<br>
                    <strong>Tipo de Terreno:</strong> {{$project->land_id?$project->getLand->name:""}}<br>
                    <strong>Total Postulantes:</strong> {{ $postulantes->count() }}<br>
                </p>
              </div>
              <div class="col-md-4">
                <p>
                    <strong>Estado: </strong>  <br>
                    @if (isset($project->getEstado->stage_id))
                    <label for="" class="text-green"> {{ $project->getEstado->stage_id?$project->getEstado->getStage->name:"" }}</label>
                    @else
                    <label for="" class="text-yellow">Pendiente</label>
                    @endif

                </p>

              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if (!isset($project->getEstado->stage_id))

  @else
  <a href="{!! action('PostulantesController@generatePDF', ['id'=>$project->id]) !!}"> <button type="button" class="btn btn-info btn-block btn-lg btn-lg">
        <i class="fa fa-file-excel-o"></i> Imprimir Listado
        </button></a>
  @endif
  @if (session('status'))
    <div class="alert alert-warning">
        {{ session('status') }}
    </div>
  @endif
  <br>
  @if (count($postulantes) > 0 )
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
            <h4 class="box-title"> Listado de Postulantes</h4>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table">
            <tbody>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th class="text-center">Cédula</th>
              <th class="text-center">Edad</th>
              <th class="text-center">Ingreso</th>
              <th class="text-center">Nivel</th>
              <th class="text-center">Miembros</th>
              <th class="text-center">Acciones</th>
            </tr>
            @foreach($postulantes as $key=>$post)
            <tr>
              <td>{{$key+1}}</td>
              <td>{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}</td>
                @if (is_numeric($post->postulante_id?$post->getPostulante->cedula:""))
                <td class="text-center">{{ number_format($post->postulante_id?$post->getPostulante->cedula:"",0,".",".")  }} </td>
                @else
                <td class="text-center">{{ $post->postulante_id?$post->getPostulante->cedula:""  }} </td>
                @endif
              <td class="text-center">{{ \Carbon\Carbon::parse( $post->postulante_id?$post->getPostulante->birthdate:"")->age }} </td>
              <td class="text-center">{{ number_format(App\Models\ProjectHasPostulantes::getIngreso($post->postulante_id),0,".",".") }} </td>
              <td class="text-center">{{ App\Models\ProjectHasPostulantes::getNivel($post->postulante_id) }}</td>
              <td class="text-center">{{ $post->getMembers->count() + 1 }}</td>
              <td class="text-center" style="width: 150px;">
                    <div class="btn-group">
                            <button type="button" class="btn btn-info">Acciones</button>
                            <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown">
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                              {{--<li><a href="{!! url('PostulantesController@show', ['id'=>$project->id,'idpostulantes'=>$post->postulante_id?$post->getPostulante->id:""]) !!}">Ver</a></li>--}}
                              @if (!isset($project->getEstado->stage_id))
                            <!--  <li><a href="{!! url('PostulantesController@edit', ['id'=>$project->id,'idpostulantes'=>$post->postulante_id?$post->getPostulante->id:""]) !!}">Editar</a></li> -->
                            <a class="dropdown-item feed-id"data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar</a>
                              @endif
                            </div>
                    </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
        </div>
      </div>
    </div>
  </div>
  @else

  @endif



<!-- Modal Cedula -->
</div>
<div class="modal fade" id="modal-default" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Ingrese Número de Cédula</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
            <form action="{{ url('projects/'.$project->id.'/postulantes/create') }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                    <input type="text" class="form-control" name="cedula"  value="">
                    {!! $errors->first('state_id','<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
            </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <div class="modal modal-danger fade" id="modal-danger">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><i class="fa  fa-warning"></i> Eliminar Postulante</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>

        </div>
        <div class="modal-body">
            <form action="{{ url('postulantes/destroy') }}" method="post">
                    {{ csrf_field() }}
            <p id="demo"></p>
            <input id="delete_id" name="delete_id" type="hidden" value="" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>

          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>
    </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>


@stop

@section('js')
    <script type="text/javascript">
    $(document).ready(function ()
    {
        $('body').on('click', '.feed-id',function(){
        document.getElementById("delete_id").value = $(this).attr('data-id');
        document.getElementById("demo").innerHTML = 'Esta seguro de eliminar el Postulante: <strong>"'+$(this).attr('data-title')+'" </strong><br> Esta acción no se puede deshacer!!!';
        console.log($(this).attr('data-id'));
        console.log($(this).attr('data-title'));
        });

        $('body').on('click', '.feed-id-proyecto',function(){
        document.getElementById("send_id").value = $(this).attr('data-id');
        document.getElementById("demoproy").innerHTML = 'Esta seguro de enviar el proyecto: "'+$(this).attr('data-title')+'" <br> Esta acción no se puede deshacer!!!';
        console.log($(this).attr('data-id'));
        console.log($(this).attr('data-title'));
        });

    });
    </script>
@endsection
