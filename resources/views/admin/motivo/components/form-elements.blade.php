<div class="form-group row align-items-center">
    <input type="hidden" v-model="form.project_id" name="project_id">
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('motivo'), 'has-success': fields.motivo && fields.motivo.valid }">
    <label for="motivo" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">
        {{ trans('admin.motivo.columns.motivo') }}
    </label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.motivo" v-validate="'required'" id="motivo" name="motivo"></textarea>
        </div>
        <div v-if="errors.has('motivo')" class="form-control-feedback form-text" v-cloak>
            @{{ errors.first('motivo') }}
        </div>
    </div>
</div>



