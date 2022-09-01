@if (!isset($project->getEstado->stage_id))
<!--<button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal-miembro">
    <i class="fa fa-plus-circle"></i> Nuevo Miembro
</button> -->
@endif

<br>
<br>
<div class="row">
    <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
            <tbody>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th class="text-center">Cédula</th>
              <th class="text-center">Edad</th>
              <th>Parentesco</th>
              <th class="text-center">Ingreso</th>
              <th class="text-center">Acciones</th>
            </tr>
            @foreach($miembros as $key=>$mi)
            <tr>
              <td>{{$key+1}}</td>
              <td>{{ $mi->miembro_id?$mi->getPostulante->first_name:"" }} {{ $mi->miembro_id?$mi->getPostulante->last_name:"" }}</td>
              <td class="text-center">{{ number_format($mi->miembro_id?$mi->getPostulante->cedula:"",0,".",".") }} </td>
              <td class="text-center">{{ \Carbon\Carbon::parse( $mi->postulante_id?$mi->getPostulante->birthdate:"")->age }} </td>
              <td>{{ $mi->miembro_id?$mi->getParentesco->name:"" }}</td>
              <td class="text-center">{{ number_format($mi->miembro_id?$mi->getPostulante->ingreso:"",0,".",".") }} </td>
              <td class="text-center" style="width: 150px;">
                    <div class="btn-group">
                            <button type="button" class="btn btn-info">Acciones</button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                @if (!isset($project->getEstado->stage_id))
                                <li><a href="{!! action('PostulantesController@editmiembro', ['id'=>$project->id,'idpostulantes'=>$mi->postulante_id?$mi->getPostulante->id:""]) !!}">Editar</a></li>
                                <li><a class="feed-idmiembro"data-toggle="modal" data-id="{{ $mi->miembro_id }}" data-target="#modal-danger" data-title="{{ $mi->miembro_id?$mi->getPostulante->first_name:"" }} {{ $mi->miembro_id?$mi->getPostulante->last_name:"" }}" href="">Eliminar</a></li>
                                @endif
                            </ul>
                          </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <br>
        <br>
    </div>
  </div>

<div class="modal fade" id="modal-miembro" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Ingrese Número de Cédula</h4>
        </div>
        <div class="modal-body">
            <form action="{{ action('PostulantesController@createmiembro', ['id' => $project->id ]) }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">
                    <input type="text" name="postulante_id" value="{{$postulante->id}}" hidden>
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
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><i class="fa  fa-warning"></i> Eliminar Miembro</h4>
        </div>
        <div class="modal-body">
            <form action="{{ action('PostulantesController@destroymiembro') }}" method="post">
                    {{ csrf_field() }}
            <p id="demo"></p>
            <input id="delete_idmiembro" name="delete_idmiembro" type="hidden" value="" />
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
  </div>
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
      document.getElementById("demo").innerHTML = 'Esta seguro de eliminar el Miembro: "'+$(this).attr('data-title')+'" <br> Esta acción no se puede deshacer!!!';
      console.log($(this).attr('data-id'));
      console.log($(this).attr('data-title'));
      });

  });
  </script>
@endsection
