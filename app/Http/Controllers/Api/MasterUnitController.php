<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\MasterUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterUnitController extends BaseController
{
    public function index()
    {
        $this->addSuccessResultKeyValue(Keys::DATA, MasterUnit::orderBy('id', 'DESC')->get());
        $this->setSuccessMessage('Master units fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:50|unique:master_units,name',
            'display_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $unit = MasterUnit::create($request->only('name', 'display_name'));

        $this->addSuccessResultKeyValue(Keys::DATA, $unit);
        $this->setSuccessMessage('Master unit created successfully.');
        return $this->sendSuccessResult();
    }

    public function show($id)
    {
        $unit = MasterUnit::find($id);

        if (!$unit) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master unit not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $unit);
        $this->setSuccessMessage('Master unit fetched successfully.');
        return $this->sendSuccessResult();
    }

    public function update(Request $request, $id)
    {
        $unit = MasterUnit::find($id);

        if (!$unit) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master unit not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|string|max:50|unique:master_units,name,' . $id,
            'display_name' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $unit->update($request->only('name', 'display_name'));

        $this->addSuccessResultKeyValue(Keys::DATA, $unit);
        $this->setSuccessMessage('Master unit updated successfully.');
        return $this->sendSuccessResult();
    }

    public function destroy($id)
    {
        $unit = MasterUnit::find($id);

        if (!$unit) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Master unit not found.');
            return $this->sendFailResultWithCode(ResponseCodes::VALIDATION_ERROR);
        }

        $unit->delete();

        $this->setSuccessMessage('Master unit deleted successfully.');
        return $this->sendSuccessResult();
    }
}
