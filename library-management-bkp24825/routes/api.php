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
Route::post('login', [Auth\LoginController::class, 'store']);
Route::post('register', [Auth\LoginController::class, 'register']);
Route::get('logout', [Auth\LoginController::class, 'logout'])->middleware('auth:api');
//------------

// Profile
Route::middleware(['auth:api'])->prefix('profile')->group(function(){
    Route::get('get', [Api\ProfileController::class, 'index']);
    Route::post('update', [Api\ProfileController::class, 'update']);
});

// Book
Route::middleware(['auth:api'])->prefix('book')->group(function(){
    Route::post('ebook-upload', [Api\BookController::class, 'ebookUpload']);
});