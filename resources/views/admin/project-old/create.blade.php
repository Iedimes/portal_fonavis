@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.project-old.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <project-old-form
            :action="'{{ url('admin/project-olds') }}'"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.project-old.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.project-old.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </project-old-form>

        </div>

        </div>

    
@endsection