<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Delegate\Entities\Delegate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryBoyPaymentRequest extends Model
{
    use HasFactory;

    protected $table = 'delivery_boy_payment_requests';

    protected $casts = [
        'attached_pieces' => 'array',
    ];

    public function delegate()
    {
        return $this->belongsTo(Delegate::class, 'delivery_man_id');
    }
}
