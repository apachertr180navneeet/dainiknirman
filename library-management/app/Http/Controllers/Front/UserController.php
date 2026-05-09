<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function deleteAccount()
    {
        // Send view data
        $this->viewData['pageTitle'] = 'Delete Account';
        
        return view("front.delete_account")->with($this->viewData);
    }

    public function deleteMyAccount(Request $request)
    {
        // dd($request->all());
        $isDelete = null;
        $user = User::where('mobile', $request->mobile)->first();
        
        // Delete User
        if($user)
        {
            // Delete User
            $isDelete = $user->delete();
        }
        
        // Set notification
        if (!is_null($isDelete))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'User']),
                '_type' => 'success',
            ];
            //---------------
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => "Mobile number not found. Cannot perform account deletion.",
                '_type' => 'error',
            ];
            //---------------
        }
        //-------------

        return redirect()->route('front.user.deleteAccount')->with(['notification' => $notification]);
    }

    public function checkAccount(Request $request)
    {
        // Set notification
        $notification = [
            '_status' => false,
            '_message' => "Mobile number not registered with us.",
            '_type' => 'error',
        ];
        //---------------

        if (!is_null($request->mobile)) 
        {
            $user = User::where('mobile', $request['mobile'])->first();

            if (!empty($user)) 
            {
                // Set notification
                $notification = [
                    '_status' => true,
                    '_message' => "Mobile number found successfully.",
                    '_type' => 'success',
                ];
                //---------------
            }
        }

        return response()->json($notification, 200);
    }
}