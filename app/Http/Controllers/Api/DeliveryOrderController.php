<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\DeliveryOrder;
use App\Http\Controllers\BaseController;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeliveryOrderController extends BaseController
{
    public function index()
    {
        $orders = DeliveryOrder::with('product')->orderBy('id', 'DESC')->get();
        $this->addSuccessResultKeyValue(Keys::DATA, $orders);
        $this->setSuccessMessage('Delivery orders fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:200',
            'product_id'    => 'required|integer|exists:products,id',
            'qty'           => 'required|numeric|min:0.01',
            'status'        => 'sometimes|in:pending,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $status = $request->input('status', DeliveryOrder::STATUS_PENDING);

        // Check stock if delivered
        if ($status === DeliveryOrder::STATUS_DELIVERED) {
            $product = Product::find($request->product_id);
            if ($product->stock_qty < $request->qty) {
                $this->addFailResultKeyValue(Keys::ERROR, 'Insufficient stock. Available: ' . $product->stock_qty);
                return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
            }
        }

        DB::transaction(function () use ($request, $status, &$order) {
            $order = DeliveryOrder::create([
                'customer_name' => $request->customer_name,
                'product_id'    => $request->product_id,
                'qty'           => $request->qty,
                'status'        => $status,
            ]);

            if ($status === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $request->product_id)->decrement('stock_qty', $request->qty);
            }
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $order->load('product'));
        $this->setSuccessMessage('Delivery order created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $order = DeliveryOrder::with('product')->find($id);

        if (!$order) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Delivery order not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $order);
        $this->setSuccessMessage('Delivery order fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $order = DeliveryOrder::find($id);

        if (!$order) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Delivery order not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'sometimes|string|max:200',
            'product_id'    => 'sometimes|integer|exists:products,id',
            'qty'           => 'sometimes|numeric|min:0.01',
            'status'        => 'sometimes|in:pending,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $oldStatus    = $order->status;
        $oldQty       = $order->qty;
        $oldProductId = $order->product_id;
        $newStatus    = $request->input('status', $oldStatus);
        $newQty       = $request->input('qty', $oldQty);
        $newProductId = $request->input('product_id', $oldProductId);

        // Check stock if transitioning to delivered
        if ($newStatus === DeliveryOrder::STATUS_DELIVERED && $oldStatus !== DeliveryOrder::STATUS_DELIVERED) {
            $product = Product::find($newProductId);
            if ($product->stock_qty < $newQty) {
                $this->addFailResultKeyValue(Keys::ERROR, 'Insufficient stock. Available: ' . $product->stock_qty);
                return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
            }
        }

        DB::transaction(function () use ($request, $order, $oldStatus, $oldQty, $oldProductId, $newStatus, $newQty, $newProductId) {
            // Reverse old stock effect if was delivered
            if ($oldStatus === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $oldProductId)->increment('stock_qty', $oldQty);
            }

            // Apply new stock effect if now delivered
            if ($newStatus === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $newProductId)->decrement('stock_qty', $newQty);
            }

            $order->update($request->only('customer_name', 'product_id', 'qty', 'status'));
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $order->fresh()->load('product'));
        $this->setSuccessMessage('Delivery order updated successfully.');
        return $this->sendSuccessResult();
    }

    public function changeStatus(Request $request, $id)
    {
        $order = DeliveryOrder::find($id);

        if (!$order) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Delivery order not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Order is already in ' . $newStatus . ' status.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        // Check stock before deducting
        if ($newStatus === DeliveryOrder::STATUS_DELIVERED && $oldStatus !== DeliveryOrder::STATUS_DELIVERED) {
            $product = Product::find($order->product_id);
            if ($product->stock_qty < $order->qty) {
                $this->addFailResultKeyValue(Keys::ERROR, 'Insufficient stock. Available: ' . $product->stock_qty);
                return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
            }
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            // Reverse stock if moving away from delivered
            if ($oldStatus === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $order->product_id)->increment('stock_qty', $order->qty);
            }

            // Deduct stock if moving to delivered
            if ($newStatus === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $order->product_id)->decrement('stock_qty', $order->qty);
            }

            $order->update(['status' => $newStatus]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $order->fresh()->load('product'));
        $this->setSuccessMessage('Delivery order status changed to ' . $newStatus . ' successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $order = DeliveryOrder::find($id);

        if (!$order) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Delivery order not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        DB::transaction(function () use ($order) {
            // Reverse stock only if it was delivered
            if ($order->status === DeliveryOrder::STATUS_DELIVERED) {
                Product::where('id', $order->product_id)->increment('stock_qty', $order->qty);
            }
            $order->delete();
        });

        $this->setSuccessMessage('Delivery order deleted successfully.');
        return $this->sendSuccessResult();
    }
}
