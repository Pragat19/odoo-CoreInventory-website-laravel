<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\DeliveryOrder;
use App\Http\Controllers\BaseController;
use App\InternalTransfer;
use App\MasterCategory;
use App\MasterUnit;
use App\Product;
use App\Receipt;
use App\StockAdjustment;
use App\Warehouse;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [

            // Products
            'total_products'    => Product::count(),
            'low_stock_items'   => Product::where('stock_qty', '>', 0)->where('stock_qty', '<=', 10)->count(),
            'out_of_stock'      => Product::where('stock_qty', '<=', 0)->count(),

            // Receipts
            'total_receipts'    => Receipt::count(),

            // Deliveries
            'pending_deliveries'   => DeliveryOrder::where('status', DeliveryOrder::STATUS_PENDING)->count(),
            'delivered_orders'     => DeliveryOrder::where('status', DeliveryOrder::STATUS_DELIVERED)->count(),
            'cancelled_deliveries' => DeliveryOrder::where('status', DeliveryOrder::STATUS_CANCELLED)->count(),
            'total_deliveries'     => DeliveryOrder::count(),

            // Internal Transfers
            'pending_transfers'   => InternalTransfer::where('status', InternalTransfer::STATUS_PENDING)->count(),
            'done_transfers'      => InternalTransfer::where('status', InternalTransfer::STATUS_DONE)->count(),
            'cancelled_transfers' => InternalTransfer::where('status', InternalTransfer::STATUS_CANCELLED)->count(),
            'total_transfers'     => InternalTransfer::count(),

            // Stock Adjustments
            'draft_adjustments'     => StockAdjustment::where('status', StockAdjustment::STATUS_DRAFT)->count(),
            'validated_adjustments' => StockAdjustment::where('status', StockAdjustment::STATUS_VALIDATED)->count(),
            'cancelled_adjustments' => StockAdjustment::where('status', StockAdjustment::STATUS_CANCELLED)->count(),
            'total_adjustments'     => StockAdjustment::count(),

            // Masters
            'total_warehouses'  => Warehouse::count(),
            'total_categories'  => MasterCategory::count(),
            'total_units'       => MasterUnit::count(),
        ];

        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        $this->setSuccessMessage('Dashboard fetched successfully.');
        return $this->sendSuccessResult();
    }
}
