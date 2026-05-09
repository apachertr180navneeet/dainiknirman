<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\Api\Auth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');



// Auth Routes
Route::post('send-otp', [Auth\LoginController::class, 'sendOtp']);
Route::post('resend-otp', [Auth\LoginController::class, 'resendOtp']);
Route::post('login', [Auth\LoginController::class, 'store']);
Route::post('register', [Auth\LoginController::class, 'register']);
Route::get('logout', [Auth\LoginController::class, 'logout'])->middleware('auth:api');
Route::post('cms', [Api\CmsController::class, 'index']);
//------------

// Profile
Route::middleware(['auth:api'])->prefix('profile')->group(function(){
    Route::get('get', [Api\ProfileController::class, 'index']);
    Route::post('update', [Api\ProfileController::class, 'update']);
    Route::get('get-dashboard', [Api\ProfileController::class, 'getDashboard']);
    Route::post('save-support-enquiry', [Api\ProfileController::class, 'saveSupportEnquiry']);
});

// Book
Route::middleware(['auth:api'])->prefix('book')->group(function(){
    Route::post('ebook-upload', [Api\BookController::class, 'ebookUpload']);
    Route::get('list', [Api\BookController::class, 'getBooks']);
    Route::post('mark-fav-unfav', [Api\BookController::class, 'markBookFavUnfav']);
    Route::get('get-my-favourite-books', [Api\BookController::class, 'getMyFavouriteBooks']);
});

// Magazines
Route::middleware(['auth:api'])->prefix('magazine')->group(function(){
    Route::get('get-magazines', [Api\MagazineController::class, 'index']);
    Route::post('save-magazine', [Api\MagazineController::class, 'saveMagazine']);
    Route::post('update-magazine', [Api\MagazineController::class, 'updateMagazine']);
});

// Conteest
Route::middleware(['auth:api'])->prefix('contest')->group(function(){
    Route::get('get-contest', [Api\ContestController::class, 'getContest']);
    Route::post('save-contest', [Api\ContestController::class, 'saveContest']);
    Route::post('get-contest-result', [Api\ContestController::class, 'getContestResult']);
    Route::get('get-contest-certificate/{contest_id}/{author_id}', [Api\ContestController::class, 'getContestCertificate']);
});

// Route::prefix('contest')->group(function(){
//     Route::get('get-contest-certificate/{contest_id}/{author_id}', [Api\ContestController::class, 'getContestCertificate']);
//     Route::post('delete-contest-certificate', [Api\ContestController::class, 'deleteContestCertificate']);
// });

// Subscription
Route::middleware(['auth:api'])->prefix('subscriptions')->group(function(){
    Route::get('get-subscriptions', [Api\SubscriptionController::class, 'getSubscriptions']);
    Route::post('purchase-subscription', [Api\SubscriptionController::class, 'purchaseSubscription']);
    Route::post('payment-gateway-response', [Api\SubscriptionController::class, 'paymentGatewayResponse']);
    Route::post('purchase-book', [Api\SubscriptionController::class, 'purchaseBook']);
    Route::post('purchase-book-payment-response', [Api\SubscriptionController::class, 'purchaseBookPaymentResponse']);
    Route::get('get-my-subscriptions', [Api\SubscriptionController::class, 'getMySubscriptions']);
    Route::post('get-my-subscription-detail', [Api\SubscriptionController::class, 'getMySubscriptionDetail']);
    Route::get('get-my-active-subscription', [Api\SubscriptionController::class, 'getMyActiveSubscription']);
});

// Anthology
Route::middleware(['auth:api'])->prefix('anthology')->group(function(){
    Route::post('save-anthology', [Api\AnthologyController::class, 'saveAnthology']);
});

// Royalty
Route::middleware(['auth:api'])->prefix('royalty')->group(function(){
    Route::post('get-royalty-data', [Api\RoyaltyController::class, 'getRoyaltyData']);
});

// Setting
Route::middleware(['auth:api'])->prefix('settings')->group(function(){
    Route::get('get-payment-setting', [Api\SettingController::class, 'getPaymentSetting']);
});