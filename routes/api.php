<?php

use App\Constants\EndPoints;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post(EndPoints::user_register,        [UserController::class, 'register']);
Route::post(EndPoints::user_login,           [UserController::class, 'login']);
Route::post(EndPoints::user_forgotPassword,  [UserController::class, 'forgotPassword']);
Route::post(EndPoints::user_verifyOtp,        [UserController::class, 'verifyOtp']);
Route::post(EndPoints::user_resetPassword,    [UserController::class, 'resetPassword']);

// Protected Routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::post(EndPoints::user_changePassword, [UserController::class, 'changePassword']);
});

// Middleware Fallback Routes
Route::get(EndPoints::unauthorised, [UserController::class, 'unauthorised'])->name(EndPoints::unauthorised);
Route::get(EndPoints::adminaccess,  [UserController::class, 'adminaccess'])->name(EndPoints::adminaccess);
Route::get(EndPoints::activeaccess, [UserController::class, 'activeaccess'])->name(EndPoints::activeaccess);
