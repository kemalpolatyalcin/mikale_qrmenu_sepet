<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'table_number',
        'total_amount',
        'cutlery_requested',
        'payment_method',
        'coupon_code',
        'order_note',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}