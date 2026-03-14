<?php

use App\Constants\EndPoints;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\MasterCategoryController;
use App\Http\Controllers\Api\MasterUnitController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReceiptController;
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

    // Master Category CRUD
    Route::get(EndPoints::master_category_list,     [MasterCategoryController::class, 'index']);
    Route::post(EndPoints::master_category_store,   [MasterCategoryController::class, 'store']);
    Route::get(EndPoints::master_category_show,     [MasterCategoryController::class, 'show']);
    Route::post(EndPoints::master_category_update,  [MasterCategoryController::class, 'update']);
    Route::post(EndPoints::master_category_delete,  [MasterCategoryController::class, 'destroy']);

    // Master Unit CRUD
    Route::get(EndPoints::master_unit_list,     [MasterUnitController::class, 'index']);
    Route::post(EndPoints::master_unit_store,   [MasterUnitController::class, 'store']);
    Route::get(EndPoints::master_unit_show,     [MasterUnitController::class, 'show']);
    Route::post(EndPoints::master_unit_update,  [MasterUnitController::class, 'update']);
    Route::post(EndPoints::master_unit_delete,  [MasterUnitController::class, 'destroy']);
    // Product CRUD
    Route::get(EndPoints::product_list,     [ProductController::class, 'index']);
    Route::post(EndPoints::product_store,   [ProductController::class, 'store']);
    Route::get(EndPoints::product_show,     [ProductController::class, 'show']);
    Route::post(EndPoints::product_update,  [ProductController::class, 'update']);
    Route::post(EndPoints::product_delete,  [ProductController::class, 'destroy']);

    // Receipt CRUD
    Route::get(EndPoints::receipt_list,     [ReceiptController::class, 'index']);
    Route::post(EndPoints::receipt_store,   [ReceiptController::class, 'store']);
    Route::get(EndPoints::receipt_show,     [ReceiptController::class, 'show']);
    Route::post(EndPoints::receipt_update,  [ReceiptController::class, 'update']);
    Route::post(EndPoints::receipt_delete,  [ReceiptController::class, 'destroy']);

    // Delivery Order CRUD
    Route::get(EndPoints::delivery_order_list,         [DeliveryOrderController::class, 'index']);
    Route::post(EndPoints::delivery_order_store,       [DeliveryOrderController::class, 'store']);
    Route::get(EndPoints::delivery_order_show,         [DeliveryOrderController::class, 'show']);
    Route::post(EndPoints::delivery_order_update,      [DeliveryOrderController::class, 'update']);
    Route::post(EndPoints::delivery_order_delete,      [DeliveryOrderController::class, 'destroy']);
    Route::post(EndPoints::delivery_order_changeStatus,[DeliveryOrderController::class, 'changeStatus']);
});

// Middleware Fallback Routes
Route::get(EndPoints::unauthorised, [UserController::class, 'unauthorised'])->name(EndPoints::unauthorised);
Route::get(EndPoints::adminaccess,  [UserController::class, 'adminaccess'])->name(EndPoints::adminaccess);
Route::get(EndPoints::activeaccess, [UserController::class, 'activeaccess'])->name(EndPoints::activeaccess);
