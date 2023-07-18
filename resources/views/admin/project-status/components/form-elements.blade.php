{{--<div class="form-group row align-items-center" :class="{'has-danger': errors.has('project_id'), 'has-success': fields.project_id && fields.project_id.valid }">
    <label for="project_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-status.columns.project_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.project_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('project_id'), 'form-control-success': fields.project_id && fields.project_id.valid}" id="project_id" name="project_id" placeholder="{{ trans('admin.project-status.columns.project_id') }}">
        <div v-if="errors.has('project_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('project_id') }}</div>
    </div>
</div>--}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('stage_id'), 'has-success': fields.stage_id && fields.stage_id.valid }">
    <label for="stage_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-status.columns.stage_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <multiselect
            v-model="form.stage"
            :options="stages"
            :multiple="false"
            track-by="id"
            label="name"
            :taggable="true"
            tag-placeholder=""
            placeholder="{{ trans('admin.applications.columns.state') }}">
            </multiselect>
        {{--<input type="text" v-model="form.stage_id" v-validate="'required|integer'" @input="validate($event)"
        class="form-control" :class="{'form-control-danger': errors.has('stage_id'), 'form-control-success': fields.stage_id && fields.stage_id.valid}"
        id="stage_id" name="stage_id" placeholder="{{ trans('admin.project-status.columns.stage_id') }}">--}}
        <div v-if="errors.has('stage_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('stage_id') }}</div>
    </div>
</div>

{{--<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-status.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.project-status.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>--}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('record'), 'has-success': fields.record && fields.record.valid }">
    <label for="record" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-status.columns.record') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <textarea v-model="form.record" v-validate="'required'" @input="validate($event)"
        class="form-control" :class="{'form-control-danger': errors.has('record'), 'form-control-success': fields.record && fields.record.valid}"
        id="record" name="record" placeholder="{{ trans('admin.project-status.columns.record') }}"></textarea>
        {{--<input type="text" v-model="form.record" v-validate="'required'" @input="validate($event)"
        class="form-control" :class="{'form-control-danger': errors.has('record'), 'form-control-success': fields.record && fields.record.valid}"
        id="record" name="record" placeholder="{{ trans('admin.project-status.columns.record') }}">--}}
        <div v-if="errors.has('record')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('record') }}</div>
    </div>
</div>
@if($errors->any())
<div class="alert alert-danger">
    {{ $errors->first('msg') }}
</div>
@endif

