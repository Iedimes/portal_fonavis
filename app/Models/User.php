<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'username', 'password', 'sat_ruc'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['resource_url'];
    protected $with = ['getsat'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getResourceUrlAttribute()
    {
        return url('/admin/users/' . $this->getKey());
    }

    public function getsat()
    {
        return $this->hasOne('App\Models\Sat', 'NucCod', 'sat_ruc');
    }

    /**
     * Método que Laravel invoca automáticamente para enviar el email de reset.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->sendUserPasswordResetNotification($token);
    }

    /**
     * Método personalizado para enviar la notificación de restablecer contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendUserPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
