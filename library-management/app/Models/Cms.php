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

class Cms extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $table = "cms";

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Get cms list
     */
    public function scopeGetCms($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Cms::select('cms.id', 'cms.title', 'cms.slug', 'cms.status', 'cms.created_at')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(cms.title) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "cms.title",
                "cms.created_at",
                "cms.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('cms.id', 'desc');
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
     * Save New Cms
     */
    public function scopeSaveCms($model, $request)
    {
        dd($request->all());
        // dd(storage_path('/'));
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $date = null;

        $data = [
            'title' => $requestArray['title'],
            'description' => $requestArray['description'] ?? null,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];

        $cms = $this->create($data);

        return $cms;
    }

    /**
     * Update cms
     */
    public function scopeUpdateCms($model, $request)
    {
        $authUser = auth()->user();
        $cms = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get Cms
        $cmsData = Cms::where('id', $requestArray['cms_id'])->first();

        if(!empty($cmsData))
        {
            // Prepare data
            $data = [
                'description' => !empty($requestArray['description']) ? htmlspecialchars($requestArray['description']) : null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $cms = $cmsData->update($data);
        }

        return $cms;
    }
}
