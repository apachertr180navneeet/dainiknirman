<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;
use Illuminate\Support\Facades\Storage;

class ContestAuthor extends Model
{
    use SoftDeletes, Statusable, StatusToggleable;

    protected $fillable = [
        'contest_id',
        'contest_title',
        'contest_date',
        'contest_description',
        'title',
        'date',
        'description',
        'remark',
        'is_accept_terms',
        'rank',
        'admin_remark',
        'status',
        'created_by',
        'updated_by'
    ];

    function author(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get contest authors list
     */
    public function scopeGetContestAuthors($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array(), $contestId)
    {
        $records = ContestAuthor::select('contest_authors.id', 'contest_authors.contest_id', 'contest_authors.contest_title', 'contest_authors.contest_date', 'contest_authors.contest_description', 'contest_authors.title', 'contest_authors.description', 'contest_authors.remark', 'contest_authors.rank', 'contest_authors.status', 'contest_authors.created_at', 'contest_authors.created_by', 'users.name as author_name')
        ->where('contest_id', $contestId)
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(contest_authors.contest_title) LIKE \'%'.$search.'%\' )');
            }
        })
        ->join("users", "users.id", "=", "contest_authors.created_by");
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "contest_authors.contest_title",
                "contest_authors.title",
                "contest_authors.date",
                "users.name",
                "contest_authors.rank",
                "contest_authors.created_at",
                "contest_authors.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('contest_authors.id', 'desc');
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
     * Update contest author
     */
    public function scopeUpdateContestAuthor($model, $request)
    {
        $authUser = auth()->user();
        $contestAuthor = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get Contest
        $contestAuthorData = ContestAuthor::where('id', $requestArray['contest_author_id'])->first();

        if(!empty($contestAuthorData) && empty($contestAuthorData->rank))
        {
            // Prepare data
            $data = [
                'rank' => $requestArray['rank'],
                'admin_remark' => $requestArray['admin_remark'] ?? null,
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $contestAuthor = $contestAuthorData->update($data);
        }

        return $contestAuthor;
    }
}
