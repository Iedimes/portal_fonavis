<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.land.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.land.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('short_name'), 'has-success': fields.short_name && fields.short_name.valid }">
    <label for="short_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.land.columns.short_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.short_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('short_name'), 'form-control-success': fields.short_name && fields.short_name.valid}" id="short_name" name="short_name" placeholder="{{ trans('admin.land.columns.short_name') }}">
        <div v-if="errors.has('short_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('short_name') }}</div>
    </div>
</div>


