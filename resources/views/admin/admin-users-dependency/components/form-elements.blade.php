<div class="form-group row align-items-center" :class="{'has-danger': errors.has('admin_user_id'), 'has-success': fields.admin_user_id && fields.admin_user_id.valid }">
    <label for="admin_user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.admin-users-dependency.columns.admin_user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.admin_user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('admin_user_id'), 'form-control-success': fields.admin_user_id && fields.admin_user_id.valid}" id="admin_user_id" name="admin_user_id" placeholder="{{ trans('admin.admin-users-dependency.columns.admin_user_id') }}">
        <div v-if="errors.has('admin_user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('admin_user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dependency_id'), 'has-success': fields.dependency_id && fields.dependency_id.valid }">
    <label for="dependency_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.admin-users-dependency.columns.dependency_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dependency_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dependency_id'), 'form-control-success': fields.dependency_id && fields.dependency_id.valid}" id="dependency_id" name="dependency_id" placeholder="{{ trans('admin.admin-users-dependency.columns.dependency_id') }}">
        <div v-if="errors.has('dependency_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dependency_id') }}</div>
    </div>
</div>


