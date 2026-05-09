<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;

class Subscription extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $fillable = [
        'name',
        'amount',
        'description',
        'validity',
        'type',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Get books list
     */
    public function scopeGetSubscriptions($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Subscription::select('subscriptions.id', 'subscriptions.name', 'subscriptions.amount', 'subscriptions.validity', 'subscriptions.status', 'subscriptions.type', 'subscriptions.created_at')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(subscriptions.name) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "subscriptions.name",
                "subscriptions.amount",
                "subscriptions.validity",
                "subscriptions.type",
                "subscriptions.created_at",
                "subscriptions.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('subscriptions.id', 'desc');
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

    /**
     * Save New Book
     */
    public function scopeSaveSubscription($model, $request)
    {
        // dd($request->all());
        // dd(storage_path('/'));
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $data = [
            'name' => $requestArray['name'],
            'amount' => $requestArray['amount'],
            'description' => $requestArray['description'] ?? null,
            'validity' => $requestArray['validity'] ?? null,
            'type' => $requestArray['type'] ?? null,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];

        $subscription = $this->create($data);

        return $subscription;
    }

    /**
     * Update user
     */
    public function scopeUpdateSubscription($model, $request)
    {
        $authUser = auth()->user();
        $subscription = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get User
        $subscriptionData = Subscription::where('id', $requestArray['subscription_id'])->first();

        if(!empty($subscriptionData))
        {
            // Prepare data
            $data = [
                'name' => $requestArray['name'],
                'amount' => $requestArray['amount'],
                'description' => $requestArray['description'] ?? null,
                'validity' => $requestArray['validity'],
                'type' => $requestArray['type'] ?? null,
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $subscription = $subscriptionData->update($data);
        }

        return $subscription;
    }
}
