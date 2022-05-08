<?php

namespace Modules\Delegate\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['content', 'order_id', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
