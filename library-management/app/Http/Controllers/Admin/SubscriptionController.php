<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Subscription' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Subscription';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.subscriptions.index")->with($this->viewData);
    }

    /**
     * Get Books list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getSubscriptions(Request $request)
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
            "filter_book_type" => $request->filter_book_type
        );

        // Get Books List
        $records_count = Subscription::GetSubscriptions(null, null, $search, $filter, $sort);
        $records = Subscription::GetSubscriptions($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $title = 'N/A';
                $amount = 'N/A';
                $validity = 'N/A';
                $type = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $title = $value->name ?? $title;
                $amount = $value->amount ?? $amount;
                $validity = $value->validity;
                $type = $value->type;
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
                            <a class="dropdown-item" href="'.route('admin.subscriptions.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                            <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.subscriptions.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "title" => $title,
                    "amount" => $amount,
                    "validity" => $validity,
                    "type" => $type,
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
     * View create Books.
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
            'Subscription' => route('admin.subscriptions.index'),
            'Create' => '',
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Subscription';
        $this->viewData['breadcrumb'] = $breadcrumb;

        return view('admin.subscriptions.create')->with($this->viewData);
    }

    /**
     * Store Book.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $subscription = null;
        $errorMessage = null;
        $notification = [
            '_status' => false,
            '_message' => __('messages.record_creation_failed', ['record' => 'Subscription']),
            '_type' => 'error',
        ];
        $redirectRoute = 'admin.subscriptions.create';
        
        // Begin Transaction
        DB::beginTransaction();
        
        // Create Book
        try {
            $subscription = Subscription::saveSubscription($request);

            DB::commit();

        } catch (\Exception $e) {
            $subscription = null;
            $errorMessage = $e->getMessage();
            DB::rollback();
            dd($e);
        }
        //------------

        if (!is_null($subscription)) 
        {
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_created', ['record' => 'Subscription']),
                '_type' => 'success',
            ];
            $redirectRoute = 'admin.subscriptions.index';
        }

        return redirect()->route($redirectRoute)->with(['notification' => $notification]);
    }

    /**
     * Edit Book.
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
            'Subscription' => route('admin.subscriptions.index'),
            'Edit' => '',
        ];

        // Book to edit
        $subscription = Subscription::where('id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Subscriptions';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['subscription'] = $subscription;

        return view('admin.subscriptions.edit')->with($this->viewData);
    }

    /**
     * Update Books.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $subscription = null;
        $errorMessage = null;
        
        // Update User
        DB::beginTransaction();
        try {
            $subscription = Subscription::updateSubscription($request);

            DB::commit();
        } catch (\Exception $e) {
            $subscription = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($subscription)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Subscription']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.subscriptions.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Subscription']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.subscriptions.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
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
        $subscription = Subscription::toggleStatus($request['ids']);

        // Set response
        if (!is_null($subscription))
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
        $subscription = Subscription::whereIn('id', $ids)->get();

        // Delete subscriptions
        if($subscription)
        {
            foreach($subscription as $key => $value)
            {
                // Delete Subscription
                $subscription = Subscription::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($subscription == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Subscription']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Subscription']),
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
        $subscription = Subscription::where('id', $id)->first();
        
        // Delete Subscription
        if($subscription)
        {
            // Delete Subscription
            $subscription = Subscription::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($subscription))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Subscription']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.subscriptions.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Subscription']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.subscriptions.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check subscription name.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkSubscriptionName(Request $request)
    {
        $status = false;

        if (!is_null($request->name)) 
        {
            $subscription = Subscription::where('name', $request['name'])->first();

            if (!is_null($subscription)) 
            {
                if ($request->filled('subscription_id') && $subscription->id == $request['subscription_id']) {
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
