<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
route::apiResource('posts', PostController::class);
route::post('register', [AuthController::class, 'register']);
route::post('login', [AuthController::class, 'login']);
route::post('logout', [AuthController::class, 'logout']);
