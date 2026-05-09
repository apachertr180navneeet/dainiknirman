<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;
use App\Models\User;
use App\Models\AuthorEbook;
use App\Models\Book;
use App\Models\BookFavourite;

class BookController extends Controller
{
    use UploadImage, UploadFile;

    /**
     * Upload ebook
     */
    public function eBookUpload(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Ebook cannot be uploaded.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'book_title' => 'required|unique:author_ebooks,title,NULL,NULL,deleted_at,NULL',
                    'author_name' => 'required',
                    'ebook_pdf' => 'required|file|max:'.(5*1024).'|mimes:pdf',
                ],
                [
                    'book_title.required' => 'Title is required field.',
                    'book_title.unique' => 'Book title must be unique.',
                    'author_name.required' => 'Author name is required field.',
                    'ebook_pdf.required' => 'Ebook file is required field.',
                    'ebook_pdf.file' => 'Ebook must be a valid PDF file.',
                    'ebook_pdf.max' => 'Ebook file cannot be more than 5MB.',
                    'ebook_pdf.mimes' => 'Ebook must be a valid PDF file.',
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $ebookData = [
                'title' => $request->book_title,
                'author_name' => $request->author_name,
                'description' => $request->description,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Upload slider image and add to data array
            if ($request->hasFile('ebook_pdf'))
            {
                if(!file_exists(storage_path("app/public/ebook/user/".$user->id))){
                    mkdir(storage_path("app/public/ebook/user/".$user->id), 0777, true);
                }
                else
                {
                    chmod(storage_path("app/public/ebook/user/".$user->id), 0777);
                }

                $customFileName = $request->file('ebook_pdf')->getClientOriginalName();
                $customFileName = pathinfo($customFileName)['filename'];
                $file = $this->uploadFile($request->file('ebook_pdf'), "ebook/user/".$user->id."/", $customFileName);

                if ($file['_status']) 
                {
                    $fileName = $file['_data'];
                    $ebookData['file_name'] = $fileName;
                }
            }

            // Ebook Create
            $data['ebook'] = AuthorEbook::create($ebookData);
            $data['file_path'] = Storage::disk('local')->url('ebook/user/'.$user->id);

            $status = true;
            $message = "Ebook uploaded successfully.";

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get all published books
     */
    public function getBooks(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Books not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $data['books'] = [];

            $books = Book::select("id", "book_name", DB::raw("(select group_concat(users.name separator ', ') as authors from book_category_authors inner join users on users.id = author_id where book_id = books.id) as author_name"), "date", "book_type", "original_price", "price", "cover_picture", "book_pdf", "description", "status", DB::raw("(select (case when count(id) > 0 then true ELSE false END) as is_fav from book_favourite where book_id = books.id and user_id = $user->id) as is_favourite"), DB::raw("(select count(id) from orders where JSON_UNQUOTE(JSON_EXTRACT(order_details, '$.id')) = books.id and user_id = ".$user->id." and transaction_status = 'SUCCESS') as is_book_purchase"));

            // if(!empty($request->book_name)){
            //     $books->whereRaw("books.book_name like '%".$request->book_name."%'");
            // }
            
            // if(!empty($request->author_name)){
            //     $books->whereRaw("books.author_name like '%".$request->author_name."%'");
            // }

            if(!empty($request->is_fav)){
                $books->having("is_favourite", $request->is_fav);
            }

            if(!empty($request->search_keyword)){
                // $books->whereRaw("(books.book_name like '%".$request->search_keyword."%' OR books.author_name like '%".$request->search_keyword."%')");
                
                $books = $books->havingRaw("(books.book_name like '%".$request->search_keyword."%' OR author_name like '%".$request->search_keyword."%')");
            }
            
            if($request->is_library){
                $books = $books->havingRaw("(select count(id) from orders where JSON_UNQUOTE(JSON_EXTRACT(order_details, '$.id')) = books.id and user_id = ".$user->id." and transaction_status = 'SUCCESS') > 0");
                
                
            }

            $books = $books->orderBy("id", "desc")->paginate($limit);

            if($books->count() > 0){
                $status = true;
                $message = "Books found successfully.";
                $data['books'] = $books;
            }

            $data['cover_picture_path'] = Storage::disk("local")->url("book/cover/");
            $data['pdf_path'] = Storage::disk("local")->url("book/pdf/");

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Set book as favourite
     */
    public function markBookFavUnfav(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Book cannot be set as favourite.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'book_id' => 'required|exists:books,id,deleted_at,NULL',
                    'type' => 'nullable|in:F,U'
                ],
                [
                    'book_id.required' => 'Book ID is required field.',
                    'book_id.exists' => 'Book does not exists.',
                    // 'type.required' => 'Type is required field.',
                    'type.in' => 'Type must be in type of Favourite or Unfavourite.'
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $bookFavData = [
                'user_id' => $user->id,
                'book_id' => $request->book_id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Set book as favourite Create
            $isBook = BookFavourite::where('user_id', $user->id)->where("book_id", $request->book_id)->first();

            if(!$isBook){
                BookFavourite::create($bookFavData);

                $status = true;
                $message = "Book set to favourite successfully.";
            }
            else{
                // BookFavourite::where("user_id", $user->id)->where("book_id", $request->book_id)->delete();
                $isBook->delete();

                $status = true;
                $message = "Book removed from favourite successfully.";
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get all published books
     */
    public function getMyFavouriteBooks(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Books not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $data['books'] = [];

            $books = BookFavourite::select("book_favourite.id", "book_favourite.user_id", "book_favourite.book_id", "book_favourite.user_id", DB::raw("(select count(id) from orders where JSON_UNQUOTE(JSON_EXTRACT(order_details, '$.id')) = book_favourite.book_id and user_id = ".$user->id." and transaction_status = 'SUCCESS') as is_book_purchase"))
            ->with([
                'book' => function($query) use($request, $user){
                    $query->select("books.id", "books.book_name", "books.author_name", "books.date", "books.book_type", "books.original_price", "books.price", "books.cover_picture", "books.book_pdf", "books.description", "books.status", DB::raw("(select count(id) from orders where JSON_UNQUOTE(JSON_EXTRACT(order_details, '$.id')) = books.id and user_id = ".$user->id." and transaction_status = 'SUCCESS') as is_book_purchase"));
                }
            ])
            ->whereHas('book')
            ->where('created_by', $user->id)
            ->orderBy("id", "desc")->paginate($limit);

            if($books->count() > 0){
                $status = true;
                $message = "Books found successfully.";
                $data['books'] = $books;
            }

            $data['cover_picture_path'] = Storage::disk("local")->url("book/cover/");
            $data['pdf_path'] = Storage::disk("local")->url("book/pdf/");

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }
}
