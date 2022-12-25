<?php

namespace Modules\Delegate\Entities;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['content', 'order_id', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }


}
