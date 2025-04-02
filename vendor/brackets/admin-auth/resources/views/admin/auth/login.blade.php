@extends('brackets/admin-ui::admin.layout.master1')

@section('title', trans('brackets/admin-auth::admin.login.title'))

@section('content')
	<div class="container" id="app">
	    <div class="row align-items-center justify-content-center auth">
	        <div class="col-md-6 col-lg-5">
				<div class="card">
					<div class="card-block">
						<div class="text-center mb-0">
							<img src="{{ asset('img/logofonavis.png') }}" alt="Logo Fonavis" class="img-fluid" width="380" height="380">
                            <h4 style="text-align: center; color: rgb(110, 109, 109);">INGRESE LOS DATOS SOLICITADOS</h4>
						</div>
						<auth-form
								:action="'{{ url('/admin/login') }}'"
								:data="{}"
								inline-template>
							<form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/login') }}" novalidate>
								{{ csrf_field() }}

								<div class="auth-body">
                                    <hr>
									@include('brackets/admin-auth::admin.auth.includes.messages')
									<div class="form-group" :class="{'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
										<label for="email">{{ trans('brackets/admin-auth::admin.auth_global.email') }}</label>
										<div class="input-group input-group--custom">
											<div class="input-group-addon"><i class="input-icon input-icon--mail"></i></div>
											<input type="text" v-model="form.email" v-validate="'required|email'" class="form-control" :class="{'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid}" id="email" name="email" placeholder="{{ trans('Ingrese su correo Electronico') }}">
										</div>
										<div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
									</div>
									<div class="form-group" :class="{'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
										<label for="password">{{ trans('brackets/admin-auth::admin.auth_global.password') }}</label>
										<div class="input-group input-group--custom">
											<div class="input-group-addon"><i class="input-icon input-icon--lock"></i></div>
											<input type="password" v-model="form.password"  class="form-control" :class="{'form-control-danger': errors.has('password'), 'form-control-success': fields.password && fields.password.valid}" id="password" name="password" placeholder="{{ trans('Ingrese su contraseña') }}">
										</div>
										<div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}</div>
									</div>
									<div class="form-group">
										<input type="hidden" name="remember" value="1">
										<button type="submit" class="btn btn-primary btn-block btn-spinner" style="background-color: blue; color: white;"><i class="fa fa-sign-out"></i> {{ trans('brackets/admin-auth::admin.login.button') }}</button>
									</div>
									<div class="form-group text-right">
                                        <a href="http://www.muvh.gov.py" style="color:blue; font-size: 1.2rem;">ir a la página web</a>
									</div>
								</div>
							</form>
						</auth-form>
					</div>
				</div>
	        </div>
	    </div>
	</div>
@endsection

@section('bottom-scripts')
<script type="text/javascript">
    document.getElementById('password').dispatchEvent(new Event('input'));
</script>
@endsection
