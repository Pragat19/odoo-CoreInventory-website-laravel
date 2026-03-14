<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends BaseController
{
    public function index()
    {
        $this->addSuccessResultKeyValue(Keys::DATA, Warehouse::orderBy('id', 'DESC')->get());
        $this->setSuccessMessage('Warehouses fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:200',
            'location'  => 'nullable|string|max:500',
            'phone'     => 'nullable|string|max:15',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $warehouse = Warehouse::create($request->only('name', 'location', 'phone', 'is_active'));

        $this->addSuccessResultKeyValue(Keys::DATA, $warehouse);
        $this->setSuccessMessage('Warehouse created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Warehouse not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $warehouse);
        $this->setSuccessMessage('Warehouse fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Warehouse not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|string|max:200',
            'location'  => 'nullable|string|max:500',
            'phone'     => 'nullable|string|max:15',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $warehouse->update($request->only('name', 'location', 'phone', 'is_active'));

        $this->addSuccessResultKeyValue(Keys::DATA, $warehouse);
        $this->setSuccessMessage('Warehouse updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Warehouse not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $warehouse->delete();

        $this->setSuccessMessage('Warehouse deleted successfully.');
        return $this->sendSuccessResult();
    }
}
