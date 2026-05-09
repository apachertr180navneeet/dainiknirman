<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;

class Anthology extends Model
{
    use SoftDeletes, Statusable, StatusToggleable;

    protected $fillable = [
        'title',
        'author_name',
        'description',
        'is_selected',
        'is_accept_terms',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeGetAnthologies($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Anthology::select('anthologies.id', 'anthologies.title', 'anthologies.author_name', 'anthologies.is_selected', 'anthologies.is_accept_terms', 'anthologies.status', 'anthologies.created_at')
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(anthologies.title) LIKE \'%'.$search.'%\' OR lower(anthologies.author_name) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] >= 0) || !empty($search))
        {
            $arr_fields = array(
                "",
                "anthologies.title",
                "anthologies.author_name",
                "anthologies.is_accept_terms",
                "anthologies.is_selected",
                "anthologies.created_at",
                "anthologies.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }

            $records->groupBy('anthologies.id');
        }
        else
        {
            $records->groupBy('anthologies.id');
            $records->orderBy('anthologies.id', 'desc');
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
     * Update Anthology
     */
    public function scopeUpdateAnthology($model, $request)
    {
        $authUser = auth()->user();
        $anthology = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get Anthology
        $anthologyData = Anthology::where('id', $requestArray['anthology_id'])->first();

        if(!empty($anthologyData))
        {
            // Prepare data
            $data = [
                'title' => $requestArray['title'],
                'author_name' => $requestArray['author_name'],
                'is_selected' => $requestArray['is_selected'],
                'description' => $requestArray['description'],
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $anthology = $anthologyData->update($data);
        }

        return $anthology;
    }
}
