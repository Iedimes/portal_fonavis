@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

<div class="fonavis"></div>

@section('auth_body')

<div class="login-header text-center mb-4">
    <img src="/img/logofonavis.png" width="280px;" class="img-responsive" alt="Logo Fonavis">
    <h2 class="text-dark font-weight-bold mt-3 mb-0">Acceso Portal</h2>
    <p class="text-muted small mt-2">Ingrese sus credenciales para continuar</p>
</div>

<style>
.login-page, .register-page {
    -ms-flex-align: center;
    align-items: center;
    background-repeat: no-repeat;
    background-position: center;
    background-image: url("/img/fondo.PNG") !important;
    -webkit-background-size: cover;
    background-size: cover;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    height: 100vh;
    -ms-flex-pack: center;
    justify-content: center;
    background-color: #f5f5f5;
}

.login-box, .register-box {
    width: 400px;
    position: relative;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    background: white;
    overflow: hidden;
}

.login-card-body, .register-card-body {
    padding: 2.5rem;
}

.fonavis {
    background-color: transparent;
    margin-top: 0;
}

.login-header {
    margin-bottom: 1.5rem;
}

.login-header img {
    max-width: 100%;
    height: auto;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.login-header h2 {
    font-size: 1.75rem;
    color: #2c3e50;
    letter-spacing: -0.5px;
}

.login-header p {
    font-size: 0.95rem;
    color: #7f8c8d;
}

/* Estilos para los labels */
.login-card-body label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
    margin-bottom: 0.6rem;
    display: block;
    letter-spacing: 0.3px;
}

/* Estilos para los inputs */
.login-card-body .form-control {
    border: 1.5px solid #e0e6ed;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.login-card-body .form-control:focus {
    border-color: #3498db;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.login-card-body .form-control::placeholder {
    color: #bdc3c7;
}

/* Estilos para el icono dentro del input */
.login-card-body .input-group-text {
    background-color: transparent;
    border: none;
    color: #95a5a6;
    padding: 0 0.75rem;
}

/* Espaciado entre campos */
.mb-3 {
    margin-bottom: 1.5rem;
}

/* Botón de login */
.btn-block {
    border-radius: 6px;
    padding: 0.9rem 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.4px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Alerta de cambio de contraseña */
.alert-danger {
    border: none;
    border-radius: 6px;
    background-color: #ffe5e5;
    border-left: 4px solid #e74c3c;
    margin-bottom: 1.5rem;
}

.alert-danger a.btn {
    border-radius: 4px;
    font-weight: 600;
}

/* Link al sitio web */
.login-footer-link {
    text-align: center;
    margin-top: 1.5rem;
}

.login-footer-link a {
    color: #3498db;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.login-footer-link a:hover {
    color: #2980b9;
    text-decoration: underline;
}

/* Error messages */
.invalid-feedback {
    font-size: 0.85rem;
    color: #e74c3c;
    font-weight: 500;
}
</style>

<form action="{{ $login_url }}" method="post">
    @csrf

    {{-- Email field --}}
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <div class="input-group">
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="correo@ejemplo.com" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{!! $message !!}</strong>
                </span>
            @enderror
        </div>
    </div>

    {{-- Password field --}}
    <div class="form-group">
        <label for="password">Contraseña</label>
        <div class="input-group">
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Ingrese su contraseña">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    @if(session('force_password_email'))
        <div class="alert alert-danger" role="alert">
            <div class="text-center">
                <strong class="d-block mb-2">⚠️ Por seguridad, debes cambiar tu contraseña</strong>
                <p class="small mb-3">Por favor, actualiza tu contraseña antes de acceder al portal.</p>
                <a href="{{ route('password.request') }}" class="btn btn-sm btn-warning font-weight-bold">
                    Cambiar contraseña
                </a>
            </div>
        </div>
    @endif

    {{-- Login button --}}
    <div class="form-group">
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-sign-in-alt mr-2"></span>
            Acceder
        </button>
    </div>

    <div class="login-footer-link">
        <a href="https://www.muvh.gov.py" target="_blank">
            <span class="fas fa-external-link-alt mr-1"></span>
            Ir a página web
        </a>
    </div>
</form>
@stop

@section('auth_footer')
    {{-- Extra footer content (opcional) --}}
@stop
