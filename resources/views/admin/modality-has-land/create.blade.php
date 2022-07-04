@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.modality-has-land.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">

        <modality-has-land-form
            :action="'{{ url('admin/modality-has-lands') }}'"
            :modality="{{$modality->toJson()}}"
            :land="{{$land->toJson()}}"

            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>

                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.modality-has-land.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.modality-has-land.components.form-elements')
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>

            </form>

        </modality-has-land-form>

        </div>

        </div>


@endsection
