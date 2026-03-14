<?php

namespace App;

class DeliveryOrder extends BaseModel
{
    protected $fillable = ['customer_name', 'product_id', 'qty', 'status'];

    const STATUS_PENDING   = 'pending';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
