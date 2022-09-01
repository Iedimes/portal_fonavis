@if (!isset($project->getEstado->stage_id))
<!-- <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-default">
    <i class="fa fa-plus-circle"></i> Nuevo Documento
</button> -->
@endif


<div class="row">
    <div class="col-xs-12 table-responsive">
      <table class="table table-striped">
        <thead>
        <tr>
          <th>Titulo</th>
          <th class="text-center">Acciones</th>
        </tr>
        </thead>
        <tbody>
          @foreach($documentos as $doc)
          <tr>
          <td>{{$doc->title}}</td>
          <td class="text-center" style="width: 220px">
              <a href="{{asset('images/')}}/{{$project->id."/project/general/".$doc->file_path}}" target="_blank">
                  <button class="btn btn-success" type="button"><i class="fa fa-download"></i> Descargar </button>
              </a>
              <button class="btn btn-danger feed-id" type="button" data-toggle="modal"
              data-target="#modal-danger" data-id="{{ $doc->id }}" data-title="{{ $doc->title }}">
                  <i class="fa fa-close"></i> Eliminar
              </button>
          </td>
        </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.col -->
  </div>
