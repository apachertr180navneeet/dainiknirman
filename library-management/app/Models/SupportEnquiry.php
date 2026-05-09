<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;

class SupportEnquiry extends Model
{
    use SoftDeletes, Statusable, StatusToggleable;

    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
        'created_by',
        'updated_by'
    ];
}
