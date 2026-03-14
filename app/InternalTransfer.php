<?php

namespace App;

class InternalTransfer extends BaseModel
{
    protected $fillable = ['product_id', 'from_warehouse_id', 'to_warehouse_id', 'qty', 'status'];

    const STATUS_PENDING   = 'pending';
    const STATUS_DONE      = 'done';
    const STATUS_CANCELLED = 'cancelled';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
