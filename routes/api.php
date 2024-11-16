<?php

use App\Http\Controllers\PickupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('users', [UserController::class, 'getlist']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [UserController::class, 'profile']);
    Route::get('me', [UserController::class, 'profile']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('pickups', PickupController::class);
});