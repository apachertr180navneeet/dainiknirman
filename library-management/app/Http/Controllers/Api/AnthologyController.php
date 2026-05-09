<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Anthology;

class AnthologyController extends Controller
{
    /**
     * Save anthology
     */
    public function saveAnthology(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Some error occurred, contest cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'title' => 'required|unique:anthologies,title,NULL,NULL,deleted_at,NULL',
                    'author_name' => 'required|max:100',
                    'description' => 'required|max:5000',
                    'is_accept_terms' => 'required|in:1',
                ],
                [
                    'title.required' => 'Title is required field.',
                    'title.unique' => 'Title must be unique.',
                    'author_name.required' => 'Author name is required field.',
                    'author_name.max' => 'Author name cannot be more than 100 characters.',
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

            // Save anthology submit by author
            $anthologyData = [
                'title' => $request->title,
                'author_name' => $request->author_name,
                'description' => $request->description,
                'is_accept_terms' => $request->is_accept_terms,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Anthology Create
            $anthology = Anthology::create($anthologyData);
            
            if(!empty($anthology)){
                $status = true;
                $message = "Anthology saved successfully.";
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
