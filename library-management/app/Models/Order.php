<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'order_details',
        'start_date',
        'end_date',
        'total_items',
        'amount',
        'payment_mode',
        'razorpay_order_id',
        'payment_details',
        'payment_gateway',
        'transaction_status',
        'created_by',
        'updated_by',
    ];

    public function detail(){
        return $this->hasOne(OrderDetail::class, 'order_id', 'id');
    }

    /**
     * Get orders list
     */
    public function scopeGetOrders($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = $model->select('orders.id', 'orders.user_id', 'orders.order_number', 'od.amount', DB::raw('((od.amount*80)/100) as royalty_amount'), DB::raw('((od.amount*20)/100) as admin_amount'), 'orders.payment_mode', 'orders.transaction_status', 'orders.created_at', 'od.id as detail_id', 'od.order_id', 'od.type', 'od.type_id', 'users.name as user_name', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(od.item_details, "$.book_name")) as book_name'))
        ->join('order_details as od', function($join){
            $join->on('od.order_id', '=', 'orders.id');
        })
        ->join('users', function($join){
            $join->on('users.id', '=', 'orders.user_id');
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('(
                lower(JSON_EXTRACT(od.item_details, "$.book_name")) LIKE \'%'.$search.'%\' OR 
                lower(users.name) LIKE \'%'.$search.'%\' OR 
                orders.order_number LIKE \'%'.$search.'%\')');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] >= 0) || !empty($search))
        {
            $arr_fields = array(
                "orders.order_number",
                "book_name",
                "users.name",
                "royalty_amount",
                "admin_amount",
                "od.amount",
                "orders.created_at"
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('orders.id', 'desc');
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
