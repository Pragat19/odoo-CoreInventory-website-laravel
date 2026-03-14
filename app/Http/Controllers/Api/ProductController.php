<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Product;
use App\StockLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::with('category', 'unit')->orderBy('id', 'DESC')->get();
        $this->addSuccessResultKeyValue(Keys::DATA, $products);
        $this->setSuccessMessage('Products fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:200',
            'sku'         => 'required|string|max:100|unique:products,sku',
            'category_id' => 'required|integer|exists:master_categories,id',
            'unit_id'     => 'required|integer|exists:master_units,id',
            'stock_qty'   => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $product = Product::create($request->only('name', 'sku', 'category_id', 'unit_id', 'stock_qty'));

        if ($product->stock_qty > 0) {
            StockLedger::create([
                'date'           => Carbon::today(),
                'product_id'     => $product->id,
                'operation'      => StockLedger::OPERATION_RECEIPT,
                'from'           => null,
                'to'             => null,
                'qty'            => $product->stock_qty,
                'reference_id'   => $product->id,
                'reference_type' => 'product_opening',
            ]);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $product->load('category', 'unit'));
        $this->setSuccessMessage('Product created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $product = Product::with('category', 'unit')->find($id);

        if (!$product) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Product not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $product);
        $this->setSuccessMessage('Product fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Product not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:200',
            'sku'         => 'sometimes|string|max:100|unique:products,sku,' . $id,
            'category_id' => 'sometimes|integer|exists:master_categories,id',
            'unit_id'     => 'sometimes|integer|exists:master_units,id',
            'stock_qty'   => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $product->update($request->only('name', 'sku', 'category_id', 'unit_id', 'stock_qty'));

        $this->addSuccessResultKeyValue(Keys::DATA, $product->load('category', 'unit'));
        $this->setSuccessMessage('Product updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Product not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $product->delete();

        $this->setSuccessMessage('Product deleted successfully.');
        return $this->sendSuccessResult();
    }
}
