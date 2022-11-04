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

<div class="fonavis">
</div>
{{-- <img src="/img/muvh transparente.png"width="300px;"class="img-responsive"> --}}

@section('auth_body')
<img src="/img/logofonavis.png"width="300px;"class="img-responsive">
<h3 class="card-title float-none text-center">INGRESE LOS DATOS SOLICITADOS  </h3> <hr>
<style>
personalizado {color:black;}

.login-page, .register-page {
    -ms-flex-align: center;
    align-items: center;
    background-repeat: no-repeat;
    background-position: -2em;
    background-image: url("/img/fondo.PNG") !important;
    -webkit-background-size: cover;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    height: 100vh;
    -ms-flex-pack: center;
    justify-content: center;
    background-color: #fff;
    box-shadow: 0 0 50px 5px rgb(0 0 0 / 30%), 0 0 5px -1px white;



}

.login-box, .register-box {
    width: 360px;
    position: relative;
    top: -31px;
}

.fonavis {background-color: #fff; margin-top:16px;}

</style>

    <form action="{{ $login_url }}" method="post">
        @csrf

        {{-- Email field --}}
        <label> CORREO ELECTRONICO</label>
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="{{ __('Ingrese su correo Electronico') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <label> CONTRASEÑA </label>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('Ingrese su contraseña') }}">

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

        {{-- Login field --}}
        <div class="row">
            <div class="col-12">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('Acceder') }}
                </button>
            </div>
        </div>
        <a href="https://www.muvh.gov.py" style="/* text-align:center; */float: right;margin-top: 18px;"> ir a la página web </a>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}

@stop
