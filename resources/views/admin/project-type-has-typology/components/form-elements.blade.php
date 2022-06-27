<div class="form-group row align-items-center" :class="{'has-danger': errors.has('project_type_id'), 'has-success': fields.project_type_id && fields.project_type_id.valid }">
    <label for="project_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-type-has-typology.columns.project_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.project_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('project_type_id'), 'form-control-success': fields.project_type_id && fields.project_type_id.valid}" id="project_type_id" name="project_type_id" placeholder="{{ trans('admin.project-type-has-typology.columns.project_type_id') }}">
        <div v-if="errors.has('project_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('project_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('typology_id'), 'has-success': fields.typology_id && fields.typology_id.valid }">
    <label for="typology_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-type-has-typology.columns.typology_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.typology_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('typology_id'), 'form-control-success': fields.typology_id && fields.typology_id.valid}" id="typology_id" name="typology_id" placeholder="{{ trans('admin.project-type-has-typology.columns.typology_id') }}">
        <div v-if="errors.has('typology_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('typology_id') }}</div>
    </div>
</div>


