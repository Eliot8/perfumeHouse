<?php

namespace Modules\Delegate\Entities;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zones';
    protected $fillable = [];

    public $timestamps = false;


    public function province() {
        return $this->belongsTo(Province::class);
    }

    public function neighborhoods() {
        return $this->hasMany(Neighborhood::class);
    }
}
