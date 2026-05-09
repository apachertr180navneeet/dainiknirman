<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Anthology;

class AnthologyController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Anthology' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Anthology';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.anthologies.index")->with($this->viewData);
    }

    /**
     * Get Anthology list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 28-12-2025
     */
    public function getAnthologies(Request $request)
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

        // Get Anthologies List
        $records_count = Anthology::GetAnthologies(null, null, $search, $filter, $sort);
        $records = Anthology::GetAnthologies($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $title = 'N/A';
                $authorName = 'N/A';
                $isAcceptTerms = 'N/A';
                $isSelected = 'N/A';
                $type = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $title = $value->title ?? $title;
                $authorName = $value->author_name ?? $authorName;
                $isAcceptTerms = $value->is_accept_terms ? 'Yes' : 'No';
                $isSelected = $value->is_selected ? 'Yes' : 'No';
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
                            <a class="dropdown-item" href="'.route('admin.anthology.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                            <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.anthology.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "title" => $title,
                    "author_name" => $authorName,
                    "is_accept_terms" => $isAcceptTerms,
                    "is_selected" => $isSelected,
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
     * Edit Anthology.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 28-12-2025
     */
    public function edit(Request $request, $id)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Anthology' => route('admin.anthology.index'),
            'Edit' => '',
        ];

        // Anthology to edit
        $anthology = Anthology::where('id', $id)->first();

        // Send view data
        $this->viewData['pageTitle'] = 'Anthology';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['anthology'] = $anthology;

        return view('admin.anthologies.edit')->with($this->viewData);
    }

    /**
     * Update Anthologies.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 28-12-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $anthology = null;
        $errorMessage = null;
        
        // Update User
        DB::beginTransaction();
        try {
            $anthology = Anthology::updateAnthology($request);

            DB::commit();
        } catch (\Exception $e) {
            $anthology = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($anthology)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Anthology']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.anthology.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Anthology']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.anthology.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
        }
    }

    /**
     * Change status.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created 28-12-2025
     */
    public function changeStatus(Request $request)
    {
        $anthology = Anthology::toggleStatus($request['ids']);

        // Set response
        if (!is_null($anthology))
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
     * @created_at 28-12-2025
     */
    public function destroy(Request $request)
    {
        $ids = $request['ids'];
        $anthologies = Anthology::whereIn('id', $ids)->get();

        // Delete anthologies
        if($anthologies)
        {
            foreach($anthologies as $key => $value)
            {
                // Delete Anthology
                $anthology = Anthology::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($anthology == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Anthology']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Anthology']),
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
     * @created_at 28-12-2025
     */
    public function deleteSingle(Request $request, $id)
    {
        $anthology = Anthology::where('id', $id)->first();
        
        // Delete Anthology
        if($anthology)
        {
            // Delete Anthology
            $anthology = Anthology::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($anthology))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Anthology']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.anthology.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Anthology']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.anthology.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check title.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 28-12-2025
     */
    public function checkTitle(Request $request)
    {
        $status = false;

        if (!is_null($request->title)) 
        {
            $anthology = Anthology::where('title', $request['title'])->first();

            if (!is_null($anthology)) 
            {
                if ($request->filled('anthology_id') && $anthology->id == $request['anthology_id']) {
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
