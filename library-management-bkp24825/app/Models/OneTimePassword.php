<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneTimePassword extends Model
{
    protected $table = 'one_time_passwords';

    protected $fillable = [
        'user_id',
        'one_time_password',
        'email',
        'mobile_number',
        'request_token',
        'type',
        'expires_at'
    ];
}
