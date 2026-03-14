<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\InternalTransfer;
use App\Product;
use App\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InternalTransferController extends BaseController
{
    private function loadRelations($query)
    {
        return $query->with('product', 'fromWarehouse', 'toWarehouse');
    }

    public function index()
    {
        $transfers = $this->loadRelations(InternalTransfer::orderBy('id', 'DESC'))->get();
        $this->addSuccessResultKeyValue(Keys::DATA, $transfers);
        $this->setSuccessMessage('Internal transfers fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'        => 'required|integer|exists:products,id',
            'from_warehouse_id' => 'required|integer|exists:warehouses,id',
            'to_warehouse_id'   => 'required|integer|exists:warehouses,id|different:from_warehouse_id',
            'qty'               => 'required|numeric|min:0.01',
            'status'            => 'sometimes|in:pending,done,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $status = $request->input('status', InternalTransfer::STATUS_PENDING);

        if ($status === InternalTransfer::STATUS_DONE) {
            $product = Product::find($request->product_id);
            if ($product->stock_qty < $request->qty) {
                $this->addFailResultKeyValue(Keys::ERROR, 'Insufficient stock. Available: ' . $product->stock_qty);
                return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
            }
        }

        DB::transaction(function () use ($request, $status, &$transfer) {
            $transfer = InternalTransfer::create([
                'product_id'        => $request->product_id,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'qty'               => $request->qty,
                'status'            => $status,
            ]);

            $from = \App\Warehouse::find($request->from_warehouse_id)->name;
            $to   = \App\Warehouse::find($request->to_warehouse_id)->name;

            StockLedger::create([
                'date'         => now()->toDateString(),
                'product_id'   => $request->product_id,
                'operation'    => StockLedger::OPERATION_TRANSFER,
                'from'         => $from,
                'to'           => $to,
                'qty'          => $request->qty,
                'reference_id' => $transfer->id,
            ]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $transfer->load('product', 'fromWarehouse', 'toWarehouse'));
        $this->setSuccessMessage('Internal transfer created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $transfer = $this->loadRelations(InternalTransfer::where('id', $id))->first();

        if (!$transfer) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Internal transfer not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $transfer);
        $this->setSuccessMessage('Internal transfer fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $transfer = InternalTransfer::find($id);

        if (!$transfer) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Internal transfer not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($transfer->status === InternalTransfer::STATUS_DONE) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Cannot update a completed transfer.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'product_id'        => 'sometimes|integer|exists:products,id',
            'from_warehouse_id' => 'sometimes|integer|exists:warehouses,id',
            'to_warehouse_id'   => 'sometimes|integer|exists:warehouses,id|different:from_warehouse_id',
            'qty'               => 'sometimes|numeric|min:0.01',
            'status'            => 'sometimes|in:pending,done,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $transfer->update($request->only('product_id', 'from_warehouse_id', 'to_warehouse_id', 'qty', 'status'));

        $this->addSuccessResultKeyValue(Keys::DATA, $transfer->fresh()->load('product', 'fromWarehouse', 'toWarehouse'));
        $this->setSuccessMessage('Internal transfer updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $transfer = InternalTransfer::find($id);

        if (!$transfer) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Internal transfer not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($transfer->status === InternalTransfer::STATUS_DONE) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Cannot delete a completed transfer.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $transfer->delete();

        $this->setSuccessMessage('Internal transfer deleted successfully.');
        return $this->sendSuccessResult();
    }

    public function changeStatus(Request $request, $id)
    {
        $transfer = InternalTransfer::find($id);

        if (!$transfer) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Internal transfer not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,done,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $oldStatus = $transfer->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Transfer is already in ' . $newStatus . ' status.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        if ($oldStatus === InternalTransfer::STATUS_DONE) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Cannot change status of a completed transfer.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        // Check stock before marking done
        if ($newStatus === InternalTransfer::STATUS_DONE) {
            $product = Product::find($transfer->product_id);
            if ($product->stock_qty < $transfer->qty) {
                $this->addFailResultKeyValue(Keys::ERROR, 'Insufficient stock. Available: ' . $product->stock_qty);
                return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
            }
        }

        DB::transaction(function () use ($transfer, $newStatus) {
            if ($newStatus === InternalTransfer::STATUS_DONE) {
                StockLedger::create([
                    'date'         => now()->toDateString(),
                    'product_id'   => $transfer->product_id,
                    'operation'    => StockLedger::OPERATION_TRANSFER,
                    'from'         => $transfer->fromWarehouse->name,
                    'to'           => $transfer->toWarehouse->name,
                    'qty'          => $transfer->qty,
                    'reference_id' => $transfer->id,
                ]);
            }
            $transfer->update(['status' => $newStatus]);
        });

        $this->addSuccessResultKeyValue(Keys::DATA, $transfer->fresh()->load('product', 'fromWarehouse', 'toWarehouse'));
        $this->setSuccessMessage('Internal transfer status changed to ' . $newStatus . ' successfully.');
        return $this->sendSuccessResult();
    }
}
