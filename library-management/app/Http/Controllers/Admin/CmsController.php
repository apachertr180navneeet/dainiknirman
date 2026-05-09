<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cms;

class CmsController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Cms' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Cms';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.cms.index")->with($this->viewData);
    }

    /**
     * Get Cms list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 18-08-2025
     */
    public function getCms(Request $request)
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
            "filter_cms_type" => $request->filter_cms_type
        );

        // Get Cms List
        $records_count = Cms::GetCms(null, null, $search, $filter, $sort);
        $records = Cms::GetCms($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $title = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $title = $value->title ?? $title;
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
                            <a class="dropdown-item" href="'.route('admin.cms.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "title" => $title,
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
     * Edit Cms.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 15-08-2025
     */
    public function edit(Request $request, $id)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Cms' => route('admin.cms.index'),
            'Edit' => '',
        ];

        // Cms to edit
        $cms = Cms::where('id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Cms';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['cms'] = $cms;

        return view('admin.cms.edit')->with($this->viewData);
    }

    /**
     * Update Cms.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 20-08-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $cms = null;
        $errorMessage = null;
        
        // Update User
        DB::beginTransaction();
        try {
            $cms = Cms::updateCms($request);

            DB::commit();
        } catch (\Exception $e) {
            $cms = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($cms)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Cms']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.cms.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Cms']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.cms.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
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
        $magazine = Magazine::toggleStatus($request['ids']);

        // Set response
        if (!is_null($magazine))
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
        $magazine = Magazine::whereIn('id', $ids)->get();

        // Delete magazine
        if($magazine)
        {
            foreach($magazine as $key => $value)
            {
                // Delete Magazine
                $magazine = Magazine::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($magazine == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Magazine']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Magazine']),
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
        $magazine = Magazine::where('id', $id)->first();
        
        // Delete Magazine
        if($magazine)
        {
            // Delete Magazine
            $magazine = Magazine::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($magazine))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Magazine']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.magazines.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Magazine']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.magazines.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check magazine name.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkMagazineName(Request $request)
    {
        $status = false;

        if (!is_null($request->title)) 
        {
            $magazine = Magazine::where('title', $request['title'])->first();

            if (!is_null($magazine)) 
            {
                if ($request->filled('magazine_id') && $magazine->id == $request['magazine_id']) {
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
