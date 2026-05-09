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

class AuthorEbook extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $fillable = [
        'title',
        'author_name',
        'description',
        'file_name',
        'publish_date',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Get ebooks list
     */
    public function scopeGetAuthorEbooks($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = AuthorEbook::select('author_ebooks.id', 'author_ebooks.title', 'author_ebooks.author_name', 'author_ebooks.status', 'author_ebooks.created_at', 'author_ebooks.publish_date')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(author_ebooks.title) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "author_ebooks.title",
                "author_ebooks.author_name",
                "author_ebooks.created_at",
                "author_ebooks.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('author_ebooks.id', 'desc');
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
     * Update ebook
     */
    public function scopeUpdateBook($model, $request)
    {
        $authUser = auth()->user();
        $book = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get User
        $bookData = AuthorEbook::where('id', $requestArray['ebook_id'])->first();

        if(!empty($bookData))
        {
            // Prepare data
            $launchDate = $bookData->publish_date;

            if(!empty($requestArray['publish_date']))
            {
                $launchDate = date("Y-m-d", strtotime($requestArray['publish_date']));
            }

            $data = [
                // 'title' => $requestArray['title'],
                // 'author_name' => $requestArray['author_name'],
                'publish_date' => $launchDate,
                // 'description' => $requestArray['description'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            $book = $bookData->update($data);
        }

        return $book;
    }
}
