<div class="form-group row align-items-center" :class="{'has-danger': errors.has('modality_id'), 'has-success': fields.modality_id && fields.modality_id.valid }">
    <label for="modality_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.modality-has-land.columns.modality_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.modality_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('modality_id'), 'form-control-success': fields.modality_id && fields.modality_id.valid}" id="modality_id" name="modality_id" placeholder="{{ trans('admin.modality-has-land.columns.modality_id') }}">
        <div v-if="errors.has('modality_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('modality_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('land_id'), 'has-success': fields.land_id && fields.land_id.valid }">
    <label for="land_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.modality-has-land.columns.land_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.land_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('land_id'), 'form-control-success': fields.land_id && fields.land_id.valid}" id="land_id" name="land_id" placeholder="{{ trans('admin.modality-has-land.columns.land_id') }}">
        <div v-if="errors.has('land_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('land_id') }}</div>
    </div>
</div>


