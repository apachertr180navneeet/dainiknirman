<?php
use App\Models\Plan;
use App\Models\PlanUser;
use App\Models\Book;
use App\Models\Setting;

if(!function_exists('userHasValidPlan')){
    function userHasValidPlan($userId, $requiredRoleSlug = null){
        $userPlan = PlanUser::with([
            'user' => function($query) use($requiredRoleSlug){
                $query->select('id',  'role_id', 'name');

                $query->with([
                    'role' => function($query) use($requiredRoleSlug){
                        $query->select('id', 'slug', 'name');

                        if(!empty($requiredRoleSlug)){
                            $query->where("slug", $requiredRoleSlug);
                        }
                    }
                ]);
            }
        ])
        ->where('user_id', $userId)
        ->where(function($query){
            $query->whereDate('start_date', '<=', date("Y-m-d"));
            $query->whereDate('end_date', '>=', date("Y-m-d"));
        })
        ->where("transaction_status", "SUCCESS")
        ->first();

        return $userPlan;
    }
}

/**
 * Get Best Seller Books for dashboard api
 */
if(!function_exists('getBooksByType')){
    function getBooksByType($user, $type, $limit = 1){
        $books = Book::select("id", "book_name", DB::raw("(select group_concat(users.name separator ', ') as authors from book_category_authors inner join users on users.id = author_id where book_id = books.id) as author_name"), "date", "book_type", "original_price", "price", "cover_picture", "book_pdf", "description", "status", DB::raw("(select (case when count(id) > 0 then true ELSE false END) as is_fav from book_favourite where book_id = books.id and user_id = $user->id) as is_favourite"), DB::raw("(select count(id) from orders where JSON_UNQUOTE(JSON_EXTRACT(order_details, '$.id')) = books.id and user_id = ".$user->id." and transaction_status = 'SUCCESS') as is_book_purchase"));

        switch($type){
            case 1: // Best Seller
                $books = $books->withCount('orderDetails')
                ->where('status', config('constants.statuses.ACTIVE.value'))
                ->having('order_details_count', '>', 0)
                ->orderBy("order_details_count", "desc")
                ->limit($limit)->get();
                break;
            case 2: // New Arrival
                $books = $books->where('status', config('constants.statuses.ACTIVE.value'))
                ->orderBy("id", "desc")
                ->limit($limit)->get();
                break;
            case 3: // Free Book
                $books = $books->where('book_type', 'F')
                ->where('status', config('constants.statuses.ACTIVE.value'))
                ->orderBy("id", "desc")
                ->limit($limit)->get();
                break;
            case 4: // All Book
                $books = $books->where('status', config('constants.statuses.ACTIVE.value'))->orderBy("id", "desc")
                ->limit($limit)->get();
                break;
            default:
            $books = $books->where('status', config('constants.statuses.ACTIVE.value'))->orderBy("id", "desc")
            ->limit($limit)->get();
            break;
        }

        return $books;
    }
}

if(!function_exists('getSettingBySlug')){
    function getSettingBySlug($settingSlug){
        $setting = Setting::where('slug', $settingSlug)->first();

        return $setting;
    }
}

if(!function_exists('userHasActivePlan')){
    function userHasActivePlan($userId){
        $planUsers = PlanUser::select("id", "user_id", "subscription_id", "subscription_amount", DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.name')) as subscription_name"), DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.type')) as subscription_type"), "start_date", "end_date", "order_number", DB::raw("if(date(now()) >= date(start_date) and date(now()) <= date(end_date), true, false) as active_plan"))
        ->where('user_id', $userId)
        ->whereRaw("date(now()) >= date(start_date) and date(now()) <= date(end_date)")
        // ->where(function($query){
        //     DB::raw("date(now()) > date(start_date) and date(now()) < date(end_date)");
        // })
        ->orderBy("id", "desc")
        ->first();

        return $planUsers;
    }
}

if(!function_exists('getPaymentSettings')){
    function getPaymentSettings(){
        $data = [];
        $razorpayActiveMode = getSettingBySlug('razorpay-active-mode');

        if(!empty($razorpayActiveMode)){
            if($razorpayActiveMode->value == 'test'){
                $razorpayKey = getSettingBySlug('razorpay-test-key');
                $razorpaySecret = getSettingBySlug('razorpay-test-secret');
            }
            else if($razorpayActiveMode->value == 'live'){
                $razorpayKey = getSettingBySlug('razorpay-live-key');
                $razorpaySecret = getSettingBySlug('razorpay-live-secret');
            }

            if(!empty($razorpayKey) && !empty($razorpaySecret)){
                $data = [
                    'razorpayActiveMode' => $razorpayActiveMode->value,
                    'razorpayKey' => $razorpayKey->value,
                    'razorpaySecret' => $razorpaySecret->value
                ];
            }
        }

        return $data;
    }
}
?>