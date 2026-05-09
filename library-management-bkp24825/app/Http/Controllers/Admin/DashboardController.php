<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    protected $viewData;

    public function __construct(){
        // Constructor
    }

    public function getCategoryCounts()
    {
        $categories = Category::selectRaw("count(id) as total_count, count(CASE when is_active = 1 THEN id END) as active_count, count(CASE when is_active = 0 THEN id END) as inactive_count")->first();

        return $categories;
    }

    public function getServiceCounts()
    {
        $services = Service::selectRaw("count(id) as total_count, count(CASE when is_active = 1 THEN id END) as active_count, count(CASE when is_active = 0 THEN id END) as inactive_count")->first();

        return $services;
    }

    public function getAgentCounts()
    {
        $agents = User::selectRaw("count(id) as total_count, count(CASE when is_active = 1 THEN id END) as active_count, count(CASE when is_active = 0 THEN id END) as inactive_count")->where('role_id', config('constants.roles.AGENT.value'))->first();

        return $agents;
    }

    public function getDistributorCounts()
    {
        $distributors = User::selectRaw("count(id) as total_count, count(CASE when is_active = 1 THEN id END) as active_count, count(CASE when is_active = 0 THEN id END) as inactive_count")->where('role_id', config('constants.roles.DISTRIBUTOR.value'))->first();

        return $distributors;
    }

    public function getCustomerCounts()
    {
        $customers = User::selectRaw("count(id) as total_count, count(CASE when is_active = 1 THEN id END) as active_count, count(CASE when is_active = 0 THEN id END) as inactive_count, count(CASE when itr_status = 'PENDING' THEN id END) as itr_pending_count, count(CASE when itr_status = 'PROCESS' THEN id END) as itr_process_count, count(CASE when itr_status = 'DOCUMENT_INSUFFICIENCY' THEN id END) as itr_insufficiency_count, count(CASE when itr_status = 'DEPARTMENTAL_QUERY' THEN id END) as itr_query_count, count(CASE when itr_status = 'COMPLETE' THEN id END) as itr_complete_count")->where('role_id', config('constants.roles.CUSTOMER.value'))->first();

        return $customers;
    }

    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => ''
        ];

        $data = [];

        // Send view data
        $this->viewData['pageTitle'] = 'Dashboard';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData["data"] = $data;

        return view("admin.dashboard.dashboard")->with($this->viewData);
    }
}
