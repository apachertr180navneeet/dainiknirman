<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\User;

class AnthologyWriteupController extends Controller
{
    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => route('admin.dashboard'),
            'Anthology Writeup' => ''
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Anthology Writeup';
        $this->viewData['breadcrumb'] = $breadcrumb;
        
        return view("admin.anthology-writeups.index")->with($this->viewData);
    }

    /**
     * Get Anthology Writeup list.
     *
     * @return response
     *
     * @author Rajesh
     * @created_at 05-08-2025
     */
    public function getAnthologyWriteups(Request $request)
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
        $records_count = Book::GetAnthologyWriteups(null, null, $search, $filter, $sort);
        $records = Book::GetAnthologyWriteups($limit, $start, $search, $filter, $sort);

        $arr_data = array();

        if(count($records) > 0)
        {
            foreach($records as $key => $value)
            {
                $userMobile = 'N/A';
                $bookName = 'N/A';
                $bookCategory = 'N/A';
                $bookPrice = 'N/A';
                $launchDate = 'N/A';
                $authorAmount = '0';
                $adminAmount = '0';

                $userMobile = $value->user_mobile ?? $userMobile;
                $bookName = $value->book_name ?? $bookName;
                $bookCategory = !empty($value->book_category) ? ucwords(strtolower($value->book_category)) : $bookCategory;
                $bookPrice = $value->price ?? $bookPrice;
                $launchDate = date("d-m-Y", strtotime($value->launch_date));
                $authorAmount = $value->author_royalty ?? $authorAmount;
                $adminAmount = $value->app_royalty ?? $adminAmount;

                // Array Parent Data
                $arr_data[] = array(
                    "id" => $value->id,
                    "book_name" => $bookName,
                    "user_mobile" => $userMobile,
                    "book_category" => $bookCategory,
                    "book_price" => $bookPrice,
                    "launch_date" => $launchDate,
                    "author_amount" => $authorAmount,
                    "admin_amount" => $adminAmount
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