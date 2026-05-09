<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\User;
use App\Models\Royalty;

class RoyaltyController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Royalties' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Royalties';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.royalties.index")->with($this->viewData);
    }

    /**
     * Get Royalties list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getRoyalties(Request $request)
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
            "filter_book_type" => $request->filter_book_type,
            "filter_book_name" => $request->filter_book_name,
            "filter_author_id" => $request->filter_author_id
        );
        // dd($filter);

        // Get Royalties List
        $records_count = Royalty::GetRoyalty(null, null, $search, $filter, $sort);
        $records = Royalty::GetRoyalty($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $bookName = 'N/A';
                $authorName = 'N/A';
                $orderNumber = 'N/A';
                $orderDate = 'N/A';
                $royaltyAmount = 'N/A';
                $created = 'N/A';
                $paymentStatus = '';
                $status = '';
                $action = '';

                $orderNumber = $value->order->order_number ?? $orderNumber;
                $orderDate = date("d-m-Y", strtotime($value->order->created_at));;
                $bookName = $value->book_name ?? $bookName;
                $authorName = $value->author_name ?? $authorName;
                $royaltyAmount = $value->author_royalty ?? $royaltyAmount;
                $created = date("d-m-Y H:i", strtotime($value->created_at));

                if($value->payment_status == 'PROCESS'){
                    $paymentStatus = '<span class="badge badge-success">'.ucwords(strtolower($value->payment_status)).'</span>';
                }
                else{
                    $paymentStatus = '<span class="badge badge-danger">'.ucwords(strtolower($value->payment_status)).'</span>';
                }

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "order_number" => $orderNumber,
                    "order_date" => $orderDate,
                    "book_name" => $bookName,
                    "author_name" => $authorName,
                    "royalty_amount" => $royaltyAmount,
                    "created" => $created,
                    "payment_status" => $value->payment_status,
                    "payment_status_badge" => $paymentStatus
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

    public function calculation()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Royalties' => route('admin.royalties.index'),
            'Royalties Calculation' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Royalties Calculation';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.royalties.calculation")->with($this->viewData);
    }

    /**
     * Get Royalties list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getRoyaltyCalculation(Request $request)
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

        // Get Royalties List
        $records_count = Royalty::GetRoyaltyCalculation(null, null, $search, $filter, $sort);
        $records = Royalty::GetRoyaltyCalculation($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $bookName = 'N/A';
                $saleQty = 'N/A';
                $bookPrice = 'N/A';
                $totalRoyalty = 'N/A';
                $authorRoyalty = 'N/A';
                $appRoyalty = 'N/A';
                $bookFile = 'N/A';
                $created = 'N/A';
                $status = '';
                $action = '';

                $bookName = $value->book_name ?? $bookName;

                if(!empty($value->book_name)){
                    $bookName = '<a href="'.(route('admin.royalties.index')).'?book='.$value->book_name.'">'.$value->book_name.'</a>';
                }
                
                $saleQty = $value->order_count ?? $saleQty;
                $bookPrice = $value->price ?? $bookPrice;
                $totalRoyalty = $value->total_royalty ?? $totalRoyalty;
                $authorRoyalty = $value->author_royalty ?? $authorRoyalty;
                $appRoyalty = $value->app_royalty ?? $appRoyalty;
                $created = date("d-m-Y H:i", strtotime($value->created_at));

                if(!empty($value->book_pdf)){
                    $bookFile = Storage::disk('local')->url("book/pdf/".$value->book_pdf);
                    $bookFile = '<a href="'.$bookFile.'" class="btn btn-info" title="Download \''.$value->book_name.'\'" download><i class="fa fa-download"></i></a>';
                }

                // Array Parent Data
                $arr_data[] = array(
                    "book_name" => $bookName,
                    "sale_qty" => $saleQty,
                    "book_price" => $bookPrice,
                    "total_royalty" => $totalRoyalty,
                    "author_royalty" => $authorRoyalty,
                    "app_royalty" => $appRoyalty,
                    "created" => $created,
                    "book_file" => $bookFile
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
     * Change payment status.
     *
     * @return boolean
     *
     * @author Rajesh
     * @created 05-08-2025
     */
    public function changePaymentStatus(Request $request)
    {
        $royaltyData = [
            'payment_status' => $request->payment_status,
            'updated_by' => Auth::user()->id,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $roylaty = Royalty::whereIn('id', $request['ids'])->update($royaltyData);

        // Set response
        if (!is_null($roylaty))
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
}
