@extends('adminlte::page')

@section('title', 'FONAVIS')


@section('content')
<br>
<div class="invoice p-3 mb-3">

    <div class="row">
    <div class="col-12">
    <h4>
    <i class="fas fa-university"></i> Proyecto: {{ $project->name }}
    @if ($project->getEstado)
    <a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
        <i class="fas fa-download"></i> IMPRIMIR PDF
    </a>
    @else
    <button type="button" class="btn btn-success float-right" onclick="allchecked()">
        <i class="fa fa-plus-circle"></i> Enviar al MUVH
        </button>
    @endif
    {{--<a type="button" href="{{ url('generate-pdf/'.$project->id) }}" class="btn btn-danger float-right"  style="margin-right: 5px;">
        <i class="fas fa-download"></i> IMPRIMIR PDF
        </a>--}}

    </h4>
    </div>

    </div>

    <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
    <address>
    <strong>Lider:</strong> {{utf8_encode($project->leader_name)}}<br>
    <strong>Departamento: </strong>{{utf8_encode($project->state_id?$project->getState->DptoNom:"")}}<br>
    <strong>Modalidad:</strong> {{utf8_encode($project->modalidad_id?$project->getModality->name:"")}}<br>
    <strong>Estado:</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : "Pendiente"}}<br>
    </address>
    </div>

    <div class="col-sm-4 invoice-col">
    <address>
    <strong>Telefono:</strong> {{utf8_encode($project->phone)}}<br>
    <strong>Distrito:</strong> {{utf8_encode($project->city_id)}}<br>
    <strong>Tipo de Terreno:</strong> {{utf8_encode($project->land_id?$project->getLand->name:"")}}<br>
    <strong>Cantidad de Viviendas:</strong> {{$postulantes->count()}}<br>
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

    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Documentos a Entregar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="custom-tabs-one-applicant-tab" data-toggle="pill" href="#custom-tabs-one-applicant" role="tab" aria-controls="custom-tabs-one-applicant" aria-selected="false">Postulantes</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Historial</a>
            </li>
        </ul>
        </div>
        <div class="card-body">
        <div class="tab-content" id="custom-tabs-one-tabContent">
    <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
        <div class="row">
            <div class="col-12 table-responsive">
            <table class="table table-striped">
            <thead>
            <tr>
            <th>#</th>
            <th>Documento</th>
            <th>N° FOLIO</th>
            <th>Check</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($docproyecto as $key => $item)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $item->document->name}}</td>
                <td>
                    <div class="row">
                        <div class="col-6">
                            <input type="number" {{ $project->getEstado ? 'disabled' : '' }}  class="form-control" id="{{'sheet-'.$item->document_id}}" placeholder=""
                            value="{{ $item->check()->where('project_id','=', $project->id)->first() ? $item->check()->where('project_id','=', $project->id)->first()['sheets']  : '0' }}">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="custom-control custom-switch">
                    <input type="checkbox" {{ $project->getEstado ? 'disabled' : '' }}  {{ $item->check()->where('project_id','=', $project->id)->first() ? 'checked' : ''}} onchange="Check(this)" class="custom-control-input" id="{{$item->document_id}}">
                    <label class="custom-control-label" for="{{$item->document_id}}"></label>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
            </div>

            </div>


    </div>
    <div class="tab-pane fade" id="custom-tabs-one-applicant" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
        <a href="{{ url('projects/'.$project->id.'/postulantes') }}">
            <button type="button" class="btn btn-info float-right">
                <i class="fa fa-user"></i> Ir a la Seccion de Postulantes
            </button>
        </a>
        <br>
        <br>
        <table class="table table-striped">
            <thead>
                <th>#</th>
                <th>Nombre</th>
                <th class="text-center">Cedula</th>
                <th class="text-center">Edad</th>
                <th class="text-center">Ingreso</th>
                <th class="text-center">Nivel</th>
            </thead>
            <tbody>
                @foreach ($postulantes as $key=>$post)
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
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>
        <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">

            <table class="table table-striped">
                <thead>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Observacion</th>
                </thead>
                <tbody>
                    @foreach ($history as $item)
                    <tr>
                        <td>{{ $item->getStage->name }}</td>
                        <td>{{ $item->created_at }} </td>
                        <td>{{ $item->getUser ? $item->getUser['first_name'] : 'N/A'}}</td>
                        <td> {{ $item->record }} </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>

        </div>
        </div>

        </div>


    </div>


    <div class="modal modal-info fade" id="modal-enviar">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
              <h4 class="modal-title"><i class="fa  fa-send"></i> Enviar Proyecto al MUVH</h4>
            </div>
            <div class="modal-body">
                <form action="{{ url('projects/send') }}" method="post">
                        {{ csrf_field() }}
                <p id="demoproy"></p>
                <input id="send_id" name="send_id" type="hidden" value="" />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-outline">Enviar</button>
            </div>
        </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

@stop

@section('js')

<script>

    function Check(value) {
      //document.getElementById('verdict').innerHTML = value.checked;
      //console.log('oiko');
      var sites = {!! json_encode($project['id']) !!};
      var abc = sites;
      var sheets = document.getElementById('sheet-'+value.id).value;
      //console.log(sheets);
      //console.log(document.getElementById('sheet-'+value.id).value);
      //console.log(sites);

      if (document.getElementById(value.id).checked == false) {
        console.log('cambio a falso');
        document.getElementById('sheet-'+value.id).value = 0;
      }

      if (sheets >= 1) {
            $.ajax({
                url: '{{URL::to('/projects')}}/ajax/'+value.id+"/checkdocuments/"+abc+"/"+sheets,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    console.log(data);
                }
            });

            $(document).Toasts('create', {
                            icon: 'fas fa-exclamation',
                            class: 'bg-success m-1',
                            autohide: true,
                            delay: 5000,
                            title: 'Importante!',
                            body: 'Cambio guardado correctamente'
                        })

        }else{
            alert('Debe completar el campo folio para checkear este documento')
            document.getElementById(value.id).checked = false
        }

    };

    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    function allchecked(){
        var sites = {!! json_encode($project['id']) !!};
        var abc = sites;
        var keys = {!! json_encode($claves) !!};
        var def = keys;
        var si = 0;
        var no = 0
        def.forEach(element => {
            //console.log('Id document: '+element);
            if (document.getElementById(element).checked) {
                //console.log('checkbox: '+element+' esta seleccionado');
                si += 1;
            }else
            {
                no += 1;
            }
        });

        console.log('Total si: '+si+' Total No: '+no);
        if (no >= 1) {
            alert('Debe completar todos los checks para enviar al MUVH')
        }else
        {
            console.log('Puede Enviar al MUVH');
            $.ajax({
                url: '{{URL::to('/projects/send')}}/'+sites,
                type: "GET",
                dataType: "json",
                success:async function(data) {
                    //console.log(data.message);
                    if (data.message == 'success') {
                        $(document).Toasts('create', {
                            icon: 'fas fa-exclamation',
                            class: 'bg-success m-1',
                            autohide: true,
                            delay: 5000,
                            title: 'Importante!',
                            body: 'El proyecto ha cambiado de estado'
                        })
                        await delay(3000);
                        location.reload()
                        console.log('refrescar');
                    } else {
                        console.log('no hace nada');
                    }
                }
            });
        }
        //console.log(def);
    }
</script>

@endsection
