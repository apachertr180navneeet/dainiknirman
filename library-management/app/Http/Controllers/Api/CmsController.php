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
use App\Models\Cms;

class CmsController extends Controller
{
    /**
     * Get cms
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Books not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'type' => 'required|exists:cms,slug,deleted_at,NULL'
                ],
                [
                    'type.required' => 'CMS type is required.',
                    'type.exists' => 'Invalid cms type or does not exists.'
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

            $data['books'] = [];

            $cms = Cms::select("id", "title", "slug", "description")
            ->where(function($query) use($request){
                if(!empty($request->type)){
                    $query->where("cms.slug", $request->type);
                }
            })
            ->first();

            if(!empty($cms)){
                $status = true;
                $message = "Data found successfully.";
                $data = $cms;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            
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
