<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Storage;
use App\Http\Traits\UploadImage;
use App\Models\User;
use App\Models\UserAccountDetail;

class ProfileController extends Controller
{
    use UploadImage;

    /**
     * Get profile
     */
    public function index()
    {
        $status = false;
        $message = "Profile not found.";
        $data = null;
        $statusCode = 200;

        try {
            $profile = User::select('id', 'role_id', 'name', 'email', 'mobile', 'gender', 'profile_photo', 'device_type', 'device_token', 'status', 'created_at')
            ->with([
                'role' => function($query){
                    $query->select('id', 'name');
                },
                'account_detail' => function($query){
                    $query->select('id', 'user_id', 'account_holder_name', 'account_number', 'ifsc_code', 'branch_name', 'city_name');
                },
            ])
            ->find(Auth::user()->id);

            $data['profile'] = $profile;
            $data['image_path'] = Storage::disk('local')->url('images/user/'.$profile->id);

            $status = true;
            $message = "Profile get successfully.";
        } 
        catch (\Exception $e) {
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

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Profile not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $profileData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'address' => $request->address,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $user->id
            ];

            // Upload slider image and add to data array
            if ($request->hasFile('profile_photo'))
            {
                if(!file_exists(storage_path("app/public/images/user/".$user->id))){
                    mkdir(storage_path("app/public/images/user/".$user->id), 0777, true);
                }
                else
                {
                    chmod(storage_path("app/public/images/user/".$user->id), 0777);
                }

                $customImageName = $request->file('profile_photo')->getClientOriginalName();
                $customImageName = pathinfo($customImageName)['filename'];
                $image = $this->uploadImage($request->file('profile_photo'), "images/user/".$user->id."/", null, $customImageName);

                if ($image['_status']) 
                {
                    $imageName = $image['_data'];
                    $profileData['profile_photo'] = $imageName;
                }
            }

            // Update profile
            $user->update($profileData);

            // Account detail add/update
            if(!empty($request->account_holder_name) && !empty($request->account_number)){
                UserAccountDetail::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'account_holder_name' => $request->account_holder_name ?? null,
                        'account_number' => $request->account_number ?? null,
                        'ifsc_code' => $request->ifsc_code ?? null,
                        'branch_name' => $request->branch_name ?? null,
                        'city_name' => $request->city_name ?? null
                    ],
                );
            }

            $profile = User::select('id', 'role_id', 'name', 'email', 'mobile', 'gender', 'profile_photo', 'device_type', 'device_token', 'status', 'created_at')
            ->with([
                'role' => function($query){
                    $query->select('id', 'name');
                },
                'account_detail' => function($query){
                    $query->select('id', 'user_id', 'account_holder_name', 'account_number', 'ifsc_code', 'branch_name', 'city_name');
                },
            ])
            ->find($user->id);

            $data['profile'] = $profile;
            $data['image_path'] = Storage::disk('local')->url('images/user/'.$user->id);

            $status = true;
            $message = "Profile updated successfully.";

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
