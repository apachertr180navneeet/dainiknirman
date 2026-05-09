<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::post('login', [Api\AuthController::class, 'index']);
Route::get('logout', [Api\AuthController::class, 'logout'])->middleware('auth:api');
Route::get('get-user', [Api\AuthController::class, 'getUser'])->middleware('auth:api');
