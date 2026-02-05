<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use  HasApiTokens,Notifiable,HasFactory;

   protected $fillable = [
    'email',
    'password',
    'email_verified',
    'email_verified_at',
        'fcm_token',

];


    protected $hidden = [
        'password',
    ];
}
