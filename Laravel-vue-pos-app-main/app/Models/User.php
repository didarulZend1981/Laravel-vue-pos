<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'mobile', 'address', 'otp', 'image'];
    protected $hidden = ['password', 'otp'];
    protected $attributes = [
        'otp' => 0,
    ];
}
