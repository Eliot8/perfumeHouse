<?php

namespace Modules\Delegate\Entities;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'delegate_product';
    protected $fillable = [];

    public function delegates() {
        return $this->belongsTo(Delegate::class, 'delegate_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
