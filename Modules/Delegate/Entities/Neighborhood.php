<?php

namespace Modules\Delegate\Entities;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    protected $table = 'neighborhood';
    protected $fillable = ['name', 'zone_id'];

    public $timestamps = false;

    public function zone() {
        return $this->belongsto(Zone::class);
    }
}
