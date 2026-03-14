<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Product;
use App\Receipt;
use App\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReceiptController extends BaseController
{
    public function index()
    {
        $receipts = Receipt::with('product')->orderBy('id', 'DESC')->get();
        $this->addSuccessResultKeyValue(Keys::DATA, $receipts);
        $this->setSuccessMessage('Receipts fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:200',
            'product_id'    => 'required|integer|exists:products,id',
            'qty'           => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        DB::transaction(function () use ($request, &$receipt) {
            $receipt = Receipt::create($request->only('supplier_name', 'product_id', 'qty'));
            Product::where('id', $request->product_id)->increment('stock_qty', $request->qty);
            StockLedger::create([
                'date'         => now()->toDateString(),
                'product_id'   => $request->product_id,
                'operation'    => StockLedger::OPERATION_RECEIPT,
                'from'         => $receipt->supplier_name,
                'to'           => null,
                'qty'          => $request->qty,
                'reference_id' => $receipt->id,
            ]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $receipt->load('product'));
        $this->setSuccessMessage('Receipt created and stock updated successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $receipt = Receipt::with('product')->find($id);

        if (!$receipt) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Receipt not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $receipt);
        $this->setSuccessMessage('Receipt fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $receipt = Receipt::find($id);

        if (!$receipt) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Receipt not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'supplier_name' => 'sometimes|string|max:200',
            'product_id'    => 'sometimes|integer|exists:products,id',
            'qty'           => 'sometimes|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        DB::transaction(function () use ($request, $receipt) {
            $oldProductId = $receipt->product_id;
            $oldQty       = $receipt->qty;
            $newProductId = $request->input('product_id', $oldProductId);
            $newQty       = $request->input('qty', $oldQty);

            // Reverse old qty from old product
            Product::where('id', $oldProductId)->decrement('stock_qty', $oldQty);

            // Apply new qty to new (or same) product
            Product::where('id', $newProductId)->increment('stock_qty', $newQty);

            $receipt->update($request->only('supplier_name', 'product_id', 'qty'));
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $receipt->fresh()->load('product'));
        $this->setSuccessMessage('Receipt updated and stock adjusted successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $receipt = Receipt::find($id);

        if (!$receipt) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Receipt not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        DB::transaction(function () use ($receipt) {
            Product::where('id', $receipt->product_id)->decrement('stock_qty', $receipt->qty);
            $receipt->delete();
        });

        $this->setSuccessMessage('Receipt deleted and stock reversed successfully.');
        return $this->sendSuccessResult();
    }
}
