<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\User;
use App\Models\PlanUser;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Orders' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Orders';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.orders.index")->with($this->viewData);
    }

    /**
     * Get Orders list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getOrders(Request $request)
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

        // Get Orders List
        $records_count = Order::GetOrders(null, null, $search, $filter, $sort);
        $records = Order::GetOrders($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $orderNumber = 'N/A';
                $bookName = 'N/A';
                $userName = 'N/A';
                $royaltyAmount = 'N/A';
                $adminAmount = 'N/A';
                $bookAmount = 'N/A';
                $paymentMode = 'N/A';
                $transactionStatus = 'N/A';
                $created = 'N/A';

                $orderNumber = $value->order_number ?? $orderNumber;
                $bookName = $value->book_name ?? $bookName;
                $userName = $value->user_name ?? $userName;
                $royaltyAmount = $value->royalty_amount;
                $adminAmount = $value->admin_amount;
                $bookAmount = $value->amount;
                $paymentMode = $value->payment_mode;
                $transactionStatus = $value->transaction_status;
                $created = date("d-m-Y H:i", strtotime($value->created_at));

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "order_number" => $orderNumber,
                    "book_name" => $bookName,
                    "user_name" => $userName,
                    "royalty_amount" => $royaltyAmount,
                    "admin_amount" => $adminAmount,
                    "book_amount" => $bookAmount,
                    "payment_mode" => $paymentMode,
                    "transaction_status" => $transactionStatus,
                    "created" => $created
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
}
