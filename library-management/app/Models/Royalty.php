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
use Illuminate\Support\Facades\DB;

class Royalty extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $fillable = [
        'order_id',
        'book_id',
        'author_id',
        'book_details',
        'author_royalty',
        'app_royalty',
        'created_by',
        'updated_by'
    ];

    /**
     * Orders relation
     */
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Orders relation
     */
    public function book(){
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }

    /**
     * Get royalties list
     */
    /* public function scopeGetRoyalty($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Royalty::select('royalties.id', 'royalties.order_id', 'royalties.book_id', 'royalties.author_id', 'royalties.book_details', 'royalties.author_royalty', 'royalties.app_royalty', 'royalties.created_at', 'royalties.payment_status', 'books.book_name', 'users.name as author_name')
        ->join('book_category_authors', function($join){
            $join->on('book_category_authors.book_id', '=', 'royalties.book_id');
            $join->where('book_category_authors.author_id', 'royalties.author_id');
        })
        ->join('books', 'books.id', '=', 'royalties.book_id')
        ->join('users', 'users.id', '=', 'royalties.author_id')
        ->with([
            'order' => function($query){
                $query->select('id', 'order_number', 'created_at');
            },
            'book' => function($query){
                $query->select('id', 'book_name');
            }
        ])
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
                "orders.order_number",
                "orders.created_at",
                "books.book_name",
                "users.name",
                "",
                "royalties.payment_status"
            );

            $records->join('orders', function($join){
                $join->on('orders.id', '=', 'royalties.order_id');
            });

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }

            // $records->groupBy('book_category_authors.author_id', 'royalties.order_id', 'royalties.id');
        }
        else
        {
            $records->orderBy('royalties.id', 'desc');
            // $records->groupBy('book_category_authors.author_id', 'royalties.order_id', 'royalties.id');
        }

        // Set final limit and records
        if(!empty($limit))
        {
            dd($records->get(), $records->get()->count());
            $records = $records->skip($offset)->take($limit);
            return $records->get();
        }
        else
        {
            return $records->get()->count();
        }
    } */

    public function scopeGetRoyalty($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Royalty::select('royalties.id', 'royalties.order_id', 'royalties.book_id', 'royalties.author_id', 'royalties.book_details', 'royalties.author_royalty', 'royalties.app_royalty', 'royalties.created_at', 'royalties.payment_status', 'books.book_name', 'users.name as author_name')
        ->join('books', 'books.id', '=', 'royalties.book_id')
        ->join('users', 'users.id', '=', 'royalties.author_id')
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(books.book_name) LIKE \'%'.$search.'%\' OR lower(users.name) LIKE \'%'.$search.'%\' )');
            }

            if(!(empty($filter)))
            {
                if(isset($filter['filter_book_name']) && !empty($filter['filter_book_name'])){
                    $query->where('books.book_name', $filter['filter_book_name']);
                }

                if(isset($filter['filter_author_id']) && !empty($filter['filter_author_id'])){
                    $query->where('royalties.author_id', $filter['filter_author_id']);
                }
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "",
                "orders.order_number",
                "orders.created_at",
                "books.book_name",
                "users.name",
                "",
                "royalties.payment_status"
            );

            $records->join('orders', function($join){
                $join->on('orders.id', '=', 'royalties.order_id');
            });

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }

            $records->groupBy('royalties.order_id', 'royalties.id');
        }
        else
        {
            $records->orderBy('royalties.id', 'desc');
            $records->groupBy('royalties.order_id', 'royalties.id');
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
     * Get royalties calculation list
     */
    /* public function scopeGetRoyaltyCalculation($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Royalty::select('royalties.id', 'royalties.order_id', 'royalties.book_id', 'royalties.book_details', 'royalties.author_royalty', 'royalties.created_at', DB::raw('sum(royalties.author_royalty) as author_royalty'), DB::raw('count(distinct royalties.order_id) as order_count'), DB::raw('sum(royalties.author_royalty) + royalties.app_royalty as total_royalty'), DB::raw('(select sum(t.app_royalty) from (select sum(distinct royalties.app_royalty) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id) as t) as app_royalty'), 'books.book_name', 'books.price', 'books.book_pdf')
        // ->join('book_category_authors', 'book_category_authors.book_id', '=', 'royalties.book_id')
        ->join('books', 'books.id', '=', 'royalties.book_id')
        // ->join('users', 'users.id', '=', 'book_category_authors.author_id')
        ->with([
            'order' => function($query){
                $query->select('id', 'order_number', 'created_at');
            },
            'book' => function($query){
                $query->select('id', 'book_name', 'price');
            }
        ])
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
            $records->orderBy('royalties.id', 'desc');
            $records->groupBy('royalties.book_id');
        }

        // Set final limit and records
        if(!empty($limit))
        {
            dd($records->dump());
            $records = $records->skip($offset)->take($limit);
            return $records->get();
        }
        else
        {
            return $records->get()->count();
        }
    } */

    public function _scopeGetRoyaltyCalculation($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Royalty::select('royalties.id', 
        DB::raw('count(order_id) as order_count'), 
        DB::raw('sum(author_royalty) + (select sum(app_royalty) from (select r.book_id, sum(distinct app_royalty) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id, r.book_id  ) as t) as total_royalty'), 
        DB::raw('sum(author_royalty) as author_royalty'), 
        DB::raw('(select sum(app_royalty) from (select r.book_id, sum(distinct app_royalty) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id, r.book_id  ) as t) as app_royalty'), 'books.book_name', 'books.price', 'books.book_pdf')
        // ->join('book_category_authors', 'book_category_authors.book_id', '=', 'royalties.book_id')
        ->join('books', 'books.id', '=', 'royalties.book_id')
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
                "books.book_name",
                DB::raw('count(order_id)'),
                "books.price",
                DB::raw('sum(author_royalty) + (select sum(app_royalty) from (select r.book_id, sum(distinct app_royalty) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id, r.book_id  ) as t)'),
                DB::raw('sum(author_royalty)'),
                DB::raw('(select sum(app_royalty) from (select r.book_id, sum(distinct app_royalty) as app_royalty from royalties as r where r.book_id = royalties.book_id group by r.order_id, r.book_id  ) as t)'),
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('royalties.id', 'desc');
        }

        $records->groupBy('royalties.book_id');

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

    public function scopeGetRoyaltyCalculation($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Royalty::select('royalties.id', 
        DB::raw('count(distinct royalties.order_id) as order_count'), 
        DB::raw('sum(royalties.author_royalty) + (sum(distinct app_royalty) * count(distinct order_id)) as total_royalty'), 
        DB::raw('sum(royalties.author_royalty) as author_royalty'), 
        DB::raw('(sum(distinct app_royalty) * count(distinct order_id)) as app_royalty'), 'books.book_name', 'books.price', 'books.book_pdf')
        ->join('books', 'books.id', '=', 'royalties.book_id')
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
                "books.book_name",
                "order_count",
                "books.price",
                "total_royalty",
                "author_royalty",
                "app_royalty",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('royalties.id', 'desc');
        }

        $records->groupBy('royalties.book_id');

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
            'author_name' => $requestArray['author_name'],
            'launch_date' => $launchDate,
            'book_type' => $requestArray['book_type'],
            'original_price' => $requestArray['original_price'],
            'price' => $requestArray['price'],
            'description' => $requestArray['description'] ?? null,
            'book_category' => $requestArray['book_category'] ?? null,
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
                'author_name' => $requestArray['author_name'],
                'launch_date' => $launchDate,
                'book_type' => $requestArray['book_type'],
                'original_price' => $requestArray['original_price'],
                'price' => $requestArray['price'],
                'description' => $requestArray['description'] ?? null,
                'book_category' => $requestArray['book_category'] ?? null,
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

        return $book;
    }
}
