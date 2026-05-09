<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contest;
use App\Models\ContestAuthor;

class ContestAuthorController extends Controller
{
    public function index($contestId)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Contest Author' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Contest Author';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['contestId'] = $contestId;
        
        return view("admin.contest-authors.index")->with($this->viewData);
    }

    /**
     * Get Contest Authors list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getContestAuthors(Request $request, $contestId)
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
        $records_count = ContestAuthor::GetContestAuthors(null, null, $search, $filter, $sort, $contestId);
        $records = ContestAuthor::GetContestAuthors($limit, $start, $search, $filter, $sort, $contestId);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $contestTitle = 'N/A';
                $userContestTitle = 'N/A';
                $contestDate = 'N/A';
                $authorName = 'N/A';
                $rank = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '-';

                $contestTitle = $value->contest_title ?? $contestTitle;
                $userContestTitle = $value->title ?? $userContestTitle;
                $contestDate = date("d-m-Y", strtotime($value->contest_date));
                $authorName = $value->author_name ?? $authorName;
                $rank = $value->rank ?? $rank;
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
                    <div class="dropdown-menu">';
                $action .= '<a class="dropdown-item" href="'.route('admin.contest-authors.edit', ['contest_id' => $value->contest_id, 'id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.contest-authors.deleteSingle', ['contest_id' => $value->contest_id, 'id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>';
                $action .= '</div></div>';

                // Array Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "contest_title" => $contestTitle,
                    "user_contest_title" => $userContestTitle,
                    "author_name" => $authorName,
                    "contest_date" => $contestDate,
                    "rank" => $rank,
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
     * Edit Contest.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function edit(Request $request, $contestId, $id)
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Contest Author' => route('admin.contest-authors.index', ['contest_id' => $contestId]),
            'Edit' => '',
        ];

        // Contest to edit
        $contestAuthor = ContestAuthor::select('contest_authors.*', 'users.name as author_name')
        ->join('users', 'users.id', '=', 'contest_authors.created_by')
        ->where('contest_id', $contestId)->where('contest_authors.id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Contest Author';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['contestAuthor'] = $contestAuthor;

        return view('admin.contest-authors.edit', ['contest_id' => $contestId, 'id' => $id])->with($this->viewData);
    }

    /**
     * Update Contests.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function update(Request $request, $contestId, $id)
    {
        $authUser = auth()->user();
        $contest = null;
        $errorMessage = null;
        
        // Update Contest
        DB::beginTransaction();
        try {
            $contest = ContestAuthor::updateContestAuthor($request);

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
                '_message' => __('messages.records_updated', ['record' => 'Contest Author']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.contest-authors.index', ['contest_id' => $contestId])->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Contest Author']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.contest-authors.edit', ['contest_id' => $contestId, 'id' => $id])->withInput()->with(['notification' => $notification]);
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
        $contest = ContestAuthor::toggleStatus($request['ids']);

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
        $contest = ContestAuthor::whereIn('id', $ids)->get();

        // Delete contests
        if($contest)
        {
            foreach($contest as $key => $value)
            {
                // Delete Contests
                $contest = ContestAuthor::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($contest == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Contest Author']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Contest Author']),
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
        $contest = ContestAuthor::where('id', $id)->first();
        
        // Delete Contest
        if($contest)
        {
            // Delete Contest
            $contest = ContestAuthor::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($contest))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Contest Author']),
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
                '_message' => __('messages.record_failed', ['record' => 'Contest Author']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.contests.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check rank.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkContestRank(Request $request)
    {
        $status = false;

        if (!is_null($request->rank) && !is_null($request->contest_id) && !is_null($request->contest_author_id)) 
        {
            $contestAuthor = ContestAuthor::where('contest_id', $request['contest_id'])
            ->where('rank', $request['rank'])
            ->first();
            // dd($contestAuthor);

            if (!is_null($contestAuthor)) 
            {
                if ($request->filled('contest_author_id') && $contestAuthor->id == $request['contest_author_id']) {
                    $status = true;
                } else {
                    $status = false;
                }
            } 
            else {
                $status = true;
            }
            // dd($status);
        }

        return response()->json($status, 200);
    }
}
