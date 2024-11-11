<div class="form-group row align-items-center">
    <input type="hidden" v-model="form.postulante_id" name="postulante_id">
</div>

<div class="form-group row align-items-center">
    <input type="hidden" v-model="form.cedula" name="cedula">
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('comentario'), 'has-success': fields.comentario && fields.comentario.valid }">
    <label for="comentario" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.comentario.columns.comentario') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.comentario" v-validate="'required'" id="comentario" name="comentario"></textarea>
        </div>
        <div v-if="errors.has('comentario')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('comentario') }}</div>
    </div>
</div>
