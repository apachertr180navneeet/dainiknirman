<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;
use Illuminate\Support\Facades\Storage;

class Contest extends Model
{
    use SoftDeletes, Statusable, StatusToggleable;

    protected $fillable = [
        'title',
        'date',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Get contests list
     */
    public function scopeGetContests($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Contest::select('contests.id', 'contests.title', 'contests.date', 'contests.status', 'contests.created_at')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(contests.title) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "contests.title",
                "contests.date",
                "contests.created_at",
                "contests.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('contests.id', 'desc');
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
     * Save New Contest
     */
    public function scopeSaveContest($model, $request)
    {
        // dd($request->all());
        // dd(storage_path('/'));
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $publishDate = null;

        if(!empty($requestArray['date']))
        {
            $publishDate = date("Y-m-d", strtotime($requestArray['date']));
        }

        $data = [
            'title' => $requestArray['title'],
            'description' => $requestArray['description'],
            'date' => $publishDate,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];

        $contest = $this->create($data);

        return $contest;
    }

    /**
     * Update contest
     */
    public function scopeUpdateContest($model, $request)
    {
        $authUser = auth()->user();
        $contest = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get Contest
        $contestData = Contest::where('id', $requestArray['contest_id'])->first();

        if(!empty($contestData))
        {
            // Prepare data
            $publishDate = $contestData->date;

            if(!empty($requestArray['date']))
            {
                $publishDate = date("Y-m-d", strtotime($requestArray['date']));
            }

            $data = [
                'title' => $requestArray['title'],
                'date' => $publishDate,
                'description' => $requestArray['description'] ?? null,
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $contest = $contestData->update($data);
        }

        return $contest;
    }
}
