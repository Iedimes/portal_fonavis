@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.projects.actions.show'))

@section('body')

<div class="row">
    <div class="col">
        <div class="card">
            <div id="projectInfo" class="collapse show">
                <div class="card-body">
                    <div class="row invoice-info">
                        <h3 class="box-title">
                            <i class="fa fa-user"></i> {{ $postulante->first_name }} {{ $postulante->last_name }}
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 invoice-col">
                            <strong>Fecha de Nacimiento: </strong>{{ date('d/m/Y', strtotime($postulante->birthdate)) }}<br>
                            <strong>Cedula:</strong> {{$postulante->cedula}}<br>
                            <strong>Estado civil:</strong> {{$postulante->marital_status}}<br>
                            <a href="{{ url('admin/projects/'.$project->id.'/showDGSO') }}">
                                <button type="button" class="btn btn-primary">
                                    <i class="fa fa-undo"></i> Volver al Proyecto
                                </button>
                            </a>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <strong>Edad:</strong> {{\Carbon\Carbon::parse($postulante->birthdate)->age}}<br>
                            <strong>Nacionalidad:</strong> {{$postulante->nacionalidad}}<br>
                            <strong>Sexo:</strong> {{$postulante->gender}}
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <strong>Ingreso:</strong> {{$postulante->ingreso}}<br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="box-title">Listado de Miembros</h4>
            </div>
            <div class="card-body">
                <table class="table">
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
                        @foreach($miembros as $key => $mi)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{ $mi->miembro_id ? $mi->getPostulante->first_name : "" }} {{ $mi->miembro_id ? $mi->getPostulante->last_name : "" }}</td>
                            <td class="text-center">{{ number_format($mi->miembro_id ? $mi->getPostulante->cedula : "", 0, ".", ".") }}</td>
                            <td class="text-center">{{ $mi->miembro_id ? \Carbon\Carbon::parse($mi->getPostulante->birthdate)->age : "" }}</td>
                            <td>{{ $mi->miembro_id ? $mi->getParentesco->name : "" }}</td>
                            <td class="text-center">{{ number_format($mi->miembro_id ? $mi->getPostulante->ingreso : "", 0, ".", ".") }}</td>
                            <td class="text-center" style="width: 150px;">
                                @if($mi->miembro_id)
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-danger"
                                            title="Eliminar miembro"
                                            data-toggle="modal"
                                            data-target="#modal-danger"
                                            data-id="{{ $mi->miembro_id }}"
                                            data-title="{{ $mi->getPostulante->first_name }} {{ $mi->getPostulante->last_name }}">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix"></div>
        </div>
    </div>
</div>

<div class="modal modal-danger fade" id="modal-danger">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-warning"></i> Eliminar Miembro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modal-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                <form action="{{ route('admin/postulantes/destroy-miembro') }}" method="post" class="d-inline" id="delete-form">
                    {{ csrf_field() }}
                    <input id="member_to_delete" name="delete_id" type="hidden" value="" />
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script directamente aquí antes del endsection --}}
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script inline cargado');

    // Usar delegación de eventos para asegurar que funcione
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('[data-target="#modal-danger"]')) {
            console.log('Botón clickeado - evento nativo');

            var button = e.target.closest('[data-target="#modal-danger"]');
            var memberId = button.getAttribute('data-id');
            var memberName = button.getAttribute('data-title');

            console.log('Member ID:', memberId);
            console.log('Member Name:', memberName);

            // Con jQuery si está disponible
            if (typeof $ !== 'undefined') {
                $('#member_to_delete').val(memberId);
                $('#modal-message').html('¿Está seguro de eliminar el Miembro: <strong>"' + memberName + '"</strong>?<br>Esta acción no se puede deshacer!!!');
            } else {
                // Sin jQuery
                document.getElementById('member_to_delete').value = memberId;
                document.getElementById('modal-message').innerHTML = '¿Está seguro de eliminar el Miembro: <strong>"' + memberName + '"</strong>?<br>Esta acción no se puede deshacer!!!';
            }
        }
    });
});
</script>

@endsection

@section('bottom-scripts')
<script type="text/javascript">
$(document).ready(function () {
    console.log('Script cargado correctamente');

    $('[data-target="#modal-danger"]').on('click', function () {
        console.log('Botón clickeado');
        console.log('Elemento:', this);

        var memberId = $(this).attr('data-id');
        var memberName = $(this).attr('data-title');

        console.log('Member ID:', memberId);
        console.log('Member Name:', memberName);

        $('#member_to_delete').val(memberId);
        $('#modal-message').html('¿Está seguro de eliminar el Miembro: <strong>"' + memberName + '"</strong>?<br>Esta acción no se puede deshacer!!!');
    });

    // Limpiar modal al cerrarse
    $('#modal-danger').on('hidden.bs.modal', function () {
        $('#member_to_delete').val('');
        $('#modal-message').html('');
    });
});
</script>
@endsection

{{-- Alternativa: Script directamente en el body --}}
@push('scripts')
<script type="text/javascript">
$(document).ready(function () {
    console.log('Script alternativo cargado');

    $('body').on('click', '[data-target="#modal-danger"]', function () {
        console.log('Botón clickeado - delegación de eventos');

        var memberId = $(this).attr('data-id');
        var memberName = $(this).attr('data-title');

        console.log('Member ID:', memberId);
        console.log('Member Name:', memberName);

        $('#member_to_delete').val(memberId);
        $('#modal-message').html('¿Está seguro de eliminar el Miembro: <strong>"' + memberName + '"</strong>?<br>Esta acción no se puede deshacer!!!');
    });
});
</script>
@endpush
