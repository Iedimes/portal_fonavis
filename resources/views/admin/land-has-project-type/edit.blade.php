@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.land-has-project-type.actions.edit', ['name' => $landHasProjectType->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <land-has-project-type-form
                :action="'{{ $landHasProjectType->resource_url }}'"
                :data="{{ $landHasProjectType->toJson() }}"
                :pt="{{$pt->toJson()}}"
                :land="{{$land->toJson()}}"
                v-cloak
                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.land-has-project-type.actions.edit', ['name' => $landHasProjectType->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.land-has-project-type.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>

                </form>

        </land-has-project-type-form>

        </div>

</div>

@endsection
