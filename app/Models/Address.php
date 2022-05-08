<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Delegate\Entities\Province;

class Address extends Model
{
    protected $fillable = ['set_default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }


    public function province(){
        return $this->belongsTo(Province::class, 'province_id');
    }
}
