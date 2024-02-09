@extends('adminlte::page')

@section('title', 'FONAVIS')

@section('content_header')

<ol class="breadcrumb">
{{-- <li><a href="{{url('projects')}}"><i class="fa fa-home"></i>Inicio</a></li>
<li><a href="{{url('projects/'.$project->id.'/postulantes')}}">Postulantes del Proyecto {{ $project->name }}</a></li>
<li class="active"><a href="#">Resumen Postulante</a></li> --}}
</ol>
{{-- <br> --}}
@stop

@section('content')

<section class="invoice">
    <!-- title row -->
    {{-- <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <i class="fa fa-user"></i> {{ $postulante->first_name }} {{ $postulante->last_name }}
          <small class="pull-right"><strong>Fecha de Nacimiento:</strong>  {{ date('d/m/Y', strtotime($postulante->birthdate)) }}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div> --}}
    <!-- info row -->
    {{-- <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        <address>
        <strong>Cedula: </strong>{{$postulante->cedula}}<br>
        <strong>Estado civil:</strong> {{$postulante->marital_status}}<br>
        <strong>Edad:</strong> {{\Carbon\Carbon::parse($postulante->birthdate)->age}}<br>

        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <address>
          <strong>Nacionalidad:</strong> {{$postulante->nacionalidad}}<br>
          <strong>Sexo:</strong> {{$postulante->gender}}<br>
          <strong>Ingreso:</strong> {{$postulante->ingreso}}<br>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">

      </div>
      <!-- /.col -->
    </div> --}}
    <div class="row">
        <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            {{-- <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Miembros</a></li>
              <li><a href="#tab_2" data-toggle="tab">Documentos</a></li>
            </ul> --}}
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                @include('postulantes.ficha.miembros')
              </div>
              <!-- /.tab-pane -->
              {{-- <div class="tab-pane" id="tab_2">
                {{-- @include('postulantes.ficha.documentos')
              </div> --}}

            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>

      </div>

    <!-- /.row -->
  </section>
  {{-- <div class="modal fade" id="modal-default" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Adjuntar Documento al Proyecto</h4>
        </div>
        <div class="modal-body">
            <form action="{{ action('PostulantesController@upload') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="text" name="postulante_id" value="{{$postulante->id}}" hidden>
                <input type="text" name="project_id" value="{{$project->id}}" hidden>
                <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                    <label for="exampleInputPassword1">Documentos Requeridos</label>
                    <select class="form-control required" name="title" required>
                        <option value="">Seleccione el Documento</option>
                            @foreach($docproyecto as $key=>$doc)
                                <option value="{{$doc->document_id}}"
                                    >{{ utf8_encode($doc->document_id?$doc->document->name:"") }}</option>
                            @endforeach
                    </select>
                    {!! $errors->first('state_id','<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group">
                    <label>Adjuntar Archivo</label>
                    <input type="file" name="image" required>
                </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
            </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div> --}}

<!-- Modal para Borrar Imagen -->

{{-- <div class="modal modal-danger fade" id="modal-danger">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
              <h4 class="modal-title"><i class="fa  fa-warning"></i> Eliminar Documento</h4>
            </div>
            <div class="modal-body">
                <form action="{{ action('PostulantesController@destroyfile') }}" method="post">
                        {{ csrf_field() }}
                <p id="demo"></p>
                <input id="delete_id" name="delete_id" type="hidden" value="" />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-outline">Eliminar</button>
            </div>
        </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div> --}}

      {{-- <div class="modal modal-danger fade" id="modal-danger">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
              <h4 class="modal-title"><i class="fa  fa-warning"></i> Eliminar Postulante</h4>
            </div>
            <div class="modal-body">
                <form action="{{ action('PostulantesController@destroy') }}" method="post">
                        {{ csrf_field() }}
                <p id="demo"></p>
                <input id="id" name="id" type="hidden" value="" />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-outline">Eliminar</button>
            </div>
        </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div> --}}

@stop

@section('js')
    <script type="text/javascript">
    $(document).ready(function ()
    {

        $('body').on('click', '.feed-id',function(){
        document.getElementById("delete_id").value = $(this).attr('data-id');
        document.getElementById("demo").innerHTML = 'Esta seguro de eliminar el documento: "'+$(this).attr('data-title')+'"';
        console.log($(this).attr('data-id'));
        console.log($(this).attr('data-title'));
        });

        $('body').on('click', '.feed-idmiembro',function(){
        document.getElementById("delete_idmiembro").value = $(this).attr('data-id');
        document.getElementById("demo").innerHTML = 'Esta seguro de eliminar el Miembro: "'+$(this).attr('data-title')+'"';
        console.log($(this).attr('data-id'));
        console.log($(this).attr('data-title'));
        });

    });
    </script>
@endsection
