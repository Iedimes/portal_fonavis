@extends('brackets/admin-ui::admin.layout.master1')

@section('title', trans('brackets/admin-auth::admin.login.title'))

@section('content')
	<style>
		.auth-page-container {
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: #f8f9fa;
			padding: 20px;
		}

		.auth-card {
			border-radius: 8px;
			box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
			border: 1px solid #e9ecef;
			overflow: hidden;
			background: white;
		}

		.auth-card .card-block {
			padding: 2.5rem;
		}

		.auth-header {
			margin-bottom: 2.5rem;
		}

		.auth-header img {
			max-width: 160px;
			height: auto;
			margin: 0 auto 1.5rem;
			display: block;
		}

		.auth-header h3 {
			font-size: 1.5rem;
			color: #212529;
			font-weight: 600;
			margin: 0 0 0.25rem 0;
			letter-spacing: 0;
			text-align: center;
		}

		.auth-header p {
			color: #6c757d;
			font-size: 0.875rem;
			margin: 0;
			text-align: center;
		}

		.auth-body label {
			font-weight: 500;
			color: #212529;
			font-size: 0.875rem;
			margin-bottom: 0.5rem;
			display: block;
		}

		.form-control {
			border: 1px solid #dee2e6 !important;
			border-radius: 4px !important;
			padding: 0.625rem 0.875rem !important;
			font-size: 0.875rem;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
			background-color: white;
			color: #212529;
		}

		.form-control:focus {
			border-color: #6c757d !important;
			background-color: white;
			box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.05) !important;
			color: #212529;
		}

		.form-control::placeholder {
			color: #adb5bd;
		}

		.input-group--custom {
			display: flex;
			align-items: center;
			position: relative;
		}

		.input-group-addon {
			background-color: transparent;
			border: none;
			padding: 0 0.5rem 0 0;
			color: #6c757d;
			position: absolute;
			left: 0.875rem;
			pointer-events: none;
		}

		.input-group--custom .form-control {
			padding-left: 2.5rem !important;
		}

		.input-icon {
			font-size: 0.95rem;
		}

		.form-group {
			margin-bottom: 1.25rem;
		}

		.form-control-danger,
		.form-control-danger:focus {
			border-color: #dc3545 !important;
			background-color: white;
		}

		.form-control-success,
		.form-control-success:focus {
			border-color: #28a745 !important;
		}

		.form-control-feedback {
			font-size: 0.8rem;
			color: #dc3545;
			margin-top: 0.25rem;
			font-weight: 500;
		}

		.btn-primary {
			background-color: #007bff !important;
			border: 1px solid #007bff !important;
			border-radius: 4px !important;
			padding: 0.625rem 1rem !important;
			font-weight: 500;
			font-size: 0.875rem;
			letter-spacing: 0;
			transition: all 0.15s ease-in-out !important;
			color: white !important;
		}

		.btn-primary:hover {
			background-color: #0056b3 !important;
			border-color: #004085 !important;
			box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15) !important;
		}

		.btn-primary:active {
			background-color: #003d82 !important;
			border-color: #00285c !important;
		}

		.btn-block {
			width: 100% !important;
		}

		.auth-footer {
			text-align: center;
			margin-top: 1.5rem;
			padding-top: 1rem;
			border-top: 1px solid #e9ecef;
		}

		.auth-footer a {
			color: #007bff;
			text-decoration: none;
			font-size: 0.8rem;
			font-weight: 500;
			transition: color 0.15s ease-in-out;
		}

		.auth-footer a:hover {
			color: #0056b3;
			text-decoration: none;
		}

		.form-text {
			display: block;
			margin-top: 0.25rem;
		}

		.col-md-6 {
			max-width: 100%;
		}

		.col-lg-5 {
			max-width: 420px;
		}
	</style>

	<div class="auth-page-container" id="app">
	    <div class="row w-100">
	        <div class="col-md-6 col-lg-5 mx-auto">
				<div class="card auth-card">
					<div class="card-block">
						<div class="auth-header text-center">
							<img src="{{ asset('img/logofonavis.png') }}" alt="Logo Fonavis" class="img-fluid">
                            <h3>Acceso Admin</h3>
                            <p>Ingrese sus credenciales administrativas</p>
						</div>
						<auth-form
								:action="'{{ url('/admin/login') }}'"
								:data="{}"
								inline-template>
							<form class="form-horizontal auth-body" role="form" method="POST" action="{{ url('/admin/login') }}" novalidate>
								{{ csrf_field() }}

                                @include('brackets/admin-auth::admin.auth.includes.messages')
								<div class="form-group" :class="{'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
									<label for="email">{{ trans('brackets/admin-auth::admin.auth_global.email') }}</label>
									<div class="input-group input-group--custom">
										<div class="input-group-addon"><i class="input-icon input-icon--mail"></i></div>
										<input type="text" v-model="form.email" v-validate="'required|email'" class="form-control" :class="{'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid}" id="email" name="email" placeholder="correo@ejemplo.com">
									</div>
									<div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
								</div>
								<div class="form-group" :class="{'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
									<label for="password">{{ trans('brackets/admin-auth::admin.auth_global.password') }}</label>
									<div class="input-group input-group--custom">
										<div class="input-group-addon"><i class="input-icon input-icon--lock"></i></div>
										<input type="password" v-model="form.password"  class="form-control" :class="{'form-control-danger': errors.has('password'), 'form-control-success': fields.password && fields.password.valid}" id="password" name="password" placeholder="Ingrese su contraseña">
									</div>
									<div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}</div>
								</div>
								<div class="form-group">
									<input type="hidden" name="remember" value="1">
									<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in-alt mr-2"></i>{{ trans('brackets/admin-auth::admin.login.button') }}</button>
								</div>
								<div class="auth-footer">
                                    <a href="http://www.muvh.gov.py" target="_blank"><i class="fa fa-external-link-alt mr-1"></i>Ir a página web</a>
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
