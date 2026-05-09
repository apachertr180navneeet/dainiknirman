<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Front;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('storage:link');
    $exitCode = Artisan::call('permission:cache-reset');
    return "All cache is cleared";
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
});

Route::get('/call-migrate', function () {
    $exitCode = Artisan::call('migrate');    
    return "All migrations run";
});

Route::get('/run-seeder/{seeder}', function ($seeder) {
    $exitCode = Artisan::call('db:seed '.$seeder);    
    return "$seeder run";
});

// Front Routes
Route::get('/privacy', [Front\PrivacyController::class, "index"])->name('front.privacy');

Route::prefix('user')->group(function(){
    Route::get('/delete-account', [Front\UserController::class, "deleteAccount"])->name('front.user.deleteAccount');
    Route::post('/delete-my-account', [Front\UserController::class, "deleteMyAccount"])->name('front.user.deleteMyAccount');
    Route::post('/check-account', [Front\UserController::class, "checkAccount"])->name('front.user.checkAccount');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/', [Admin\Auth\LoginController::class, "index"])->name('login');
    Route::post("/login", [Admin\Auth\LoginController::class, "authenticate"])->name("login.post");
    Route::get("/forgot-password", [Admin\Auth\ForgotPasswordController::class, "index"])->name("forgotPassword");
    Route::post("/forgot-password", [Admin\Auth\ForgotPasswordController::class, "sendForgotPasswordMail"])->name("forgotPassword.post");
    Route::get("/logout", [Admin\Auth\LoginController::class, "destroy"])->name("logout");

    Route::middleware(["checkadminauth"])->group(function(){
        Route::get("/dashboard", [Admin\DashboardController::class, "index"])->name("dashboard");

        Route::prefix("profile")->group(function(){
            Route::get("/", [Admin\ProfileController::class, "index"])->name("admin.profile.index");
            Route::post("/update", [Admin\ProfileController::class, "update"])->name("admin.profile.update");
        });

        Route::prefix("users")->group(function(){
            Route::get("/", [Admin\UserController::class, "index"])->name("users.index");
            Route::get('/get-users', [Admin\UserController::class, 'getUsers'])->name('users.getUsers');
            Route::get("/create", [Admin\UserController::class, "create"])->name("users.create");
            Route::post("/store", [Admin\UserController::class, "store"])->name("users.store");
            Route::get("/edit/{id}", [Admin\UserController::class, "edit"])->name("users.edit");
            Route::post("/update/{id}", [Admin\UserController::class, "update"])->name("users.update");
            Route::post("/change-status", [Admin\UserController::class, "changeStatus"])->name("users.changeStatus");
            Route::delete('/destroy', [Admin\UserController::class, 'destroy'])->name('users.destroy');
            Route::get('/destroy-single/{id}', [Admin\UserController::class, 'deleteSingle'])->name('users.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\UserController::class, 'export'])->name('users.export');

            // Ajax check
            Route::post('/check-mobile', [Admin\UserController::class, 'checkUserMobile'])->name('users.checkUserMobile');
            Route::post('/check-email', [Admin\UserController::class, 'checkUserEmail'])->name('users.checkUserEmail');
            Route::post('/check-account-number', [Admin\UserController::class, 'checkBankAccountNumber'])->name('users.checkBankAccountNumber');
            Route::post('/ajax-get-send-to-users', [Admin\UserController::class, 'ajaxGetSendTo'])->name('users.ajaxGetSendTo');
        });

        Route::prefix("books")->group(function(){
            Route::get("/", [Admin\BookController::class, "index"])->name("books.index");
            Route::get('/get-books', [Admin\BookController::class, 'getBooks'])->name('books.getBooks');
            Route::get("/create", [Admin\BookController::class, "create"])->name("books.create");
            Route::post("/store", [Admin\BookController::class, "store"])->name("books.store");
            Route::get("/edit/{id}", [Admin\BookController::class, "edit"])->name("books.edit");
            Route::post("/update/{id}", [Admin\BookController::class, "update"])->name("books.update");
            Route::post("/change-status", [Admin\BookController::class, "changeStatus"])->name("books.changeStatus");
            Route::delete('/destroy', [Admin\BookController::class, 'destroy'])->name('books.destroy');
            Route::get('/destroy-single/{id}', [Admin\BookController::class, 'deleteSingle'])->name('books.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\BookController::class, 'export'])->name('books.export');

            // Ajax check
            Route::post('/check-book-name', [Admin\BookController::class, 'checkBookName'])->name('books.checkBookName');
            Route::post('/get-authors', [Admin\BookController::class, 'getAuthors'])->name('books.getAuthors');
        });

        Route::prefix("magazines")->group(function(){
            Route::get("/", [Admin\MagazineController::class, "index"])->name("magazines.index");
            Route::get('/get-magazines', [Admin\MagazineController::class, 'getMagazines'])->name('magazines.getMagazines');
            Route::get("/create", [Admin\MagazineController::class, "create"])->name("magazines.create");
            Route::post("/store", [Admin\MagazineController::class, "store"])->name("magazines.store");
            Route::get("/edit/{id}", [Admin\MagazineController::class, "edit"])->name("magazines.edit");
            Route::post("/update/{id}", [Admin\MagazineController::class, "update"])->name("magazines.update");
            Route::post("/change-status", [Admin\MagazineController::class, "changeStatus"])->name("magazines.changeStatus");
            Route::delete('/destroy', [Admin\MagazineController::class, 'destroy'])->name('magazines.destroy');
            Route::get('/destroy-single/{id}', [Admin\MagazineController::class, 'deleteSingle'])->name('magazines.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\MagazineController::class, 'export'])->name('magazines.export');

            // Ajax check
            Route::post('/check-magazine-name', [Admin\MagazineController::class, 'checkMagazineName'])->name('magazines.checkMagazineName');
        });

        Route::prefix("cms")->group(function(){
            Route::get("/", [Admin\CmsController::class, "index"])->name("cms.index");
            Route::get('/get-cms', [Admin\CmsController::class, 'getCms'])->name('cms.getCms');
            // Route::get("/create", [Admin\CmsController::class, "create"])->name("cms.create");
            // Route::post("/store", [Admin\CmsController::class, "store"])->name("cms.store");
            Route::get("/edit/{id}", [Admin\CmsController::class, "edit"])->name("cms.edit");
            Route::post("/update/{id}", [Admin\CmsController::class, "update"])->name("cms.update");
            Route::post("/change-status", [Admin\CmsController::class, "changeStatus"])->name("cms.changeStatus");
            // Route::delete('/destroy', [Admin\CmsController::class, 'destroy'])->name('cms.destroy');
            // Route::get('/destroy-single/{id}', [Admin\CmsController::class, 'deleteSingle'])->name('cms.deleteSingle');
            // Route::get('/export/{type}/{id?}', [Admin\CmsController::class, 'export'])->name('cms.export');
        });

        Route::prefix("contests")->group(function(){
            Route::get("/", [Admin\ContestController::class, "index"])->name("contests.index");
            Route::get('/get-contests', [Admin\ContestController::class, 'getContests'])->name('contests.getContests');
            Route::get("/create", [Admin\ContestController::class, "create"])->name("contests.create");
            Route::post("/store", [Admin\ContestController::class, "store"])->name("contests.store");
            Route::get("/edit/{id}", [Admin\ContestController::class, "edit"])->name("contests.edit");
            Route::post("/update/{id}", [Admin\ContestController::class, "update"])->name("contests.update");
            Route::post("/change-status", [Admin\ContestController::class, "changeStatus"])->name("contests.changeStatus");
            Route::delete('/destroy', [Admin\ContestController::class, 'destroy'])->name('contests.destroy');
            Route::get('/destroy-single/{id}', [Admin\ContestController::class, 'deleteSingle'])->name('contests.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\ContestController::class, 'export'])->name('contests.export');

            // Ajax check
            Route::post('/check-contest-title', [Admin\ContestController::class, 'checkContestTitle'])->name('contests.checkContestTitle');
        });

        Route::prefix("contest-authors")->group(function(){
            Route::get("/{contest_id}", [Admin\ContestAuthorController::class, "index"])->name("contest-authors.index");
            Route::get('/get-contest-authors/{contest_id}', [Admin\ContestAuthorController::class, 'getContestAuthors'])->name('contest-authors.getContestAuthors');
            Route::get("/edit/{contest_id}/{id}", [Admin\ContestAuthorController::class, "edit"])->name("contest-authors.edit");
            Route::post("/update/{contest_id}/{id}", [Admin\ContestAuthorController::class, "update"])->name("contest-authors.update");
            Route::post("/change-status", [Admin\ContestAuthorController::class, "changeStatus"])->name("contest-authors.changeStatus");
            Route::delete('/destroy', [Admin\ContestAuthorController::class, 'destroy'])->name('contest-authors.destroy');
            Route::get('/destroy-single/{contest_id}/{id}', [Admin\ContestAuthorController::class, 'deleteSingle'])->name('contest-authors.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\ContestAuthorController::class, 'export'])->name('contest-authors.export');

            // Ajax check
            Route::post('/check-contest-rank', [Admin\ContestAuthorController::class, 'checkContestRank'])->name('contest-authors.checkContestRank');
        });

        Route::prefix("author-ebooks")->group(function(){
            Route::get("/", [Admin\EbookController::class, "index"])->name("author-ebooks.index");
            Route::get('/get-author-ebooks', [Admin\EbookController::class, 'getAuthorEbooks'])->name('author-ebooks.getAuthorEbooks');
            Route::get("/edit/{id}", [Admin\EbookController::class, "edit"])->name("author-ebooks.edit");
            Route::post("/update/{id}", [Admin\EbookController::class, "update"])->name("author-ebooks.update");
            Route::post("/change-status", [Admin\EbookController::class, "changeStatus"])->name("author-ebooks.changeStatus");
            Route::delete('/destroy', [Admin\EbookController::class, 'destroy'])->name('author-ebooks.destroy');
            Route::get('/destroy-single/{id}', [Admin\EbookController::class, 'deleteSingle'])->name('author-ebooks.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\EbookController::class, 'export'])->name('author-ebooks.export');

            // Ajax check
            // Route::post('/check-contest-rank', [Admin\EbookController::class, 'checkContestRank'])->name('author-ebooks.checkContestRank');
        });

        Route::prefix("subscriptions")->group(function(){
            Route::get("/", [Admin\SubscriptionController::class, "index"])->name("subscriptions.index");
            Route::get('/get-subscriptions', [Admin\SubscriptionController::class, 'getSubscriptions'])->name('subscriptions.getSubscriptions');
            Route::get("/create", [Admin\SubscriptionController::class, "create"])->name("subscriptions.create");
            Route::post("/store", [Admin\SubscriptionController::class, "store"])->name("subscriptions.store");
            Route::get("/edit/{id}", [Admin\SubscriptionController::class, "edit"])->name("subscriptions.edit");
            Route::post("/update/{id}", [Admin\SubscriptionController::class, "update"])->name("subscriptions.update");
            Route::post("/change-status", [Admin\SubscriptionController::class, "changeStatus"])->name("subscriptions.changeStatus");
            Route::delete('/destroy', [Admin\SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
            Route::get('/destroy-single/{id}', [Admin\SubscriptionController::class, 'deleteSingle'])->name('subscriptions.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\SubscriptionController::class, 'export'])->name('subscriptions.export');

            // Ajax check
            Route::post('/check-subscription-name', [Admin\SubscriptionController::class, 'checkSubscriptionName'])->name('subscriptions.checkSubscriptionName');
        });

        Route::prefix("settings")->group(function(){
            Route::get("/", [Admin\SettingController::class, "index"])->name("settings.index");
            Route::post("/update", [Admin\SettingController::class, "update"])->name("settings.update");
        });

        Route::prefix("royalties")->group(function(){
            Route::get("/", [Admin\RoyaltyController::class, "index"])->name("royalties.index");
            Route::get('/get-royalties', [Admin\RoyaltyController::class, 'getRoyalties'])->name('royalties.getRoyalties');

            Route::get("/calculation", [Admin\RoyaltyController::class, "calculation"])->name("royalties.calculation");
            Route::get('/get-royalties-calculation', [Admin\RoyaltyController::class, 'getRoyaltyCalculation'])->name('royalties.getRoyaltyCalculation');

            Route::get("/create", [Admin\RoyaltyController::class, "create"])->name("royalties.create");
            Route::post("/store", [Admin\RoyaltyController::class, "store"])->name("royalties.store");
            Route::get("/edit/{id}", [Admin\RoyaltyController::class, "edit"])->name("royalties.edit");
            Route::post("/update/{id}", [Admin\RoyaltyController::class, "update"])->name("royalties.update");
            Route::post("/change-status", [Admin\RoyaltyController::class, "changeStatus"])->name("royalties.changeStatus");
            Route::delete('/destroy', [Admin\RoyaltyController::class, 'destroy'])->name('royalties.destroy');
            Route::get('/destroy-single/{id}', [Admin\RoyaltyController::class, 'deleteSingle'])->name('royalties.deleteSingle');
            Route::get('/export/{type}/{id?}', [Admin\RoyaltyController::class, 'export'])->name('royalties.export');
            Route::post('/change-payment-status', [Admin\RoyaltyController::class, 'changePaymentStatus'])->name('royalties.changePaymentStatus');
        });

        Route::prefix("anthology-writeup")->group(function(){
            Route::get("/", [Admin\AnthologyWriteupController::class, "index"])->name("anthologyWriteup.index");
            Route::get('/get-anthology-writeups', [Admin\AnthologyWriteupController::class, 'getAnthologyWriteups'])->name('anthologyWriteup.getAnthologyWriteups');
        });

        Route::prefix("payment-management")->group(function(){
            Route::get("/", [Admin\PaymentManagementController::class, "index"])->name("paymentManagement.index");
            Route::get('/get-payment-management', [Admin\PaymentManagementController::class, 'getPayments'])->name('paymentManagement.getPayments');
        });

        Route::prefix("orders")->group(function(){
            Route::get("/", [Admin\OrderController::class, "index"])->name("orders.index");
            Route::get('/get-orders', [Admin\OrderController::class, 'getOrders'])->name('orders.getOrders');
        });

        Route::prefix("notifications")->group(function(){
            Route::get("/", [Admin\NotificationController::class, "index"])->name("notifications.index");
            Route::post("/ajax-get-send-to-users", [Admin\NotificationController::class, "ajaxGetSendTo"])->name("notifications.ajaxGetSendTo");
            Route::post("/send-notification", [Admin\NotificationController::class, "add"])->name("notifications.add");
        });

        Route::prefix("anthology")->group(function(){
            Route::get("/", [Admin\AnthologyController::class, "index"])->name("anthology.index");
            Route::get('/get-anthologies', [Admin\AnthologyController::class, 'getAnthologies'])->name('anthology.getAnthologies');

            // Route::get("/create", [Admin\AnthologyController::class, "create"])->name("anthology.create");
            // Route::post("/store", [Admin\AnthologyController::class, "store"])->name("anthology.store");

            Route::get("/edit/{id}", [Admin\AnthologyController::class, "edit"])->name("anthology.edit");
            Route::post("/update/{id}", [Admin\AnthologyController::class, "update"])->name("anthology.update");

            Route::post("/change-status", [Admin\AnthologyController::class, "changeStatus"])->name("anthology.changeStatus");
            Route::delete('/destroy', [Admin\AnthologyController::class, 'destroy'])->name('anthology.destroy');
            Route::get('/destroy-single/{id}', [Admin\AnthologyController::class, 'deleteSingle'])->name('anthology.deleteSingle');
            Route::post('/check-title', [Admin\AnthologyController::class, 'checkTitle'])->name('anthology.checkTitle');
        });
    });
});
