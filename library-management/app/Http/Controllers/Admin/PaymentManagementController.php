<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\User;
use App\Models\PlanUser;

class PaymentManagementController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Payments' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Payments';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.payments.index")->with($this->viewData);
    }

    /**
     * Get Payments list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getPayments(Request $request)
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
        $records_count = PlanUser::GetPayments(null, null, $search, $filter, $sort);
        $records = PlanUser::GetPayments($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $orderNumber = 'N/A';
                $subscriptionName = 'N/A';
                $userName = 'N/A';
                $startDate = 'N/A';
                $endDate = 'N/A';
                $paymentMode = 'N/A';
                $transactionStatus = 'N/A';
                $created = 'N/A';

                $orderNumber = $value->order_number ?? $orderNumber;
                $subscriptionName = $value->subscription_name ?? $subscriptionName;
                $userName = $value->user_name ?? $userName;
                $startDate = date("d-m-Y", strtotime($value->start_date));
                $endDate = date("d-m-Y", strtotime($value->end_date));
                $paymentMode = $value->payment_mode;
                $transactionStatus = $value->transaction_status;
                $created = date("d-m-Y H:i", strtotime($value->created_at));

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "order_number" => $orderNumber,
                    "subscription_name" => $subscriptionName,
                    "user_name" => $userName,
                    "start_date" => $startDate,
                    "end_date" => $endDate,
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
