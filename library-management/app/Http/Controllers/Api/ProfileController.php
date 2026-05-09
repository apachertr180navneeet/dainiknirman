<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Storage;
use App\Http\Traits\UploadImage;
use App\Models\User;
use App\Models\UserAccountDetail;
use App\Models\Book;
use App\Models\SupportEnquiry;

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
                    $query->select('id', 'name', 'slug');
                },
                'account_detail' => function($query){
                    $query->select('id', 'user_id', 'account_holder_name', 'account_number', 'ifsc_code', 'branch_name', 'city_name');
                },
            ])
            ->find(Auth::user()->id);

            $data['profile'] = $profile;
            $data['hasPlan'] = userHasValidPlan(Auth::user()->id, 'author');
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
                    ]
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

    /**
     * Get dashboard
     */
    public function getDashboard(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Dashboard items not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $limit = 10;
            $bookTypes = [
                'bestSeller' => 1,
                'newArrival' => 2,
                'freeBooks' => 3,
                'all' => 4
            ];

            $data['books'] = [
                'all' => [],
                'best_seller' => [],
                'new_arrival' => [],
                'free' => [],
            ];

            $data['books']['best_seller'] = getBooksByType($user, $bookTypes['bestSeller'], $limit);
            $data['books']['new_arrival'] = getBooksByType($user, $bookTypes['newArrival'], $limit);
            $data['books']['free'] = getBooksByType($user, $bookTypes['freeBooks'], $limit);
            $data['books']['all'] = getBooksByType($user, $bookTypes['all'], $limit);

            $status = true;
            $message = "Dashboard get successfully.";
            $data['cover_picture_path'] = Storage::disk("local")->url("book/cover/");
            $data['pdf_path'] = Storage::disk("local")->url("book/pdf/");

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

    /**
     * Save support enquiry
     */
    public function saveSupportEnquiry(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Some error occurred, contest cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'name' => 'required|max:100',
                    'email' => 'required|max:200',
                    'message' => 'required|max:500'
                ],
                [
                    'name.required' => 'Author name is required field.',
                    'name.max' => 'Author name cannot be more than 100 characters.',
                    'email.required' => 'Email is required field.',
                    'email.max' => 'Email cannot be more than 200 characters.',
                    'message.required' => 'Description is required field.',
                    'message.max' => 'Description cannot be more than 500 characters.'
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

            // Save support form
            $supportData = [
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Support Enquiry Create
            $support = SupportEnquiry::create($supportData);
            
            if(!empty($support)){
                $status = true;
                $message = "Support enquiry saved successfully.";
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
