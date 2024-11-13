@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.motivo.actions.edit', ['name' => $motivo->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <motivo-form
                :action="'{{ $motivo->resource_url }}'"
                :data="{{ $motivo->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.motivo.actions.edit', ['name' => $motivo->id]) }}
                    </div>

                    <div class="card-body">
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