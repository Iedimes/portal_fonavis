@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.applications.actions.transition'))

@section('body')


<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Importante!</strong> <br>

    {{ $mensaje }}

    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>



<div class="card">

<div class="card-header">
         <div class="row">
            <div class="form-group col-sm-6">
                <p class="card-text">Proyecto: {{ $project->name }}</p>
            </div>

            <div class="form-group col-sm-6">
                <p class="card-text">Estado Actual: {{ $project->getEstado ? $project->getEstado->getStage->name : "Pendiente"}}</p>
            </div>
        </div>
    </div>

    <div class="card-body">
        <project-status-form
            :action="'{{ url('admin/project-statuses') }}'"
            :project="{{$project->id}}"
            :estado={{$estado}}
            :stages="{{ $stages->toJson() }}"
            :user="{{$user}}"
            :dependencia="{{$dependencia}}"
            email={{$email}}
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>

                 <input type="hidden" name="dependencia" value="{{ $dependencia }}">

                <div class="card-body">
                    @include('admin.project-status.components.form-elements')

                    @if ($estado==1 || $estado==3 || $estado==9 || ($estado==11 && $dependencia != 4) || $estado==13 || $estado==21)

                    @elseif($estado==8 && $dependencia == 1)

                    @elseif ($estado==11 && $dependencia == 4 )
                        @php
                            $collection = app(App\Models\ProjectStatus::class)->getMediaCollection('gallery');
                            $collection->maxFilesize(1024 * 1024 * 30); // Forzar 30MB aquí
                        @endphp

                        @include('brackets/admin-ui::admin.includes.media-uploader', [
                            'mediaCollection' => $collection,
                            'label' => 'Documentos Adjuntos'
                        ])
                    @else
                    @php
                        $collection = app(App\Models\ProjectStatus::class)->getMediaCollection('gallery');
                        $collection->maxFilesize(1024 * 1024 * 30); // Forzar 30MB aquí
                    @endphp

                    @include('brackets/admin-ui::admin.includes.media-uploader', [
                        'mediaCollection' => $collection,
                        'label' => 'Documentos Adjuntos'
                    ])
                    @endif

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>

            </form>

        </project-status-form>
    </div>
   </div>
@endsection
