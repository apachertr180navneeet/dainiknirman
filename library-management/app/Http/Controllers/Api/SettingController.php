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

class SettingController extends Controller
{
    use UploadImage, UploadFile;

    /**
     * Get payment settings
     */
    public function getPaymentSetting()
    {
        $user = Auth::user();
        $status = false;
        $message = "Settings not found.";
        $data = [];
        $razorpayKey = null;
        $razorpaySecret = null;
        $statusCode = 200;

        try {
            $razorpayActiveMode = getSettingBySlug('razorpay-active-mode');

            if(!empty($razorpayActiveMode)){
                if($razorpayActiveMode->value == 'test'){
                    $razorpayKey = getSettingBySlug('razorpay-test-key');
                    $razorpaySecret = getSettingBySlug('razorpay-test-secret');
                }
                else if($razorpayActiveMode->value == 'live'){
                    $razorpayKey = getSettingBySlug('razorpay-live-key');
                    $razorpaySecret = getSettingBySlug('razorpay-live-secret');
                }

                if(!empty($razorpayKey) && !empty($razorpaySecret)){
                    $status = true;
                    $message = "Settings found successfully.";
                    $data = [
                        'razorpayActiveMode' => $razorpayActiveMode->value,
                        'razorpayKey' => $razorpayKey->value,
                        'razorpaySecret' => $razorpaySecret->value
                    ];
                }
            }
        } 
        catch (\Exception $e) {
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
