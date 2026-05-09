<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

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
        });
    });
});
