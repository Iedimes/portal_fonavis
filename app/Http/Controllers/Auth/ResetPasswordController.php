<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;

    // Este método actualiza la contraseña
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->must_change_password = false;
        $user->save();

        $this->guard()->login($user);
    }

    // ✅ Aquí agregás tus reglas de validación de contraseña segura
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[a-z]/',      // al menos una letra minúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[@$!%*#?&.]/', // al menos un símbolo especial
                'confirmed',
            ],
        ];
    }

    // ✅ Mensajes de error personalizados
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, un número y un carácter especial (@ $ ! % * # ? & .)',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
