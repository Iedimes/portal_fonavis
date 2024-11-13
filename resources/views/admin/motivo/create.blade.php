@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.motivo.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">

        <motivo-form
            :action="'{{ url('admin/motivos') }}'"
            :project_id="{{ json_encode($project_id) }}"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>

                <div class="card-header">
                    <i class="fa fa-pencil"></i> {{ trans('MOTIVO POR EL CUAL SE DA DE BAJA EL PROYECTO') }}
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>Proyecto:</strong> {{ $proyecto->id }}<br>
                        <strong>Nombre:</strong> {{ $proyecto->name }}
                    </div>
                    @include('admin.motivo.components.form-elements')
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>

            </form>

        </motivo-form>

        </div>

        </div>


@endsection
