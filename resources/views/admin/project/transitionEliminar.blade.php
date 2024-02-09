@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.applications.actions.transitionEliminar'))

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
        <project-status-form-eliminar
            :action="'{{ url('admin/project-statuses/'. $project->id .'/eliminar') }}'"
            :project="{{$project->id}}"
            :stages="{{ $stages->toJson() }}"
            :user="{{$user}}"
            email={{$email}}
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" @submit.prevent="onSubmit" :action="action" novalidate>


                <div class="card-body">
                    @include('admin.project-status.components.form-elementsE')
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('VOLVER AL ESTADO PENDIENTE') }}
                    </button>
                </div>

            </form>

        </project-status-form-eliminar>
    </div>
   </div>
@endsection
