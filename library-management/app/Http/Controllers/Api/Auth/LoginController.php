<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Http\Requests\CheckLogin;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\OneTimePassword;
use App\Http\Traits\UploadImage;
use App\Models\BookCategoryAuthor;

class LoginController extends Controller
{
    use UploadImage;

    /**
     * Handle an incoming authentication request.
     */
    public function sendOtp(Request $request)
    {
        $status = false;
        $message = "OTP cannot be sent";
        $data = null;
        $statusCode = 200;

        try {
            $userDetails = User::where('mobile', $request['mobile'])
            ->where('role_id', '!=', 1)
            ->whereNotNull('role_id')
            ->first();

            if(!empty($userDetails))
            {
                if($userDetails->status == 0){
                    // Set response
                    $message = __('messages.account_inactive');
                }
                else
                {
                    // Store user
                    $oneTimePassword = OneTimePassword::updateOrCreate([
                        'mobile_number'     => $request['mobile'],
                    ], [
                        'user_id'           => 1,
                        'one_time_password' => rand(1000, 9999),
                        'type'              => 'VERIFICATION',
                        'request_token'     => (string) Str::uuid(),
                        'expires_at'        => Carbon::now()->addMinutes(5)
                    ]);
                    //------------------------

                    // Send SMS
                    if (!empty($oneTimePassword)) {
                        $this->sendSMS($oneTimePassword);
                        $status = true;
                        // $data = $oneTimePassword->one_time_password;
                        $data = null;
                        $message = "OTP sent successfully.";
                    }

                    DB::commit();
                }
            }
        } 
        catch (\Exception $e) {
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
     * Handle an incoming authentication request.
     */
    public function resendOtp(Request $request)
    {
        $status = false;
        $message = "OTP cannot be sent";
        $data = null;
        $statusCode = 200;

        try {
            $userDetails = User::where('mobile', $request['mobile'])->where(function($query){
                $query->where('role_id', '!=', 1);
                $query->orWhereNull('role_id');
            })->first();

            if(!empty($userDetails))
            {
                if($userDetails->status == 0){
                    // Set response
                    $message = __('messages.account_inactive');
                }
                else
                {
                    // Get existing OTP
                    $oneTimePassword = OneTimePassword::where('mobile_number', $request['mobile'])
                    ->whereRaw('date_format(expires_at, "%Y-%m-%d %H:%i") > "'.Carbon::now()->format("Y-m-d H:i").'"')
                    ->where('type', 'VERIFICATION')
                    ->first();

                    // Check if exisiting otp empty, create new one
                    if (empty($oneTimePassword))
                    {
                        $oneTimePassword = OneTimePassword::updateOrCreate([
                            'mobile_number'     => $request['mobile'],
                        ], [
                            'user_id'           => 1,
                            'one_time_password' => rand(1000, 9999),
                            'type'              => 'VERIFICATION',
                            'request_token'     => (string) Str::uuid(),
                            'expires_at'        => Carbon::now()->addMinutes(5)
                        ]);
                        //------------------------
                    }

                    // Send SMS
                    if (!empty($oneTimePassword)) {
                        $this->sendSMS($oneTimePassword);
                        $status = true;
                        // $data = $oneTimePassword->one_time_password;
                        $data = null;
                        $message = "OTP sent successfully.";
                    }

                    DB::commit();
                }
            }
        } 
        catch (\Exception $e) {
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
     * Send SMS.
     *
     * @param      object  $user
     * @param      object  $oneTimePassword
     * @return     void
     * @created_at 15 August 2025
     */
    private function sendSMS($oneTimePassword)
    {
        $userOneTimePassword = $oneTimePassword->one_time_password;
        $mobileNumber = $oneTimePassword->mobile_number;
        $url = config('constants.CREDENTIALS.SMS.URL');
        $senderId = config('constants.CREDENTIALS.SMS.SENDER_ID');
        $authKey = config('constants.CREDENTIALS.SMS.AUTHKEY');
        $routeId = 1;

        $message = config('constants.CREDENTIALS.SMS.MESSAGES.OTP');
        $message = str_replace(['{otp}'], [$userOneTimePassword], $message);

        $getData = 'mobileNos='.$mobileNumber.'&message='.urlencode($message).'&senderId='.$senderId.'&routeId='.$routeId;
        
        //API URL
        $smsUrl = $url.$authKey."&".$getData;

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $smsUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        
        //get response
        $output = curl_exec($ch);
        
        //Print error if any
        if(curl_errno($ch))
        {
            echo 'error:' .curl_error($ch);
        }
        
        curl_close($ch);
        return $output;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(CheckLogin $request)
    {
        $status = false;
        $message = "Login failed, please check credentials.";
        $data = null;
        $userDetails = null;
        $statusCode = 200;

        try {
            $userOtp = OneTimePassword::where('mobile_number',$request['mobile'])->where('one_time_password',$request['otp'])->first();

            if(!empty($userOtp))
            {
                // Store user
                if (!empty($request['mobile']) && !empty($request['otp'])) {

                    $userDetails = User::where('mobile',$request['mobile'])->where(function($query){
                        $query->where('role_id', '!=', 1);
                        $query->orWhereNull('role_id');
                    })->first();

                    if(!empty($userDetails)){
                        if($userDetails->status == 0){
                            // Account is inactive
                            $message = "Your account is inactive. Kindly contact to administrator.";
                        }
                        else{
                            // Login user
                            Auth::login($userDetails);

                            $token = $userDetails->createToken('DainikNirmanAppKey')->accessToken;

                            // Update login status
                            User::where('id', $userDetails->id)->update([
                                'is_login' => 1,
                                'login_datetime' => now(),
                                'device_token' => $request['device_token'],
                                'mobile_verified_at' => empty($userDetails->mobile_verified_at) ? now() : $userDetails->mobile_verified_at
                            ]);

                            // Book Category Authors for Royalty
                            $authorHasBook = BookCategoryAuthor::where('author_id', $userDetails->id)->count();

                            $status = true;
                            $message = "Logged-In successfully.";
                            $data = [
                                'token' => $token,
                                'user' => [
                                    'id' => $userDetails->id,
                                    'name' => $userDetails->name,
                                    'email' => $userDetails->email,
                                    'mobile' => $userDetails->mobile,
                                    'address' => $userDetails->address,
                                    'role_id' => $userDetails->role_id,
                                    'role_name' => $userDetails->role->name ?? null,
                                    'profile_photo' => $userDetails->profile_photo,
                                    'active_plan' => userHasActivePlan($userDetails->id),
                                    'has_book_for_royalty' => $authorHasBook
                                ]
                            ];

                            $data['image_path'] = Storage::disk('local')->url('images/user/'.$userDetails->id);
                        }
                    }
                }
            }
        } 
        catch (\Exception $e) {
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
            // dd($e);
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function logout()
    {
        $status = false;
        $message = "Unauthenticated user.";
        $data = null;
        $statusCode = 200;

        try {
            if (Auth::user()) {
                // Success
                Auth::user()->token()->delete();
        
                $status = true;
                $message = "Logout successful.";
            }
        } 
        catch (\Exception $e) {
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
     * Handle an incoming authentication request.
     * @param int user_id
     * @param string device_type
     * @param string device_token
     */
    public function checkLogin(Request $request)
    {
        $status = false;
        $message = "Login failed, please check credentials.";
        $data = null;
        $statusCode = 200;

        try {
            $user = Auth::User();

            if(!empty($user))
            {
                // Check is active or not
                if($user->status)
                {
                    // update device type and token
                    $arrUpdate = array(
                        "device_type" => $request->device_type,
                        "device_token" => $request->device_token,
                        "updated_at" => date("Y-m-d H:i:s"),
                    );
                    $user->update($arrUpdate);
    
                    $status = true;
                    $message = "Logged-In successfully.";
                    $data = [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'mobile' => $user->mobile,
                            'address' => $user->address,
                            'role_id' => $user->role_id,
                            'role_name' => $user->role->name,
                            'profile_photo' => $user->profile_photo
                        ]
                    ];

                    $data['image_path'] = Storage::disk('local')->url('images/user/');
                }
                else
                {
                    $status = false;
                    $message = "Your account is inactive. Kindly contact to administrator.";
                }
            }
            else
            {
                $status = false;
            }
        } 
        catch (\Exception $e) {
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
     * Handle an incoming authentication request.
     * @param int user_id
     * @param string device_type
     * @param string device_token
     */
    public function register(RegistrationRequest $request)
    {
        $status = false;
        $message = "User cannot be created.";
        $data = null;
        $statusCode = 200;
        $user = null;

        DB::beginTransaction();
        try {
            $user = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'role_id' => $request->role_id,
                'address' => $request->address ?? null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            // Create user
            $user = User::create($user);
            $user->update([
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            // Upload slider image and add to data array
            if ($request->hasFile('profile_photo'))
            {
                if(!file_exists(storage_path("app/public/images/user/".$user->id))){
                    mkdir(storage_path("app/public/images/user/".$user->id), 0777, true);
                }

                $customImageName = $request->file('profile_photo')->getClientOriginalName();
                $customImageName = pathinfo($customImageName)['filename'];
                $image = $this->uploadImage($request->file('profile_photo'), "images/user/".$user->id."/", null, $customImageName);

                if ($image['_status']) 
                {
                    $imageName = $image['_data'];
                    $imageData['profile_photo'] = $imageName;
                    $user->update($imageData);
                }
            }

            $status = true;
            $message = "Registration successful.";

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            // dd($e);
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
