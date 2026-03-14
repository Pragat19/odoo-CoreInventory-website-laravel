<?php

use App\Constants\EndPoints;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\InternalTransferController;
use App\Http\Controllers\Api\MasterCategoryController;
use App\Http\Controllers\Api\MasterUnitController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\StockAdjustmentController;
use App\Http\Controllers\Api\StockLedgerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
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
    Route::get(EndPoints::user_profile,          [UserController::class, 'profile']);
    Route::post(EndPoints::user_updateProfile,   [UserController::class, 'updateProfile']);
    Route::post(EndPoints::user_logout,          [UserController::class, 'logout']);

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

    // Warehouse CRUD
    Route::get(EndPoints::warehouse_list,    [WarehouseController::class, 'index']);
    Route::post(EndPoints::warehouse_store,  [WarehouseController::class, 'store']);
    Route::get(EndPoints::warehouse_show,    [WarehouseController::class, 'show']);
    Route::post(EndPoints::warehouse_update, [WarehouseController::class, 'update']);
    Route::post(EndPoints::warehouse_delete, [WarehouseController::class, 'destroy']);

    // Internal Transfer CRUD
    Route::get(EndPoints::internal_transfer_list,         [InternalTransferController::class, 'index']);
    Route::post(EndPoints::internal_transfer_store,       [InternalTransferController::class, 'store']);
    Route::get(EndPoints::internal_transfer_show,         [InternalTransferController::class, 'show']);
    Route::post(EndPoints::internal_transfer_update,      [InternalTransferController::class, 'update']);
    Route::post(EndPoints::internal_transfer_delete,      [InternalTransferController::class, 'destroy']);
    Route::post(EndPoints::internal_transfer_changeStatus,[InternalTransferController::class, 'changeStatus']);

    // Stock Adjustment CRUD
    Route::get(EndPoints::stock_adjustment_list,         [StockAdjustmentController::class, 'index']);
    Route::post(EndPoints::stock_adjustment_store,       [StockAdjustmentController::class, 'store']);
    Route::get(EndPoints::stock_adjustment_show,         [StockAdjustmentController::class, 'show']);
    Route::post(EndPoints::stock_adjustment_update,      [StockAdjustmentController::class, 'update']);
    Route::post(EndPoints::stock_adjustment_delete,      [StockAdjustmentController::class, 'destroy']);
    Route::post(EndPoints::stock_adjustment_changeStatus,[StockAdjustmentController::class, 'changeStatus']);

    // Stock Ledger (History)
    Route::get(EndPoints::stock_ledger_list, [StockLedgerController::class, 'index']);

    // Dashboard
    Route::get(EndPoints::dashboard, [DashboardController::class, 'index']);
});

// Middleware Fallback Routes
Route::get(EndPoints::unauthorised, [UserController::class, 'unauthorised'])->name(EndPoints::unauthorised);
Route::get(EndPoints::adminaccess,  [UserController::class, 'adminaccess'])->name(EndPoints::adminaccess);
Route::get(EndPoints::activeaccess, [UserController::class, 'activeaccess'])->name(EndPoints::activeaccess);
