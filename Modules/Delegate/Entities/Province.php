<?php

namespace Modules\Delegate\Entities;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    protected $fillable = [];
    public $timestamps = false;

    public function zones(){
        return $this->hasMany(Zone::class);
    }

    public function delegates() {
        return $this->hasMany(Delegate::class);
    }
}
