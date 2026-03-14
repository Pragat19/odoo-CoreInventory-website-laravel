<?php

namespace App;

class StockAdjustment extends BaseModel
{
    protected $fillable = ['product_id', 'location_id', 'counted', 'difference', 'status'];

    const STATUS_DRAFT     = 'draft';
    const STATUS_VALIDATED = 'validated';
    const STATUS_CANCELLED = 'cancelled';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Warehouse::class, 'location_id');
    }
}
