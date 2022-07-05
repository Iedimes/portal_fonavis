@extends('adminlte::page')

@section('title', 'FONAVIS')


@section('content')
<br>
<div class="invoice p-3 mb-3">

    <div class="row">
    <div class="col-12">
    <h4>
    <i class="fas fa-university"></i> Proyecto: {{ $project->name }}
    <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
        <i class="fas fa-download"></i> IMPRIMIR PDF
        </a>
    </h4>
    </div>

    </div>

    <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
    <address>
    <strong>Lider:</strong> {{utf8_encode($project->leader_name)}}<br>
    <strong>Departamento: </strong>{{utf8_encode($project->state_id?$project->getState->DptoNom:"")}}<br>
    <strong>Modalidad:</strong> {{utf8_encode($project->modalidad_id?$project->getModality->name:"")}}<br>

    </address>
    </div>

    <div class="col-sm-4 invoice-col">
    <address>
    <strong>Telefono:</strong> {{utf8_encode($project->phone)}}<br>
    <strong>Distrito:</strong> {{utf8_encode($project->city_id)}}<br>
    <strong>Tipo de Terreno:</strong> {{utf8_encode($project->land_id?$project->getLand->name:"")}}<br>
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


    <div class="row">
    <div class="col-12 table-responsive">
    <table class="table table-striped">
    <thead>
    <tr>
    <th>#</th>
    <th>Documento</th>

    <th>Check</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($docproyecto as $key => $item)
    <tr>
        <td>{{ $key+1 }}</td>
        <td>{{ $item->document->name}}</td>

        <td>
            <div class="custom-control custom-switch">
            <input type="checkbox" {{ $item->check()->where('project_id','=', $project->id)->first() ? 'checked' : ''}} onchange="Check(this)" class="custom-control-input" id="{{$item->document_id}}">
            <label class="custom-control-label" for="{{$item->document_id}}"></label>
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>

    </div>

    <div class="row no-print">
    <div class="col-12">
    <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
    <i class="fas fa-download"></i> IMPRIMIR PDF
    </a>
    </div>
    </div>
    </div>

@stop

@section('js')

<script>

    function Check(value) {
      //document.getElementById('verdict').innerHTML = value.checked;
      console.log('oiko');
      var sites = {!! json_encode($project['id']) !!};
      var abc = sites;
      console.log(sites);
      $.ajax({
            url: '{{URL::to('/projects')}}/ajax/'+value.id+"/checkdocuments/"+abc,
            type: "GET",
            dataType: "json",
            success:function(data) {
                console.log(data);
                /*$('select[name="land_id"]').empty();
                $('select[name="land_id"]').append('<option value="">Selecciona el Tipo de Terreno</option>');

                $.each(data, function(key, value) {
                    $('select[name="land_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                });*/

            }
        });
    };
</script>

@endsection
