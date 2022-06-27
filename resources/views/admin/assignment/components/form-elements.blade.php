<div class="form-group row align-items-center" :class="{'has-danger': errors.has('document_id'), 'has-success': fields.document_id && fields.document_id.valid }">
    <label for="document_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assignment.columns.document_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.document_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('document_id'), 'form-control-success': fields.document_id && fields.document_id.valid}" id="document_id" name="document_id" placeholder="{{ trans('admin.assignment.columns.document_id') }}">
        <div v-if="errors.has('document_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('document_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('category_id'), 'has-success': fields.category_id && fields.category_id.valid }">
    <label for="category_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assignment.columns.category_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.category_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('category_id'), 'form-control-success': fields.category_id && fields.category_id.valid}" id="category_id" name="category_id" placeholder="{{ trans('admin.assignment.columns.category_id') }}">
        <div v-if="errors.has('category_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('category_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('project_type_id'), 'has-success': fields.project_type_id && fields.project_type_id.valid }">
    <label for="project_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assignment.columns.project_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.project_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('project_type_id'), 'form-control-success': fields.project_type_id && fields.project_type_id.valid}" id="project_type_id" name="project_type_id" placeholder="{{ trans('admin.assignment.columns.project_type_id') }}">
        <div v-if="errors.has('project_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('project_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('stage_id'), 'has-success': fields.stage_id && fields.stage_id.valid }">
    <label for="stage_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assignment.columns.stage_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.stage_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('stage_id'), 'form-control-success': fields.stage_id && fields.stage_id.valid}" id="stage_id" name="stage_id" placeholder="{{ trans('admin.assignment.columns.stage_id') }}">
        <div v-if="errors.has('stage_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('stage_id') }}</div>
    </div>
</div>


