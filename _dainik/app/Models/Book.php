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

class Book extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $fillable = [
        'book_name',
        'author_name',
        'date',
        'launch_date',
        'book_type',
        'price',
        'cover_picture',
        'book_pdf',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Get books list
     */
    public function scopeGetBooks($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Book::select('books.id', 'books.book_name', 'books.author_name', 'books.launch_date', 'books.book_type', 'books.price', 'books.cover_picture', 'books.book_pdf', 'books.status', 'books.created_at')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(books.book_name) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "books.book_name",
                "books.author_name",
                "books.launch_date",
                "books.book_type",
                "books.created_at",
                "books.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('books.id', 'desc');
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
    public function scopeSaveBook($model, $request)
    {
        // dd($request->all());
        // dd(storage_path('/'));
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $launchDate = null;

        if(!empty($requestArray['launch_date']))
        {
            $launchDate = date("Y-m-d", strtotime($requestArray['launch_date']));
        }

        $data = [
            'book_name' => $requestArray['book_name'],
            'author_name' => $requestArray['author_name'],
            'launch_date' => $launchDate,
            'book_type' => $requestArray['book_type'],
            'price' => $requestArray['price'],
            'description' => $requestArray['description'] ?? null,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];

        // Upload image and add to data array
        if ($request->hasFile('cover_picture'))
        {
            if(!file_exists(storage_path("app/public/book/cover/"))){
                mkdir(storage_path("app/public/book/cover/"), 0777, true);
            }
            else
            {
                chmod(storage_path("app/public/book/cover/"), 0777);
            }

            $image = $this->uploadImage($request->file('cover_picture'), "public/book/cover/", 70, null);

            if ($image['_status']) 
            {
                $imageName = $image['_data'];
                $data['cover_picture'] = $imageName;
            }
        }

        // Upload image and add to data array
        if ($request->hasFile('book_pdf'))
        {
            if(!file_exists(storage_path("app/public/book/pdf/"))){
                mkdir(storage_path("app/public/book/pdf/"), 0777, true);
            }
            else
            {
                chmod(storage_path("app/public/book/pdf/"), 0777);
            }

            $file = $this->uploadFile($request->file('book_pdf'), "public/book/pdf/", 70, null);

            if ($file['_status']) 
            {
                $fileName = $file['_data'];
                $data['book_pdf'] = $fileName;
            }
        }

        $book = $this->create($data);

        return $book;
    }

    /**
     * Update user
     */
    public function scopeUpdateBook($model, $request)
    {
        $authUser = auth()->user();
        $book = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get User
        $bookData = Book::where('id', $requestArray['book_id'])->first();

        if(!empty($bookData))
        {
            // Prepare data
            $launchDate = $bookData->launch_date;

            if(!empty($requestArray['launch_date']))
            {
                $launchDate = date("Y-m-d", strtotime($requestArray['launch_date']));
            }

            $data = [
                'book_name' => $requestArray['book_name'],
                'author_name' => $requestArray['author_name'],
                'launch_date' => $launchDate,
                'book_type' => $requestArray['book_type'],
                'price' => $requestArray['price'],
                'description' => $requestArray['description'] ?? null,
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];

            // Upload image and add to data array
            if ($request->hasFile('cover_picture'))
            {
                // Remove old image
                if(!empty($bookData->cover_picture)){
                    $deleted = Storage::disk("public")->delete("book/cover/".$bookData->cover_picture);
                }
                //-----------------

                if(!file_exists(storage_path("app/public/book/cover/"))){
                    mkdir(storage_path("app/public/book/cover/"), 0777, true);
                }
                else
                {
                    chmod(storage_path("app/public/book/cover/"), 0777);
                }

                $image = $this->uploadImage($request->file('cover_picture'), "book/cover/", 70, null);

                if ($image['_status']) 
                {
                    $imageName = $image['_data'];
                    $data['cover_picture'] = $imageName;
                }
            }

            // Upload image and add to data array
            if ($request->hasFile('book_pdf'))
            {
                // Remove old file
                if(!empty($bookData->book_pdf)){
                    $deleted = Storage::disk("public")->delete("book/pdf/".$bookData->book_pdf);
                }
                //-----------------

                if(!file_exists(storage_path("app/public/book/pdf/"))){
                    mkdir(storage_path("app/public/book/pdf/"), 0777, true);
                }
                else
                {
                    chmod(storage_path("app/public/book/pdf/"), 0777);
                }

                $file = $this->uploadFile($request->file('book_pdf'), "book/pdf/", null, null);

                if ($file['_status']) 
                {
                    $fileName = $file['_data'];
                    $data['book_pdf'] = $fileName;
                }
            }

            $user = $bookData->update($data);
        }

        return $book;
    }
}
