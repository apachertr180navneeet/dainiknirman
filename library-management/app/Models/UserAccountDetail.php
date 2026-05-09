<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountDetail extends Model
{
    protected $table = "user_account_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'city_name'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
