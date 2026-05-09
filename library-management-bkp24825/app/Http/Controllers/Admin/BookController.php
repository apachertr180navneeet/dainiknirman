<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Book' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Book';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.books.index")->with($this->viewData);
    }

    /**
     * Get Books list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getBooks(Request $request)
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
        $records_count = Book::GetBooks(null, null, $search, $filter, $sort);
        $records = Book::GetBooks($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $bookName = 'N/A';
                $authorName = 'N/A';
                $date = 'N/A';
                $launchDate = 'N/A';
                $type = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $bookName = $value->book_name ?? $bookName;
                $authorName = $value->author_name ?? $authorName;
                $date = date("d-m-Y", strtotime($value->date));
                $launchDate = date("d-m-Y", strtotime($value->launch_date));
                $type = $value->book_type == 'P' ? 'Paid' : 'Free';
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
                            <a class="dropdown-item" href="'.route('admin.books.edit', ['id' => $value->id]).'"><i class="fa fa-pencil-alt"></i> Edit</a>
                            <a class="dropdown-item text-danger dt-delete-single" data-url="'.route('admin.books.deleteSingle', ['id' => $value->id]).'" href="javascript:;"><i class="fa fa-trash"></i> Delete</a>
                          </div>
                        </div>';

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "book_name" => $bookName,
                    "author_name" => $authorName,
                    "date" => $date,
                    "launch_date" => $launchDate,
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
            'Books' => route('admin.books.index'),
            'Create' => '',
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Books';
        $this->viewData['breadcrumb'] = $breadcrumb;

        return view('admin.books.create')->with($this->viewData);
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
        $book = null;
        $errorMessage = null;
        $notification = [
            '_status' => false,
            '_message' => __('messages.record_creation_failed', ['record' => 'Book']),
            '_type' => 'error',
        ];
        $redirectRoute = 'admin.books.create';
        
        // Begin Transaction
        DB::beginTransaction();
        
        // Create Book
        try {
            $book = Book::saveBook($request);

            DB::commit();

        } catch (\Exception $e) {
            $book = null;
            $errorMessage = $e->getMessage();
            DB::rollback();
            dd($e);
        }
        //------------

        if (!is_null($book)) 
        {
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_created', ['record' => 'Book']),
                '_type' => 'success',
            ];
            $redirectRoute = 'admin.books.index';
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
            'Books' => route('admin.books.index'),
            'Edit' => '',
        ];

        // Book to edit
        $book = Book::where('id', $id)->first();
        
        // Send view data
        $this->viewData['pageTitle'] = 'Books';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData['book'] = $book;

        return view('admin.books.edit')->with($this->viewData);
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
        $book = null;
        $errorMessage = null;
        
        // Update User
        DB::beginTransaction();
        try {
            $book = Book::updateBook($request);

            DB::commit();
        } catch (\Exception $e) {
            $book = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        if (!is_null($book)) 
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.records_updated', ['record' => 'Book']),
                '_type' => 'success',
            ];
            //-----------------

            return redirect()->route('admin.books.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.records_updation_failed', ['record' => 'Book']),
                '_type' => 'error',
            ];
            //-----------------

            return redirect()->route('admin.books.edit', ['id' => $id])->withInput()->with(['notification' => $notification]);
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
        $book = Book::toggleStatus($request['ids']);

        // Set response
        if (!is_null($book))
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
        $book = Book::whereIn('id', $ids)->get();

        // Delete books
        if($book)
        {
            foreach($book as $key => $value)
            {
                // Delete Books
                $book = Book::where('id', $value->id)->delete();
            }
        }
        
        // Set response
        if ($book == true) 
        {
            $response = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Book']),
                '_type' => 'success',
            ];
        } 
        else 
        {
            $response = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Book']),
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
        $book = Book::where('id', $id)->first();
        
        // Delete Book
        if($book)
        {
            // Delete Book
            $book = Book::where('id', $id)->delete();
        }
        
        // Set notification
        if (!is_null($book))
        {
            // Set notification
            $notification = [
                '_status' => true,
                '_message' => __('messages.record_deleted', ['record' => 'Book']),
                '_type' => 'success',
            ];
            //---------------

            return redirect()->route('admin.books.index')->with(['notification' => $notification]);
        } 
        else 
        {
            // Set notification
            $notification = [
                '_status' => false,
                '_message' => __('messages.record_failed', ['record' => 'Book']),
                '_type' => 'error',
            ];
            //---------------

            return redirect()->route('admin.books.index')->with(['notification' => $notification]);
        }
        //-------------

        return response()->json($response, 200);
    }

    /**
     * Check book name.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function checkBookName(Request $request)
    {
        $status = false;

        if (!is_null($request->book_name)) 
        {
            $book = Book::where('book_name', $request['book_name'])->first();

            if (!is_null($book)) 
            {
                if ($request->filled('book_id') && $book->id == $request['book_id']) {
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
