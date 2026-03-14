<?php

namespace App;

class Warehouse extends BaseModel
{
    protected $fillable = ['name', 'location', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
