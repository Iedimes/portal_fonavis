@extends('adminlte::page')

@section('title', 'FONAVIS')


@section('content')
<br>
<div class="invoice p-3 mb-3">


    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-university"></i> Proyecto  {{ $project->name }}
                        @php
                            $hasMultipleMembers = false;
                        @endphp
                        @foreach($postulantes as $key => $post)
                            @php
                                $memberCount = $post->getMembers->count() + 1;
                                if ($memberCount > 2) {
                                    $hasMultipleMembers = true;
                                    break; // Romper el bucle si se encuentra un postulante con más de 1 miembro
                                }
                            @endphp
                        @endforeach
                        @if ($hasMultipleMembers && ($project->getEstado && $project->getEstado->stage_id == 7))
                        <button id="enviarGrupoFamiliarBtn" class="btn btn-success float-right">
                            <i class="fa fa-plus-circle"></i> Enviar Grupo Familiar
                        </button>
                        @endif


                        @if ($project->getEstado && $project->getEstado->stage_id == 8)
                        <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
                            <i class="fas fa-download"></i> IMPRIMIR PDF
                        </a>
                    @endif

                   @if (!isset($project->getEstado->stage_id) ||
                        ($project->getEstado->stage_id == 7 && !(($project->modalidad_id == 2 && $project->land_id == 8) ||
                        ($project->modalidad_id == 3 && $project->land_id == 11))) ||
                        $project->getEstado->stage_id == 22)
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
                <strong>Distrito:</strong> {{$project->getcity->CiuNom}}<br>
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

  <style>
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      width: 300px;
      background-color: #28a745;
      border: 1px solid #ccc;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
    }

    .toast.show {
      opacity: 1;
    }

    .toast-body {
      padding: 10px;
      color: #fff; /* Cambiar a color blanco */
    }

    .toast.success {
      background-color: #dff0d8;
      border-color: #d0e9c6;
      color: #3c763d;
    }

    .toast.error {
      background-color: #f2dede;
      border-color: #ebccd1;
      color: #a94442;
    }
  </style>

  <div id="messageContainer" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body"></div>
  </div>



  @if (!isset($project->getEstado->stage_id))

  @else
  {{-- <a href="{!! action('PostulantesController@generatePDF', ['id'=>$project->id]) !!}"> <button type="button" class="btn btn-info btn-block btn-lg btn-lg">
        <i class="fa fa-file-excel-o"></i> Imprimir Listado
        </button></a> --}}

        <a href="{{ url('imprimir', ['id' => $project->id]) }}">
            <button type="button" class="btn btn-info btn-block btn-lg">
                <i class="fa fa-file-excel-o"></i> Imprimir Listado
            </button>
        </a>
  @endif



  <div id="messageContainer" style="font-size: 20px; color: #333; text-align: center;"></div>

  {{-- Mensaje de status (advertencia) --}}
  @if (session('status'))
    <div class="alert alert-warning" id="status-message" style="display: block;">
        <i class="fa fa-exclamation-triangle"></i> {{ session('status') }}
    </div>
  @endif

  {{-- Mensaje de error (para validación de edad) --}}
  @if (session('error'))
    <div class="alert alert-danger" id="error-message" style="display: block;">
        <i class="fa fa-times-circle"></i> {{ session('error') }}
    </div>
  @endif

  {{-- Mensaje de éxito --}}
  @if (session('success'))
    <div class="alert alert-success" id="success-message" style="display: block;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
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
              <td class="text-center">{{ number_format($ingresos[$post->postulante_id] ?? 0,0,".",".") }}</td>
              <td class="text-center">{{ $niveles[$post->postulante_id] ?? '' }}</td>
              <td class="text-center">{{ $post->getMembers->count() + 1 }}</td>
              <td class="text-center" style="width: 150px;">
                    <div class="btn-group">
                            <button type="button" class="btn btn-info">Acciones</button>
                            <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown">
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                              {{--<li><a href="{!! url('PostulantesController@show', ['id'=>$project->id,'idpostulantes'=>$post->postulante_id?$post->getPostulante->id:""]) !!}">Ver</a></li>--}}
                              {{-- @if (!isset($project->getEstado->stage_id)) --}}
                               {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Miembro</a> --}}
                               {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Ver Miembros</a>
                               {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a> --}}
                               {{-- <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a> --}}
                               {{-- <a class="dropdown-item feed-id"data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar</a> --}}
                              {{-- @endif --}}

                                @if (!isset($project->getEstado) || !isset($project->getEstado->stage_id))
                                {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Miembro</a> //en la primera etapa cuando el estado esta vacio no deben aparecer las opciones
                                <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a> --}}
                                @if (($post->getMembers->count() + 1) ==1)
                                 <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Conyuge</a>
                                @elseif (($post->getMembers->count() + 1) > 1)
                                 <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a>
                                @endif
                                <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('postulantes.edit', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Editar Postulante</a>

                                <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar Postulante</a>
                                @elseif($project->getEstado->stage_id == 7)
                                    <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Miembro</a>
                                    <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a>
                                    {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar</a> --}}
                                    <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar Postulante</a>
                                @endif

                                @if (isset($project->getEstado) && $project->getEstado->stage_id == 22)

                                    {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Conyuge</a> --}}

                                    {{-- <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a> --}}

                                    <a class="dropdown-item feed-id" data-toggle="modal" data-target="#modal-default1" data-postulante-id="{{ $post->postulante_id }}" href="#">Agregar Miembro</a>

                                    <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('postulantes.edit', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Editar Postulante</a>

                                    <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar Postulante</a>


                                    {{-- <a class="dropdown-item feed-id" data-postulante-id="{{ $post->postulante_id }}" href="{{ route('projects.postulantes.show', ['id' => $project->id, 'idpostulante' => $post->postulante_id]) }}">Ver Miembros</a> --}}
                                    {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar</a> --}}
                                    {{-- <a class="dropdown-item feed-id" data-toggle="modal" data-id="{{ $post->postulante_id }}" data-target="#modal-danger" data-title="{{ $post->postulante_id?$post->getPostulante->first_name:"" }} {{ $post->postulante_id?$post->getPostulante->last_name:"" }}" href="#">Eliminar Postulante</a> --}}
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

  <div class="modal fade" id="modal-default1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ingrese Número de Cédula Miembro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="miembro-form" action="{{ isset($post) ? url('projects/'.$project->id.'/postulantes/'.$post->postulante_id.'/createmiembro') : '#' }}" method="GET">
                    {{ csrf_field() }}
                    <input type="hidden" name="postulante_id" id="postulante_id" value="{{ isset($post) ? $post->postulante_id : '' }}">
                    <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                        <input type="text" class="form-control" name="cedula" value="">
                        {!! $errors->first('state_id','<span class="help-block">:message</span>') !!}
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
            </form>
        </div>
    </div>
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

  <div id="loader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000;">
      <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Enviando...</span>
      </div>
  </div>


