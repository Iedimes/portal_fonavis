<!-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('stage_id'), 'has-success': fields.stage_id && fields.stage_id.valid }">
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
        <div v-if="errors.has('stage_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('stage_id') }}</div>
    </div>
</div> -->

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('record'), 'has-success': fields.record && fields.record.valid }">
    <label for="record" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.project-status.columns.record') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <textarea v-model="form.record" v-validate="'required'" @input="validate($event)"
        class="form-control" :class="{'form-control-danger': errors.has('record'), 'form-control-success': fields.record && fields.record.valid}"
        id="record" name="record" placeholder="{{ trans('admin.project-status.columns.record') }}"></textarea>
        <div v-if="errors.has('record')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('record') }}</div>
    </div>
</div>
@if($errors->any())
<div class="alert alert-danger">
    {{ $errors->first('msg') }}
</div>
@endif

