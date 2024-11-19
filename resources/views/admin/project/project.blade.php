@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('IMPRIMIR LISTADO DE POSTULANTES'))

@section('body')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex align-items-center">

                <span>
                    {{ trans('CODIGO: ') . $project->id }}
                    <br>
                    {{ trans('PROYECTO: ') . $project->name }}
                </span>
            </div>


            <div class="card-body">
                <div class="row invoice-info">
                    {{-- Información del proyecto --}}
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>{{ trans('Líder:') }}</strong> {{ $project->leader_name }}<br>
                            <strong>{{ trans('Departamento:') }}</strong> {{ $project->state_id ? $project->getState->DptoNom : '' }}<br>
                            <strong>{{ trans('Modalidad:') }}</strong> {{ $project->modalidad_id ? $project->getModality->name : '' }}<br>
                            <strong>{{ trans('Estado:') }}</strong> {{ $project->getEstado ? $project->getEstado->getStage->name : 'Pendiente' }}<br>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>{{ trans('Teléfono:') }}</strong> {{ $project->phone }}<br>
                            <strong>{{ trans('Distrito:') }}</strong> {{ $project->city_id ? strtoupper($project->getCity->CiuNom) : '' }}<br>
                            <strong>{{ trans('Tipo de Terreno:') }}</strong> {{ $project->land_id ? $project->getLand->name : '' }}<br>
                            <strong>{{ trans('Cantidad de Viviendas:') }}</strong> {{ $postulantes->count() }}<br>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>{{ trans('SAT:') }}</strong> {{ $project->sat_id ? $project->getSat->NucNomSat : '' }}<br>
                            <strong>{{ trans('Localidad:') }}</strong> {{ $project->localidad }}<br>
                            <strong>{{ trans('Tipología:') }}</strong> {{ $project->typology_id ? $project->getTypology->name : '' }}<br>
                        </address>
                    </div>
                </div>
              {{-- Botón para imprimir listado --}}
              <a href="{{ url('admin/postulantes/' . $project->id . '/imprimir') }}" class="btn bg-primary text-white btn-block btn-lg">
                {{ trans('Imprimir Listado de Postulantes') }}
            </a>


                <br><br>

                {{-- Tabla de postulantes --}}
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('Nombre') }}</th>
                            <th class="text-center">{{ trans('Cédula') }}</th>
                            <th class="text-center">{{ trans('Edad') }}</th>
                            <th class="text-center">{{ trans('Ingreso') }}</th>
                            <th class="text-center">{{ trans('Nivel') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($postulantes) > 0)
                            @foreach ($postulantes as $key => $post)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $post->postulante_id ? $post->getPostulante->first_name . ' ' . $post->getPostulante->last_name : '' }}</td>
                                    <td class="text-center">
                                        {{ is_numeric($post->postulante_id ? $post->getPostulante->cedula : '')
                                            ? number_format($post->getPostulante->cedula, 0, '.', '.')
                                            : $post->getPostulante->cedula }}
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($post->postulante_id ? $post->getPostulante->birthdate : '')->age }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format(App\Models\ProjectHasPostulantes::getIngreso($post->postulante_id), 0, '.', '.') }}
                                    </td>
                                    <td class="text-center">
                                        {{ App\Models\ProjectHasPostulantes::getNivel($post->postulante_id) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">{{ trans('admin.project.messages.no_applicants') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
