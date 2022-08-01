<?php

namespace Modules\Delegate\Entities;

use Illuminate\Database\Eloquent\Model;

class WeekOrders extends Model
{

    protected $table = 'week_orders';
    protected $fillable = [];


    public function delegate()
    {
        return $this->belongsTo(Delegate::class, 'delegate_id');
    }

}
