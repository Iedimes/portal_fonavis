@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.discapacidad.actions.edit', ['name' => $discapacidad->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <discapacidad-form
                :action="'{{ $discapacidad->resource_url }}'"
                :data="{{ $discapacidad->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.discapacidad.actions.edit', ['name' => $discapacidad->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.discapacidad.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </discapacidad-form>

        </div>
    
</div>

@endsection