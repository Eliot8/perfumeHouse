<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateUserHistory extends Model
{
    use HasFactory;

    protected $table = 'affiliate_users_histories';

    public function affiliate_user()
    {
        return $this->belongsTo(AffiliateUser::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