@stop



@section('js')
<script type="text/javascript">
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    $(document).ready(function () {

        // Botón eliminar postulante
        $('body').on('click', '.feed-id', function () {
            $('#delete_id').val($(this).data('id'));
            $('#demo').html(`Esta seguro de eliminar el Postulante: <strong>"${$(this).data('title')}"</strong><br> Esta acción no se puede deshacer!!!`);
            console.log($(this).data('id'));
            console.log($(this).data('title'));
        });

        // Botón enviar proyecto
        $('body').on('click', '.feed-id-proyecto', function () {
            $('#send_id').val($(this).data('id'));
            $('#demoproy').html(`Esta seguro de enviar el proyecto: "${$(this).data('title')}" <br> Esta acción no se puede deshacer!!!`);
            console.log($(this).data('id'));
            console.log($(this).data('title'));
        });

        // Ocultar mensajes después de 30s
        setTimeout(() => $('#status-message').hide(), 30000);
        setTimeout(() => $('#error-message').hide(), 30000);
        setTimeout(() => $('#success-message').hide(), 30000);

        // Acción enviar grupo familiar
        $('#enviarGrupoFamiliarBtn').on('click', function () {
            let projectId = {{ $project->id }};
            $('#loader').show(); // ⏳ Mostrar spinner

            $.ajax({
                url: '/projectsMiembros/' + projectId,
                type: 'GET',
                success: function (response) {
                    $('#loader').hide(); // ✅ Ocultar spinner
                    showToast(response.message, 'success');
                    setTimeout(() => location.reload(), 5000);
                },
                error: function (xhr) {
                    $('#loader').hide(); // ❌ Ocultar spinner
                    let errorMessage = xhr.responseJSON?.message || 'Ocurrió un error inesperado.';
                    showToast(errorMessage, 'error');
                    setTimeout(() => location.reload(), 5000);
                }
            });
        });


        // Toast con Bootstrap 5
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = 1055;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
            toast.classList.add(bgColor);

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Acción para asignar formulario de miembro
        $('.feed-id').click(function () {
            const postulante_id = $(this).data('postulante-id');
            $('#postulante_id').val(postulante_id);
            $('#miembro-form').attr('action', '{{ url("projects/" . $project->id . "/postulantes") }}/' + postulante_id + '/createmiembro');
        });

    });
</script>
@endsection
