<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Magazine;

class MagazineController extends Controller
{
    public function index(Request $request)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Magazines' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Magazines';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['type'] = $request->get('type');

        if($request->get('type') && $request->get('type') == 'u'){
             $breadcrumb = [
                'Dashboard' => route('admin.dashboard'),
                'User Magazines' => ''
            ];
            
            $this->viewData['pageTitle'] = 'User Magazines';
            $this->viewData['breadcrumb'] = $breadcrumb;

            // return view("admin.magazines.user_magazine_index")->with($this->viewData);
        }
        
        return view("admin.magazines.index")->with($this->viewData);
    }

    /**
     * Get Magazines list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 18-08-2025
     */
    public function getMagazines(Request $request)
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
            "filter_magazine_type" => $request->filter_magazine_type
        );

        // Get Magazines List
        $records_count = Magazine::GetMagazines(null, null, $search, $filter, $sort);
        $records = Magazine::GetMagazines($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $title = 'N/A';
                $type = 'N/A';
                $date = 'N/A';
                $coverImage = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';
                $isSelected = 'N/A';

                if(!empty($value->cover_picture)){
                    $coverImage = "";
                }

                $title = $value->title ?? $title;
                $date = date("d-m-Y", strtotime($value->date));
                $type = ($value->type == 'D' ? 'Daily' : ($value->type == 'M' ? 'Monthly' : $type));
                $created = date("d-m-Y H:i", strtotime($value->created_at));
                $isSelected = $value->is_selected ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';

                if($value->status == 1){
                    $status = '<label class="badge badge-success">Active</label> &nbsp;';
                } 
                else{
                    $status = '<label class="badge badge-warning">Inactive</label> &nbsp;';
                }

                $action = '<div class="btn-group">
                          <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                          </button>
                          <div class="dropdown-menu">';
                if($request->filter_magazine_type){
                    $action .= '<a class="dropdown-item" href="'.route('admin.magazines.edit', ['id' => $value->id]).'?type='.$request->filter_magazine_type.'"><i class="fa fa-pencil-alt"></i> Edit</a>';
                    
                    $action .= '<a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.magazines.deleteSingle', ['id' => $value->id]).'?type='.$request->filter_magazine_type.'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>';
                }
                else{
                    $action .= '<a class="dropdown-item" href="'.route('admin.magazines.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>';

                    $action .= '<a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.magazines.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>';
                }
                            $action .= '
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "title" => $title,
                    "date" => $date,
                    "cover_picture" => $coverImage,
                    "type" => $type,
                    "status" => $status,
                    "created" => $created,
                    "action" => $action,
                    "is_selected" => $isSelected,
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
     * View create Magazines.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 18-08-2025
     */
    public function create()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Magazines' => route('admin.magazines.index'),
            'Create' => '',
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Magazines';
        $this->viewData['breadcrumb'] = $breadcrumb;

        return view('admin.magazines.create')->with($this->viewData);
    }

    /**
     * Store Magazine.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 18-08-2025
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $magazine = null;
        $errorMessage = null;
        $notification = [
            '_status' => false,
            '_message' => __('messages.record_creation_failed', ['record' => 'Magazine']),
            '_type' => 'error',
        ];
        $redirectRoute = 'admin.magazines.create';
        
        // Begin Transaction
        DB::beginTransaction();
        
        // Create Magazine
        try {
            $magazine = Magazine::saveMagazine($request);

            DB::commit();

        } catch (\Exception $e) {
            $magazine = null;
            $errorMessage = $e->getMessage();
            DB::rollback();
            dd($e);
        }
        //------------

        if (!is_null($magazine)) 
        {
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_created', ['record' => 'Magazine']),
                '_type' => 'success',
            ];
            $redirectRoute = 'admin.magazines.index';
        }

        return redirect()->route($redirectRoute)->with(['notification' => $notification]);
    }

    /**
     * Edit Magazine.
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
            'Magazines' => route('admin.magazines.index'),
            'Edit' => '',
        ];

        // Magazine to edit
        $magazine = Magazine::where('id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Magazine';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['magazine'] = $magazine;

        if($request->get('type') && $request->get('type') == 'u'){
            $breadcrumb = [
                'Dashboard' => route('admin.dashboard'),
                'User Magazines' => route('admin.magazines.index', ['type' => 'u']),
                'Edit' => '',
            ];
            $this->viewData['pageTitle'] = 'User Magazines';
            $this->viewData['breadcrumb'] = $breadcrumb;

            return view('admin.magazines.user_magazine_edit')->with($this->viewData);
        }

        return view('admin.magazines.edit')->with($this->viewData);
    }

    /**
     * Update Magazines.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 20-08-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $magazine = null;
        $errorMessage = null;
        $queryParams = [];
        
        // Update User
        DB::beginTransaction();
        try {
            $magazine = Magazine::updateMagazine($request);

            if($request->filter_magazine_type && $request->filter_magazine_type == 'u'){
                $queryParams = ['type' => $request->filter_magazine_type];
            }

            DB::commit();
        } catch (\Exception $e) {
            $magazine = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($magazine)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Magazine']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.magazines.index', $queryParams)->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Magazine']),
                '_type' => 'error',
            ];
            //-----------------

            $queryParams += ['id' => $id];

            return redirect()->route('admin.magazines.edit', $queryParams)->withInput()->with(['notification' => $notification]);
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
        $queryParams = [];
        
        // Delete Magazine
        if($magazine)
        {
            // Delete Magazine
            $magazine = Magazine::where('id', $id)->delete();
        }

        if($request->get('type') && $request->get('type') == 'u'){
            $queryParams = ['type' => $request->get('type')];
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

            return redirect()->route('admin.magazines.index', $queryParams)->with(['notification' => $notification]);
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

            return redirect()->route('admin.magazines.index', $queryParams)->with(['notification' => $notification]);
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
