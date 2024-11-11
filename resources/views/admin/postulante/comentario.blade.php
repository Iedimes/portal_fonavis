@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('Eliminacion de postulante'))

@section('body')





<div class="card">

    <div class="card-header">
         <div class="row">
            <div class="form-group col-sm-6">
                <p class="card-text">Cedula: {{ $postulante->cedula }}</p>
                <p class="card-text">Nombre: {{ $postulante->first_name }} {{ $postulante->last_name }}</p>
            </div>
         </div>
    </div>


    <div class="card-body">
        <comentario-form
            {{-- :action="'{{ url('admin/project-statuses') }}'"
            :project="{{$project->id}}"
            :estado={{$estado}}
            :stages="{{ $stages->toJson() }}"
            :user="{{$user}}"
            email={{$email}} --}}
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                <div class="card-body">

                    <div class="form-group row align-items-center" :class="{'has-danger': errors.has('comentario'), 'has-success': fields.comentario && fields.comentario.valid }">
                        <label for="comentario" class="col-form-label text-md-right col-md-2">{{ trans('COMENTARIO') }}</label>
                        <div class="col-md-9 col-xl-8">
                            <textarea v-model="form.comentario" v-validate="'required'" @input="validate($event)"
                                class="form-control" :class="{'form-control-danger': errors.has('comentario'), 'form-control-success': fields.comentario && fields.comentario.valid}"
                                id="comentario" name="comentario" placeholder="{{ trans('') }}"></textarea>
                            <div v-if="errors.has('comentario')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('comentario') }}</div>
                        </div>
                    </div>

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
