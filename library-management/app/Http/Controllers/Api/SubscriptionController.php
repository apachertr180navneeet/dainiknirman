<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;
use App\Models\User;
use App\Models\Subscription;
use App\Models\PlanUser;
use App\Models\Role;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Royalty;
use App\Models\BookCategoryAuthor;
use Razorpay\Api\Api;

class SubscriptionController extends Controller
{
    /**
     * Get all subscriptions
     */
    public function getSubscriptions(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscriptions not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $data['subscriptions'] = [];

            $subscriptions = Subscription::select("id", "name", "amount", "description", "validity", "status", "type")->get();

            if($subscriptions->count() > 0){
                $status = true;
                $message = "Subscriptions found successfully.";
                $data['subscriptions'] = $subscriptions;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Purchase Subscription
     */
    public function _purchaseSubscription(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    // 'user_id' => 'required|exists:users,id,deleted_at,NULL',
                    'subscription_id' => 'required|exists:subscriptions,id,deleted_at,NULL'
                ],
                [
                    'user_id.required' => 'User ID is required field.',
                    'user_id.exists' => 'User does not exists.',
                    'subscription_id.required' => 'Subscription ID is required field.',
                    'subscription_id.exists' => 'Subscription does not exists.',
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            if(!empty(userHasActivePlan($user->id))){
                $message = "You already have an active plan.";
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $subscriptionDetail = Subscription::find($request->subscription_id);

            // Get razorpay crendentials
            $razorpayKeyId = config('constants.RAZORPAY_KEY_ID');
            $razorpayKeySecret = config('constants.RAZORPAY_KEY_SECRET');

            // Generate random receipt id
            $receiptId = time();
            /* $api = new api($razorpayKeyId, $razorpayKeySecret);

            // Creating order by converting rupees into paisa by multiplying 100
            $order = $api->order->create(array(
                'receipt' => "$receiptId",
                'amount' => $subscriptionDetail->amount * 100,
                'payment_capture' => 1,
                'currency' => 'INR'
            )); */

            // Get Razorpay order id
            // $razorpayOrderId = $order->id;
            $razorpayOrderId = "Order_".time();

            $subscriptionDetailArr = [
                "id" => $subscriptionDetail->id,
                "name" => $subscriptionDetail->name,
                "amount" => $subscriptionDetail->amount,
                "description" => $subscriptionDetail->description,
                "validity" => $subscriptionDetail->validity,
                "type" => $subscriptionDetail->type,
                "status" => $subscriptionDetail->status
            ];

            $userSubscription = [
                'user_id' => $user->id,
                'subscription_id' => $subscriptionDetail->id,
                'subscription_amount' => $subscriptionDetail->amount,
                'subscription_details' => json_encode($subscriptionDetailArr),
                'start_date' => date("Y-m-d H:i:s"),
                'end_date' => date("Y-m-d H:i:s", strtotime("+".$subscriptionDetail->validity." months")),
                'order_number' => $receiptId,
                'razorpay_order_id' => $razorpayOrderId,
                'payment_gateway' => 'RAZORPAY',
                'transaction_status' => 'PENDING',
                'payment_mode' => 'ONLINE',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            $planUser = PlanUser::create($userSubscription);

            if(!empty($planUser)){
                // Set Data
                $status = true;
                $message = "Subscription order generated subscessfully.";
                $data = [
                    'system_order_id' => $receiptId,
                    'transaction_amount' => $subscriptionDetail->amount,
                    'status' => 'Pending',
                    'subscription_id' => $subscriptionDetail->id,
                    'subscription_name' => $subscriptionDetail->name,
                    'razorpay_order_id' => $razorpayOrderId,
                    'currency' => "INR"
                ];
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e);
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    // public function purchaseSubscription(Request $request)
    // {
    //     $user = Auth::user();
    //     $status = false;
    //     $message = "Subscription cannot be saved.";
    //     $data = null;
    //     $statusCode = 200;

    //     DB::beginTransaction();
    //     try {

    //         // ✅ Validation
    //         $validation = Validator::make($request->all(), [
    //             'subscription_id' => 'required|exists:subscriptions,id,deleted_at,NULL'
    //         ], [
    //             'subscription_id.required' => 'Subscription ID is required field.',
    //             'subscription_id.exists' => 'Subscription does not exist.',
    //         ]);

    //         if ($validation->fails()) {
    //             return response()->json([
    //                 'status' => $status,
    //                 'message' => $validation->errors()->first(),
    //                 'data' => $data
    //             ], $statusCode);
    //         }

    //         // ✅ Check active plan
    //         if (!empty(userHasActivePlan($user->id))) {
    //             return response()->json([
    //                 'status' => $status,
    //                 'message' => "You already have an active plan.",
    //                 'data' => $data
    //             ], $statusCode);
    //         }

    //         // ✅ Get subscription details
    //         $subscriptionDetail = Subscription::find($request->subscription_id);

    //         // ✅ Generate order id
    //         $receiptId = time();

    //         // ✅ Prepare subscription data
    //         $subscriptionDetailArr = [
    //             "id" => $subscriptionDetail->id,
    //             "name" => $subscriptionDetail->name,
    //             "amount" => $subscriptionDetail->amount,
    //             "description" => $subscriptionDetail->description,
    //             "validity" => $subscriptionDetail->validity,
    //             "type" => $subscriptionDetail->type,
    //             "status" => $subscriptionDetail->status
    //         ];

    //         // ✅ Save subscription (NO Razorpay)
    //         $userSubscription = [
    //             'user_id' => $user->id,
    //             'subscription_id' => $subscriptionDetail->id,
    //             'subscription_amount' => $subscriptionDetail->amount,
    //             'subscription_details' => json_encode($subscriptionDetailArr),
    //             'start_date' => date("Y-m-d H:i:s"),
    //             'end_date' => date("Y-m-d H:i:s", strtotime("+".$subscriptionDetail->validity." months")),
    //             'order_number' => $receiptId,

    //             // ❌ Removed Razorpay
    //             'razorpay_order_id' => null,

    //             // ✅ Custom payment flow
    //             'payment_gateway' => 'CUSTOM_URL',
    //             'transaction_status' => 'PENDING',
    //             'payment_mode' => 'ONLINE',

    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'created_by' => $user->id,
    //             'updated_by' => $user->id
    //         ];

    //         $planUser = PlanUser::create($userSubscription);

    //         if ($planUser) {

    //             // ✅ Your custom payment URL
    //             $paymentUrl = "https://dainiknirman.com/payment/subcription.php?order_id=" . $receiptId;

    //             $status = true;
    //             $message = "Subscription order created successfully.";

    //             $data = [
    //                 'system_order_id' => $receiptId,
    //                 'transaction_amount' => $subscriptionDetail->amount,
    //                 'status' => 'Pending',
    //                 'subscription_id' => $subscriptionDetail->id,
    //                 'subscription_name' => $subscriptionDetail->name,
    //                 'currency' => "INR",
    //                 'payment_url' => $paymentUrl // ⭐ IMPORTANT
    //             ];
    //         }

    //         DB::commit();

    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         $status = false;
    //         $message = $e->getMessage();
    //         $data = null;
    //         $statusCode = 500;
    //     }

    //     return response()->json([
    //         'status' => $status,
    //         'message' => $message,
    //         'data' => $data
    //     ], $statusCode);
    // }

    public function purchaseSubscription(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {

            // ✅ Validation
            $validation = Validator::make($request->all(), [
                'subscription_id' => 'required|exists:subscriptions,id,deleted_at,NULL'
            ], [
                'subscription_id.required' => 'Subscription ID is required field.',
                'subscription_id.exists' => 'Subscription does not exist.',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => $status,
                    'message' => $validation->errors()->first(),
                    'data' => $data
                ], $statusCode);
            }

            // ✅ Check active plan
            if (!empty(userHasActivePlan($user->id))) {
                return response()->json([
                    'status' => $status,
                    'message' => "You already have an active plan.",
                    'data' => $data
                ], $statusCode);
            }

            // ✅ Get subscription details
            $subscriptionDetail = Subscription::find($request->subscription_id);

            // ✅ Generate order id
            $receiptId = time();

            // ✅ Prepare subscription data
            $subscriptionDetailArr = [
                "id" => $subscriptionDetail->id,
                "name" => $subscriptionDetail->name,
                "amount" => $subscriptionDetail->amount,
                "description" => $subscriptionDetail->description,
                "validity" => $subscriptionDetail->validity,
                "type" => $subscriptionDetail->type,
                "status" => $subscriptionDetail->status
            ];

            // ✅ Save subscription (NO Razorpay)
            $userSubscription = [
                'user_id' => $user->id,
                'subscription_id' => $subscriptionDetail->id,
                'subscription_amount' => $subscriptionDetail->amount,
                'subscription_details' => json_encode($subscriptionDetailArr),
                'start_date' => date("Y-m-d H:i:s"),
                'end_date' => date("Y-m-d H:i:s", strtotime("+".$subscriptionDetail->validity." months")),
                'order_number' => $receiptId,

                // ❌ Removed Razorpay
                'razorpay_order_id' => $receiptId,

                // ✅ Custom payment flow
                'payment_gateway' => 'CUSTOM_URL',
                'transaction_status' => 'PENDING',
                'payment_mode' => 'ONLINE',

                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            $planUser = PlanUser::create($userSubscription);

            if ($planUser) {

                // ✅ Your custom payment URL
                $paymentUrl = "https://dainiknirman.com/payment/subcription.php?order_id=" . $planUser->id;

                $status = true;
                $message = "Subscription order created successfully.";

                $data = [
                    'system_order_id' => $planUser->id,
                    'transaction_amount' => $subscriptionDetail->amount,
                    'status' => 'Pending',
                    'subscription_id' => $subscriptionDetail->id,
                    'subscription_name' => $subscriptionDetail->name,
                    'currency' => "INR",
                    'payment_url' => $paymentUrl // ⭐ IMPORTANT
                ];
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            $status = false;
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Payment gateway response
     * @param system_order_id
     * @param payment_id
     */
    public function _paymentGatewayResponse(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $planUserDetail = PlanUser::where("order_number", $request->system_order_id)->first();

            // Get razorpay crendentials
            $razorpayKeyId = config('constants.RAZORPAY_KEY_ID');
            $razorpayKeySecret = config('constants.RAZORPAY_KEY_SECRET');
            
            if(!empty($planUserDetail) && !empty($planUserDetail->razorpay_order_id)){
                $status = true;
                $message = "Subscription purchase successfully.";
                $data = $planUserDetail->order_number;
            }
            else
            {
                // Generate random receipt id
                /* $api = new api($razorpayKeyId, $razorpayKeySecret);
                $paymentID = $request['payment_id'];
                $payment = $api->payment->fetch($paymentID);

                if($payment->status == 'authorized'){
                    $payment = $api->payment->fetch($paymentID)->capture(array('amount'=>$payment['amount']));
                }

                $gatewayResponse = json_encode($payment->toArray());
                $transactionId = null;
                $transactionStatus = 'FAILED'; */

                $gatewayResponse = json_encode(['status' => 'captured', 'order_id' => uniqid()]);
                $payment = (object)[
                    'status' => 'captured',
                    'order_id' => uniqid()
                ];
                
                if($payment->status === 'captured') 
                {
                    $transactionStatus = 'SUCCESS';
                    $transactionId = $payment->order_id;

                    // Update plan user status
                    $planUserDetail->payment_details = $gatewayResponse;
                    $planUserDetail->transaction_status = $transactionStatus;
                    $planUserDetail->save();

                    // Set user role on success
                    $subscriptionDetail = Subscription::find($planUserDetail->subscription_id);
                    
                    // Get role
                    $role = Role::where([
                        'slug' => strtolower($subscriptionDetail->type)
                    ])->first();

                    $user->role_id = $role->id;
                    $user->updated_at = date("Y-m-d H:i:s");
                    $user->save();
                    $user->assignRole($role->id);

                    $status = true;
                    $message = "Subscription purchase successfully.";
                    $data = $transactionId;
                }
            }

            

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    public function paymentGatewayResponse(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {

            $planUserDetail = PlanUser::where("id", $request->system_order_id)->first();

                
                
                    // Set user role on success
                    $subscriptionDetail = Subscription::find($planUserDetail->subscription_id);
                    
                    // Get role
                    $role = Role::where([
                        'slug' => strtolower($subscriptionDetail->type)
                    ])->first();

                    $user->role_id = $role->id;
                    $user->updated_at = date("Y-m-d H:i:s");
                    $user->save();
                    $user->assignRole($role->id);

                    $status = true;
                    $message = "Subscription purchase successfully.";
                    $data = "";

            

            DB::commit();
        } 
        catch (\Exception $e) {
            dd($e);
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Purchase Book
     */
    public function _purchaseBook(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Book order placement has failed.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'book_id' => 'required|exists:books,id,deleted_at,NULL'
                ],
                [
                    'book_id.required' => 'Subscription ID is required field.',
                    'book_id.exists' => 'Subscription does not exists.'
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $bookDetail = Book::find($request->book_id);

            // Get razorpay crendentials
            $razorpayKeyId = config('constants.RAZORPAY_KEY_ID');
            $razorpayKeySecret = config('constants.RAZORPAY_KEY_SECRET');

            // Generate random receipt id
            $receiptId = time();
            /* $api = new api($razorpayKeyId, $razorpayKeySecret);

            // Creating order by converting rupees into paisa by multiplying 100
            $order = $api->order->create(array(
                'receipt' => "$receiptId",
                'amount' => $subscriptionDetail->amount * 100,
                'payment_capture' => 1,
                'currency' => 'INR'
            )); */

            // Get Razorpay order id
            // $razorpayOrderId = $order->id;
            $razorpayOrderId = "Order_".time();

            $bookDetailArr = [
                "id" => $bookDetail->id,
                "book_name" => $bookDetail->book_name,
                "author_name" => $bookDetail->author_name,
                "launch_date" => $bookDetail->launch_date,
                "book_type" => $bookDetail->book_type,
                "original_price" => $bookDetail->original_price,
                "price" => $bookDetail->price,
                "cover_picture" => $bookDetail->cover_picture,
                "book_pdf" => $bookDetail->book_pdf,
                "description" => $bookDetail->description,
                "status" => $bookDetail->status,
                "book_category" => $bookDetail->book_category,
                "book_category_author" => $bookDetail->bookCategoryAuthor->toArray()
            ];

            $activePlan = userHasValidPlan($user->id);

            $bookOrder = [
                'user_id' => $user->id,
                'order_number' => $receiptId,
                'order_details' => json_encode($bookDetailArr),
                'start_date' => date("Y-m-d H:i:s"),
                'end_date' => $activePlan->end_date ?? date("Y-m-d", strtotime("+1 months")),
                'total_items' => 1,
                'amount' => $bookDetail->price,
                'payment_mode' => 'ONLINE',
                'razorpay_order_id' => $razorpayOrderId,
                'payment_details' => null,
                'payment_gateway' => 'RAZORPAY',
                'transaction_status' => 'PENDING',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            $bookOrder = Order::create($bookOrder);

            if(!empty($bookOrder)){
                // Add order detail
                $orderDetailArr = [
                    'order_id' => $bookOrder->id,
                    'type' => 'BOOK',
                    'type_id' => $bookDetail->id,
                    'item_details' => json_encode($bookDetailArr),
                    'amount' => $bookDetail->price,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                OrderDetail::create($orderDetailArr);

                // Set Data
                $status = true;
                $message = "Book order generated successfully.";
                $data = [
                    'system_order_id' => $receiptId,
                    'transaction_amount' => $bookOrder->amount,
                    'status' => $bookOrder->transaction_status,
                    'order_id' => $bookOrder->id,
                    'book_name' => $bookDetail->book_name,
                    'razorpay_order_id' => $razorpayOrderId,
                    'currency' => "INR"
                ];
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e);
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    // public function purchaseBook(Request $request)
    // {
    //     $user = Auth::user();
    //     $status = false;
    //     $message = "Book order placement has failed.";
    //     $data = null;
    //     $statusCode = 200;

    //     DB::beginTransaction();
    //     try {
    //         $validation = Validator::make($request->all(), [
    //                 'book_id' => 'required|exists:books,id,deleted_at,NULL',
    //                 'book_quantity' => 'required|integer',
    //             ],
    //             [
    //                 'book_id.required' => 'Subscription ID is required field.',
    //                 'book_id.exists' => 'Subscription does not exists.',
    //                 'book_quantity.required' => 'Book quantity is required field.',
    //             ]
    //         );

    //         if($validation->fails()){
    //             $message = $validation->errors()->first();
    //             $response = [
    //                 'status' => $status,
    //                 'message' => $message,
    //                 'data' => $data
    //             ];

    //             return response()->json($response, $statusCode);
    //         }

    //         $bookDetail = Book::find($request->book_id);
    //         $bookAmount = $request->book_quantity * $bookDetail->price;

    //         // Get razorpay crendentials
    //         $paymentSettings = getPaymentSettings();

    //         if(empty($paymentSettings)){
    //             $response = [
    //                 'status' => $status,
    //                 'message' => "Payment settings not found.",
    //                 'data' => $data
    //             ];

    //             return response()->json($response, $statusCode);
    //         }

    //         $razorpayKeyId = $paymentSettings['razorpayKey'];
    //         $razorpayKeySecret = $paymentSettings['razorpaySecret'];

    //         // Generate random receipt id
    //         $receiptId = time();
    //         $api = new api($razorpayKeyId, $razorpayKeySecret);

    //         // Creating order by converting rupees into paisa by multiplying 100
    //         $order = $api->order->create(array(
    //             'receipt' => "$receiptId",
    //             'amount' => $bookAmount * 100,
    //             'payment_capture' => 1,
    //             'currency' => 'INR'
    //         ));

    //         // Get Razorpay order id
    //         $razorpayOrderId = $order->id;
    //         // $razorpayOrderId = "Order_".time();

    //         $bookDetailArr = [
    //             "id" => $bookDetail->id,
    //             "book_name" => $bookDetail->book_name,
    //             "author_name" => $bookDetail->author_name,
    //             "launch_date" => $bookDetail->launch_date,
    //             "book_type" => $bookDetail->book_type,
    //             "original_price" => $bookDetail->original_price,
    //             "price" => $bookDetail->price,
    //             "cover_picture" => $bookDetail->cover_picture,
    //             "book_pdf" => $bookDetail->book_pdf,
    //             "description" => $bookDetail->description,
    //             "status" => $bookDetail->status,
    //             "book_category" => $bookDetail->book_category,
    //             "book_category_author" => $bookDetail->bookCategoryAuthor->toArray()
    //         ];

    //         $activePlan = userHasValidPlan($user->id);

    //         $bookOrder = [
    //             'user_id' => $user->id,
    //             'order_number' => $receiptId,
    //             'order_details' => json_encode($bookDetailArr),
    //             'start_date' => date("Y-m-d H:i:s"),
    //             'end_date' => $activePlan->end_date ?? date("Y-m-d", strtotime("+1 months")),
    //             'total_items' => 1,
    //             'amount' => $bookAmount,
    //             'payment_mode' => 'ONLINE',
    //             'razorpay_order_id' => $razorpayOrderId,
    //             'payment_details' => null,
    //             'payment_gateway' => 'RAZORPAY',
    //             'transaction_status' => 'PENDING',
    //             'created_at' => date("Y-m-d H:i:s"),
    //             'updated_at' => date("Y-m-d H:i:s"),
    //             'created_by' => $user->id,
    //             'updated_by' => $user->id
    //         ];

    //         $bookOrder = Order::create($bookOrder);

    //         if(!empty($bookOrder)){
    //             // Add order detail
    //             $orderDetailArr = [
    //                 'order_id' => $bookOrder->id,
    //                 'type' => 'BOOK',
    //                 'type_id' => $bookDetail->id,
    //                 'item_details' => json_encode($bookDetailArr),
    //                 'amount' => $bookAmount,
    //                 'created_at' => date("Y-m-d H:i:s"),
    //                 'updated_at' => date("Y-m-d H:i:s")
    //             ];
    //             OrderDetail::create($orderDetailArr);

    //             // Set Data
    //             $status = true;
    //             $message = "Book order generated successfully.";
    //             $data = [
    //                 'system_order_id' => $receiptId,
    //                 'transaction_amount' => $bookOrder->amount,
    //                 'status' => $bookOrder->transaction_status,
    //                 'order_id' => $bookOrder->id,
    //                 'book_name' => $bookDetail->book_name,
    //                 'razorpay_order_id' => $razorpayOrderId,
    //                 'currency' => "INR"
    //             ];
    //         }

    //         DB::commit();
    //     } 
    //     catch (\Exception $e) {
    //         DB::rollback();
    //         dd($e);
            
    //         $message = $e->getMessage();
    //         $data = null;
    //         $statusCode = 500;
    //     }

    //     $response = [
    //         'status' => $status,
    //         'message' => $message,
    //         'data' => $data
    //     ];
    //     return response()->json($response, $statusCode);
    // }


    public function purchaseBook(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Book order placement has failed.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {

            // Validation
            $validation = Validator::make($request->all(), [
                'book_id' => 'required|exists:books,id,deleted_at,NULL',
            ], [
                'book_id.required' => 'Subscription ID is required field.',
                'book_id.exists' => 'Subscription does not exist.',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => $status,
                    'message' => $validation->errors()->first(),
                    'data' => $data
                ], $statusCode);
            }

            // Get Book
            $bookDetail = Book::find($request->book_id);
            $bookAmount = $bookDetail->price;

            // Generate Order Number
            $receiptId = time();

            $razorpayOrderId = "Order_".time();

            // Prepare Book Data
            $bookDetailArr = [
                "id" => $bookDetail->id,
                "book_name" => $bookDetail->book_name,
                "author_name" => $bookDetail->author_name,
                "launch_date" => $bookDetail->launch_date,
                "book_type" => $bookDetail->book_type,
                "original_price" => $bookDetail->original_price,
                "price" => $bookDetail->price,
                "cover_picture" => $bookDetail->cover_picture,
                "book_pdf" => $bookDetail->book_pdf,
                "description" => $bookDetail->description,
                "status" => $bookDetail->status,
                "book_category" => $bookDetail->book_category,
                "book_category_author" => $bookDetail->bookCategoryAuthor->toArray()
            ];

            $activePlan = userHasValidPlan($user->id);

            // Create Order (NO Razorpay)
            $bookOrder = [
                'user_id' => $user->id,
                'order_number' => $receiptId,
                'order_details' => json_encode($bookDetailArr),
                'start_date' => date("Y-m-d H:i:s"),
                'end_date' => $activePlan->end_date ?? date("Y-m-d", strtotime("+1 months")),
                'total_items' => 1,
                'amount' => $bookAmount,
                'payment_mode' => 'ONLINE', // changed
                'payment_gateway' => 'payu',   // removed Razorpay
                'razorpay_order_id' => $razorpayOrderId, // removed
                'payment_details' => null,
                'transaction_status' => 'PENDING', // direct success
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            $bookOrder = Order::create($bookOrder);

            if (!empty($bookOrder)) {

                // Order Details
                OrderDetail::create([
                    'order_id' => $bookOrder->id,
                    'type' => 'BOOK',
                    'type_id' => $bookDetail->id,
                    'item_details' => json_encode($bookDetailArr),
                    'amount' => $bookAmount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $paymentUrl = "https://dainiknirman.com/payment/book.php?order_id=" . $receiptId;

                $status = true;
                $message = "Book order created successfully.";

                $data = [
                    'system_order_id' => $receiptId,
                    'transaction_amount' => $bookOrder->amount,
                    'status' => $bookOrder->transaction_status,
                    'order_id' => $bookOrder->id,
                    'book_name' => $bookDetail->book_name,
                    'payment_url' => $paymentUrl
                ];
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            $message = $e->getMessage();
            $statusCode = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Payment gateway response
     * @param system_order_id
     * @param payment_id
     */
    public function purchaseBookPaymentResponse(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Order detail cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $orderDetail = Order::where("order_number", $request->system_order_id)->first();

            if (!empty($orderDetail)) {
                    // Get updated order detail
                    $orderData = Order::find($orderDetail->id);
                    $orderDetailData = OrderDetail::where('order_id', $orderDetail->id)->first();
                    

                    // Add royalty
                    $authorRoyalty = 0;
                    $appRoyalty = 0;
                    $orderBookDetail = json_decode($orderDetailData->item_details);
                    $bookPrice = $orderBookDetail->price;
                    $bookAmount = $orderDetailData->amount;
                    $bookAuthors = [];
                    $bookAuthors = BookCategoryAuthor::where("book_id", $orderBookDetail->id)
                    ->with([
                        'author' => function($query){
                            $query->select('id', 'name');
                        }
                    ])->get();

                    if($orderBookDetail->book_category == "ANTHOLOGY"){
                        // Distribute royalty amount
                        if($bookAuthors->count() > 0){
                            $authorRoyalty = ($bookAmount * 80) / 100;
                            $authorRoyalty = $authorRoyalty / $bookAuthors->count();
                            $appRoyalty = ($bookAmount * 20) / 100;
                        }
                    }
                    else if($orderBookDetail->book_category == "SINGLE_AUTHOR"){
                        // Distribute royalty amount
                        if($bookAuthors->count() > 0){
                            $authorRoyalty = ($bookAmount * 80) / 100;
                            $appRoyalty = ($bookAmount * 20) / 100;
                        }
                    }
                    else{
                        // No action for NATIVE book category
                    }

                    if($authorRoyalty > 0){
                        foreach ($bookAuthors as $key => $value) {
                            $royaltyData = [
                                'order_id' => $orderData->id,
                                'book_id' => $orderDetailData->type_id,
                                'author_id' => $value->author_id,
                                'book_details' => $orderDetailData->item_details,
                                'author_royalty' => $authorRoyalty,
                                'app_royalty' => $appRoyalty,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s"),
                                'created_by' => $user->id,
                                'updated_by' => $user->id
                            ];
        
                            Royalty::create($royaltyData);
                        }
                    }
                    
                    $status = true;
                    $message = "Order completed successfully.";
                // }
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get my subscriptions
     */
    public function getMySubscriptions(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscriptions not found.";
        $data = null;
        $statusCode = 200;
        $limit = config('constants.pagination_limit');

        DB::beginTransaction();
        try {
            $data['plans'] = [];

            $planUsers = PlanUser::select("id", "user_id", "subscription_id", "subscription_amount", DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.name')) as subscription_name"), DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.type')) as subscription_type"), "start_date", "end_date", "order_number", DB::raw("if(date(now()) >= date(start_date) and date(now()) <= date(end_date), true, false) as active_plan"))
            ->where('user_id', $user->id);

            $planUsers = $planUsers->orderBy("id", "desc")->paginate($limit);

            if($planUsers->count() > 0){
                $status = true;
                $message = "Subscriptions found successfully.";
                $data['plans'] = $planUsers;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get my subscriptions
     */
    public function getMySubscriptionDetail(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'subscription_id' => 'required|exists:subscriptions,id,deleted_at,NULL'
                ],
                [
                    'subscription_id.required' => 'Subscription ID is required field.',
                    'subscription_id.exists' => 'Subscription does not exists.'
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $planUsers = PlanUser::select("id", "user_id", "subscription_id", "subscription_amount", DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.name')) as subscription_name"), DB::raw("JSON_UNQUOTE(JSON_EXTRACT(subscription_details, '$.type')) as subscription_type"), "start_date", "end_date", "order_number", DB::raw("if(date(now()) >= date(start_date) and date(now()) <= date(end_date), true, false) as active_plan"))
            ->where('user_id', $user->id)
            ->where('id', $request->subscription_id)
            ->first();

            if(!empty($planUsers)){
                $status = true;
                $message = "Subscription found successfully.";
                $data = $planUsers;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get my active subscriptions
     */
    public function getMyActiveSubscription(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Subscription not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $planUsers = userHasActivePlan($user->id);

            if(!empty($planUsers)){
                $status = true;
                $message = "Subscription found successfully.";
                $data = $planUsers;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }
}
