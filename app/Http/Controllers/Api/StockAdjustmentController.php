<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Product;
use App\StockAdjustment;
use App\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockAdjustmentController extends BaseController
{
    public function index()
    {
        $adjustments = StockAdjustment::with('product', 'location')->orderBy('id', 'DESC')->get();
        $this->addSuccessResultKeyValue(Keys::DATA, $adjustments);
        $this->setSuccessMessage('Stock adjustments fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'  => 'required|integer|exists:products,id',
            'location_id' => 'required|integer|exists:warehouses,id',
            'counted'     => 'required|numeric|min:0',
            'status'      => 'sometimes|in:draft,validated,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $status     = $request->input('status', StockAdjustment::STATUS_DRAFT);
        $product    = Product::find($request->product_id);
        $difference = $request->counted - $product->stock_qty;

        DB::transaction(function () use ($request, $status, $difference, $product, &$adjustment) {
            $adjustment = StockAdjustment::create([
                'product_id'  => $request->product_id,
                'location_id' => $request->location_id,
                'counted'     => $request->counted,
                'difference'  => $difference,
                'status'      => $status,
            ]);

            if ($status === StockAdjustment::STATUS_VALIDATED) {
                $product->update(['stock_qty' => $request->counted]);
            }

            $location = \App\Warehouse::find($request->location_id)->name;

            StockLedger::create([
                'date'         => now()->toDateString(),
                'product_id'   => $request->product_id,
                'operation'    => StockLedger::OPERATION_ADJUSTMENT,
                'from'         => null,
                'to'           => $location,
                'qty'          => $difference,
                'reference_id' => $adjustment->id,
            ]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $adjustment->load('product', 'location'));
        $this->setSuccessMessage('Stock adjustment created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $adjustment = StockAdjustment::with('product', 'location')->find($id);

        if (!$adjustment) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Stock adjustment not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $adjustment);
        $this->setSuccessMessage('Stock adjustment fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $adjustment = StockAdjustment::find($id);

        if (!$adjustment) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Stock adjustment not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($adjustment->status !== StockAdjustment::STATUS_DRAFT) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Only draft adjustments can be updated.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'product_id'  => 'sometimes|integer|exists:products,id',
            'location_id' => 'sometimes|integer|exists:warehouses,id',
            'counted'     => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $productId  = $request->input('product_id', $adjustment->product_id);
        $counted    = $request->input('counted', $adjustment->counted);
        $product    = Product::find($productId);
        $difference = $counted - $product->stock_qty;

        $adjustment->update([
            'product_id'  => $productId,
            'location_id' => $request->input('location_id', $adjustment->location_id),
            'counted'     => $counted,
            'difference'  => $difference,
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $adjustment->fresh()->load('product', 'location'));
        $this->setSuccessMessage('Stock adjustment updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $adjustment = StockAdjustment::find($id);

        if (!$adjustment) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Stock adjustment not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($adjustment->status !== StockAdjustment::STATUS_DRAFT) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Only draft adjustments can be deleted.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $adjustment->delete();

        $this->setSuccessMessage('Stock adjustment deleted successfully.');
        return $this->sendSuccessResult();
    }

    public function changeStatus(Request $request, $id)
    {
        $adjustment = StockAdjustment::find($id);

        if (!$adjustment) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Stock adjustment not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,validated,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $oldStatus = $adjustment->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Adjustment is already in ' . $newStatus . ' status.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($oldStatus !== StockAdjustment::STATUS_DRAFT) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Only draft adjustments can change status.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        DB::transaction(function () use ($adjustment, $newStatus) {
            if ($newStatus === StockAdjustment::STATUS_VALIDATED) {
                $adjustment->product->update(['stock_qty' => $adjustment->counted]);
                StockLedger::create([
                    'date'         => now()->toDateString(),
                    'product_id'   => $adjustment->product_id,
                    'operation'    => StockLedger::OPERATION_ADJUSTMENT,
                    'from'         => null,
                    'to'           => $adjustment->location->name,
                    'qty'          => $adjustment->difference,
                    'reference_id' => $adjustment->id,
                ]);
            }
            $adjustment->update(['status' => $newStatus]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $adjustment->fresh()->load('product', 'location'));
        $this->setSuccessMessage('Stock adjustment status changed to ' . $newStatus . ' successfully.');
        return $this->sendSuccessResult();
    }
}
