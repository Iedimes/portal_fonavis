<div class="form-group row align-items-center" :class="{'has-danger': errors.has('land_id'), 'has-success': fields.land_id && fields.land_id.valid }">
    <label for="land_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.land-has-project-type.columns.land_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.land_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('land_id'), 'form-control-success': fields.land_id && fields.land_id.valid}" id="land_id" name="land_id" placeholder="{{ trans('admin.land-has-project-type.columns.land_id') }}"> --}}
        <multiselect
            v-model="form.land"
            :options="land"
            :multiple="false"
            track-by="id"
            label="name"
            :taggable="true"
            tag-placeholder=""
            placeholder="">
        </multiselect>
        <div v-if="errors.has('land_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('land_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('project_type_id'), 'has-success': fields.project_type_id && fields.project_type_id.valid }">
    <label for="project_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.land-has-project-type.columns.project_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.project_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('project_type_id'), 'form-control-success': fields.project_type_id && fields.project_type_id.valid}" id="project_type_id" name="project_type_id" placeholder="{{ trans('admin.land-has-project-type.columns.project_type_id') }}"> --}}

        <multiselect
            v-model="form.project_type"
            :options="pt"
            :multiple="false"
            track-by="id"
            label="name"
            :taggable="true"
            tag-placeholder=""
            placeholder="">
        </multiselect>
        <div v-if="errors.has('project_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('project_type_id') }}</div>
    </div>
</div>


