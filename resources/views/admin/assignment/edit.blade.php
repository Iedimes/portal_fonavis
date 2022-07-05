@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assignment.actions.edit', ['name' => $assignment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <assignment-form
                :action="'{{ $assignment->resource_url }}'"
                :data="{{ $assignment->toJson() }}"
                :document="{{$document->toJson()}}"
                :category="{{$category->toJson()}}"
                :pt="{{$pt->toJson()}}"
                :stage="{{$stage->toJson()}}"




                v-cloak
                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.assignment.actions.edit', ['name' => $assignment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.assignment.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>

                </form>

        </assignment-form>

        </div>

</div>

@endsection
