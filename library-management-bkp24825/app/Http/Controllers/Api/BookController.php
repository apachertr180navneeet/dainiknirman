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
}
