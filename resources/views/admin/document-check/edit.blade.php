@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.document-check.actions.edit', ['name' => $documentCheck->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <document-check-form
                :action="'{{ $documentCheck->resource_url }}'"
                :data="{{ $documentCheck->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.document-check.actions.edit', ['name' => $documentCheck->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.document-check.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </document-check-form>

        </div>
    
</div>

@endsection