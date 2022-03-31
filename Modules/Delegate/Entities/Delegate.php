<?php

namespace Modules\Delegate\Entities;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Delegate extends Model
{
    protected $table = 'delegates';
    protected $fillable = [];
    protected $casts = ['zones' => 'array'];

    public function province() {
        return $this->belongsTo(Province::class, 'province_id');
    }

    // public function products(){
    //     return $this->belongsToMany(Product::class, 'delegate_product', 'delegate_id');
    // }
    
}
