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

