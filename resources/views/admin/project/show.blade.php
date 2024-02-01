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
            <strong>Lider:</strong> {{utf8_encode($project->leader_name)}}<br>
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
            <strong>Cantidad de Viviendas:</strong> {{utf8_encode($project->households)}}<br>
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
            <a href="{{ url('admin/projects/'. $project->id .'/transition') }}" type="button"  class="btn btn-primary">CAMBIAR ESTADO</a>
            <a href="{{ url('admin/projects/'. $project->id .'/transitionEliminar') }}" type="button"  class="btn btn-primary">VOLVER AL ESTADO PENDIENTE DE ENVIO</a>
             {{-- <a href="{{ url('admin/project-statuses/'. $project->id .'/eliminar') }}" type="button"  class="btn btn-primary">VOLVER A ESTADO ANTERIOR</a> --}}
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


<div class="card">
    <div class="card-header text-center">
        DOCUMENTOS PRESENTADOS
    </div>
    <div class="card-body">
            <div class="card-block">
                <table class="table table-hover table-listing">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Documento</th>
                    <th class="text-center">N° FOLIO</th>
                    <th class="text-center">Check</th>
                    <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($docproyecto as $key => $item)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $item->document->name}}</td>
                    <td class="text-center">
                        {{ $item->check()->where('project_id','=', $project->id)->first() ? $item->check()->where('project_id','=', $project->id)->first()['sheets']  : '0' }}
                    </td>
                    <td class="text-center">

                        <i class="fa fa-check-square text-success " aria-hidden="true"></i>
                    </td>
                    <td></td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>
    </div>
</div>


@endsection
