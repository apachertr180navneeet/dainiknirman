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
use App\Models\Magazine;

class MagazineController extends Controller
{
    use UploadImage, UploadFile;

    /**
     * Magazine screen api for daily and monthly types
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Magazines not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'type' => 'required|in:D,M',
                    // 'date' => 'required_if:type,D|date_format:d-m-Y'
                    'date' => 'nullable|date_format:d-m-Y'
                ],
                [
                    'type.required' => 'Type is required field.',
                    'type.in' => 'Type must be in daily or monthly.',
                    'date.required_if' => 'Date is required field.',
                    'date.date_format' => 'Please enter valid date format, d-m-Y.'
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

            // Get magazines by type 
            $magazines = Magazine::select('id', 'title', 'type', 'date', 'description', 'cover_picture', 'file_name', 'genre_name', 'author_name', 'mobile_number', 'is_accept_terms')
            ->where(function($query) use($request){
                $query->where('type', $request->type);
            });

            if(!empty($request->search)){
                $magazines = $magazines->whereRaw("magazines.title like '%".$request->search."%'");
            }

            if(!empty($request->type)){
                if($request->type == 'D'){
                    $magazines = $magazines->whereDate('date', '>=', date("Y-m-d", strtotime("-7 days")));
                }
                
                if($request->type == 'M'){
                    $magazines = $magazines->whereDate('date', '>=', date("Y-m-d", strtotime("-12 months")));
                }

                if(!empty($request->date)){
                    $magazines = $magazines->where('date', date("Y-m-d", strtotime($request->date)));
                }
            }

            $magazines = $magazines->orderBy("id", "desc")->paginate($limit);

            if($magazines->count() > 0){
                $status = true;
                $message = "Magazines found successfully.";
                $data['magazines'] = $magazines;
            }

            // File path
            $data['cover_file_path'] = Storage::disk('local')->url('magazine/cover/');
            $data['pdf_file_path'] = Storage::disk('local')->url('magazine/pdf/');

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
     * Save magazine
     */
    public function saveMagazine(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Some error occurred, magazine cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'title' => 'required|unique:magazines,title,NULL,NULL,deleted_at,NULL',
                    'type' => 'required|in:D,M',
                    'genre_name' => 'required|max:255',
                    'author_name' => 'required|max:255',
                    'mobile_number' => 'required|max:20',
                    'description' => 'required|max:5000',
                    'is_accept_terms' => 'required|in:1',
                ],
                [
                    'title.required' => 'Title is required field.',
                    'title.unique' => 'The title is already registered.',
                    'type.required' => 'Type is required field.',
                    'type.in' => 'Type must be Daily or Monthly.',
                    'genre_name.required' => 'Genre name is required field.',
                    'author_name.required' => 'Author name is required field.',
                    'mobile_number.required' => 'Mobile number is required field.',
                    'description.required' => 'Description is required field.',
                    'description.max' => 'Description cannot be more than 5000 characters.',
                    'is_accept_terms.required' => 'Please accept the terms.',
                    'is_accept_terms.in' => 'Please accept the terms.'
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

            // Save
            $magazineData = [
                'title' => $request->title,
                'type' => $request->type,
                'date' => null,
                'genre_name' => $request->genre_name,
                'author_name' => $request->author_name,
                'mobile_number' => $request->mobile_number,
                'is_accept_terms' => $request->is_accept_terms,
                'description' => $request->description,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Magazine author Create
            $magazine = Magazine::create($magazineData);
            
            if(!empty($magazine)){
                $status = true;
                $message = "Magazine saved successfully.";
                $data = $magazine->id;
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
     * Update magazine
     */
    public function updateMagazine(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Some error occurred, magazine cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'magazine_id' => 'required|exists:magazines,id,deleted_at,NULL',
                    'title' => 'required|unique:magazines,title,'.$request->magazine_id.',id,deleted_at,NULL',
                    'type' => 'required|in:D,M',
                    'genre_name' => 'required|max:255',
                    'author_name' => 'required|max:255',
                    'mobile_number' => 'required|max:20',
                    'description' => 'required|max:5000',
                    'is_accept_terms' => 'required|in:1',
                ],
                [
                    'magazine_id.required' => 'Magazine Id is required.',
                    'magazine_id.exists' => 'Magazine not found.',
                    'title.required' => 'Title is required field.',
                    'title.unique' => 'Title must be unique.',
                    'type.required' => 'Type is required field.',
                    'type.in' => 'Type must be Daily or Monthly.',
                    'genre_name.required' => 'Genre name is required field.',
                    'author_name.required' => 'Author name is required field.',
                    'mobile_number.required' => 'Mobile number is required field.',
                    'description.required' => 'Description is required field.',
                    'description.max' => 'Description cannot be more than 5000 characters.',
                    'is_accept_terms.required' => 'Please accept the terms.',
                    'is_accept_terms.in' => 'Please accept the terms.'
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

            $magazineDetail = Magazine::find($request->magazine_id);

            // Save
            $magazineData = [
                'title' => $request->title,
                'type' => $request->type,
                'date' => null,
                'genre_name' => $request->genre_name,
                'author_name' => $request->author_name,
                'mobile_number' => $request->mobile_number,
                'is_accept_terms' => $request->is_accept_terms,
                'description' => $request->description,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Magazine
            $magazine = $magazineDetail->update($magazineData);
            
            if(!empty($magazine)){
                $status = true;
                $message = "Magazine updated successfully.";
                $data = $magazineDetail->id;
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
}
