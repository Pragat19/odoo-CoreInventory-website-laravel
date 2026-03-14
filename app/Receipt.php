<?php

namespace App;

class Receipt extends BaseModel
{
    protected $fillable = ['supplier_name', 'product_id', 'qty'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
