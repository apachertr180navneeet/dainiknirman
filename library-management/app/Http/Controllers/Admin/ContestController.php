<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contest;
use App\Models\ContestAuthor;

class ContestController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Contest' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Contest';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.contests.index")->with($this->viewData);
    }

    /**
     * Get Contests list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getContests(Request $request)
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

        // Get Contests List
        $records_count = Contest::GetContests(null, null, $search, $filter, $sort);
        $records = Contest::GetContests($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $title = 'N/A';
                $description = 'N/A';
                $date = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '-';
                $hasContestAuthor = 0;

                $hasContestAuthor = ContestAuthor::where("contest_id", $value->id)->count();

                $title = $value->title ?? $title;
                $description = $value->description ?? $description;
                $date = date("d-m-Y", strtotime($value->date));
                $created = date("d-m-Y H:i", strtotime($value->created_at));
                $contestAuthorCount = $hasContestAuthor;

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

                if($hasContestAuthor == 0){
                    $action .= '<a class="dropdown-item" href="'.route('admin.contests.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                    <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.contests.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>';
                }
                else
                {
                    $action .= '<a class="dropdown-item" href="'.route('admin.contest-authors.index', ['contest_id' => $value->id]).'"><i class="fa fa-users"></i> Authors</a>';
                }

                $action .= '</div></div>';

                // Array Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "title" => $title,
                    "description" => $description,
                    "date" => $date,
                    "status" => $status,
                    "created" => $created,
                    "action" => $action,
                    "hasContestAuthor" => $hasContestAuthor,
                    "contestAuthorCount" => $contestAuthorCount
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
     * View create Contest.
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
            'Contests' => route('admin.contests.index'),
            'Create' => '',
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Contests';
        $this->viewData['breadcrumb'] = $breadcrumb;

        return view('admin.contests.create')->with($this->viewData);
    }

    /**
     * Store Contest.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $contest = null;
        $errorMessage = null;
        $notification = [
            '_status' => false,
            '_message' => __('messages.record_creation_failed', ['record' => 'Contest']),
            '_type' => 'error',
        ];
        $redirectRoute = 'admin.contests.create';
        
        // Begin Transaction
        DB::beginTransaction();
        
        // Create Contest
        try {
            $contest = Contest::saveContest($request);

            DB::commit();

        } catch (\Exception $e) {
            $contest = null;
            $errorMessage = $e->getMessage();
            DB::rollback();
            dd($e);
        }
        //------------

        if (!is_null($contest)) 
        {
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_created', ['record' => 'Contest']),
                '_type' => 'success',
            ];
            $redirectRoute = 'admin.contests.index';
        }

        return redirect()->route($redirectRoute)->with(['notification' => $notification]);
    }

    /**
     * Edit Contest.
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
            'Contests' => route('admin.contests.index'),
            'Edit' => '',
        ];

        // Contest to edit
        $contest = Contest::where('id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Contests';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['contest'] = $contest;

        return view('admin.contests.edit')->with($this->viewData);
    }

    /**
     * Update Contests.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $contest = null;
        $errorMessage = null;
        
        // Update Contest
        DB::beginTransaction();
        try {
            $contest = Contest::updateContest($request);

            DB::commit();
        } catch (\Exception $e) {
            $contest = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($contest)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Contest']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.contests.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Contest']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.contests.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
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
        $contest = Contest::toggleStatus($request['ids']);

        // Set response
        if (!is_null($contest))
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
        $contest = Contest::whereIn('id', $ids)->get();

        // Delete contests
        if($contest)
        {
            foreach($contest as $key => $value)
            {
                // Delete Contests
                $contest = Contest::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($contest == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Contest']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Contest']),
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
        $contest = Contest::where('id', $id)->first();
        
        // Delete Contest
        if($contest)
        {
            // Delete Contest
            $contest = Contest::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($contest))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Contest']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.contests.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Contest']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.contests.index')->with(['notification' => $notification]);
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
     * @created_at 05-08-2025
     */
    public function checkContestTitle(Request $request)
    {
        $status = false;

        if (!is_null($request->title)) 
        {
            $contest = Contest::where('title', $request['title'])->first();

            if (!is_null($contest)) 
            {
                if ($request->filled('contest_id') && $contest->id == $request['contest_id']) {
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
