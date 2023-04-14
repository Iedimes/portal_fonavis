<!DOCTYPE html>
<html>
<head>
    <style>
        hr {
            border-top: 0.5px solid rgb(182, 180, 180);
        }






    </style>
       <center><img src="{{storage_path('images/MUVHG.jpg')}}" class="imagencentro" width="950" height="140"></center>

    </head>

    <div>
        <h4>Proyecto: {{ $project->name }}</h4>
    </div>

<body>

    <table style="font-size: 13px;" CELLPADDING=5 CELLSPACING=0 width="750">
        <tr>
            <td>
                <strong>Código:</strong> {{ $project->id }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Lider:</strong> {{$project->leader_name}}
            </td>
            <td>
                <strong>Telefono:</strong> {{$project->phone}}
            </td>
            <td>
                <strong>SAT:</strong> {{$project->sat_id?$project->getSat->NucNomSat:""}}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Departamento: </strong>{{$project->state_id?$project->getState->DptoNom:""}}
            </td>
            <td>
                <strong>Distrito:</strong> {{$project->city_id}}
            </td>
            <td>
                <strong>Localidad:</strong> {{ $project->localidad }}

            </td>
        </tr>

        <tr>
            <td>
                <strong>Modalidad:</strong> {{$project->modalidad_id?$project->getModality->name:""}}
            </td>
            <td>
                <strong>Tipo de Terreno:</strong> {{$project->land_id?$project->getLand->name:""}}
            </td>
            <td>
                <strong>Tipologia:</strong> {{$project->typology_id?$project->getTypology->name:""}}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Estado:</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : "Pendiente"}}
            </td>
            <td>
                <strong>Cantidad de Viviendas:</strong> {{$postulantes->count()}}<br>
            </td>
        </tr>


</table>
<br><br>
<table style="font-size: 13px;" CELLPADDING=5 CELLSPACING=0 width="750">
        <thead>
            <th style="text-align:left">#</th>
            <th style="text-align:left">Nombre</th>
            <th style="text-align:left">Cedula</th>
            <th style="text-align:left">Edad</th>
            <th style="text-align:left">Ingreso</th>
            <th style="text-align:left">Nivel</th>
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
</body>
</html>
