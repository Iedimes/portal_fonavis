@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.comentario.actions.create'))

@section('body')

    <div class="container-xl">
        <div class="card">
            <comentario-form
                :action="'{{ url('admin/comentarios') }}'"
                :postulante_id="{{ json_encode($postulante_id) }}"
                :cedula="{{ json_encode($cedula) }}"
                v-cloak
                inline-template>
                <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                    <div class="card-header">
                        <h5 class="card-title"><i class="fa fa-pencil"></i> {{ trans('MOTIVO POR EL CUAL SE DA DE BAJA AL POSTULANTE') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Cédula:</strong> {{ $postulante->cedula }}<br>
                            <strong>Nombre:</strong> {{ $postulante->first_name }} {{ $postulante->last_name }}
                        </div>
                        @include('admin.comentario.components.form-elements')
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                </form>
            </comentario-form>
        </div>
    </div>

@endsection
