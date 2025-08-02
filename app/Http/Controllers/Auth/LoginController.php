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

    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->must_change_password) {
            // Evitar logueo y mostrar mensaje
            session()->flash('force_password_email', $user->email);
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
}
