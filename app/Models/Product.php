<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')
            ->withPivot('stock');
    }
}
