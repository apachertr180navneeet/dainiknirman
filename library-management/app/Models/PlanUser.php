<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PlanUser extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'subscription_amount',
        'subscription_details',
        'start_date',
        'end_date',
        'order_number',
        'razorpay_order_id',
        'payment_details',
        'payment_gateway',
        'transaction_status',
        'payment_mode',
        'payment_gateway_response',
        'created_by',
        'updated_by',
    ];

    public function plan(){
        return $this->belongsTo(Plan::class, 'subscription_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get payments list
     */
    public function scopeGetPayments($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = $model->select('plan_users.id', 'plan_users.user_id', 'plan_users.subscription_id', 'plan_users.subscription_amount', 'plan_users.start_date', 'plan_users.end_date', 'plan_users.order_number', 'plan_users.transaction_status', 'plan_users.payment_mode', 'plan_users.created_at', 'users.name as user_name', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(plan_users.subscription_details, "$.name")) as subscription_name'))
        ->join('users', function($join){
            $join->on('users.id', '=', 'plan_users.user_id');
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('(
                lower(JSON_EXTRACT(plan_users.subscription_details, "$.name")) LIKE \'%'.$search.'%\' OR 
                lower(users.name) LIKE \'%'.$search.'%\' OR 
                plan_users.order_number LIKE \'%'.$search.'%\')');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] >= 0) || !empty($search))
        {
            $arr_fields = array(
                "plan_users.order_number",
                "subscription_name",
                "users.name",
                "plan_users.start_date",
                "plan_users.end_date",
                "plan_users.payment_mode",
                "plan_users.transaction_status",
                "plan_users.created_at"
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('plan_users.id', 'desc');
        }

        // Set final limit and records
        if(!empty($limit))
        {
            $records = $records->skip($offset)->take($limit);
            return $records->get();
        }
        else
        {
            return $records->get()->count();
        }
    }
}
