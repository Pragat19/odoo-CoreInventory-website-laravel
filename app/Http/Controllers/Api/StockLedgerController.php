<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Http\Controllers\BaseController;
use App\StockLedger;
use Illuminate\Http\Request;

class StockLedgerController extends BaseController
{
    public function index(Request $request)
    {
        $query = StockLedger::with('product')->orderBy('date', 'DESC')->orderBy('id', 'DESC');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('operation')) {
            $query->where('operation', $request->operation);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $ledgers = $query->get();

        $this->addSuccessResultKeyValue(Keys::DATA, $ledgers);
        $this->addSuccessResultKeyValue('total', $ledgers->count());
        $this->setSuccessMessage('Stock ledger fetched successfully.');
        return $this->sendSuccessResult();
    }
}
