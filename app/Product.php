<?php

namespace App;

class Product extends BaseModel
{
    protected $fillable = ['name', 'sku', 'category_id', 'unit_id', 'stock_qty'];

    public function category()
    {
        return $this->belongsTo(MasterCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(MasterUnit::class, 'unit_id');
    }
}
