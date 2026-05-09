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
use DB;

class Book extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $fillable = [
        'book_name',
        'author_name',
        'date',
        'launch_date',
        'book_type',
        'original_price',
        'price',
        'cover_picture',
        'book_pdf',
        'description',
        'status',
        'book_category',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_favourite' => 'boolean',
    ];

    /**
     * Orders relation
     */
    public function orderDetails(){
        return $this->hasMany(OrderDetail::class, 'type_id', 'id')->where('type', 'BOOK');
    }

    /**
     * Orders relation
     */
    public function bookCategoryAuthor(){
        return $this->hasMany(BookCategoryAuthor::class, 'book_id', 'id');
    }

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
        $bookCategoryAuthors = null;

        if(!empty($requestArray['launch_date']))
        {
            $launchDate = date("Y-m-d", strtotime($requestArray['launch_date']));
        }

        $data = [
            'book_name' => $requestArray['book_name'],
            'author_name' => null,
            'launch_date' => $launchDate,
            'book_type' => $requestArray['book_type'],
            'original_price' => $requestArray['original_price'] ?? null,
            'price' => $requestArray['price'] ?? null,
            'description' => $requestArray['description'] ?? null,
            'book_category' => $requestArray['category_type'] ?? null,
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
            if(!file_exists(storage_path("app/public/book/pdf/"))){
                mkdir(storage_path("app/public/book/pdf/"), 0777, true);
            }
            else
            {
                chmod(storage_path("app/public/book/pdf/"), 0777);
            }

            $file = $this->uploadFile($request->file('book_pdf'), "book/pdf/", null);

            if ($file['_status']) 
            {
                $fileName = $file['_data'];
                $data['book_pdf'] = $fileName;
            }
        }

        $book = $this->create($data);

        // If book category authors get
        if(isset($requestArray['author_id'])){
            if(is_array($requestArray['author_id']) && !empty($requestArray['author_id']))
            {
                foreach ($requestArray['author_id'] as $key => $value) {
                    $bookCategoryAuthors = [
                        'book_id' => $book->id,
                        'author_id' => $value,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_by' => $authUser->id,
                        'updated_by' => $authUser->id
                    ];

                    BookCategoryAuthor::updateOrCreate(
                        [
                            'book_id' => $book->id,
                            'author_id' => $value,
                        ],
                        $bookCategoryAuthors
                    );
                }
            }
            else
            {
                $bookCategoryAuthors = [
                    'book_id' => $book->id,
                    'author_id' => $requestArray['author_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => $authUser->id,
                    'updated_by' => $authUser->id
                ];

                BookCategoryAuthor::updateOrCreate(
                    [
                        'book_id' => $book->id,
                        'author_id' => $requestArray['author_id'],
                    ],
                    $bookCategoryAuthors
                );
            }
        }

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
            $bookCategoryAuthors = null;

            if(!empty($requestArray['launch_date']))
            {
                $launchDate = date("Y-m-d", strtotime($requestArray['launch_date']));
            }

            $data = [
                'book_name' => $requestArray['book_name'],
                'author_name' => null,
                'launch_date' => $launchDate,
                'book_type' => $requestArray['book_type'],
                'original_price' => $requestArray['original_price'] ?? null,
                'price' => $requestArray['price'] ?? null,
                'description' => $requestArray['description'] ?? null,
                'book_category' => $requestArray['category_type'] ?? $bookData->book_category,
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

                $file = $this->uploadFile($request->file('book_pdf'), "book/pdf/", null);

                if ($file['_status']) 
                {
                    $fileName = $file['_data'];
                    $data['book_pdf'] = $fileName;
                }
            }

            $user = $bookData->update($data);

            // If book category authors get
            if(isset($requestArray['author_id'])){
                if(is_array($requestArray['author_id']) && !empty($requestArray['author_id']))
                {
                    BookCategoryAuthor::where('book_id', $bookData->id)
                    ->whereNotIn('author_id', $requestArray['author_id'])
                    ->delete();
                    
                    foreach ($requestArray['author_id'] as $key => $value) {
                        $bookCategoryAuthors = [
                            'book_id' => $bookData->id,
                            'author_id' => $value,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_by' => $authUser->id,
                            'updated_by' => $authUser->id
                        ];

                        BookCategoryAuthor::updateOrCreate(
                            [
                                'book_id' => $bookData->id,
                                'author_id' => $value,
                            ],
                            $bookCategoryAuthors
                        );
                    }
                }
                else
                {
                    BookCategoryAuthor::where('book_id', $bookData->id)
                    ->whereNotIn('author_id', [$requestArray['author_id']])
                    ->delete();

                    $bookCategoryAuthors = [
                        'book_id' => $bookData->id,
                        'author_id' => $requestArray['author_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_by' => $authUser->id,
                        'updated_by' => $authUser->id
                    ];

                    BookCategoryAuthor::updateOrCreate(
                        [
                            'book_id' => $bookData->id,
                            'author_id' => $requestArray['author_id'],
                        ],
                        $bookCategoryAuthors
                    );
                }
            }
            
        }

        return $book;
    }

    /**
     * Get anthology writeups list
     */
    public function scopeGetAnthologyWriteups($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Book::select('books.id', 'books.book_name', 'books.author_name', 'books.launch_date', 'books.book_type', 'books.book_category', 'books.price', 'books.cover_picture', 'books.book_pdf', 'books.status', 'books.created_at', DB::raw("sum(
        CASE 
        when books.book_category = 'ANTHOLOGY' THEN royalties.author_royalty
        when books.book_category = 'SINGLE_AUTHOR' THEN royalties.author_royalty
        when books.book_category = 'NATIVE' THEN royalties.author_royalty
        ELSE 0
        END
        ) AS author_royalty,
        (select sum(app_royalty) from (select r.book_id, sum(distinct CASE 
        when books.book_category = 'ANTHOLOGY' THEN royalties.app_royalty
        when books.book_category = 'SINGLE_AUTHOR' THEN royalties.app_royalty
        when books.book_category = 'NATIVE' THEN royalties.app_royalty
        ELSE 0
        END) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id, r.book_id  ) as t
        
        ) AS app_royalty"))
        ->leftJoin('royalties', function($join){
            $join->on("royalties.book_id", "=", "books.id");
        })
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
        if((!(empty($sort)) && $sort['column'] >= 0) || !empty($search))
        {
            $arr_fields = array(
                // "", 
                "books.book_name",
                "books.book_category",
                "books.price",
                "books.launch_date",
                "author_royalty",
                "app_royalty"
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }

            $records->groupBy('books.id');
        }
        else
        {
            $records->groupBy('books.id');
            $records->orderBy('books.id', 'desc');
        }

        // Set final limit and records
        if(!empty($limit))
        {
            // dd($records->dump());
            $records = $records->skip($offset)->take($limit);
            return $records->get();
        }
        else
        {
            return $records->get()->count();
        }
    }
}
