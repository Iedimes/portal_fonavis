<div class="form-group row align-items-center" :class="{'has-danger': errors.has('project_id'), 'has-success': fields.project_id && fields.project_id.valid }">
    <label for="project_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-old.columns.project_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.project_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('project_id'), 'form-control-success': fields.project_id && fields.project_id.valid}" id="project_id" name="project_id" placeholder="{{ trans('admin.project-old.columns.project_id') }}">
        <div v-if="errors.has('project_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('project_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-old.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.project-old.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>


