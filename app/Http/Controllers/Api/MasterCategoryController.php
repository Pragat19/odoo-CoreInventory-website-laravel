<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterCategoryController extends BaseController
{
    public function index()
    {
        $this->addSuccessResultKeyValue(Keys::DATA, MasterCategory::orderBy('id', 'DESC')->get());
        $this->setSuccessMessage('Master categories fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:100|unique:master_categories,name',
            'display_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $category = MasterCategory::create($request->only('name', 'display_name'));

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        $this->setSuccessMessage('Master category created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $category = MasterCategory::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master category not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        $this->setSuccessMessage('Master category fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $category = MasterCategory::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master category not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|string|max:100|unique:master_categories,name,' . $id,
            'display_name' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $category->update($request->only('name', 'display_name'));

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        $this->setSuccessMessage('Master category updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $category = MasterCategory::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master category not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $category->delete();

        $this->setSuccessMessage('Master category deleted successfully.');
        return $this->sendSuccessResult();
    }
}
