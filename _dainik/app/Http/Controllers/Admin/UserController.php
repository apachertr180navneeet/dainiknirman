<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\UserAccountDetail;

class UserController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'User' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'User';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.users.index")->with($this->viewData);
    }

    /**
     * Get Users list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 04-08-2025
     */
    public function getUsers(Request $request)
    {
        $authUser = auth()->user();

        // Ajax Post Parameters from table
        $draw = $request->get('draw');
        $start = $request->get('start');
        $limit = $request->get('length');
        $sort = $request->get('order')[0];
        $search = $request->get('search')['value'];
        
        // Filter Parameters
        $filter = array(
            "filter_itr_status" => $request->filter_itr_status
        );

        // Get Users List
        $records_count = User::GetUsers(null, null, $search, $filter, $sort);
        $records = User::GetUsers($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $name = 'N/A';
                $gender = 'N/A';
                $email = 'N/A';
                $mobile = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $name = $value->name ?? $name;
                $email = $value->email ?? $email;
                $mobile = $value->mobile ?? $mobile;
                $gender = !empty($value->gender && $value->gender == 'M') ? "Male" : ($value->gender == 'F' ? "Female" : $gender);
                $created = date("d-m-Y H:i", strtotime($value->created_at));

                if($value->status == 1){
                    $status = '<label class="badge badge-success">Active</label> &nbsp;';
                } 
                else{
                    $status = '<label class="badge badge-warning">Inactive</label> &nbsp;';
                }

                $action = '<div class="btn-group">
                          <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="'.route('admin.users.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                            <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.users.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "name" => $name,
                    "email" => $email,
                    "mobile" => $mobile,
                    "gender" => $gender,
                    "status" => $status,
                    "created" => $created,
                    "action" => $action
                );
            }
        }
        $totalRecords = $records_count;
        $totalDisplayRecord = $arr_data;

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $arr_data
        );

        return json_encode($response);
    }

    /**
     * View create Users.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function create()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'User' => route('admin.users.index'),
            'Create' => '',
        ];

        // Get Roles list
        $roles = Role::select('id', 'name')
        ->whereIn('id', [
            config('constants.roles.AUTHOR.value'),
            config('constants.roles.READER.value'),
            config('constants.roles.AUTHOR_READER.value')
        ])
        ->get();

        // Send view data
        $this->viewData['pageTitle'] = 'User';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['roles'] = $roles;

        return view('admin.users.create')->with($this->viewData);
    }

    /**
     * Store User.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $user = null;
        $errorMessage = null;
        $notification = [
            '_status' => false,
            '_message' => __('messages.record_creation_failed', ['record' => 'User']),
            '_type' => 'error',
        ];
        $redirectRoute = 'admin.users.create';
        
        // Begin Transaction
        DB::beginTransaction();
        
        // Create User
        try {
            $user = User::saveUser($request);

            DB::commit();

        } catch (\Exception $e) {
            $user = null;
            $errorMessage = $e->getMessage();
            DB::rollback();
            dd($e);
        }
        //------------

        if (!is_null($user)) 
        {
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_created', ['record' => 'User']),
                '_type' => 'success',
            ];
            $redirectRoute = 'admin.users.index';
        }

        return redirect()->route($redirectRoute)->with(['notification' => $notification]);
    }

    /**
     * Edit Customer.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function edit(Request $request, $id)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'User' => route('admin.users.index'),
            'Edit' => '',
        ];

        // User to edit
        $user = User::with([
            'account_detail'
        ])
        ->where('users.id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'User';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['user'] = $user;

        return view('admin.users.edit')->with($this->viewData);
    }

    /**
     * Update Users.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $user = null;
        $errorMessage = null;
        
        // Update User
        DB::beginTransaction();
        try {
            $user = User::updateUser($request);

            DB::commit();
        } catch (\Exception $e) {
            $user = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($user)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'User']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.users.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'User']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.users.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
        }
    }

    /**
     * Change status.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function changeStatus(Request $request)
    {
        $customer = User::toggleStatus($request['ids']);

        // Set response
        if (!is_null($customer))
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.status_changed'),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.status_change_failed'),
                '_type' => 'error',
            ];
        }
        //-------------
        
        return response()->json($response, 200);
    }

    /**
     * Destroy.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function destroy(Request $request)
    {
        $ids = $request['ids'];
        $user = User::whereIn('id', $ids)->get();

        // Delete child or sub categories if any
        if($user)
        {
            foreach($user as $key => $value)
            {
                // Delete User
                $user = User::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($user == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'User']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'User']),
                '_type' => 'error',
            ];
        }
        //-------------
        
        return response()->json($response, 200);
    }

    /**
     * Delete Single.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function deleteSingle(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        
        // Delete User
        if($user)
        {
            // Delete User
            $user = User::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($user))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'User']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.users.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'User']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.users.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check user mobile.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkUserMobile(Request $request)
    {
        $status = false;

        if (!is_null($request->mobile)) 
        {
            $user = User::where('mobile', $request['mobile'])->first();

            if (!is_null($user)) 
            {
                if ($request->filled('user_id') && $user->id == $request['user_id']) {
                    $status = true;
                } else {
                    $status = false;
                }
            } 
            else {
                $status = true;
            }
        }

        return response()->json($status, 200);
    }

    /**
     * Check user email.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkUserEmail(Request $request)
    {
        $status = false;

        if (!is_null($request->email)) 
        {
            $user = User::where('email', $request['email'])->first();

            if (!is_null($user)) 
            {
                if ($request->filled('user_id') && $user->id == $request['user_id']) {
                    $status = true;
                } else {
                    $status = false;
                }
            } 
            else {
                $status = true;
            }
        }

        return response()->json($status, 200);
    }

    /**
     * Check bank account number.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkBankAccountNumber(Request $request)
    {
        $status = false;

        if (!is_null($request->bank_account_number)) 
        {
            $user = UserAccountDetail::where('account_number', $request['bank_account_number'])->first();

            if (!is_null($user)) 
            {
                if ($request->filled('user_id') && $user->user_id == $request['user_id']) {
                    $status = true;
                } else {
                    $status = false;
                }
            } 
            else {
                $status = true;
            }
        }

        return response()->json($status, 200);
    }
}
