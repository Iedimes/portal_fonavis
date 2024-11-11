@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.comentario.actions.edit', ['name' => $comentario->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <comentario-form
                :action="'{{ $comentario->resource_url }}'"
                :data="{{ $comentario->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.comentario.actions.edit', ['name' => $comentario->id]) }}
                    </div>

                    <div class="card-body">
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