<?php

namespace App;

class StockLedger extends BaseModel
{
    protected $fillable = ['date', 'product_id', 'operation', 'from', 'to', 'qty', 'reference_id'];

    const OPERATION_RECEIPT    = 'receipt';
    const OPERATION_TRANSFER   = 'transfer';
    const OPERATION_DELIVERY   = 'delivery';
    const OPERATION_ADJUSTMENT = 'adjustment';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
