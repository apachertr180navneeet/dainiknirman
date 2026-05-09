<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\PlanUser;
use App\Models\Book;
use App\Models\AuthorEbook;

class DashboardController extends Controller
{
    protected $viewData;

    public function __construct(){
        // Constructor
    }

    public function getUserCounts()
    {
        $users = User::selectRaw("count(id) as total_count, count(CASE when status = 1 THEN id END) as active_count, count(CASE when status = 0 THEN id END) as inactive_count")->first();

        return $users;
    }

    public function getSubscriptionUserCounts()
    {
        $users = User::selectRaw("count(id) as total_count, count(CASE when role_id = ".config('constants.roles.AUTHOR.value')." THEN id END) as author_count, count(CASE when role_id = ".config('constants.roles.READER.value')." THEN id END) as reader_count, count(CASE when role_id = ".config('constants.roles.AUTHOR_READER.value')." THEN id END) as both_count")
        ->where('status', 1)
        ->whereNotNull('role_id')->first();

        return $users;
    }

    public function getSubscriptionCounts()
    {
        $subscriptions = PlanUser::selectRaw("count(CASE when transaction_status = 'SUCCESS' THEN id END) as total_count, count(CASE when transaction_status = 'SUCCESS' and date(created_at) between date_format(curdate(), '%Y-%m-01') and last_day(curdate()) THEN id END) as monthly_count")
        ->first();

        return $subscriptions;
    }

    public function getBooksCounts()
    {
        $books = Book::selectRaw("count(books.id) as total_count, sum(CASE when orders.transaction_status = 'SUCCESS' THEN total_items END) as sale_count, sum(CASE when orders.transaction_status = 'SUCCESS' and date(orders.created_at) between date_format(curdate(), '%Y-%m-01') and last_day(curdate()) THEN total_items END) as monthly_count")
        ->join('order_details', function($join){
            $join->on("order_details.type_id", "=", "books.id");
            $join->where('order_details.type', 'BOOK');
        })
        ->join('orders', function($join){
            $join->on("orders.id", "=", "order_details.order_id");
            $join->where('orders.transaction_status', 'SUCCESS');
        })
        ->first();

        return $books;
    }
    
    public function getContestCounts()
    {
        $contests = DB::select("select count(id) as total_count, sum(case when authors > 0 then authors end) as players_count, sum(current_month) as monthly_count from (select contests.id, (select count(id) from contest_authors where contest_id = contests.id) as authors, (select count(ct.id) from contests as ct where date(created_at) between date_format(curdate(), '%Y-%m-01') and last_day(curdate()) and ct.id = contests.id) as current_month from contests) as t");

        return $contests ? $contests[0] : null;
    }

    public function getEbooksPublicationCounts()
    {
        $books = AuthorEbook::selectRaw("count(CASE when status = 1 THEN id END) as total_count, count(CASE when publish_date is not null and status = 1 THEN id END) as publish_count")->first();

        return $books;
    }

    public function index()
    {
        // Adding breadcrumb array
        $breadcrumb = [
            'Dashboard' => ''
        ];

        $users = $this->getUserCounts();
        $subscriptionUsers = $this->getSubscriptionUserCounts();
        $subscriptions = $this->getSubscriptionCounts();
        $books = $this->getBooksCounts();
        $contests = $this->getContestCounts();
        $ebooks = $this->getEbooksPublicationCounts();

        $data = [
            'users' => [
                'total' => $users->total_count,
                'active' => $users->active_count,
                'inactive' => $users->inactive_count,
            ],
            'subscription_users' => [
                'total' => ($subscriptionUsers->author_count + $subscriptionUsers->reader_count + $subscriptionUsers->both_count),
                'author' => $subscriptionUsers->author_count,
                'reader' => $subscriptionUsers->reader_count,
                'both' => $subscriptionUsers->both_count,
            ],
            'subscriptions' => [
                'total' => $subscriptions->total_count,
                'this_month' => $subscriptions->monthly_count
            ],
            'books' => [
                'total' => $books->total_count,
                'sale_count' => $books->sale_count,
                'monthly_count' => $books->monthly_count
            ],
            'contests' => [
                'total' => $contests->total_count ?? 0,
                'players_count' => $contests->players_count ?? 0,
                'monthly_count' => $contests->current_month ?? 0
            ],
            'ebooks' => [
                'total' => $ebooks->total_count,
                'publish_count' => $ebooks->publish_count
            ],
        ];

        // Send view data
        $this->viewData['pageTitle'] = 'Dashboard';
        $this->viewData['breadcrumb'] = $breadcrumb;
        $this->viewData["data"] = $data;

        return view("admin.dashboard.dashboard")->with($this->viewData);
    }
}
