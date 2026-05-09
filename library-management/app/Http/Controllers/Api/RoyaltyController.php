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
use App\Models\Royalty;

class RoyaltyController extends Controller
{
    /**
     * Get all royalties
     */
    public function getRoyaltyData(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Record not found.";
        $data = null;
        $statusCode = 200;
        
        try {
            // $royalty = Royalty::selectRaw(DB::raw("sum(if(payment_status = 'PROCESS', author_royalty, 0)) as paid_royalty, sum(if(payment_status = 'PENDING', author_royalty, 0)) as pending_royalty, sum(author_royalty) as total_royalty"))
            // ->join('book_category_authors', function($join) use($user){
            //     $join->on('book_category_authors.book_id', '=', 'royalties.book_id');
            //     $join->where('book_category_authors.author_id', $user->id);
            // })
            // ->first();

            $royalty = Royalty::selectRaw(DB::raw("sum(if(payment_status = 'PROCESS', author_royalty, 0)) as paid_royalty, sum(if(payment_status = 'PENDING', author_royalty, 0)) as pending_royalty, sum(author_royalty) as total_royalty"))
            ->where('royalties.author_id', $user->id)
            ->first();

            if(!empty($royalty)){
                $status = true;
                $message = "Record found successfully.";
                $data = [
                    'total' => $royalty->total_royalty,
                    'pending' => $royalty->pending_royalty,
                    'process' => $royalty->paid_royalty,
                ];
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
