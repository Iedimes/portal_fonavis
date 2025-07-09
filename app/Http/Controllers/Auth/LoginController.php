<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Intenta loguear, pero bloquea si el usuario debe cambiar su contraseña
    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->must_change_password) {
            // Bloquea login
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    // Muestra un mensaje claro si se bloqueó el login por contraseña forzada
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->must_change_password) {
            throw ValidationException::withMessages([
                'email' => [
                    'Por seguridad, debés cambiar tu contraseña. '
                    . 'Hacé clic en <a href="' . route('password.request') . '">¡¡¡Cambiar contraseña!!!</a> para continuar.'
                ],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
